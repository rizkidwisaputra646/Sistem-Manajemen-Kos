<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookingExpirationAndConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    protected User $userA;
    protected User $userB;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(Carbon::now());
        config(['services.midtrans.serverKey' => 'test-server-key-xyz']);

        $this->userA = User::factory()->create([
            'role' => 'user',
            'email' => 'userA@kosku.com',
        ]);

        $this->userB = User::factory()->create([
            'role' => 'user',
            'email' => 'userB@kosku.com',
        ]);

        $this->room = Room::create([
            'name' => 'Kamar Cempaka 01',
            'price' => 750000,
            'facilities' => 'AC, WiFi, Lemari, Kasur',
            'description' => 'Kamar nyaman dan bersih',
            'status' => 'available',
            'image' => 'default.jpg'
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /**
     * 1. Booking pending yang umurnya > 45 menit otomatis dibatalkan saat membuka halaman index.
     */
    public function test_pending_booking_older_than_45_minutes_is_auto_cancelled_on_index(): void
    {
        $booking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $booking->fresh()->status);

        // Majukan waktu 50 menit
        Carbon::setTestNow(Carbon::now()->addMinutes(50));

        // Kunjungi index bookings
        $response = $this->actingAs($this->userA)->get(route('user.bookings.index'));
        $response->assertOk();

        // Status booking otomatis menjadi cancelled
        $this->assertEquals('cancelled', $booking->fresh()->status);
        // Tidak lagi muncul di daftar pesanan aktif
        $response->assertDontSee('Bayar Sekarang');
    }

    /**
     * 2. Booking pending yang umurnya > 45 menit otomatis dibatalkan saat membuka detail pesanan.
     */
    public function test_pending_booking_older_than_45_minutes_is_auto_cancelled_on_show(): void
    {
        $booking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        // Majukan waktu 46 menit
        Carbon::setTestNow(Carbon::now()->addMinutes(46));

        $response = $this->actingAs($this->userA)->get(route('user.bookings.show', $booking));
        $response->assertOk();

        // Status booking otomatis menjadi cancelled
        $this->assertEquals('cancelled', $booking->fresh()->status);
        $response->assertSee('PESANAN TELAH DIBATALKAN');
        $response->assertDontSee('Bayar Sekarang');
    }

    /**
     * 3. Kamar otomatis bebas dan bisa dipesan orang lain jika pesanan sebelumnya sudah lewat 45 menit.
     */
    public function test_room_is_freed_for_new_bookings_when_previous_booking_exceeds_45_minutes(): void
    {
        // Booking lama User A
        $bookingA = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        // Majukan waktu 46 menit (booking User A hangus)
        Carbon::setTestNow(Carbon::now()->addMinutes(46));

        // User B datang dan memesan kamar yang sama pada tanggal yang sama
        $response = $this->actingAs($this->userB)->post(route('rooms.store', $this->room), [
            'check_in_date' => Carbon::today()->toDateString(),
        ]);

        $response->assertSessionHasNoErrors();

        // Booking User B berhasil tersimpan
        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->userB->id,
            'room_id' => $this->room->id,
            'status' => 'pending',
        ]);

        // Booking lama User A otomatis berstatus cancelled
        $this->assertEquals('cancelled', $bookingA->fresh()->status);
    }

    /**
     * 4. Race Condition Protection: Dua pengguna tidak bisa memesan kamar dan tanggal yang sama secara bersamaan.
     */
    public function test_two_concurrent_users_cannot_book_same_room_and_dates_simultaneously(): void
    {
        // User A memesan kamar (masih dalam batas 45 menit)
        $responseA = $this->actingAs($this->userA)->post(route('rooms.store', $this->room), [
            'check_in_date' => Carbon::today()->toDateString(),
        ]);
        $responseA->assertSessionHasNoErrors();

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'status' => 'pending',
        ]);

        // User B mencoba memesan kamar yang sama pada periode yang bentrok
        $responseB = $this->actingAs($this->userB)->post(route('rooms.store', $this->room), [
            'check_in_date' => Carbon::today()->addDays(5)->toDateString(),
        ]);

        // User B harus ditolak karena kamar sedang ditahan User A
        $responseB->assertSessionHasErrors(['check_in_date']);
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->userB->id,
            'room_id' => $this->room->id,
        ]);
    }

    /**
     * 5. Rekonsiliasi & Race Condition pada Callback Terlambat:
     *    Jika User A membayar setelah 45 menit padahal kamar sudah lunas diambil User B,
     *    sistem menolak mengaktifkan sewa User A dan menandai refund manual.
     */
    public function test_late_payment_callback_on_expired_booking_does_not_override_new_tenant(): void
    {
        $today = Carbon::today();
        $oneMonth = $today->copy()->addMonth();

        // 1. User A punya booking yang kedaluwarsa (>45 menit)
        $bookingA = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => $today->toDateString(),
            'check_out_date' => $oneMonth->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'cancelled',
        ]);

        // Majukan waktu 60 menit
        Carbon::setTestNow(Carbon::now()->addMinutes(60));

        // 2. User B berhasil menyewa kamar tersebut sampai lunas (success)
        $bookingB = Booking::create([
            'user_id' => $this->userB->id,
            'room_id' => $this->room->id,
            'check_in_date' => $today->toDateString(),
            'check_out_date' => $oneMonth->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);
        $this->room->update(['status' => 'full']);

        // 3. Tiba-tiba callback Midtrans settlement datang untuk order User A
        $orderIdA = 'KOS-' . $bookingA->id . '-LATE1';
        $grossAmount = number_format($this->room->price, 2, '.', '');
        $statusCode = '200';
        $serverKey = config('services.midtrans.serverKey');
        $signatureKey = hash('sha512', $orderIdA . $statusCode . $grossAmount . $serverKey);

        $response = $this->post(route('midtrans.callback'), [
            'order_id' => $orderIdA,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200);

        // Booking A tetap cancelled dan TIDAK menjadi success
        $this->assertEquals('cancelled', $bookingA->fresh()->status);

        // Kamar tetap dimiliki oleh User B
        $this->assertEquals('success', $bookingB->fresh()->status);
        $this->assertEquals('full', $this->room->fresh()->status);

        // Catatan Payment User A ditandai deny & refund
        $paymentA = Payment::where('order_id', $orderIdA)->first();
        $this->assertNotNull($paymentA);
        $this->assertEquals('deny', $paymentA->status);
        $this->assertArrayHasKey('reconciliation_note', $paymentA->payload);
    }

    /**
     * 6. Callback Midtrans untuk booking kamar baru bersifat idempoten.
     */
    public function test_new_booking_callback_is_idempotent(): void
    {
        $today = Carbon::today();
        $booking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => $today->toDateString(),
            'check_out_date' => $today->copy()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        $orderId = 'KOS-' . $booking->id . '-TEST1';
        $grossAmount = number_format($this->room->price, 2, '.', '');
        $statusCode = '200';
        $serverKey = config('services.midtrans.serverKey');
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payload = [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
        ];

        // Callback 1
        $res1 = $this->post(route('midtrans.callback'), $payload);
        $res1->assertStatus(200);
        $this->assertEquals('success', $booking->fresh()->status);

        // Callback 2 (Duplikat)
        $res2 = $this->post(route('midtrans.callback'), $payload);
        $res2->assertStatus(200);
        $this->assertEquals('success', $booking->fresh()->status);
    }

    /**
     * 7. Perintah Artisan bookings:cancel-expired membatalkan pesanan yang kedaluwarsa.
     */
    public function test_artisan_command_cancels_expired_bookings(): void
    {
        // 2 booking dibuat
        $old1 = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        $old2 = Booking::create([
            'user_id' => $this->userB->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->addMonths(2)->toDateString(),
            'check_out_date' => Carbon::today()->addMonths(3)->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        // Majukan waktu 50 menit
        Carbon::setTestNow(Carbon::now()->addMinutes(50));

        // 1 booking baru (<45 menit)
        $recent = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->addMonths(4)->toDateString(),
            'check_out_date' => Carbon::today()->addMonths(5)->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        Artisan::call('bookings:cancel-expired');

        $this->assertEquals('cancelled', $old1->fresh()->status);
        $this->assertEquals('cancelled', $old2->fresh()->status);
        $this->assertEquals('pending', $recent->fresh()->status);
    }

    /**
     * 8. 1 Akun: User tidak boleh checkout atau memesan kamar lain jika masih memiliki booking yang berstatus pending (<45 menit).
     */
    public function test_user_cannot_checkout_or_book_another_room_while_having_pending_booking(): void
    {
        $room2 = Room::create([
            'name' => 'Kamar Test 2',
            'price' => 750000,
            'description' => 'Kamar kedua',
            'image' => 'room2.jpg',
            'status' => 'available',
            'facilities' => 'AC, WiFi',
        ]);

        // User A punya booking pending pada room 1
        $pendingBooking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
        ]);

        // User A mencoba checkout kamar 2 -> harus diarahkan kembali ke pesanan pendingnya
        $checkoutResponse = $this->actingAs($this->userA)->get(route('rooms.checkout', $room2));
        $checkoutResponse->assertRedirect(route('user.bookings.show', $pendingBooking));
        $checkoutResponse->assertSessionHas('error');

        // User A mencoba post store ke kamar 2 -> harus ditolak dengan error
        $storeResponse = $this->actingAs($this->userA)->post(route('rooms.store', $room2), [
            'check_in_date' => Carbon::today()->toDateString(),
        ]);
        $storeResponse->assertSessionHasErrors(['check_in_date']);
    }

    /**
     * 9. Sinkronisasi Pembayaran: Endpoint sync-payment mengaktifkan booking dan mengunci kamar.
     */
    public function test_sync_payment_endpoint_updates_booking_and_room(): void
    {
        $booking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
            'snap_token' => 'dummy-snap-token',
        ]);

        $response = $this->actingAs($this->userA)->postJson(route('user.bookings.sync-payment', $booking), [
            'order_id' => 'KOS-' . $booking->id . '-TEST',
            'transaction_status' => 'settlement',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'status' => 'success',
        ]);

        $this->assertEquals('success', $booking->fresh()->status);
        $this->assertEquals('full', $this->room->fresh()->status);
    }

    /**
     * 10. Pilihan Metode Bayar: User bisa memilih bayar langsung ke pemilik (manual) saat checkout.
     */
    public function test_user_can_choose_manual_payment_method_at_checkout(): void
    {
        $response = $this->actingAs($this->userA)->post(route('rooms.store', $this->room), [
            'check_in_date' => Carbon::today()->toDateString(),
            'payment_method' => 'manual',
        ]);

        $response->assertSessionHasNoErrors();

        $booking = Booking::where('user_id', $this->userA->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals('manual', $booking->payment_method);
        $this->assertEquals('pending', $booking->status);

        // Pada halaman show, tidak boleh membuat snap token Midtrans
        $showRes = $this->actingAs($this->userA)->get(route('user.bookings.show', $booking));
        $showRes->assertOk();
        $this->assertNull($booking->fresh()->snap_token);
        $showRes->assertSee('Menunggu Konfirmasi Pemilik Kos');
    }

    /**
     * 11. Booking Manual Tidak Hangus dalam 45 Menit.
     */
    public function test_manual_booking_does_not_expire_after_45_minutes(): void
    {
        $booking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'pending',
            'payment_method' => 'manual',
        ]);

        // Majukan waktu 60 menit
        Carbon::setTestNow(Carbon::now()->addMinutes(60));

        // Akses index & show
        $this->actingAs($this->userA)->get(route('user.bookings.index'));
        $this->actingAs($this->userA)->get(route('user.bookings.show', $booking));

        // Booking manual tetap berstatus pending (tidak auto cancelled dalam 45 menit)
        $this->assertEquals('pending', $booking->fresh()->status);
    }

    /**
     * 12. Admin dicegah mengonfirmasi booking jika kamar sudah terisi sewa lain (Double Booking Protection).
     */
    public function test_admin_cannot_confirm_conflicting_booking(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // User A punya booking manual yang sudah expired atau pending
        $bookingA = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'cancelled',
            'payment_method' => 'manual',
        ]);

        // User B sudah memesan dan lunas (success) pada tanggal yang sama
        Booking::create([
            'user_id' => $this->userB->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        // Admin mencoba mengonfirmasi pesanan User A -> harus ditolak
        $response = $this->actingAs($admin)->patch(route('admin.reservations.update-status', $bookingA), [
            'status' => 'success',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals('cancelled', $bookingA->fresh()->status);
    }

    /**
     * 13. User dapat menghapus seluruh riwayat pesanan (clear all history) meskipun memiliki relasi payment.
     */
    public function test_user_can_clear_history_even_with_payment_records(): void
    {
        $pastBooking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->subMonths(2)->toDateString(),
            'check_out_date' => Carbon::today()->subMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        Payment::create([
            'booking_id' => $pastBooking->id,
            'user_id' => $this->userA->id,
            'order_id' => 'ORDER-PAST-TEST-123',
            'amount' => $this->room->price,
            'status' => 'settlement',
        ]);

        $response = $this->actingAs($this->userA)->delete(route('user.bookings.clear-history'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('bookings', ['id' => $pastBooking->id]);
        $this->assertDatabaseMissing('payments', ['order_id' => 'ORDER-PAST-TEST-123']);
    }

    /**
     * 14. User dapat menghapus satu riwayat pesanan yang memiliki relasi payment.
     */
    public function test_user_can_delete_single_history_with_payment_records(): void
    {
        $cancelledBooking = Booking::create([
            'user_id' => $this->userA->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'cancelled',
        ]);

        Payment::create([
            'booking_id' => $cancelledBooking->id,
            'user_id' => $this->userA->id,
            'order_id' => 'ORDER-CANCELLED-TEST-456',
            'amount' => $this->room->price,
            'status' => 'cancel',
        ]);

        $response = $this->actingAs($this->userA)->delete(route('user.bookings.destroy', $cancelledBooking));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('bookings', ['id' => $cancelledBooking->id]);
        $this->assertDatabaseMissing('payments', ['order_id' => 'ORDER-CANCELLED-TEST-456']);
    }
}
