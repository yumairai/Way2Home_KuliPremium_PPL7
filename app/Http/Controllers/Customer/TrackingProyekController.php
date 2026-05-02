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
        $persentase     = $totalTask > 0 ? round(($selesaiTask / $totalTask) * 100) : 0;
        $milestoneAktif = $proyek->tasks->firstWhere('is_selesai', false)?->nama_task ?? 'Semua Task Selesai';
        $milestoneSelesai = $proyek->tasks->where('is_selesai', true)->last()?->nama_task ?? '-';

        $estimasiSelesai = null;
        if ($proyek->tanggal_mulai && $proyek->detailBangun?->desainRumah?->estimasi_durasi) {
            $estimasiSelesai = \Carbon\Carbon::parse($proyek->tanggal_mulai)
                ->addMonths($proyek->detailBangun->desainRumah->estimasi_durasi)
                ->format('d M Y');
        }

        return view('customer-layouts.customer_tracking', compact(
            'proyek',
            'persentase',
            'milestoneAktif',
            'milestoneSelesai',
            'estimasiSelesai',
        ));
    }
}