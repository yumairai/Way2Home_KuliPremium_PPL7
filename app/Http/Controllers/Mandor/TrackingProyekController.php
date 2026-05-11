<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\ProyekMilestone;
use App\Models\ProyekAktivitas;
use App\Models\ProyekDokumentasi;
use App\Models\ProgressProyek;
use App\Models\Mandor;
use App\Models\MandorActivityHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackingProyekController extends Controller
{
    private array $bobotMilestone = [
        'Fondasi'   => 15,
        'Struktur'  => 35,
        'Atap'      => 15,
        'MEP'       => 15,
        'Finishing' => 20,
    ];

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

        $persentase = $this->hitungPersentase($proyek->tasks);
        $milestoneAktif = $proyek->tasks->firstWhere('is_selesai', false)?->milestone ?? 'Semua Selesai';

        $isHaveProject    = true;
        $isHaveRenovation = false;
        $isAccepted       = false;
        $renovationData   = null;

        return view('mandor.mandor_tracking', compact(
            'proyek',
            'persentase',
            'milestoneAktif',
            'isHaveProject',
            'isHaveRenovation',
            'isAccepted',
            'renovationData',
        ));
    }

    public function completeTask(ProyekMilestone $task)
    {
        $mandor = $this->currentMandor();
        abort_if($task->proyek->mandor_id !== $mandor->id, 403);

        $proyek = $task->proyek;
        $tasks  = $proyek->tasks()->orderBy('urutan')->get();
        $milestoneAktif = $tasks->firstWhere('is_selesai', false)?->milestone ?? null;

        abort_if($task->milestone !== $milestoneAktif, 403, 'Task ini bukan bagian dari milestone aktif.');

        $task->update(['is_selesai' => true]);

        $tasks = $proyek->tasks()->orderBy('urutan')->get();
        $persentase = $this->hitungPersentase($tasks);
        $milestoneAktif = $tasks->firstWhere('is_selesai', false)?->milestone ?? 'Semua Selesai';

        ProgressProyek::updateOrCreate(
            ['proyek_id' => $proyek->id],
            [
                'milestone_aktif' => $milestoneAktif,
                'persentase'      => $persentase,
                'tanggal_update'  => now(),
            ]
        );

        if ($persentase === 100) {
            DB::transaction(function () use ($proyek, $mandor) {
                $proyek->update([
                    'status_proyek' => 'Selesai',
                    'mandor_id'     => null,
                ]);
                $mandor->update(['status' => 'aktif']);
                MandorActivityHistory::logCompletedProject($mandor, $proyek);
            });
        }

        return response()->json([
            'success'         => true,
            'persentase'      => $persentase,
            'milestone_aktif' => $milestoneAktif,
            'is_done'         => $persentase === 100,
            'message'         => $persentase === 100 ? 'Proyek selesai!' : 'Task berhasil diselesaikan.',
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

    public function uploadDokumentasi(Request $request, Proyek $proyek, \App\Services\SupabaseStorageService $storage)
    {
        $mandor = $this->currentMandor();
        abort_if($proyek->mandor_id !== $mandor->id, 403);

        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:5120',
        ]);

        $storagePath = $storage->uploadPrivate(
            $request->file('foto'),
            $mandor->user_id,
            'proyek/dokumentasi/' . $proyek->id
        );

        $dok = ProyekDokumentasi::create([
            'proyek_id'    => $proyek->id,
            'path_foto'    => $storagePath, // simpan path saja
            'storage_path' => $storagePath,
        ]);

        $signedUrl = $storage->getSignedUrl($storagePath, 3600);

        return response()->json([
            'success'  => true,
            'foto_url' => $signedUrl,
            'tanggal'  => $dok->created_at->format('d M Y'),
        ]);
    }

    public function getDokumentasiUrl(ProyekDokumentasi $dok, \App\Services\SupabaseStorageService $storage)
    {
        $mandor = $this->currentMandor();
        abort_if($dok->proyek->mandor_id !== $mandor->id, 403);

        $url = $storage->getSignedUrl($dok->storage_path, 3600);
        return redirect($url);
    }

    private function hitungPersentase($tasks): int
    {
        $persentase = 0;
        foreach ($this->bobotMilestone as $milestone => $bobot) {
            $milestoneTask = $tasks->where('milestone', $milestone);
            $total         = $milestoneTask->count();
            $selesai       = $milestoneTask->where('is_selesai', true)->count();
            if ($total > 0) {
                $persentase += ($selesai / $total) * $bobot;
            }
        }
        return (int) round($persentase);
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