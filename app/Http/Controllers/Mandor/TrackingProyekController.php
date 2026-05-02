<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\ProyekTask;
use App\Models\ProyekAktivitas;
use App\Models\ProyekDokumentasi;
use App\Models\ProgressProyek;
use App\Models\Mandor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class TrackingProyekController extends Controller
{
    public function tracking()
    {
        $mandor = $this->currentMandor();

        $proyek = Proyek::with([
            'tasks' => fn($q) => $q->orderBy('urutan'),
            'aktivitas',
            'dokumentasi',
            'progress',
            'detailBangun.desainRumah',
            'customer.user',
        ])
        ->where('mandor_id', $mandor->id)
        ->where('status_proyek', 'In Progress')
        ->where('jenis_proyek', 'Bangun Rumah')
        ->first();

        abort_if(!$proyek, 404, 'Tidak ada proyek pembangunan aktif.');

        $totalTask     = $proyek->tasks->count();
        $selesaiTask   = $proyek->tasks->where('is_selesai', true)->count();
        $persentase    = $totalTask > 0 ? round(($selesaiTask / $totalTask) * 100) : 0;
        $milestoneAktif = $proyek->tasks->firstWhere('is_selesai', false)?->nama_task ?? 'Semua Task Selesai';

        $isHaveProject    = true;
        $isHaveRenovation = false;
        $isAccepted       = false;
        $renovationData   = null;

        return view('mandor.mandor_tracking', compact(
            'proyek',
            'persentase',
            'milestoneAktif',
            'isHaveProject',    // ← tambah
            'isHaveRenovation', // ← tambah
            'isAccepted',       // ← tambah
            'renovationData',   // ← tambah
        ));
    }

    public function completeTask(ProyekTask $task)
    {
        $mandor = $this->currentMandor();

        abort_if($task->proyek->mandor_id !== $mandor->id, 403);

        $task->update(['is_selesai' => true]);

        $proyek      = $task->proyek;
        $totalTask   = $proyek->tasks()->count();
        $selesaiTask = $proyek->tasks()->where('is_selesai', true)->count();
        $persentase  = $totalTask > 0 ? round(($selesaiTask / $totalTask) * 100) : 0;
        $milestoneAktif = $proyek->tasks()->where('is_selesai', false)->orderBy('urutan')->first()?->nama_task ?? 'Semua Task Selesai';

        ProgressProyek::updateOrCreate(
            ['proyek_id' => $proyek->id],
            [
                'milestone_aktif' => $milestoneAktif,
                'persentase'      => $persentase,
                'tanggal_update'  => now(),
            ]
        );

        // Auto selesaikan proyek kalau semua task done
        if ($persentase === 100) {
            $proyek->update(['status_proyek' => 'Selesai']);
        }

        return response()->json([
            'success'        => true,
            'persentase'     => $persentase,
            'milestone_aktif' => $milestoneAktif,
        ]);
    }

    public function tambahAktivitas(Request $request, Proyek $proyek)
    {
        $mandor = $this->currentMandor();
        abort_if($proyek->mandor_id !== $mandor->id, 403);

        $request->validate([
            'judul'     => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $aktivitas = ProyekAktivitas::create([
            'proyek_id' => $proyek->id,
            'judul'     => $request->judul,
            'deskripsi' => $request->deskripsi,
        ]);

        return response()->json([
            'success'   => true,
            'aktivitas' => [
                'judul'      => $aktivitas->judul,
                'deskripsi'  => $aktivitas->deskripsi,
                'created_at' => $aktivitas->created_at->format('d M Y'),
            ],
        ]);
    }

    public function uploadDokumentasi(Request $request, Proyek $proyek)
    {
        $mandor = $this->currentMandor();
        abort_if($proyek->mandor_id !== $mandor->id, 403);

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $path = $request->file('foto')->store('proyek/dokumentasi', 'public');

        $dok = ProyekDokumentasi::create([
            'proyek_id' => $proyek->id,
            'path_foto' => $path,
        ]);

        return response()->json([
            'success'  => true,
            'foto_url' => asset('storage/' . $dok->path_foto),
            'tanggal'  => $dok->created_at->format('d M Y'),
        ]);
    }

    private function currentMandor(): Mandor
    {
        $user = Auth::user();
        if ($user && method_exists($user, 'mandor') && $user->mandor) {
            return $user->mandor;
        }
        abort(403, 'Data mandor tidak ditemukan.');
    }
}