<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\RentalExtension;
use App\Models\Room;
use App\Models\User;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class RentalExtensionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $tenant;
    protected Room $room;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.midtrans.serverKey' => 'test-server-key-12345']);

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@kosku.com',
        ]);

        $this->tenant = User::factory()->create([
            'role' => 'user',
            'email' => 'tenant@gmail.com',
        ]);

        $this->room = Room::create([
            'name' => 'Kamar Dahlia 01',
            'price' => 800000,
            'facilities' => 'AC, WiFi, Kasur, Kamar Mandi Dalam',
            'status' => 'full',
            'description' => 'Kamar nyaman dan bersih',
            'image' => 'default.jpg'
        ]);
    }

    /**
     * 1. Penghuni dapat mengajukan perpanjangan sewa untuk penyewaan aktif.
     */
    public function test_tenant_can_request_rental_extension_for_active_rental(): void
    {
        $checkIn = Carbon::today()->subDays(20);
        $checkOut = $checkIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $response = $this->actingAs($this->tenant)
            ->post(route('user.bookings.extensions.store', $booking));

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('rental_extensions', [
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'price' => $this->room->price,
            'status' => 'pending',
        ]);

        $createdExt = RentalExtension::where('booking_id', $booking->id)->first();
        $this->assertEquals($checkOut->toDateString(), $createdExt->new_check_in_date->toDateString());
        $this->assertEquals($checkOut->copy()->addMonth()->toDateString(), $createdExt->new_check_out_date->toDateString());

        // Masa sewa booking awal belum berubah sebelum lunas
        $this->assertEquals($checkOut->toDateString(), $booking->fresh()->check_out_date);
    }

    /**
     * 2. Penghuni tidak dapat mengajukan perpanjangan untuk kamar milik user lain.
     */
    public function test_tenant_cannot_request_extension_on_another_users_booking(): void
    {
        $otherUser = User::factory()->create(['role' => 'user']);

        $booking = Booking::create([
            'user_id' => $otherUser->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $response = $this->actingAs($this->tenant)
            ->post(route('user.bookings.extensions.store', $booking));

        $response->assertStatus(403);
    }

    /**
     * 3. Penghuni tidak dapat mengajukan dua perpanjangan bersamaan pada booking yang sama.
     */
    public function test_tenant_cannot_request_duplicate_active_extension(): void
    {
        $checkIn = Carbon::today();
        $checkOut = $checkIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        // Buat pengajuan pertama
        RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $checkOut->toDateString(),
            'new_check_in_date' => $checkOut->toDateString(),
            'new_check_out_date' => $checkOut->copy()->addMonth()->toDateString(),
            'price' => $this->room->price,
            'status' => 'pending',
        ]);

        // Coba ajukan kedua
        $response = $this->actingAs($this->tenant)
            ->post(route('user.bookings.extensions.store', $booking));

        $response->assertSessionHas('error');
        $this->assertEquals(1, RentalExtension::where('booking_id', $booking->id)->count());
    }

    /**
     * 4. Admin berhasil menyetujui perpanjangan dan tagihan payment dibuat.
     */
    public function test_admin_can_approve_rental_extension_and_invoice_is_created(): void
    {
        $checkIn = Carbon::today();
        $checkOut = $checkIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $extension = RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $checkOut->toDateString(),
            'new_check_in_date' => $checkOut->toDateString(),
            'new_check_out_date' => $checkOut->copy()->addMonth()->toDateString(),
            'price' => $this->room->price,
            'status' => 'pending',
        ]);

        // Mock Midtrans Snap Token
        $midtransMock = $this->mock(MidtransService::class);
        $midtransMock->shouldReceive('createSnapToken')
            ->once()
            ->andReturn('mock-snap-token-ext-999');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.extensions.approve', $extension));

        $response->assertSessionHas('success');

        $freshExt = $extension->fresh();
        $this->assertEquals('approved', $freshExt->status);
        $this->assertEquals($this->admin->id, $freshExt->admin_id);
        $this->assertNotNull($freshExt->approved_at);

        // Pastikan tagihan di tabel payments dibuat
        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'rental_extension_id' => $extension->id,
            'user_id' => $this->tenant->id,
            'amount' => $this->room->price,
            'status' => 'pending',
            'snap_token' => 'mock-snap-token-ext-999',
        ]);

        $payment = Payment::where('rental_extension_id', $extension->id)->first();
        $this->assertNotNull($payment->payment_expires_at);
        $this->assertTrue(Carbon::parse($payment->payment_expires_at)->greaterThan(now()));
    }

    /**
     * 5. Admin menolak perpanjangan sewa dengan catatan.
     */
    public function test_admin_can_reject_rental_extension_with_notes(): void
    {
        $checkIn = Carbon::today();
        $checkOut = $checkIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $checkIn->toDateString(),
            'check_out_date' => $checkOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $extension = RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $checkOut->toDateString(),
            'new_check_in_date' => $checkOut->toDateString(),
            'new_check_out_date' => $checkOut->copy()->addMonth()->toDateString(),
            'price' => $this->room->price,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.extensions.reject', $extension), [
                'admin_notes' => 'Kamar akan direnovasi pada bulan berikutnya.',
            ]);

        $response->assertSessionHas('success');

        $freshExt = $extension->fresh();
        $this->assertEquals('rejected', $freshExt->status);
        $this->assertEquals('Kamar akan direnovasi pada bulan berikutnya.', $freshExt->admin_notes);
        $this->assertEquals($this->admin->id, $freshExt->admin_id);

        // Booking induk tetap aman dan tidak berubah
        $this->assertEquals('success', $booking->fresh()->status);
        $this->assertEquals($checkOut->toDateString(), $booking->fresh()->check_out_date);
    }

    /**
     * 6. Kamar yang sedang ditahan oleh perpanjangan berstatus approved tidak bisa dibooking user baru.
     */
    public function test_new_user_cannot_book_period_held_by_approved_extension(): void
    {
        $extStart = Carbon::today()->addDays(5);
        $extEnd = $extStart->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->subDays(25)->toDateString(),
            'check_out_date' => $extStart->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $extension = RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $extStart->toDateString(),
            'new_check_in_date' => $extStart->toDateString(),
            'new_check_out_date' => $extEnd->toDateString(),
            'price' => $this->room->price,
            'status' => 'approved',
            'admin_id' => $this->admin->id,
            'approved_at' => now(),
        ]);

        // Buat payment pending aktif (belum expired)
        Payment::create([
            'booking_id' => $booking->id,
            'rental_extension_id' => $extension->id,
            'user_id' => $this->tenant->id,
            'order_id' => 'EXT-' . $extension->id . '-TEST',
            'amount' => $this->room->price,
            'status' => 'pending',
            'payment_expires_at' => now()->addMinutes(45),
        ]);

        // User baru coba booking kamar tersebut pada rentang periode yang bentrok
        $newUser = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($newUser)->post(route('rooms.store', $this->room), [
            'check_in_date' => $extStart->copy()->addDays(2)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['check_in_date']);
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $newUser->id,
            'room_id' => $this->room->id,
        ]);
    }

    /**
     * 7. Callback Midtrans settlement memperpanjang masa sewa booking induk.
     */
    public function test_midtrans_webhook_settlement_extends_rental_period(): void
    {
        $oldCheckIn = Carbon::today()->subDays(25);
        $oldCheckOut = $oldCheckIn->copy()->addMonth();
        $newCheckIn = $oldCheckOut->copy();
        $newCheckOut = $newCheckIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $oldCheckIn->toDateString(),
            'check_out_date' => $oldCheckOut->toDateString(),
            'due_date' => $oldCheckOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $extension = RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $oldCheckOut->toDateString(),
            'new_check_in_date' => $newCheckIn->toDateString(),
            'new_check_out_date' => $newCheckOut->toDateString(),
            'price' => $this->room->price,
            'status' => 'approved',
            'admin_id' => $this->admin->id,
        ]);

        $orderId = 'EXT-' . $extension->id . '-ABC12';
        $grossAmount = number_format($this->room->price, 2, '.', '');
        $statusCode = '200';
        $serverKey = config('services.midtrans.serverKey');
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'rental_extension_id' => $extension->id,
            'user_id' => $this->tenant->id,
            'order_id' => $orderId,
            'amount' => $this->room->price,
            'status' => 'pending',
            'payment_expires_at' => now()->addHour(),
        ]);

        // Kirim callback Midtrans
        $response = $this->post(route('midtrans.callback'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(200);

        // Verifikasi payment lunas
        $this->assertEquals('settlement', $payment->fresh()->status);
        // Verifikasi status extension paid
        $this->assertEquals('paid', $extension->fresh()->status);
        // Verifikasi booking check_out_date diperpanjang
        $this->assertEquals($newCheckOut->toDateString(), $booking->fresh()->check_out_date);
    }

    /**
     * 8. Callback Midtrans duplikat bersifat idempoten (tidak memperpanjang dua kali).
     */
    public function test_duplicate_midtrans_callback_is_idempotent(): void
    {
        $oldCheckIn = Carbon::today()->subDays(25);
        $oldCheckOut = $oldCheckIn->copy()->addMonth();
        $newCheckIn = $oldCheckOut->copy();
        $newCheckOut = $newCheckIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $oldCheckIn->toDateString(),
            'check_out_date' => $oldCheckOut->toDateString(),
            'due_date' => $oldCheckOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $extension = RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $oldCheckOut->toDateString(),
            'new_check_in_date' => $newCheckIn->toDateString(),
            'new_check_out_date' => $newCheckOut->toDateString(),
            'price' => $this->room->price,
            'status' => 'approved',
            'admin_id' => $this->admin->id,
        ]);

        $orderId = 'EXT-' . $extension->id . '-IDEMP1';
        $grossAmount = number_format($this->room->price, 2, '.', '');
        $statusCode = '200';
        $serverKey = config('services.midtrans.serverKey');
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'rental_extension_id' => $extension->id,
            'user_id' => $this->tenant->id,
            'order_id' => $orderId,
            'amount' => $this->room->price,
            'status' => 'pending',
            'payment_expires_at' => now()->addHour(),
        ]);

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

        $expectedDate = $newCheckOut->toDateString();
        $this->assertEquals($expectedDate, $booking->fresh()->check_out_date);

        // Callback 2 (Duplikat)
        $res2 = $this->post(route('midtrans.callback'), $payload);
        $res2->assertStatus(200);

        // Tanggal sewa tetap sama, tidak bertambah lagi
        $this->assertEquals($expectedDate, $booking->fresh()->check_out_date);
    }

    /**
     * 9. Callback pembayaran expired menandai perpanjangan expired dan tidak memperpanjang sewa.
     */
    public function test_expired_payment_callback_does_not_extend_rental(): void
    {
        $oldCheckIn = Carbon::today()->subDays(25);
        $oldCheckOut = $oldCheckIn->copy()->addMonth();

        $booking = Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => $oldCheckIn->toDateString(),
            'check_out_date' => $oldCheckOut->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $extension = RentalExtension::create([
            'booking_id' => $booking->id,
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'previous_check_out_date' => $oldCheckOut->toDateString(),
            'new_check_in_date' => $oldCheckOut->toDateString(),
            'new_check_out_date' => $oldCheckOut->copy()->addMonth()->toDateString(),
            'price' => $this->room->price,
            'status' => 'approved',
            'admin_id' => $this->admin->id,
        ]);

        $orderId = 'EXT-' . $extension->id . '-EXP01';
        $grossAmount = number_format($this->room->price, 2, '.', '');
        $statusCode = '200';
        $serverKey = config('services.midtrans.serverKey');
        $signatureKey = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'rental_extension_id' => $extension->id,
            'user_id' => $this->tenant->id,
            'order_id' => $orderId,
            'amount' => $this->room->price,
            'status' => 'pending',
            'payment_expires_at' => now()->subMinutes(10),
        ]);

        $response = $this->post(route('midtrans.callback'), [
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signatureKey,
            'transaction_status' => 'expire',
        ]);

        $response->assertStatus(200);

        $this->assertEquals('expire', $payment->fresh()->status);
        $this->assertEquals('expired', $extension->fresh()->status);
        // Booking check_out_date tidak berubah
        $this->assertEquals($oldCheckOut->toDateString(), $booking->fresh()->check_out_date);
    }

    /**
     * 10. Penghuni yang sudah punya 1 sewa aktif tidak boleh checkout kamar baru.
     */
    public function test_tenant_with_active_rental_cannot_checkout_new_room(): void
    {
        // Sewa aktif pertama
        Booking::create([
            'user_id' => $this->tenant->id,
            'room_id' => $this->room->id,
            'check_in_date' => Carbon::today()->toDateString(),
            'check_out_date' => Carbon::today()->addMonth()->toDateString(),
            'total_price' => $this->room->price,
            'status' => 'success',
        ]);

        $room2 = Room::create([
            'name' => 'Kamar Dahlia 02',
            'price' => 850000,
            'facilities' => 'AC, WiFi',
            'description' => 'Kamar Dahlia 02 lantai atas',
            'status' => 'available',
            'image' => 'default.jpg'
        ]);

        // Coba buka halaman checkout kamar 2
        $resCheckout = $this->actingAs($this->tenant)->get(route('rooms.checkout', $room2));
        $resCheckout->assertRedirect(route('dashboard'));
        $resCheckout->assertSessionHas('error');

        // Coba submit booking kamar 2
        $resStore = $this->actingAs($this->tenant)->post(route('rooms.store', $room2), [
            'check_in_date' => Carbon::today()->addDays(2)->toDateString(),
        ]);
        $resStore->assertSessionHasErrors(['check_in_date']);

        // Tidak boleh ada booking baru
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->tenant->id,
            'room_id' => $room2->id,
        ]);
    }
}
