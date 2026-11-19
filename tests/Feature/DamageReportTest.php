<?php

namespace Tests\Feature;

use App\Models\DamageReport;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DamageReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Setup base dummy room
        Room::create([
            'name' => 'Kamar Melati 01',
            'price' => 750000,
            'facilities' => 'AC, WiFi, Kasur, Lemari',
            'status' => 'available',
            'description' => 'Kamar standar ber-AC',
            'image' => 'default.jpg'
        ]);
    }

    public function test_tenant_can_view_damage_reports_index_and_create_page(): void
    {
        $tenant = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($tenant)->get(route('user.damage-reports.index'));
        $response->assertOk();
        $response->assertSee('Laporan Kerusakan Fasilitas');

        $response = $this->actingAs($tenant)->get(route('user.damage-reports.create'));
        $response->assertOk();
        $response->assertSee('Form Laporan Kerusakan Fasilitas');
    }

    public function test_tenant_can_submit_damage_report_with_photo(): void
    {
        $tenant = User::factory()->create(['role' => 'user']);
        $room = Room::first();

        $photo = UploadedFile::fake()->create('keran_rusak.jpg', 500, 'image/jpeg');

        $response = $this->actingAs($tenant)->post(route('user.damage-reports.store'), [
            'room_id' => $room->id,
            'facility_name' => 'Keran Air Wastafel',
            'category' => 'Pipa / Saluran Air',
            'description' => 'Keran air bocor terus menetes dan tidak bisa ditutup rapat.',
            'photo' => $photo
        ]);

        $response->assertRedirect(route('user.damage-reports.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('damage_reports', [
            'user_id' => $tenant->id,
            'room_id' => $room->id,
            'facility_name' => 'Keran Air Wastafel',
            'category' => 'Pipa / Saluran Air',
            'status' => 'Menunggu',
            'is_read_by_admin' => false,
            'is_read_by_user' => true,
        ]);

        $report = DamageReport::first();
        $this->assertNotNull($report->photo_path);
        // Clean up uploaded test photo
        if ($report->photo_path && file_exists(public_path('images/damage_reports/' . $report->photo_path))) {
            @unlink(public_path('images/damage_reports/' . $report->photo_path));
        }
    }

    public function test_tenant_can_only_view_their_own_damage_report(): void
    {
        $tenant1 = User::factory()->create(['role' => 'user']);
        $tenant2 = User::factory()->create(['role' => 'user']);
        $room = Room::first();

        $report1 = DamageReport::create([
            'user_id' => $tenant1->id,
            'room_id' => $room->id,
            'facility_name' => 'Lampu Kamar Mandi',
            'category' => 'Kelistrikan',
            'description' => 'Lampu redup lalu padam',
            'status' => 'Menunggu'
        ]);

        // Tenant 1 can view
        $response = $this->actingAs($tenant1)->get(route('user.damage-reports.show', $report1));
        $response->assertOk();
        $response->assertSee('Lampu Kamar Mandi');

        // Tenant 2 is forbidden
        $response = $this->actingAs($tenant2)->get(route('user.damage-reports.show', $report1));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_all_reports_and_filter(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenant = User::factory()->create(['role' => 'user']);
        $room = Room::first();

        DamageReport::create([
            'user_id' => $tenant->id,
            'room_id' => $room->id,
            'facility_name' => 'Stopkontak Konslet',
            'category' => 'Kelistrikan',
            'description' => 'Keluar percikan api',
            'status' => 'Menunggu'
        ]);

        $response = $this->actingAs($admin)->get(route('admin.damage-reports.index'));
        $response->assertOk();
        $response->assertSee('Pelaporan Kerusakan Fasilitas');
        $response->assertSee('Stopkontak Konslet');

        // Filter by category
        $response = $this->actingAs($admin)->get(route('admin.damage-reports.index', ['category' => 'Kelistrikan']));
        $response->assertOk();
        $response->assertSee('Stopkontak Konslet');
    }

    public function test_admin_can_update_status_and_provide_response(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $tenant = User::factory()->create(['role' => 'user']);
        $room = Room::first();

        $report = DamageReport::create([
            'user_id' => $tenant->id,
            'room_id' => $room->id,
            'facility_name' => 'Kunci Pintu Rusak',
            'category' => 'Pintu / Jendela',
            'description' => 'Kunci macet',
            'status' => 'Menunggu',
            'is_read_by_admin' => false,
            'is_read_by_user' => true
        ]);

        // When admin opens detail, is_read_by_admin becomes true
        $this->actingAs($admin)->get(route('admin.damage-reports.show', $report));
        $report->refresh();
        $this->assertTrue((bool)$report->is_read_by_admin);

        // Admin updates status to Diproses
        $response = $this->actingAs($admin)->put(route('admin.damage-reports.update', $report), [
            'status' => 'Diproses',
            'admin_response' => 'Teknisi akan datang besok pagi jam 09:00.'
        ]);

        $response->assertRedirect(route('admin.damage-reports.show', $report));
        $report->refresh();

        $this->assertSame('Diproses', $report->status);
        $this->assertSame('Teknisi akan datang besok pagi jam 09:00.', $report->admin_response);
        $this->assertSame($admin->id, $report->admin_id);
        $this->assertFalse((bool)$report->is_read_by_user); // Unread flag set for user!

        // Tenant reads the report, unread flag becomes true
        $this->actingAs($tenant)->get(route('user.damage-reports.show', $report));
        $report->refresh();
        $this->assertTrue((bool)$report->is_read_by_user);
    }

    public function test_non_admin_cannot_access_admin_damage_reports(): void
    {
        $tenant = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($tenant)->get(route('admin.damage-reports.index'));
        $response->assertRedirect(route('dashboard'));
    }
}
