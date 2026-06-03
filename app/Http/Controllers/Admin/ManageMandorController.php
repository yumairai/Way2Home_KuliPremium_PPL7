<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mandor;
use App\Models\Proyek;
use App\Models\ProgressProyek;
use App\Models\MandorActivityHistory;
use Database\Seeders\ProyekMilestoneSeeder;

class ManageMandorController extends Controller
{
    public function index()
    {
        // Load mandor beserta user dan proyek aktifnya
        $mandors = Mandor::with(['user', 'proyekAktif', 'renovasiAktif'])
            ->where('status', '!=', 'suspend')
            ->where('is_ghost', false)
            ->get();

        // Proyek yang belum ada mandornya
        $proyeksAvailable = Proyek::whereNull('mandor_id')
            ->where('status_proyek', 'Pengalokasian Mandor')
            ->with('detailBangun.desainRumah')
            ->get();

        $stats = [
            'total'    => $mandors->count(),
            'available' => $mandors->where('status', 'aktif')->count(),
            'busy'     => $mandors->where('status', 'nonaktif')->count(),
            // Pakai status dari tabel mandors kamu: aktif/nonaktif
            // Nanti bisa kita sesuaikan dengan logika "sedang bertugas"
        ];

        return view('admin.manajemen_mandor', compact('mandors', 'proyeksAvailable', 'stats'));
    }

    public function assign(Request $request)
    {
        $request->validate([
            'mandor_id' => 'required|exists:mandors,id',
            'proyek_id' => 'required|exists:proyek,id',
        ]);

        $mandor = Mandor::findOrFail($request->mandor_id);
        $proyek = Proyek::findOrFail($request->proyek_id);

        // Cek apakah mandor sedang busy
        if ($mandor->status === 'nonaktif') {
            return response()->json([
                'success' => false,
                'message' => 'Mandor ini sedang menangani proyek lain!'
            ], 422);
        }

        if ($proyek->mandor_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Proyek ini sudah memiliki mandor!'
            ], 422);
        }

        $proyek->update([
            'mandor_id'     => $mandor->id,
            'tanggal_mulai' => now(),
            'status_proyek' => 'In Progress',
        ]);

        $proyek->load('detailBangun.desainRumah');
        $proyek->generateCicilan();

        $mandor->update([
            'status' => 'nonaktif',
        ]);

        // Auto-generate task standar pembangunan
        ProyekMilestoneSeeder::generateForProyek($proyek->id);

        // Buat progress awal
        ProgressProyek::create([
            'proyek_id'       => $proyek->id,
            'milestone_aktif' => 'Fondasi',
            'persentase'      => 0,
            'tanggal_update'  => now(),
        ]);

        // Log aktivitas mandor
        MandorActivityHistory::logAssignedProject($mandor, $proyek);

        return response()->json([
            'success' => true,
            'message' => "Mandor {$mandor->user->name} berhasil diassign!"
        ]);
    }

    public function unassign(Request $request)
    {
        $request->validate([
            'mandor_id' => 'required|exists:mandors,id',
        ]);

        $mandor = Mandor::findOrFail($request->mandor_id);
        $proyek = Proyek::where('mandor_id', $mandor->id)->first();

        if ($proyek) {
            // Hapus semua data terkait proyek
            $proyek->tasks()->delete();
            $proyek->progress()->delete();
            $proyek->pembayaranProyek()->delete();
            $proyek->detailBangun()?->delete();
            $proyek->dokumen()->delete();
            $proyek->aktivitas()->delete();
            $proyek->dokumentasi()->delete();
            
            // Hapus proyek secara permanen
            $proyek->delete();
        }

        $mandor->update(['status' => 'aktif']);

        return response()->json([
            'success' => true,
            'message' => "Mandor {$mandor->user->name} berhasil dilepas dari proyek."
        ]);
    }
}
