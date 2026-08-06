<?php

namespace Tests\Feature;

use App\Http\Controllers\MemberController;
use App\Models\HargaPaket;
use App\Models\Member;
use App\Models\Pelatih;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertStatus(200);
    }

    public function test_member_registration_default_uses_member_package_price(): void
    {
        HargaPaket::create([
            'nama_paket' => 'member',
            'harga' => 25000,
        ]);

        $controller = new MemberController();
        $response = $controller->index(new Request());

        $this->assertSame(25000, $response->getData()['hargaBulanan']);
    }

    public function test_kasir_cannot_access_price_update_route(): void
    {
        $role = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
        ]);

        $kasir = User::create([
            'name' => 'Kasir Test',
            'username' => 'kasir-test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);

        $this->actingAs($kasir);

        $response = $this->post('/harga/update', [
            'harga' => [1 => 25000],
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_access_price_update_route(): void
    {
        $adminRole = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin-test',
            'password' => Hash::make('secret123'),
            'role_id' => $adminRole->id,
        ]);

        $harga = HargaPaket::create([
            'nama_paket' => 'member',
            'harga' => 10000,
        ]);

        $this->actingAs($admin);

        $response = $this->from('/harga')->post('/harga/update', [
            'harga' => [$harga->id => 25000],
        ]);

        $response->assertRedirect('/harga');
    }

    

    public function test_kasir_cannot_delete_member(): void
    {
        $role = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
        ]);

        $kasir = User::create([
            'name' => 'Kasir Delete Tester',
            'username' => 'kasir-delete-tester',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);

        $member = Member::create([
            'nama_member' => 'Delete Member Tester',
            'nomor_telepon' => '081234567891',
            'tanggal_kadaluarsa' => Carbon::today()->addDays(30),
            'total_checkin' => 0,
        ]);

        $this->actingAs($kasir);

        $response = $this->delete('/member/' . $member->id);

        $response->assertStatus(403);
    }

    public function test_kasir_cannot_delete_pelatih(): void
    {
        $role = Role::create([
            'name' => 'Kasir',
            'slug' => 'kasir',
        ]);

        $kasir = User::create([
            'name' => 'Kasir Delete Pelatih',
            'username' => 'kasir-delete-pelatih',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);

        $pelatih = Pelatih::create([
            'nama_pelatih' => 'Pelatih Delete Tester',
            'nomor_telepon' => '081234567892',
            'tarif_bulanan' => 100000,
            'tarif_harian' => 50000,
            'status_hadir' => 'hadir',
        ]);

        $this->actingAs($kasir);

        $response = $this->delete('/pelatih/' . $pelatih->id);

        $response->assertStatus(403);
    }

    public function test_login_is_rate_limited_after_six_failed_attempts(): void
    {
        $response = null;

        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $response = $this->from('/login')->post('/login', [
                'username' => 'wrong-user',
                'password' => 'wrong-password',
            ]);

            $response->assertStatus(302);
        }

        $response = $this->from('/login')->post('/login', [
            'username' => 'wrong-user',
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(429);
    }

    public function test_expired_qr_token_is_rejected_on_checkin(): void
    {
        $user = User::create([
            'name' => 'QR Tester',
            'username' => 'qr-tester',
            'password' => Hash::make('secret123'),
            'role_id' => null,
        ]);

        $member = Member::create([
            'nama_member' => 'QR Expired Member',
            'nomor_telepon' => '081234567890',
            'tanggal_kadaluarsa' => Carbon::today()->addDays(30),
            'total_checkin' => 0,
        ]);

        $expiredToken = Crypt::encryptString(json_encode([
            'member_id' => $member->id,
            'expires_at' => Carbon::now()->subHours(25)->toIso8601String(),
        ]));

        $response = $this->actingAs($user)->postJson('/proses-checkin-qr', [
            'member_id' => $expiredToken,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'error',
            ])
            ->assertJsonFragment([
                'message' => 'QR Code sudah expired. Silakan generate ulang dari data member.'
            ]);
    }
    public function test_kasir_cannot_update_pelatih_full_data(): void
    {
        $role = Role::firstOrCreate(['slug' => 'kasir'], ['name' => 'Kasir']);
        $kasir = User::create([
            'name' => 'Kasir Pelatih Test',
            'username' => 'kasir-pelatih-test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);

        $pelatih = Pelatih::create([
            'nama_pelatih' => 'Test Pelatih',
            'nomor_telepon' => '081234567899',
            'tarif_bulanan' => 100000,
            'tarif_harian' => 50000,
            'status_hadir' => 'hadir',
        ]);

        $this->actingAs($kasir);

        $response = $this->put('/pelatih/' . $pelatih->id, [
            'nama_pelatih' => 'Updated Name',
            'nomor_telepon' => '081234567899',
            'tarif_harian' => 60000,
            'tarif_bulanan' => 100000,
            'status_hadir' => 'hadir',
        ]);

        $response->assertStatus(403);
    }

    public function test_kasir_can_mark_pelatih_attendance(): void
    {
        $role = Role::firstOrCreate(['slug' => 'kasir'], ['name' => 'Kasir']);
        $kasir = User::create([
            'name' => 'Kasir Absen Test',
            'username' => 'kasir-absen-test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);

        $pelatih = Pelatih::create([
            'nama_pelatih' => 'Test Pelatih Absen',
            'nomor_telepon' => '081234567888',
            'tarif_bulanan' => 100000,
            'tarif_harian' => 50000,
            'status_hadir' => 'hadir',
        ]);

        $this->actingAs($kasir);

        $response = $this->patch('/pelatih/' . $pelatih->id . '/absen', [
            'status_hadir' => 'tidak_hadir',
        ]);

        $response->assertRedirect('/pelatih');
        $this->assertDatabaseHas('pelatih', [
            'id' => $pelatih->id,
            'status_hadir' => 'tidak_hadir',
        ]);
    }

    public function test_admin_can_update_pelatih_full_data(): void
    {
        $role = Role::firstOrCreate(['slug' => 'admin'], ['name' => 'Admin']);
        $admin = User::create([
            'name' => 'Admin Pelatih Test',
            'username' => 'admin-pelatih-test',
            'password' => Hash::make('secret123'),
            'role_id' => $role->id,
        ]);

        $pelatih = Pelatih::create([
            'nama_pelatih' => 'Test Pelatih Admin',
            'nomor_telepon' => '081234567777',
            'tarif_bulanan' => 100000,
            'tarif_harian' => 50000,
            'status_hadir' => 'hadir',
        ]);

        $this->actingAs($admin);

        $response = $this->put('/pelatih/' . $pelatih->id, [
            'nama_pelatih' => 'Updated By Admin',
            'nomor_telepon' => '081234567777',
            'tarif_harian' => 75000,
            'tarif_bulanan' => 120000,
            'status_hadir' => 'hadir',
        ]);

        $response->assertRedirect('/pelatih');
        $this->assertDatabaseHas('pelatih', [
            'id' => $pelatih->id,
            'nama_pelatih' => 'Updated By Admin',
            'tarif_harian' => 75000,
        ]);
    }
    public function test_nonaktif_user_cannot_login()
{
    $role = \App\Models\Role::firstOrCreate(['slug' => 'kasir'], ['name' => 'Kasir']); // Sesuaikan kolom name/nama_role
    $user = \App\Models\User::create([
        'name' => 'User Nonaktif',
        'username' => 'nonaktifuser',
        'password' => bcrypt('password123'),
        'role_id' => $role->id,
        'status' => 'nonaktif'
    ]);

    $response = $this->post('/login', [
        'username' => 'nonaktifuser',
        'password' => 'password123'
    ]);

    $response->assertSessionHas('error', 'Akun Anda tidak aktif. Hubungi admin untuk informasi lebih lanjut.');
    $this->assertGuest();
}

public function test_aktif_user_login_updates_last_login_at()
{
    $role = \App\Models\Role::firstOrCreate(['slug' => 'kasir'], ['name' => 'Kasir']); // Sesuaikan kolom name/nama_role
    $user = \App\Models\User::create([
        'name' => 'User Aktif',
        'username' => 'aktifuser',
        'password' => bcrypt('password123'),
        'role_id' => $role->id,
        'status' => 'aktif'
    ]);

    $this->assertNull($user->last_login_at);

    $response = $this->post('/login', [
        'username' => 'aktifuser',
        'password' => 'password123'
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);
    
    $user->refresh();
    $this->assertNotNull($user->last_login_at);
}
}
