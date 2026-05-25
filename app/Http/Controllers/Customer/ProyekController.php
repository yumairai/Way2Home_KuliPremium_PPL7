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
use Illuminate\Support\Facades\Log;
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

        $alamat = $request->query('alamat', '');
        $old_proyek_id = $request->query('old_proyek_id', '');

        return view('customer-layouts.form_pembangunan_rumah', compact('desain', 'alamat', 'old_proyek_id'));
    }

    public function index()
    {
        $customer = Auth::user()->customer;
        $proyeks  = Proyek::with([
            'detailBangun.desainRumah',
            'detailBangun.dokumenProyek',
            'pembayaranProyek',
        ])
            ->where('customer_id', $customer->id)
            ->where('jenis_proyek', 'Bangun Rumah')
            ->get();

        $proyek = $proyeks->first();

        return view('customer-layouts.proyek.show', compact('proyek', 'proyeks'));
    }

    public function show($id)
    {
        $customer = Auth::user()->customer;

        $proyeks = Proyek::with([
            'detailBangun.desainRumah',
            'detailBangun.dokumenProyek',
            'pembayaranProyek',
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
            'package'          => 'required|in:paket-komplit,material-only',
            'alamat_proyek'    => 'required_if:package,paket-komplit|nullable|string',
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
                    // 🔥 DEBUG PENTING
                    if (!$path) {
                        throw new \Exception("Upload gagal: path kosong");
                    }

                    Log::info('UPLOAD SUCCESS', [
                        'label' => $label,
                        'path' => $path,
                    ]);
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
        if ($request->package === 'material-only') {
            $desain = \App\Models\DesainRumah::find($request->desain_id);
            if ($desain && $desain->material_digunakan) {
                $materials = explode(';', $desain->material_digunakan);
                foreach ($materials as $materialStr) {
                    if (empty(trim($materialStr))) continue;
                    $parts = explode(':', $materialStr);
                    if (count($parts) >= 1) {
                        $namaMaterial = trim($parts[0]);
                        $qty = 1;
                        if (count($parts) == 2) {
                            preg_match('/(\d+)/', trim($parts[1]), $matches);
                            if (isset($matches[1])) $qty = (int)$matches[1];
                        }

                        $material = \App\Models\Material::where('nama_material', 'LIKE', '%' . $namaMaterial . '%')->first();
                        if ($material) {
                            $cartItem = \App\Models\Cart::where('user_id', Auth::id())
                                ->where('material_id', $material->id)
                                ->first();
                            if ($cartItem) {
                                $cartItem->increment('jumlah', $qty);
                            } else {
                                \App\Models\Cart::create([
                                    'user_id' => Auth::id(),
                                    'material_id' => $material->id,
                                    'jumlah' => $qty
                                ]);
                            }
                        }
                    }
                }
            }
            return response()->json([
                'status'    => 'success',
                'message_2' => 'Bahan material berhasil ditambahkan ke keranjang!',
            ], 200);
        }

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

            $proyek->load('detailBangun.desainRumah');
            $proyek->generateDP();

            if ($request->filled('old_proyek_id')) {
                Proyek::where('id', $request->old_proyek_id)
                      ->where('customer_id', $customer->id)
                      ->update(['status_proyek' => 'Dibatalkan']);
            }

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

    public function batal($id)
    {
        $customer = Auth::user()->customer;
        $proyek = Proyek::where('customer_id', $customer->id)
            ->where('id', $id)
            ->first();

        if (!$proyek) {
            return response()->json(['status' => 'error', 'message' => 'Proyek tidak ditemukan.'], 404);
        }

        // Cek jika proyek sudah In Progress atau Selesai
        if (in_array($proyek->status_proyek, ['In Progress', 'Selesai'])) {
            return response()->json(['status' => 'error', 'message' => 'Proyek yang sudah berjalan tidak dapat dibatalkan.'], 422);
        }

        // Cek DP apakah sudah dibayar
        $dp = $proyek->pembayaranProyek()->where('periode', 0)->first();
        if ($dp && $dp->status_pembayaran === 'berhasil') {
            return response()->json(['status' => 'error', 'message' => 'Proyek tidak dapat dibatalkan karena DP sudah dibayar.'], 422);
        }

        $proyek->update(['status_proyek' => 'Dibatalkan']);

        return response()->json([
            'status' => 'success',
            'message' => 'Proyek berhasil dibatalkan.'
        ]);
    }
}
