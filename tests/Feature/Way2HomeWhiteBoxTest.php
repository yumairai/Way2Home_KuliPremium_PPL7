<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\{Hash, Storage, Event, Mail, Auth, DB, Http};
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Mockery;

// --- MODEL IMPORTS ---
use App\Models\{User, Customer, Mandor, DesainRumah, DetailProyekBangun, DokumenProyek,
    RequestRenovasi, PenawaranRenovasi, Proyek, DetailProyekRenovasi, Material, Cart,
    OrderMaterial, DetailOrder, PembayaranProyek, ProyekMilestone, ProyekAktivitas,
    ProyekDokumentasi, ProgressProyek, NegosiasiRenovasi, MandorActivityHistory};

// --- SERVICE IMPORTS ---
use App\Services\{RekomendasiService, NotificationService, SupabaseStorageService};

class Way2HomeWhiteBoxTest extends TestCase
{
    use RefreshDatabase;

    // ────────────────────────────────────────────────────────────────────────
    // SETUP & HELPERS
    // ────────────────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
        Mail::fake();
        
        // Mock default Supabase behavior unless explicitly overridden
        $this->supabaseMock = Mockery::mock(SupabaseStorageService::class);
        $this->app->instance(SupabaseStorageService::class, $this->supabaseMock);
    }

    protected function createUser(array $attributes = []): User
    {
        return User::create(array_merge([
            'name' => 'John Doe',
            'email' => 'user_' . uniqid() . '@way2home.id',
            'password' => Hash::make('Secret123!'),
            'role' => 'customer',
            'phone_number' => '08123456789',
            'email_verified_at' => now(),
        ], $attributes));
    }

    protected function createCustomer(User $user): Customer
    {
        return Customer::create([
            'user_id' => $user->id,
            'no_hp' => $user->phone_number,
        ]);
    }

    protected function createMandor(User $user, array $attributes = []): Mandor
    {
        return Mandor::create(array_merge([
            'user_id' => $user->id,
            'sertifikasi' => 'Sertifikasi Keahlian',
            'area_kerja' => 'Bandung',
            'lama_pengalaman' => 5,
            'status' => 'aktif',
        ], $attributes));
    }

    protected function createDesainRumah(array $attributes = []): DesainRumah
    {
        return DesainRumah::create(array_merge([
            'tipe_rumah' => 'Tipe 36 Modern',
            'deskripsi' => 'Desain rumah modern minimalis tipe 36.',
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_tanah' => 72,
            'luas_bangunan' => 36,
            'jumlah_kamar_tidur' => 2,
            'jumlah_kamar_mandi' => 1,
            'jumlah_lantai' => 1,
            'estimasi_biaya' => 300000000,
            'estimasi_durasi' => 4,
            'tahun_bangun' => 2023,
            'material_utama' => 'Beton',
            'material_digunakan' => 'Semen: 50; Bata: 1000;',
        ], $attributes));
    }

    protected function createProyek(Customer $customer, Mandor $mandor = null, array $attributes = []): Proyek
    {
        return Proyek::create(array_merge([
            'customer_id' => $customer->id,
            'mandor_id' => $mandor ? $mandor->id : null,
            'jenis_proyek' => 'Bangun Rumah',
            'alamat_proyek' => 'Jl. Merdeka No. 10, Bandung',
            'tanggal_mulai' => now(),
            'status_proyek' => 'Menunggu Verifikasi',
        ], $attributes));
    }

    protected function createMaterial(array $attributes = []): Material
    {
        return Material::create(array_merge([
            'nama_material' => 'Semen Padang',
            'kategori' => 'Semen',
            'harga' => 60000,
            'deskripsi' => 'Semen Padang kualitas premium.',
            'stok' => 100,
            'satuan' => 'Sak',
        ], $attributes));
    }

    protected function callPrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 1: AuthController (WB001–WB007)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb001_login_with_valid_credentials_redirects_to_dashboard(): void
    {
        $user = $this->createUser([
            'email' => 'customer@way2home.id',
            'password' => Hash::make('Secret123!'),
            'role' => 'customer'
        ]);
        $this->createCustomer($user);

        $response = $this->postJson('/login', [
            'email' => 'customer@way2home.id',
            'password' => 'Secret123!',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('redirect', route('customer-layouts.dashboard'));
    }

    /** @test */
    public function test_wb002_login_with_wrong_password_returns_401(): void
    {
        $user = $this->createUser([
            'email' => 'customer@way2home.id',
            'password' => Hash::make('Secret123!')
        ]);

        $response = $this->postJson('/login', [
            'email' => 'customer@way2home.id',
            'password' => 'WrongPassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Email atau Password Salah']);
    }

    /** @test */
    public function test_wb003_login_with_unregistered_email_returns_401(): void
    {
        $response = $this->postJson('/login', [
            'email' => 'ghost@way2home.id',
            'password' => 'anypass',
        ]);

        $response->assertStatus(401)
                 ->assertJson(['message' => 'Email atau Password Salah']);
    }

    /** @test */
    public function test_wb004_register_with_valid_data_creates_user_and_customer(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Budi Santoso',
            'email' => 'new@way2home.id',
            'password' => 'NewPass123!',
            'password_confirmation' => 'NewPass123!',
            'phone_number' => '08123456789',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'Register berhasil, cek email untuk verifikasi.']);

        $this->assertDatabaseHas('users', ['email' => 'new@way2home.id']);
        $user = User::where('email', 'new@way2home.id')->first();
        $this->assertDatabaseHas('customers', ['user_id' => $user->id]);
    }

    /** @test */
    public function test_wb005_register_with_duplicate_email_returns_validation_error(): void
    {
        $this->createUser(['email' => 'existing@way2home.id']);

        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'existing@way2home.id',
            'password' => 'Pass1234!',
            'password_confirmation' => 'Pass1234!',
            'phone_number' => '08123456789',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /** @test */
    public function test_wb006_register_with_mismatched_passwords_returns_validation_error(): void
    {
        $response = $this->postJson('/register', [
            'name' => 'Test User',
            'email' => 'test@way2home.id',
            'password' => 'Pass1234!',
            'password_confirmation' => 'DifferentPass!',
            'phone_number' => '08123456789',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }

    /** @test */
    public function test_wb007_logout_invalidates_session(): void
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $this->assertFalse(Auth::check());
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 2: RekomendasiService (WB008–WB021)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb008_normalize_scales_values_to_unit_interval(): void
    {
        $service = new RekomendasiService();
        $stats = ['min' => 10, 'max' => 110];
        $result = $this->callPrivateMethod($service, 'normalize', [60.0, $stats]);
        $this->assertEquals(0.5, $result);
    }

    /** @test */
    public function test_wb009_normalize_boundary_max_value_returns_one(): void
    {
        $service = new RekomendasiService();
        $stats = ['min' => 10, 'max' => 110];
        $result = $this->callPrivateMethod($service, 'normalize', [110.0, $stats]);
        $this->assertEquals(1.0, $result);
    }

    /** @test */
    public function test_wb010_normalize_boundary_min_value_returns_zero(): void
    {
        $service = new RekomendasiService();
        $stats = ['min' => 10, 'max' => 110];
        $result = $this->callPrivateMethod($service, 'normalize', [10.0, $stats]);
        $this->assertEquals(0.0, $result);
    }

    /** @test */
    public function test_wb011_proximity_similarity_calculates_correct_score_for_exact_match(): void
    {
        $service = new RekomendasiService();
        $result = $this->callPrivateMethod($service, 'proximitySimilarity', [0.5, 0.5]);
        $this->assertEquals(1.0, $result);
    }

    /** @test */
    public function test_wb012_proximity_similarity_calculates_correct_score_for_difference(): void
    {
        $service = new RekomendasiService();
        $result = $this->callPrivateMethod($service, 'proximitySimilarity', [0.8, 0.5]);
        $this->assertEquals(0.7, $result);
    }

    /** @test */
    public function test_wb013_harga_similarity_handles_under_budget_correctly(): void
    {
        $service = new RekomendasiService();
        $result = $this->callPrivateMethod($service, 'hargaSimilarity', [0.4, 0.6]);
        $this->assertEquals(0.9, $result); // 1.0 - |-0.2| * 0.5 = 0.9
    }

    /** @test */
    public function test_wb014_harga_similarity_penalizes_over_budget_correctly(): void
    {
        $service = new RekomendasiService();
        $result = $this->callPrivateMethod($service, 'hargaSimilarity', [0.7, 0.5]);
        $this->assertEquals(0.6, $result); // 1.0 - 0.2 * 2.0 = 0.6
    }

    /** @test */
    public function test_wb015_hitung_skor_sums_weighted_components_correctly(): void
    {
        $service = new RekomendasiService();
        $desain = $this->createDesainRumah([
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_tanah' => 190, // stats range [30, 350] -> normalizes to 0.5
            'jumlah_kamar_tidur' => 5, // stats range [1, 10] -> normalizes to ~0.444
            'estimasi_biaya' => 7859241750, // stats range [22500, 15718461000] -> normalizes to 0.5
        ]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_area' => 190,
            'jumlah_kamar' => 5,
            'budget' => 7859241750,
            'prioritas' => 'biaya',
        ];

        $normUser = [
            'luas' => 0.5,
            'kamar' => 0.444,
            'harga' => 0.5,
        ];

        $weights = ['lokasi' => 0.10, 'gaya' => 0.10, 'luas' => 0.10, 'kamar' => 0.15, 'harga' => 0.55];

        $score = $this->callPrivateMethod($service, 'hitungSkor', [$desain, $preferensi, $normUser, $weights]);
        $this->assertGreaterThan(0.9, $score);
    }

    /** @test */
    public function test_wb016_rekomendasi_ranks_desains_by_location_match(): void
    {
        $service = new RekomendasiService();
        $d1 = $this->createDesainRumah(['lokasi' => 'Bandung', 'gaya_arsitektur' => 'Modern', 'estimasi_biaya' => 200_000_000]);
        $d2 = $this->createDesainRumah(['lokasi' => 'Jakarta', 'gaya_arsitektur' => 'Modern', 'estimasi_biaya' => 200_000_000]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_area' => 100,
            'jumlah_kamar' => 3,
            'budget' => 300_000_000,
            'prioritas' => 'biaya',
        ];

        $recommendations = $service->rekomendasikan($preferensi);

        $this->assertEquals($d1->id, $recommendations->first()['id']);
    }

    /** @test */
    public function test_wb017_rekomendasi_ranks_desains_by_style_match(): void
    {
        $service = new RekomendasiService();
        $d1 = $this->createDesainRumah(['lokasi' => 'Bandung', 'gaya_arsitektur' => 'Modern', 'estimasi_biaya' => 200_000_000]);
        $d2 = $this->createDesainRumah(['lokasi' => 'Bandung', 'gaya_arsitektur' => 'Classic', 'estimasi_biaya' => 200_000_000]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Classic',
            'luas_area' => 100,
            'jumlah_kamar' => 3,
            'budget' => 300_000_000,
            'prioritas' => 'biaya',
        ];

        $recommendations = $service->rekomendasikan($preferensi);

        $this->assertEquals($d2->id, $recommendations->first()['id']);
    }

    /** @test */
    public function test_wb018_rekomendasi_ranks_desains_with_biaya_priority(): void
    {
        $service = new RekomendasiService();
        $dCheap = $this->createDesainRumah(['estimasi_biaya' => 100_000_000]);
        $dExp   = $this->createDesainRumah(['estimasi_biaya' => 900_000_000]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_area' => 70,
            'jumlah_kamar' => 2,
            'budget' => 150_000_000,
            'prioritas' => 'biaya',
        ];

        $recommendations = $service->rekomendasikan($preferensi);
        $this->assertEquals($dCheap->id, $recommendations->first()['id']);
    }

    /** @test */
    public function test_wb019_rekomendasi_ranks_desains_with_estetik_priority(): void
    {
        $service = new RekomendasiService();
        $dStyle = $this->createDesainRumah(['lokasi' => 'Bandung', 'gaya_arsitektur' => 'Modern', 'estimasi_biaya' => 800_000_000]);
        $dPlain = $this->createDesainRumah(['lokasi' => 'Jakarta', 'gaya_arsitektur' => 'Minimalis', 'estimasi_biaya' => 200_000_000]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_area' => 70,
            'jumlah_kamar' => 2,
            'budget' => 300_000_000,
            'prioritas' => 'estetik',
        ];

        $recommendations = $service->rekomendasikan($preferensi);
        $this->assertEquals($dStyle->id, $recommendations->first()['id']);
    }

    /** @test */
    public function test_wb020_rekomendasi_ranks_desains_with_cepat_priority(): void
    {
        $service = new RekomendasiService();
        $dFit   = $this->createDesainRumah(['luas_tanah' => 100]);
        $dHuge  = $this->createDesainRumah(['luas_tanah' => 300]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_area' => 100,
            'jumlah_kamar' => 3,
            'budget' => 400_000_000,
            'prioritas' => 'cepat',
        ];

        $recommendations = $service->rekomendasikan($preferensi);
        $this->assertEquals($dFit->id, $recommendations->first()['id']);
    }

    /** @test */
    public function test_wb021_rekomendasi_returns_sorted_by_score_descending(): void
    {
        $service = new RekomendasiService();
        $d1 = $this->createDesainRumah(['estimasi_biaya' => 200_000_000]);
        $d2 = $this->createDesainRumah(['estimasi_biaya' => 400_000_000]);
        $d3 = $this->createDesainRumah(['estimasi_biaya' => 600_000_000]);

        $preferensi = [
            'lokasi' => 'Bandung',
            'gaya_arsitektur' => 'Modern',
            'luas_area' => 70,
            'jumlah_kamar' => 2,
            'budget' => 300_000_000,
            'prioritas' => 'biaya',
        ];

        $recommendations = $service->rekomendasikan($preferensi);
        $scores = $recommendations->pluck('skor')->toArray();

        $sortedScores = $scores;
        rsort($sortedScores);

        $this->assertEquals($sortedScores, $scores);
    }

    // Helper functions checks
    /** @test */
    public function test_wb_helper_format_harga(): void
    {
        $this->assertEquals('Rp 500 jt', RekomendasiService::formatHarga(500_000_000));
        $this->assertEquals('Rp 1,5 M', RekomendasiService::formatHarga(1_500_000_000));
    }

    /** @test */
    public function test_wb_helper_estimasi_durasi(): void
    {
        $this->assertEquals('3 Bulan', RekomendasiService::estimasiDurasi(60));
        $this->assertEquals('4 Bulan', RekomendasiService::estimasiDurasi(120));
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 3: Document Upload and Verification (WB022–WB029)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb022_upload_valid_project_documents_saves_successfully(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);
        $desain = $this->createDesainRumah();

        $this->supabaseMock->shouldReceive('uploadPrivate')->times(3)->andReturn('fake-docs/doc.pdf');

        $response = $this->actingAs($user)->postJson('/proyek/ajukan', [
            'package' => 'paket-komplit',
            'alamat_proyek' => 'Jl. Anggrek No. 12',
            'desain_id' => $desain->id,
            'sertifikat_tanah' => UploadedFile::fake()->create('sertifikat.pdf', 500, 'application/pdf'),
            'ktp_pemilik' => UploadedFile::fake()->create('ktp.png', 500, 'image/png'),
            'imb_pbg' => UploadedFile::fake()->create('imb.jpg', 500, 'image/jpeg'),
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('proyek', [
            'customer_id' => $customer->id,
            'status_proyek' => 'Menunggu Verifikasi',
        ]);
        $this->assertDatabaseHas('dokumen_proyek', [
            'jenis_dokumen' => 'Sertifikat Tanah',
            'status_verifikasi' => 'pending',
        ]);
    }

    /** @test */
    public function test_wb023_upload_project_with_invalid_mime_rejected(): void
    {
        $user = $this->createUser();
        $this->createCustomer($user);
        $desain = $this->createDesainRumah();

        $response = $this->actingAs($user)->postJson('/proyek/ajukan', [
            'package' => 'paket-komplit',
            'alamat_proyek' => 'Jl. Anggrek No. 12',
            'desain_id' => $desain->id,
            'sertifikat_tanah' => UploadedFile::fake()->create('script.sh', 100, 'text/plain'),
            'ktp_pemilik' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'imb_pbg' => UploadedFile::fake()->create('imb.pdf', 500, 'application/pdf'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['sertifikat_tanah']);
    }

    /** @test */
    public function test_wb024_upload_project_with_file_exceeding_size_limit_rejected(): void
    {
        $user = $this->createUser();
        $this->createCustomer($user);
        $desain = $this->createDesainRumah();

        $response = $this->actingAs($user)->postJson('/proyek/ajukan', [
            'package' => 'paket-komplit',
            'alamat_proyek' => 'Jl. Anggrek No. 12',
            'desain_id' => $desain->id,
            'sertifikat_tanah' => UploadedFile::fake()->create('sertifikat.pdf', 3000, 'application/pdf'), // 3MB > 2MB limit
            'ktp_pemilik' => UploadedFile::fake()->create('ktp.pdf', 500, 'application/pdf'),
            'imb_pbg' => UploadedFile::fake()->create('imb.pdf', 500, 'application/pdf'),
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['sertifikat_tanah']);
    }

    /** @test */
    public function test_wb025_verifikasi_update_approves_project_when_documents_valid(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer);
        $detail = DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $this->createDesainRumah()->id]);
        $doc = DokumenProyek::create([
            'detail_bangun_id' => $detail->id,
            'jenis_dokumen' => 'KTP Pemilik',
            'file_path' => 'doc.pdf',
            'status_verifikasi' => 'pending'
        ]);

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimStatusProyek')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($admin)->put("/admin/verifikasi/{$proyek->id}", [
            'status_proyek' => 'Pembayaran DP',
            'catatan_admin' => 'Dokumen lunas verifikasi.',
            'status_dokumen' => [
                $doc->id => 'disetujui'
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('proyek', ['id' => $proyek->id, 'status_proyek' => 'Pembayaran DP']);
        $this->assertDatabaseHas('dokumen_proyek', ['id' => $doc->id, 'status_verifikasi' => 'disetujui']);
    }

    /** @test */
    public function test_wb026_verifikasi_update_forces_revisi_status_when_document_rejected(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer);
        $detail = DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $this->createDesainRumah()->id]);
        $doc = DokumenProyek::create([
            'detail_bangun_id' => $detail->id,
            'jenis_dokumen' => 'KTP Pemilik',
            'file_path' => 'doc.pdf',
            'status_verifikasi' => 'pending'
        ]);

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimStatusProyek')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($admin)->put("/admin/verifikasi/{$proyek->id}", [
            'status_proyek' => 'Pembayaran DP',
            'catatan_admin' => 'Tolong revisi KTP.',
            'status_dokumen' => [
                $doc->id => 'ditolak'
            ]
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('proyek', ['id' => $proyek->id, 'status_proyek' => 'Revisi Dokumen']);
    }

    /** @test */
    public function test_wb027_verifikasi_update_saves_catatan_admin_successfully(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer);
        $detail = DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $this->createDesainRumah()->id]);
        $doc = DokumenProyek::create([
            'detail_bangun_id' => $detail->id,
            'jenis_dokumen' => 'KTP Pemilik',
            'file_path' => 'doc.pdf',
            'status_verifikasi' => 'pending'
        ]);

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimStatusProyek')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($admin)->put("/admin/verifikasi/{$proyek->id}", [
            'status_proyek' => 'Pembayaran DP',
            'catatan_admin' => 'Perlu revisi minor.',
            'status_dokumen' => [
                $doc->id => 'disetujui'
            ]
        ]);

        $this->assertDatabaseHas('detail_proyek_bangun', [
            'proyek_id' => $proyek->id,
            'catatan_admin' => 'Perlu revisi minor.'
        ]);
    }

    /** @test */
    public function test_wb028_verifikasi_update_blocks_editing_once_status_is_final(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer);
        $detail = DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $this->createDesainRumah()->id]);
        $doc = DokumenProyek::create([
            'detail_bangun_id' => $detail->id,
            'jenis_dokumen' => 'KTP Pemilik',
            'file_path' => 'doc.pdf',
            'status_verifikasi' => 'disetujui'
        ]);

        $response = $this->actingAs($admin)->put("/admin/verifikasi/{$proyek->id}", [
            'status_proyek' => 'Pembayaran DP',
            'catatan_admin' => 'Mencoba mengedit.',
            'status_dokumen' => [
                $doc->id => 'ditolak'
            ]
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Dokumen sudah final, tidak bisa diubah lagi.');
        $this->assertDatabaseHas('dokumen_proyek', ['id' => $doc->id, 'status_verifikasi' => 'disetujui']);
    }

    /** @test */
    public function test_wb029_verifikasi_index_and_show_resolve_signed_urls(): void
    {
        $admin = $this->createUser(['role' => 'admin']);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer);
        $detail = DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $this->createDesainRumah()->id]);
        DokumenProyek::create([
            'detail_bangun_id' => $detail->id,
            'jenis_dokumen' => 'KTP Pemilik',
            'file_path' => 'fake/path/ktp.pdf',
            'status_verifikasi' => 'pending'
        ]);

        $this->supabaseMock->shouldReceive('getAdminSignedUrls')
            ->andReturn(['fake/path/ktp.pdf' => 'https://signed-url.com/ktp.pdf']);

        $response = $this->actingAs($admin)->get("/admin/verifikasi");
        $response->assertStatus(200);
        $response->assertViewHas('proyek');
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 4: Cart and Checkout (WB030–WB036)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb030_checkout_creates_order_material_in_pending_status(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);
        $material = $this->createMaterial(['harga' => 100_000]);

        Cart::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'jumlah' => 2,
        ]);

        Mockery::mock('alias:Midtrans\Snap')
            ->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('mock-snap-token-123');

        $response = $this->actingAs($user)->postJson('/payment/checkout', [
            'alamat' => 'Jl. Veteran No. 99',
            'telepon' => '08123456789',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('token', 'mock-snap-token-123');

        $this->assertDatabaseHas('order_material', [
            'customer_id' => $customer->id,
            'status_order' => 'pending',
            'subtotal_material' => 200_000,
        ]);
        $this->assertDatabaseHas('detail_order', [
            'material_id' => $material->id,
            'jumlah' => 2,
            'harga_satuan' => 100_000,
        ]);
    }

    /** @test */
    public function test_wb031_checkout_returns_existing_pending_order_token(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);
        
        OrderMaterial::create([
            'customer_id' => $customer->id,
            'order_id_midtrans' => 'W2H-123-456',
            'tanggal_order' => now(),
            'alamat_pengiriman' => 'Alamat Pengiriman',
            'subtotal_material' => 100_000,
            'biaya_layanan' => 5000,
            'total_harga' => 105_000,
            'status_order' => 'pending',
            'snap_token' => 'existing-snap-token'
        ]);

        $material = $this->createMaterial();
        Cart::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'jumlah' => 1
        ]);

        $response = $this->actingAs($user)->postJson('/payment/checkout');

        $response->assertStatus(200)
                 ->assertJsonPath('status', 'success')
                 ->assertJsonPath('token', 'existing-snap-token')
                 ->assertJsonPath('order_id', 'W2H-123-456');
    }

    /** @test */
    public function test_wb032_checkout_with_empty_cart_returns_400(): void
    {
        $user = $this->createUser();
        $this->createCustomer($user);

        $response = $this->actingAs($user)->postJson('/payment/checkout');

        $response->assertStatus(400)
                 ->assertJson(['message' => 'Keranjang Anda kosong.']);
    }

    /** @test */
    public function test_wb033_payment_success_callback_marks_order_as_paid_and_clears_cart(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);
        $material = $this->createMaterial();

        Cart::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'jumlah' => 2
        ]);

        $order = OrderMaterial::create([
            'customer_id' => $customer->id,
            'order_id_midtrans' => 'W2H-999-888',
            'tanggal_order' => now(),
            'alamat_pengiriman' => 'Alamat Pengiriman',
            'subtotal_material' => 120_000,
            'biaya_layanan' => 5000,
            'total_harga' => 125_000,
            'status_order' => 'pending'
        ]);

        $response = $this->actingAs($user)->postJson('/payment/checkout/success', [
            'order_id' => 'W2H-999-888',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('order_material', [
            'id' => $order->id,
            'status_order' => 'paid',
        ]);
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
    }

    /** @test */
    public function test_wb034_payment_failure_callback_marks_order_as_gagal_and_preserves_cart(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);
        $material = $this->createMaterial();

        Cart::create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'jumlah' => 2
        ]);

        $order = OrderMaterial::create([
            'customer_id' => $customer->id,
            'order_id_midtrans' => 'W2H-999-888',
            'tanggal_order' => now(),
            'alamat_pengiriman' => 'Alamat Pengiriman',
            'subtotal_material' => 120_000,
            'biaya_layanan' => 5000,
            'total_harga' => 125_000,
            'status_order' => 'pending'
        ]);

        $response = $this->actingAs($user)->postJson('/payment/checkout/success', [
            'order_id' => 'W2H-999-888',
            'transaction_status' => 'cancel',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('order_material', [
            'id' => $order->id,
            'status_order' => 'cancel',
        ]);
        $this->assertDatabaseHas('carts', ['user_id' => $user->id]);
    }

    /** @test */
    public function test_wb035_checkout_calculates_correct_service_fee(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);

        // 1. Min Service Fee (2% of 100,000 = 2,000 -> defaults to 5,000)
        $m1 = $this->createMaterial(['harga' => 100_000]);
        Cart::create(['user_id' => $user->id, 'material_id' => $m1->id, 'jumlah' => 1]);

        Mockery::mock('alias:Midtrans\Snap')->shouldReceive('getSnapToken')->andReturn('token');
        $this->actingAs($user)->postJson('/payment/checkout');
        $this->assertDatabaseHas('order_material', ['customer_id' => $customer->id, 'biaya_layanan' => 5000]);

        OrderMaterial::truncate();
        Cart::truncate();

        // 2. Normal Service Fee (2% of 1,000,000 = 20,000)
        $m2 = $this->createMaterial(['harga' => 1_000_000]);
        Cart::create(['user_id' => $user->id, 'material_id' => $m2->id, 'jumlah' => 1]);
        $this->actingAs($user)->postJson('/payment/checkout');
        $this->assertDatabaseHas('order_material', ['customer_id' => $customer->id, 'biaya_layanan' => 20000]);

        OrderMaterial::truncate();
        Cart::truncate();

        // 3. Max Service Fee (2% of 5,000,000 = 100,000 -> capped at 50,000)
        $m3 = $this->createMaterial(['harga' => 5_000_000]);
        Cart::create(['user_id' => $user->id, 'material_id' => $m3->id, 'jumlah' => 1]);
        $this->actingAs($user)->postJson('/payment/checkout');
        $this->assertDatabaseHas('order_material', ['customer_id' => $customer->id, 'biaya_layanan' => 50000]);
    }

    /** @test */
    public function test_wb036_checkout_calculates_correct_grand_total(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);
        $material = $this->createMaterial(['harga' => 1_000_000]);
        Cart::create(['user_id' => $user->id, 'material_id' => $material->id, 'jumlah' => 1]);

        Mockery::mock('alias:Midtrans\Snap')->shouldReceive('getSnapToken')->andReturn('token');
        $this->actingAs($user)->postJson('/payment/checkout');

        $this->assertDatabaseHas('order_material', [
            'customer_id' => $customer->id,
            'total_harga' => 1_020_000
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 5: RenovasiController (WB037–WB043)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb037_store_renovation_request_creates_pending_request(): void
    {
        $user = $this->createUser();
        $customer = $this->createCustomer($user);

        $this->supabaseMock->shouldReceive('uploadPrivate')->once()->andReturn('fake-path/renov.jpg');

        $response = $this->actingAs($user)->post('/renovation', [
            'deskripsi_renovasi' => 'Plester dinding retak di area kamar tidur utama.',
            'budget_estimasi' => 'Rp 5.000.000',
            'alamat' => 'Jl. Dago No. 15',
            'foto_detail' => [
                UploadedFile::fake()->image('damage.jpg')
            ]
        ]);

        $response->assertRedirect(route('customer.renovation'));
        $this->assertDatabaseHas('request_renovasi', [
            'customer_id' => $customer->id,
            'budget_estimasi' => 5000000,
            'status_request' => 'pending'
        ]);
    }

    /** @test */
    public function test_wb038_store_renovation_request_with_short_description_rejected(): void
    {
        $user = $this->createUser();
        $this->createCustomer($user);

        $response = $this->actingAs($user)->post('/renovation', [
            'deskripsi_renovasi' => 'Retak',
            'budget_estimasi' => 'Rp 5.000.000',
            'alamat' => 'Jl. Dago No. 15',
        ]);

        $response->assertSessionHasErrors(['deskripsi_renovasi']);
    }

    /** @test */
    public function test_wb039_store_renovation_request_with_small_budget_rejected(): void
    {
        $user = $this->createUser();
        $this->createCustomer($user);

        $response = $this->actingAs($user)->post('/renovation', [
            'deskripsi_renovasi' => 'Retak dinding cukup parah di bagian dapur.',
            'budget_estimasi' => 'Rp 50.000',
            'alamat' => 'Jl. Dago No. 15',
        ]);

        $response->assertSessionHasErrors(['budget_estimasi']);
    }

    /** @test */
    public function test_wb040_store_renovation_request_with_missing_required_fields_rejected(): void
    {
        $user = $this->createUser();
        $this->createCustomer($user);

        $response = $this->actingAs($user)->post('/renovation', []);
        $response->assertSessionHasErrors(['budget_estimasi', 'deskripsi_renovasi', 'alamat']);
    }

    /** @test */
    public function test_wb041_mandor_submit_offer_creates_pending_offer_and_marks_mandor_nonactive(): void
    {
        $mandorUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mandorUser, ['status' => 'aktif']);

        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $request = RequestRenovasi::create([
            'customer_id' => $customer->id,
            'deskripsi_renovasi' => 'Atap bocor sangat parah.',
            'budget_estimasi' => 10_000_000,
            'alamat' => 'Jl. Test',
            'tanggal_request' => now()
        ]);

        $material = $this->createMaterial();

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimPenawaranRenovasi')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($mandorUser)->postJson("/mandor/renovation/{$request->id}/offer", [
            'feedback' => 'Analisis keretakan: genteng bergeser parah sekali.',
            'estimasi_biaya' => 12_000_000,
            'materials' => [
                ['material_id' => $material->id, 'jumlah' => 5]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('penawaran_renovasi', [
            'request_renovasi_id' => $request->id,
            'mandor_id' => $mandor->id,
            'status_penawaran' => 'pending',
            'estimasi_biaya' => 12_000_000
        ]);

        $mandor->refresh();
        $this->assertEquals('nonaktif', $mandor->status);
    }

    /** @test */
    public function test_wb042_customer_accept_offer_creates_active_project_and_rejects_others(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $request = RequestRenovasi::create([
            'customer_id' => $customer->id,
            'deskripsi_renovasi' => 'Atap bocor sangat parah.',
            'budget_estimasi' => 10_000_000,
            'alamat' => 'Jl. Test',
            'tanggal_request' => now()
        ]);

        $m1 = $this->createUser(['role' => 'mandor']);
        $mandor1 = $this->createMandor($m1);
        $offer1 = PenawaranRenovasi::create([
            'request_renovasi_id' => $request->id,
            'mandor_id' => $mandor1->id,
            'analisis_dari_mandor' => 'Cukup parah atapnya.',
            'estimasi_biaya' => 12_000_000,
            'estimasi_durasi' => 14,
            'status_penawaran' => 'pending'
        ]);

        $m2 = $this->createUser(['role' => 'mandor']);
        $mandor2 = $this->createMandor($m2);
        $offer2 = PenawaranRenovasi::create([
            'request_renovasi_id' => $request->id,
            'mandor_id' => $mandor2->id,
            'analisis_dari_mandor' => 'Bisa cepat selesai.',
            'estimasi_biaya' => 11_000_000,
            'estimasi_durasi' => 14,
            'status_penawaran' => 'pending'
        ]);

        $response = $this->actingAs($customerUser)->postJson("/renovation/{$request->id}/accept-offer");

        $response->assertStatus(200);

        $offer2->refresh();
        $offer1->refresh();
        $request->refresh();

        $this->assertEquals('diterima', $offer2->status_penawaran);
        $this->assertEquals('ditolak', $offer1->status_penawaran);
        $this->assertEquals('disetujui', $request->status_request);

        $this->assertDatabaseHas('proyek', [
            'customer_id' => $customer->id,
            'mandor_id' => $mandor2->id,
            'jenis_proyek' => 'Renovasi',
            'status_proyek' => 'In Progress'
        ]);
    }

    /** @test */
    public function test_wb043_customer_reject_offer_denies_offer_and_reactivates_mandor(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $request = RequestRenovasi::create([
            'customer_id' => $customer->id,
            'deskripsi_renovasi' => 'Atap bocor sangat parah.',
            'budget_estimasi' => 10_000_000,
            'alamat' => 'Jl. Test',
            'tanggal_request' => now()
        ]);

        $mUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mUser, ['status' => 'nonaktif']);
        $offer = PenawaranRenovasi::create([
            'request_renovasi_id' => $request->id,
            'mandor_id' => $mandor->id,
            'analisis_dari_mandor' => 'Tawaran saya.',
            'estimasi_biaya' => 12_000_000,
            'estimasi_durasi' => 14,
            'status_penawaran' => 'pending'
        ]);

        $response = $this->actingAs($customerUser)->postJson("/renovation/{$request->id}/reject-offer", [
            'pesan' => 'Tawaran terlalu mahal.'
        ]);

        $response->assertStatus(200);
        $offer->refresh();
        $request->refresh();
        $mandor->refresh();

        $this->assertEquals('ditolak', $offer->status_penawaran);
        $this->assertEquals('ditolak', $request->status_request);
        $this->assertEquals('aktif', $mandor->status);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 6: Mandor Progress and Milestones (WB044–WB049)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb044_complete_task_advances_progress_percentage(): void
    {
        $mandorUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mandorUser);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer, $mandor, ['status_proyek' => 'In Progress']);

        $t1 = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'Gali Fondasi', 'milestone' => 'Fondasi', 'urutan' => 1, 'is_selesai' => false]);
        $t2 = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'Cor Kolom', 'milestone' => 'Struktur', 'urutan' => 2, 'is_selesai' => false]);

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimProgressPembangunan')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($mandorUser)->postJson("/mandor/task/{$t1->id}/complete");

        $response->assertStatus(200)
                 ->assertJsonPath('success', true)
                 ->assertJsonPath('persentase', 15);

        $this->assertDatabaseHas('progress_proyek', [
            'proyek_id' => $proyek->id,
            'persentase' => 15,
            'milestone_aktif' => 'Struktur'
        ]);
    }

    /** @test */
    public function test_wb045_complete_task_out_of_active_milestone_order_fails(): void
    {
        $mandorUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mandorUser);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer, $mandor, ['status_proyek' => 'In Progress']);

        $t1 = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'Gali Fondasi', 'milestone' => 'Fondasi', 'urutan' => 1, 'is_selesai' => false]);
        $t2 = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'Cor Kolom', 'milestone' => 'Struktur', 'urutan' => 2, 'is_selesai' => false]);

        $response = $this->actingAs($mandorUser)->postJson("/mandor/task/{$t2->id}/complete");

        $response->assertStatus(403);
    }

    /** @test */
    public function test_wb046_complete_task_blocked_by_unpaid_installment_milestones(): void
    {
        $mandorUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mandorUser);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer, $mandor, ['status_proyek' => 'In Progress']);

        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'F1', 'milestone' => 'Fondasi', 'urutan' => 1, 'is_selesai' => true]);
        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'S1', 'milestone' => 'Struktur', 'urutan' => 2, 'is_selesai' => true]);
        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'A1', 'milestone' => 'Atap', 'urutan' => 3, 'is_selesai' => true]);

        $mepTask = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'M1', 'milestone' => 'MEP', 'urutan' => 4, 'is_selesai' => false]);

        $response = $this->actingAs($mandorUser)->postJson("/mandor/task/{$mepTask->id}/complete");

        $response->assertStatus(403)
                 ->assertJsonPath('success', false)
                 ->assertJsonPath('message', 'Tidak dapat menyelesaikan task ini. Pelanggan belum melunasi pembayaran Cicilan Periode 2.');
    }

    /** @test */
    public function test_wb047_complete_task_by_another_mandor_fails_403(): void
    {
        $mandor1User = $this->createUser(['role' => 'mandor']);
        $mandor1 = $this->createMandor($mandor1User);

        $mandor2User = $this->createUser(['role' => 'mandor']);
        $this->createMandor($mandor2User);

        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer, $mandor1, ['status_proyek' => 'In Progress']);

        $t1 = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'Gali Fondasi', 'milestone' => 'Fondasi', 'urutan' => 1, 'is_selesai' => false]);

        $response = $this->actingAs($mandor2User)->postJson("/mandor/task/{$t1->id}/complete");

        $response->assertStatus(403);
    }

    /** @test */
    public function test_wb048_completing_all_tasks_sets_project_to_selesai_and_frees_mandor(): void
    {
        $mandorUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mandorUser, ['status' => 'nonaktif']);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer, $mandor, ['status_proyek' => 'In Progress']);

        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'F1', 'milestone' => 'Fondasi', 'urutan' => 1, 'is_selesai' => true]);
        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'S1', 'milestone' => 'Struktur', 'urutan' => 2, 'is_selesai' => true]);
        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'A1', 'milestone' => 'Atap', 'urutan' => 3, 'is_selesai' => true]);
        ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'M1', 'milestone' => 'MEP', 'urutan' => 4, 'is_selesai' => true]);

        $tLast = ProyekMilestone::create(['proyek_id' => $proyek->id, 'nama_task' => 'F2', 'milestone' => 'Finishing', 'urutan' => 5, 'is_selesai' => false]);

        PembayaranProyek::create([
            'proyek_id' => $proyek->id,
            'periode' => 3,
            'jumlah_bayar' => 10_000_000,
            'status_pembayaran' => 'berhasil'
        ]);

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimProgressPembangunan')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($mandorUser)->postJson("/mandor/task/{$tLast->id}/complete");

        $response->assertStatus(200)
                 ->assertJsonPath('is_done', true);

        $proyek->refresh();
        $mandor->refresh();

        $this->assertEquals('Selesai', $proyek->status_proyek);
        $this->assertEquals('aktif', $mandor->status);
        $this->assertNull($proyek->mandor_id);
    }

    /** @test */
    public function test_wb049_tambah_aktivitas_creates_proyek_aktivitas_and_sends_notification(): void
    {
        $mandorUser = $this->createUser(['role' => 'mandor']);
        $mandor = $this->createMandor($mandorUser);
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $proyek = $this->createProyek($customer, $mandor, ['status_proyek' => 'In Progress']);

        $notifMock = Mockery::mock(NotificationService::class);
        $notifMock->shouldReceive('kirimAktivitasProyek')->once();
        $this->app->instance(NotificationService::class, $notifMock);

        $response = $this->actingAs($mandorUser)->postJson("/mandor/proyek/{$proyek->id}/aktivitas", [
            'judul' => 'Plester Dinding Retak',
            'deskripsi' => 'Dinding plester retak telah diperbaiki.'
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('proyek_aktivitas', [
            'proyek_id' => $proyek->id,
            'judul' => 'Plester Dinding Retak',
            'deskripsi' => 'Dinding plester retak telah diperbaiki.'
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 7: PaymentDPInstallmentsTest (WB050–WB054)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb050_generate_dp_calculates_exact_30_percent_of_desain_cost(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $desain = $this->createDesainRumah(['estimasi_biaya' => 500_000_000]);
        $proyek = $this->createProyek($customer);
        DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $desain->id]);

        $proyek->generateDP();

        $this->assertDatabaseHas('pembayaran_proyek', [
            'proyek_id' => $proyek->id,
            'periode' => 0,
            'jumlah_bayar' => 150_000_000,
            'status_pembayaran' => 'belum_bayar'
        ]);
    }

    /** @test */
    public function test_wb051_initiate_payment_dp_creates_pending_midtrans_transaction(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $desain = $this->createDesainRumah(['estimasi_biaya' => 300_000_000]);
        $proyek = $this->createProyek($customer);
        DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $desain->id]);
        $proyek->generateDP();

        $pembayaran = PembayaranProyek::where('proyek_id', $proyek->id)->first();

        Mockery::mock('alias:Midtrans\Snap')
            ->shouldReceive('getSnapToken')
            ->once()
            ->andReturn('mock-snap-token-dp');

        $response = $this->actingAs($customerUser)->postJson('/proyek/bayar', [
            'pembayaran_id' => $pembayaran->id
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('snap_token', 'mock-snap-token-dp');

        $pembayaran->refresh();
        $this->assertEquals('pending', $pembayaran->status_pembayaran);
    }

    /** @test */
    public function test_wb052_dp_payment_success_callback_advances_project_status(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $desain = $this->createDesainRumah(['estimasi_biaya' => 300_000_000]);
        $proyek = $this->createProyek($customer, null, ['status_proyek' => 'Pembayaran DP']);
        DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $desain->id]);
        $proyek->generateDP();

        $pembayaran = PembayaranProyek::where('proyek_id', $proyek->id)->first();
        $pembayaran->update([
            'order_id' => 'DP-' . $proyek->id . '-12345',
            'status_pembayaran' => 'pending'
        ]);

        $signature = hash('sha512', $pembayaran->order_id . '200' . $pembayaran->jumlah_bayar . config('midtrans.server_key'));

        $response = $this->postJson('/midtrans/callback', [
            'order_id' => $pembayaran->order_id,
            'status_code' => '200',
            'gross_amount' => $pembayaran->jumlah_bayar,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'credit_card'
        ]);

        $response->assertStatus(200);
        $pembayaran->refresh();
        $proyek->refresh();

        $this->assertEquals('berhasil', $pembayaran->status_pembayaran);
        $this->assertEquals('Pengalokasian Mandor', $proyek->status_proyek);
    }

    /** @test */
    public function test_wb053_pay_installment_out_of_sequence_rejected_422(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $desain = $this->createDesainRumah(['estimasi_biaya' => 300_000_000]);
        $proyek = $this->createProyek($customer);
        DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $desain->id]);
        
        $proyek->generateDP();
        $proyek->generateCicilan();

        $cicilan1 = PembayaranProyek::where('proyek_id', $proyek->id)->where('periode', 1)->first();

        $response = $this->actingAs($customerUser)->postJson('/proyek/bayar', [
            'pembayaran_id' => $cicilan1->id
        ]);

        $response->assertStatus(422)
                 ->assertJson(['message' => 'Harap selesaikan pembayaran sebelumnya terlebih dahulu.']);
    }

    /** @test */
    public function test_wb054_generate_cicilan_creates_exact_three_installments(): void
    {
        $customerUser = $this->createUser();
        $customer = $this->createCustomer($customerUser);
        $desain = $this->createDesainRumah(['estimasi_biaya' => 400_000_000]);
        $proyek = $this->createProyek($customer);
        DetailProyekBangun::create(['proyek_id' => $proyek->id, 'desain_rumah_id' => $desain->id]);

        $proyek->generateCicilan();

        $this->assertDatabaseHas('pembayaran_proyek', [
            'proyek_id' => $proyek->id,
            'periode' => 1,
            'jumlah_bayar' => 100_000_000
        ]);
        $this->assertDatabaseHas('pembayaran_proyek', [
            'proyek_id' => $proyek->id,
            'periode' => 2,
            'jumlah_bayar' => 100_000_000
        ]);
        $this->assertDatabaseHas('pembayaran_proyek', [
            'proyek_id' => $proyek->id,
            'periode' => 3,
            'jumlah_bayar' => 80_000_000
        ]);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // BLOK 8: Middleware & Authorization (WB055–WB058)
    // ════════════════════════════════════════════════════════════════════════════

    /** @test */
    public function test_wb055_authenticated_request_passes_auth_middleware(): void
    {
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_wb056_unauthenticated_request_blocked(): void
    {
        $response = $this->getJson('/profile');
        $response->assertStatus(401);
    }

    /** @test */
    public function test_wb057_customer_accessing_mandor_route_blocked_403(): void
    {
        $user = $this->createUser(['role' => 'customer']);
        $this->createCustomer($user);

        $response = $this->actingAs($user)->get('/mandor/dashboard');
        
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Anda tidak punya akses ke halaman mandor.');
    }

    /** @test */
    public function test_wb058_admin_accessing_admin_route_succeeds(): void
    {
        $user = $this->createUser(['role' => 'admin']);
        
        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertStatus(200);
    }
}
