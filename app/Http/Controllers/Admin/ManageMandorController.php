<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Mandor;
use App\Models\Proyek;

class ManageMandorController extends Controller
{
    public function index()
    {
        // Load mandor beserta user dan proyek aktifnya
        $mandors = Mandor::with(['user', 'proyekAktif'])
            ->where('status', '!=', 'suspend')
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

        if ($proyek->mandor_id !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Proyek ini sudah memiliki mandor!'
            ], 422);
        }

        $proyek->update([
            'mandor_id'     => $mandor->id,
            'tanggal_mulai' => now(),
            // Ganti status proyek sesuai flow kamu setelah mandor diassign
            // 'status_proyek' => 'Sedang Dikerjakan',
        ]);

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

        Proyek::where('mandor_id', $mandor->id)->update([
            'mandor_id'     => null,
            'tanggal_mulai' => null,
            'status_proyek' => 'Pengalokasian Mandor',
        ]);

        return response()->json([
            'success' => true,
            'message' => "Mandor {$mandor->user->name} berhasil dilepas dari proyek."
        ]);
    }
}
