<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\DokumenProyek;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiProyekController extends Controller
{

    public function index()
    {
        $proyek = Proyek::with(['customer.user', 'detailBangun.dokumenProyek'])->get();
        return view('admin.verifikasi_dokumen', compact('proyek'));
    }

    public function show($id)
    {
        $proyek = Proyek::with(['customer.user', 'detailBangun.dokumenProyek'])->findOrFail($id);
        return view('admin.verifikasi.show', compact('proyek'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status_proyek' => 'required',
            'catatan_admin' => 'nullable|string',
            'status_dokumen' => 'required|array'
        ]);

        $proyek = Proyek::findOrFail($id);

        DB::transaction(function () use ($request, $proyek) {
            $adaDitolak = false;

            // 1. Update status tiap dokumen (IMB, KTP, dll)
            foreach ($request->status_dokumen as $docId => $status) {
                DokumenProyek::where('id', $docId)->update(['status_verifikasi' => $status]);
                if ($status == 'ditolak') $adaDitolak = true;
            }

            // 2. Simpan catatan admin ke Detail Proyek Bangun
            $proyek->detailBangun()->update([
                'catatan_admin' => $request->catatan_admin
            ]);

            // 3. Update status utama proyek
            $statusFinal = $request->status_proyek;
            // Jika admin pilih ACC tapi ada file ditolak, paksa ke status Revisi
            if ($statusFinal == 'Pembayaran DP' && $adaDitolak) {
                $statusFinal = 'Revisi Dokumen';
            }

            $proyek->update(['status_proyek' => $statusFinal]);
        });

        return redirect()->back()->with('success', 'Verifikasi berhasil diperbarui');
    }
}
