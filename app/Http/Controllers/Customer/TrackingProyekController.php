<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\ProyekDokumentasi;
use Illuminate\Support\Facades\Auth;
use App\Services\SupabaseStorageService;

class TrackingProyekController extends Controller
{
    public function tracking($id)
    {
        $customer = Auth::user()->customer;
        abort_if(!$customer, 403);

        $proyek = Proyek::with([
            'tasks' => fn($q) => $q->orderBy('urutan'),
            'aktivitas',
            'dokumentasi',
            'progress',
            'detailBangun.desainRumah',
            'mandor.user',
        ])
        ->where('customer_id', $customer->id)
        ->where('id', $id)
        ->where('status_proyek', 'In Progress')
        ->where('jenis_proyek', 'Bangun Rumah')
        ->first();

        abort_if(!$proyek, 404);

        $totalTask      = $proyek->tasks->count();
        $selesaiTask    = $proyek->tasks->where('is_selesai', true)->count();
        $daftarMilestone = ['Fondasi', 'Struktur', 'Atap', 'MEP', 'Finishing'];
        $milestoneSelesai = '-';
        foreach (array_reverse($daftarMilestone) as $nama) {
            $tasks = $proyek->tasks->where('milestone', $nama);
            if ($tasks->count() > 0 && $tasks->where('is_selesai', true)->count() === $tasks->count()) {
                $milestoneSelesai = $nama;
                break;
            }
        }
        $milestoneAktif      = 'Semua Selesai';
        $milestoneBerikutnya = '-';
        foreach ($daftarMilestone as $i => $nama) {
            $tasks   = $proyek->tasks->where('milestone', $nama);
            $total   = $tasks->count();
            $selesai = $tasks->where('is_selesai', true)->count();

            if ($total > 0 && $selesai < $total) {
                $milestoneAktif = $nama;
                // Cari milestone berikutnya yang punya task
                foreach (array_slice($daftarMilestone, $i + 1) as $next) {
                    if ($proyek->tasks->where('milestone', $next)->count() > 0) {
                        $milestoneBerikutnya = $next;
                        break;
                    }
                }
                break;
            }
        }

        $bobotMilestone = [
            'Fondasi'   => 15,
            'Struktur'  => 35,
            'Atap'      => 15,
            'MEP'       => 15,
            'Finishing' => 20,
        ];

        $persentase = 0;
        foreach ($bobotMilestone as $milestone => $bobot) {
            $tasks        = $proyek->tasks->where('milestone', $milestone);
            $total        = $tasks->count();
            $selesai      = $tasks->where('is_selesai', true)->count();
            if ($total > 0) {
                $persentase += ($selesai / $total) * $bobot;
            }
        }
        $persentase = round($persentase);

        $estimasiSelesai = null;
        if ($proyek->tanggal_mulai && $proyek->detailBangun?->desainRumah?->estimasi_durasi) {
            $estimasiSelesai = \Carbon\Carbon::parse($proyek->tanggal_mulai)
                ->addMonths($proyek->detailBangun->desainRumah->estimasi_durasi)
                ->format('d M Y');
        }

        $statusMilestone = [];
        foreach ($daftarMilestone as $nama) {
            $tasks   = $proyek->tasks->where('milestone', $nama);
            $total   = $tasks->count();
            $selesai = $tasks->where('is_selesai', true)->count();

            $statusMilestone[$nama] = match(true) {
                $total === 0        => 'pending',
                $selesai === $total => 'completed',
                $selesai > 0        => 'in-progress',
                default             => 'pending',
            };
        }
        $forceNext = true;
        foreach ($daftarMilestone as $nama) {
            if ($statusMilestone[$nama] === 'completed') {
                continue;
            }
            if ($statusMilestone[$nama] === 'pending' && $forceNext) {
                $statusMilestone[$nama] = 'in-progress';
            }
            break;
        }

        $milestones = collect($daftarMilestone)->map(fn($nama) => [
            'nama'   => $nama,
            'status' => $statusMilestone[$nama],
        ]);

        return view('customer-layouts.customer_tracking', compact(
            'proyek',
            'persentase',
            'milestoneAktif',
            'milestoneSelesai',
            'milestoneBerikutnya',
            'estimasiSelesai',
            'milestones',
        ));
    }

    public function getDokumentasiUrl(ProyekDokumentasi $dok, \App\Services\SupabaseStorageService $storage)
    {
        $customer = Auth::user()->customer;
        abort_if($dok->proyek->customer_id !== $customer->id, 403);

        $url = $storage->getSignedUrl($dok->storage_path, 3600);
        return redirect($url);
    }
}
