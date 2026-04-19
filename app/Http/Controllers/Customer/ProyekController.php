<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\DetailProyekBangun;
use App\Models\DokumenProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProyekController extends Controller
{
    public function show($id)
    {
        return view('customer-layouts.proyek_user', ['id' => $id]);
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
        ]);

        DB::beginTransaction();

        try {
            $customerId = Auth::user()->customer->id;
            $proyek = Proyek::create([
                'customer_id'   => $customerId,
                'jenis_proyek' => 'Bangun Rumah',
                'alamat_proyek' => $request->alamat_proyek,
                'status_proyek' => 'perencanaan',
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
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ], 500);
        }
    }
}
