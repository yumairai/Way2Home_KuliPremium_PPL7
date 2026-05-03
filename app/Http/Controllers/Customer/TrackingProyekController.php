<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use Illuminate\Support\Facades\Auth;

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
        $milestoneAktif   = $proyek->tasks->firstWhere('is_selesai', false)?->milestone ?? 'Semua Selesai';
        $milestoneSelesai = $proyek->tasks->where('is_selesai', true)->last()?->milestone ?? '-';
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

        $milestones = collect(['Fondasi', 'Struktur', 'Atap', 'MEP', 'Finishing'])
            ->map(function ($nama) use ($proyek) {
                $tasks   = $proyek->tasks->where('milestone', $nama);
                $total   = $tasks->count();
                $selesai = $tasks->where('is_selesai', true)->count();
                $status  = match(true) {
                    $total === 0        => 'pending',
                    $selesai === $total => 'completed',
                    $selesai > 0        => 'in-progress',
                    default             => 'pending',
                };
                return ['nama' => $nama, 'status' => $status];
            });

        return view('customer-layouts.customer_tracking', compact(
            'proyek',
            'persentase',
            'milestoneAktif',
            'milestoneSelesai',
            'estimasiSelesai',
            'milestones',
        ));
    }
}