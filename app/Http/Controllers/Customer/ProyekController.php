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
        $proyek   = Proyek::where('customer_id', $customer->id)->first();

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
            ->get();

        $proyek = $proyeks->first(fn($p) => $p->id == $id);

        abort_if(is_null($proyek), 404);

        return view('customer-layouts.proyek.show', compact('proyek', 'proyeks'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

        DB::beginTransaction();

        try {
            $customerId = Auth::user()?->customer?->id;
            abort_if(!$customerId, 403, 'Akun customer tidak ditemukan.');

            $proyek = Proyek::create([
                'customer_id'   => $customerId,
                'jenis_proyek' => 'Bangun Rumah',
                'alamat_proyek' => $request->alamat_proyek,
                'status_proyek' => 'Menunggu Verifikasi',
                'tanggal_mulai' => now(),
            ]);

            $detail = DetailProyekBangun::create([
                'proyek_id' => $proyek->id,
                'desain_rumah_id' => $request->desain_id,
            ]);

            $dokumenList = [
                'sertifikat_tanah' => 'Sertifikat Tanah',
                'ktp_pemilik' => 'KTP Pemilik',
                'imb_pbg' => 'IMB/PBG',
                'surat_kuasa' => 'Surat Kuasa'
            ];

            foreach ($dokumenList as $inputName => $label) {
                if ($request->hasFile($inputName)) {
                    $path = $request->file($inputName)->store('proyek/dokumen', 'public');

                    DokumenProyek::create([
                        'detail_bangun_id' => $detail->id,
                        'jenis_dokumen' => $label,
                        'file_path' => $path,
                        'status_verifikasi' => 'pending',
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message_1' => 'Pengajuan pembangunan berhasil dikirim!',
                'message_2' => 'Pengajuan pemesanan material berhasil dikirim!',
                'data' => $proyek
            ], 201);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
