<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\DetailProyekBangun;
use App\Models\DokumenProyek;
use App\Models\DesainRumah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProyekController extends Controller
{

    public function create(Request $request)
    {
        $desainId = $request->query('desain_id', $request->query('rumah_id'));
        $desain = null;

        if (!empty($desainId)) {
            $desain = DesainRumah::query()->find($desainId);
        }

        if (!$desain) {
            $desain = DesainRumah::query()->orderBy('id')->first();
        }

        abort_if(!$desain, 404, 'Data desain rumah belum tersedia.');

        return view('customer-layouts.form_pembangunan_rumah', compact('desain'));
    }

    public function index()
    {
        $customer = Auth::user()->customer;
        $proyek   = Proyek::where('customer_id', $customer->id)->where('jenis_proyek', 'Bangun Rumah')->first();

        if ($proyek) {
            return redirect()->route('proyek.show', $proyek->id);
        }

        return redirect()->route('customer-layouts.dashboard');
    }

    public function show($id)
    {
        $customer = Auth::user()->customer;

        $proyeks = Proyek::with([
            'detailBangun.desainRumah',
            'detailBangun.dokumenProyek',
            'pembayaranDP',
        ])
            ->where('customer_id', $customer->id)
            ->where('jenis_proyek', 'Bangun Rumah')
            ->get();

        $proyek = $proyeks->first(fn($p) => $p->id == $id);

        abort_if(is_null($proyek), 404);

        return view('customer-layouts.proyek.show', compact('proyek', 'proyeks'));
    }

    public function store(Request $request, \App\Services\SupabaseStorageService $storageService)
    {
        // 1. Validasi input
        $request->validate([
            'package'          => 'required|in:paket-komplit,paket-standar',
            'alamat_proyek'    => 'required|string',
            'desain_id'        => 'required|exists:desain_rumah,id',
            'sertifikat_tanah' => 'required_if:package,paket-komplit|file|mimes:pdf,jpg,png|max:2048',
            'ktp_pemilik'      => 'required_if:package,paket-komplit|file|mimes:pdf,jpg,png|max:2048',
            'imb_pbg'          => 'required_if:package,paket-komplit|file|mimes:pdf,jpg,png|max:2048',
            'surat_kuasa'      => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ], [
            'sertifikat_tanah.uploaded' => 'File Sertifikat Tanah terlalu besar. Maksimal 2 MB per file.',
            'ktp_pemilik.uploaded' => 'File KTP Pemilik terlalu besar. Maksimal 2 MB per file.',
            'imb_pbg.uploaded' => 'File IMB/PBG terlalu besar. Maksimal 2 MB per file.',
            'surat_kuasa.uploaded' => 'File Surat Kuasa terlalu besar. Maksimal 2 MB per file.',
        ]);

        // 2. Cek customer profile
        $customer = Auth::user()->customer;
        abort_if(!$customer, 403, 'Profil customer tidak ditemukan.');

        // 3. Upload semua file ke Supabase DULU sebelum sentuh DB
        $dokumenList = [
            'sertifikat_tanah' => 'Sertifikat Tanah',
            'ktp_pemilik'      => 'KTP Pemilik',
            'imb_pbg'          => 'IMB/PBG',
            'surat_kuasa'      => 'Surat Kuasa',
        ];

        $uploadedFiles = [];

        foreach ($dokumenList as $inputName => $label) {
            if ($request->hasFile($inputName)) {
                try {
                    $path = $storageService->uploadPrivate(
                        $request->file($inputName),
                        Auth::id(),
                        'proyek/dokumen'
                    );
                    $uploadedFiles[] = ['path' => $path, 'label' => $label];

                } catch (\Exception $e) {
                    foreach ($uploadedFiles as $uploaded) {
                        $storageService->deletePrivate($uploaded['path']);
                    }
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'Gagal upload dokumen "' . $label . '": ' . $e->getMessage()
                    ], 500);
                }
            }
        }

        // 4. Semua file berhasil → baru masuk DB transaction
        DB::beginTransaction();

        try {
            $proyek = Proyek::create([
                'customer_id'   => $customer->id,
                'jenis_proyek'  => 'Bangun Rumah',
                'alamat_proyek' => $request->alamat_proyek,
                'status_proyek' => 'Menunggu Verifikasi',
                'tanggal_mulai' => now(),
            ]);

            $detail = DetailProyekBangun::create([
                'proyek_id'       => $proyek->id,
                'desain_rumah_id' => $request->desain_id,
            ]);

            foreach ($uploadedFiles as $uploaded) {
                DokumenProyek::create([
                    'detail_bangun_id'  => $detail->id,
                    'jenis_dokumen'     => $uploaded['label'],
                    'file_path'         => $uploaded['path'],
                    'status_verifikasi' => 'pending',
                ]);
            }

            DB::commit();

            return response()->json([
                'status'    => 'success',
                'message_1' => 'Pengajuan pembangunan berhasil dikirim!',
                'message_2' => 'Pengajuan pemesanan material berhasil dikirim!',
                'data'      => $proyek
            ], 201);

        } catch (\Throwable $e) {
            DB::rollback();

            foreach ($uploadedFiles as $uploaded) {
                $storageService->deletePrivate($uploaded['path']);
            }

            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }

}
