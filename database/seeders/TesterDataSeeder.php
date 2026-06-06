<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Customer;

class TesterDataSeeder extends Seeder
{
    private const DOCS = [
        'imb'              => 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/dokumen/imb.jpg',
        'ktp'              => 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/dokumen/ktp.jpg',
        'sertifikat_tanah' => 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/dokumen/sertifikat_tanah.jpg',
        'surat_kuasa'      => 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/dokumen/surat_kuasa.jpg',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            // ─── 1. Ensure Budi Santoso (Customer ID: 2) is a Tester ──────────
            $customerUser = User::updateOrCreate(
                ['email' => 'tester.customer02@way2home.test'],
                [
                    'name'              => 'Budi Santoso',
                    'password'          => bcrypt('password'),
                    'role'              => 'customer',
                    'phone_number'      => '08100000002',
                    'address'           => 'Jl. Mawar No. 2, Depok',
                    'email_verified_at' => now(),
                    'is_tester'         => true,
                    'is_first_login'    => false,
                ]
            );

            Customer::updateOrCreate(
                ['id' => 2],
                [
                    'user_id' => $customerUser->id,
                    'no_hp'   => '08100000002',
                ]
            );

            // ─── 2. Clean up existing tester projects ─────────────────────────
            $targetAddresses = [
                'Jl. Pembangunan No. 32',
                'Jl. Fondasi Raya No. 33',
                'Perumahan Renovasi Blok C/34',
                'Cluster Admin 1 Verifikasi Blok QA-01',
                'Cluster Admin 1 Verifikasi Blok QA-07',
                'Kawasan Alokasi Mandor 1 No. QA-02',
                'Cluster Admin 2 Verifikasi Blok QA-03',
                'Cluster Admin 2 Verifikasi Blok QA-08',
                'Kawasan Alokasi Mandor 2 No. QA-04',
                'Cluster Admin 3 Verifikasi Blok QA-05',
                'Cluster Admin 3 Verifikasi Blok QA-09',
                'Kawasan Alokasi Mandor 3 No. QA-06',
            ];

            $proyekIds = DB::table('proyek')
                ->where('customer_id', 2)
                ->whereIn('alamat_proyek', $targetAddresses)
                ->pluck('id');

            if ($proyekIds->isNotEmpty()) {
                DB::table('proyek_milestone')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('pembayaran_proyek')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('progress_proyek')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('proyek_aktivitas')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('proyek_dokumentasi')->whereIn('proyek_id', $proyekIds)->delete();

                $detailBangunIds = DB::table('detail_proyek_bangun')
                    ->whereIn('proyek_id', $proyekIds)->pluck('id');
                if ($detailBangunIds->isNotEmpty()) {
                    DB::table('dokumen_proyek')->whereIn('detail_bangun_id', $detailBangunIds)->delete();
                }
                DB::table('detail_proyek_bangun')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('detail_proyek_renovasi')->whereIn('proyek_id', $proyekIds)->delete();
                DB::table('proyek')->whereIn('id', $proyekIds)->delete();
            }

            // ─── 3. Resolve shared IDs ────────────────────────────────────────
            $m1       = DB::table('mandors')->where('user_id', 32)->value('id') ?? 1;
            $m2       = DB::table('mandors')->where('user_id', 33)->value('id') ?? 2;
            $m3       = DB::table('mandors')->where('user_id', 34)->value('id') ?? 3;
            $desainId = DB::table('desain_rumah')->value('id') ?? 1;

            // ─── 4. Bangun Rumah Projects (customer tester — Bambang & Sudarmo) ─
            $bangunProjects = [
                ['alamat' => 'Jl. Pembangunan No. 32', 'mandor_id' => $m1],
                ['alamat' => 'Jl. Fondasi Raya No. 33', 'mandor_id' => $m2],
            ];

            foreach ($bangunProjects as $spec) {
                $pid = DB::table('proyek')->insertGetId([
                    'customer_id'   => 2,
                    'mandor_id'     => $spec['mandor_id'],
                    'jenis_proyek'  => 'Bangun Rumah',
                    'alamat_proyek' => $spec['alamat'],
                    'status_proyek' => 'In Progress',
                    'tanggal_mulai' => now()->toDateString(),
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);

                DB::table('pembayaran_proyek')->insert([
                    'proyek_id' => $pid, 'periode' => 0, 'jumlah_bayar' => 15000000,
                    'tanggal_jatuh_tempo' => null, 'tanggal_bayar' => now()->toDateString(),
                    'metode_pembayaran' => 'Tester Auto-Pay', 'status_pembayaran' => 'berhasil',
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                foreach ([1, 2, 3] as $period) {
                    DB::table('pembayaran_proyek')->insert([
                        'proyek_id' => $pid, 'periode' => $period,
                        'jumlah_bayar' => $period === 3 ? 10000000 : 12500000,
                        'tanggal_jatuh_tempo' => now()->subMonths(4 - $period)->toDateString(),
                        'tanggal_bayar' => now()->subMonths(4 - $period)->toDateString(),
                        'metode_pembayaran' => 'Tester Auto-Pay', 'status_pembayaran' => 'berhasil',
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                DB::table('progress_proyek')->insert([
                    'proyek_id' => $pid, 'milestone_aktif' => 'Fondasi', 'persentase' => 0,
                    'catatan' => 'Tester Initial State', 'tanggal_update' => now(),
                    'created_at' => now(), 'updated_at' => now(),
                ]);

                foreach (self::defaultTasks() as [$nama, $milestone, $urutan]) {
                    DB::table('proyek_milestone')->insert([
                        'proyek_id' => $pid, 'nama_task' => $nama,
                        'milestone' => $milestone, 'urutan' => $urutan,
                        'is_selesai' => false, 'created_at' => now(), 'updated_at' => now(),
                    ]);
                }

                DB::table('detail_proyek_bangun')->insert([
                    'proyek_id' => $pid, 'desain_rumah_id' => $desainId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }

            // ─── 5. Renovasi Request (Wahyu Tester) ───────────────────────────
            $oldPenIds = DB::table('penawaran_renovasi')->where('mandor_id', $m3)->pluck('id');
            if ($oldPenIds->isNotEmpty()) {
                DB::table('negosiasi_renovasi')->whereIn('penawaran_renovasi_id', $oldPenIds)->delete();
                $oldReqIds = DB::table('penawaran_renovasi')->whereIn('id', $oldPenIds)->pluck('request_renovasi_id');
                DB::table('penawaran_renovasi')->whereIn('id', $oldPenIds)->delete();
                if ($oldReqIds->isNotEmpty()) {
                    $oldProjIds = DB::table('detail_proyek_renovasi')
                        ->whereIn('request_renovasi_id', $oldReqIds)->pluck('proyek_id');
                    if ($oldProjIds->isNotEmpty()) {
                        DB::table('detail_proyek_renovasi')->whereIn('proyek_id', $oldProjIds)->delete();
                        DB::table('proyek')->whereIn('id', $oldProjIds)->delete();
                    }
                    DB::table('request_renovasi')->whereIn('id', $oldReqIds)->delete();
                }
            }
            DB::table('request_renovasi')
                ->where('customer_id', 2)
                ->whereNotIn('id', function ($q) {
                    $q->select('request_renovasi_id')->from('penawaran_renovasi');
                })
                ->delete();

            DB::table('request_renovasi')->insert([
                'customer_id'           => 1,
                'alamat'                => 'Perumahan Renovasi Blok C/34',
                'path_foto_detail'      => 'https://ovyjfudrdwrlyioygotq.supabase.co/storage/v1/object/public/public-assets/testing/renovasi/foto_renovasi.jpg',
                'deskripsi_renovasi'    => 'Renovasi Dapur dan Kamar Mandi — QA Tester',
                'budget_estimasi'       => 50000000,
                'status_request'        => 'pending',
                'tanggal_request'       => now()->toDateString(),
                'created_at'            => now(),
                'updated_at'            => now(),
            ]);

            DB::table('mandors')->where('id', $m3)
                ->update(['status' => 'aktif', 'updated_at' => now()]);

            // ─── 6. Admin-testing Projects ────────────────────────────────────
            // Mapping eksplisit: jenis_dokumen (sesuai blade) → file path
            $docTypes = ['Sertifikat Tanah', 'KTP Pemilik', 'IMB/PBG', 'Surat Kuasa'];
            $docPathMap = [
                'Sertifikat Tanah' => self::DOCS['sertifikat_tanah'],
                'KTP Pemilik'      => self::DOCS['ktp'],
                'IMB/PBG'          => self::DOCS['imb'],
                'Surat Kuasa'      => self::DOCS['surat_kuasa'],
            ];

            for ($i = 1; $i <= 3; $i++) {
                $addressA  = "Cluster Admin {$i} Verifikasi Blok QA-0" . (2 * $i - 1); // QA-01, QA-03, QA-05
                $addressA2 = "Cluster Admin {$i} Verifikasi Blok QA-0" . (6 + $i);     // QA-07, QA-08, QA-09
                $addressB  = "Kawasan Alokasi Mandor {$i} No. QA-0"    . (2 * $i);     // QA-02, QA-04, QA-06

                // ── Project A: Menunggu Verifikasi (pertama) ──────────────────
                $pAid = DB::table('proyek')->insertGetId([
                    'customer_id'   => 2,
                    'mandor_id'     => null,
                    'jenis_proyek'  => 'Bangun Rumah',
                    'alamat_proyek' => $addressA,
                    'status_proyek' => 'Menunggu Verifikasi',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                DB::table('detail_proyek_bangun')->insert([
                    'proyek_id' => $pAid, 'desain_rumah_id' => $desainId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $detailAId = DB::table('detail_proyek_bangun')->where('proyek_id', $pAid)->value('id');
                foreach ($docTypes as $docType) {
                    DB::table('dokumen_proyek')->insert([
                        'detail_bangun_id'  => $detailAId,
                        'jenis_dokumen'     => $docType,
                        'file_path'         => $docPathMap[$docType],
                        'status_verifikasi' => 'pending',
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

                // ── Project A2: Menunggu Verifikasi (kedua) ───────────────────
                $pA2id = DB::table('proyek')->insertGetId([
                    'customer_id'   => 2,
                    'mandor_id'     => null,
                    'jenis_proyek'  => 'Bangun Rumah',
                    'alamat_proyek' => $addressA2,
                    'status_proyek' => 'Menunggu Verifikasi',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                DB::table('detail_proyek_bangun')->insert([
                    'proyek_id' => $pA2id, 'desain_rumah_id' => $desainId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $detailA2Id = DB::table('detail_proyek_bangun')->where('proyek_id', $pA2id)->value('id');
                foreach ($docTypes as $docType) {
                    DB::table('dokumen_proyek')->insert([
                        'detail_bangun_id'  => $detailA2Id,
                        'jenis_dokumen'     => $docType,
                        'file_path'         => $docPathMap[$docType],
                        'status_verifikasi' => 'pending',
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

                // ── Project B: Pengalokasian Mandor ───────────────────────────
                $pBid = DB::table('proyek')->insertGetId([
                    'customer_id'   => 2,
                    'mandor_id'     => null,
                    'jenis_proyek'  => 'Bangun Rumah',
                    'alamat_proyek' => $addressB,
                    'status_proyek' => 'Pengalokasian Mandor',
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ]);
                DB::table('detail_proyek_bangun')->insert([
                    'proyek_id' => $pBid, 'desain_rumah_id' => $desainId,
                    'created_at' => now(), 'updated_at' => now(),
                ]);
                $detailBId = DB::table('detail_proyek_bangun')->where('proyek_id', $pBid)->value('id');
                foreach ($docTypes as $docType) {
                    DB::table('dokumen_proyek')->insert([
                        'detail_bangun_id'  => $detailBId,
                        'jenis_dokumen'     => $docType,
                        'file_path'         => $docPathMap[$docType],
                        'status_verifikasi' => 'disetujui',
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]);
                }

                // DP lunas untuk Project B
                DB::table('pembayaran_proyek')->insert([
                    'proyek_id'           => $pBid,
                    'periode'             => 0,
                    'jumlah_bayar'        => 15000000,
                    'tanggal_jatuh_tempo' => null,
                    'tanggal_bayar'       => now()->toDateString(),
                    'metode_pembayaran'   => 'Tester Auto-Pay',
                    'status_pembayaran'   => 'berhasil',
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }

            // ─── 7. Tester Orders ─────────────────────────────────────────────
            $materialIds = DB::table('materials')->limit(4)->pluck('id');
            $m1Id = $materialIds[0] ?? 1;
            $m2Id = $materialIds[1] ?? 2;

            DB::table('order_material')
                ->where('order_id_midtrans', 'like', 'W2H-TESTER-QA%')
                ->get()
                ->each(fn($o) => DB::table('detail_order')->where('order_material_id', $o->id)->delete());
            DB::table('order_material')
                ->where('order_id_midtrans', 'like', 'W2H-TESTER-QA%')
                ->delete();

            $orderSpecs = [
                ['prefix' => 'W2H-TESTER-QA1-001', 'status' => 'paid',    'alamat' => 'Jl. Admin 1 No. 1'],
                ['prefix' => 'W2H-TESTER-QA1-002', 'status' => 'dikirim', 'alamat' => 'Jl. Admin 1 No. 2'],
                ['prefix' => 'W2H-TESTER-QA2-001', 'status' => 'paid',    'alamat' => 'Jl. Admin 2 No. 1'],
                ['prefix' => 'W2H-TESTER-QA2-002', 'status' => 'dikirim', 'alamat' => 'Jl. Admin 2 No. 2'],
                ['prefix' => 'W2H-TESTER-QA3-001', 'status' => 'paid',    'alamat' => 'Jl. Admin 3 No. 1'],
                ['prefix' => 'W2H-TESTER-QA3-002', 'status' => 'dikirim', 'alamat' => 'Jl. Admin 3 No. 2'],
            ];

            foreach ($orderSpecs as $spec) {
                $orderId = DB::table('order_material')->insertGetId([
                    'customer_id'       => 2,
                    'order_id_midtrans' => $spec['prefix'],
                    'tanggal_order'     => now()->toDateString(),
                    'alamat_pengiriman' => $spec['alamat'],
                    'subtotal_material' => 500000,
                    'biaya_layanan'     => 10000,
                    'total_harga'       => 510000,
                    'status_order'      => $spec['status'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                DB::table('detail_order')->insert([
                    ['order_material_id' => $orderId, 'material_id' => $m1Id, 'jumlah' => 2, 'harga_satuan' => 150000, 'subtotal' => 300000, 'created_at' => now(), 'updated_at' => now()],
                    ['order_material_id' => $orderId, 'material_id' => $m2Id, 'jumlah' => 1, 'harga_satuan' => 200000, 'subtotal' => 200000, 'created_at' => now(), 'updated_at' => now()],
                ]);
            }
        });


    }

    public static function autoAcceptRenovasi(int $reqId, int $penId, int $mandorId): void
    {
        DB::table('penawaran_renovasi')->where('id', $penId)
            ->update(['status_penawaran' => 'diterima', 'updated_at' => now()]);

        DB::table('request_renovasi')->where('id', $reqId)
            ->update(['status_request' => 'disetujui', 'updated_at' => now()]);

        DB::table('negosiasi_renovasi')->insert([
            'request_renovasi_id'   => $reqId,
            'penawaran_renovasi_id' => $penId,
            'pengirim'              => 'customer',
            'tipe'                  => 'setuju',
            'pesan'                 => 'Penawaran disetujui oleh customer (Tester Auto-Accept).',
            'nominal_tawaran'       => null,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        $proyekId = DB::table('proyek')->insertGetId([
            'customer_id'   => 2,
            'mandor_id'     => $mandorId,
            'jenis_proyek'  => 'Renovasi',
            'alamat_proyek' => 'Perumahan Renovasi Blok C/34',
            'status_proyek' => 'In Progress',
            'tanggal_mulai' => now()->toDateString(),
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        DB::table('detail_proyek_renovasi')->insert([
            'proyek_id'             => $proyekId,
            'request_renovasi_id'   => $reqId,
            'penawaran_renovasi_id' => $penId,
            'created_at'            => now(),
            'updated_at'            => now(),
        ]);

        DB::table('mandors')->where('id', $mandorId)
            ->update(['status' => 'nonaktif', 'updated_at' => now()]);
    }

    public static function defaultTasks(): array
    {
        return [
            ['Galian & Urugan',            'Fondasi',    1],
            ['Pemasangan Batu Kali',        'Fondasi',    2],
            ['Sloof Beton',                 'Fondasi',    3],
            ['Pemasangan Hebel/Dinding',    'Struktur',   4],
            ['Kolom & Balok',               'Struktur',   5],
            ['Plester & Acian',             'Struktur',   6],
            ['Rangka Atap',                 'Atap',       7],
            ['Pemasangan Genteng',          'Atap',       8],
            ['Plafon & Lipslang',           'Atap',       9],
            ['Instalasi Listrik',           'MEP',       10],
            ['Instalasi Air & Sanitasi',    'MEP',       11],
            ['Pemasangan Titik Lampu',      'MEP',       12],
            ['Pemasangan Lantai & Keramik', 'Finishing', 13],
            ['Pengecatan & Kusen',          'Finishing', 14],
            ['Sanitari & Aksesoris',        'Finishing', 15],
        ];
    }
}