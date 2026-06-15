<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

use App\Models\Proyek;
use App\Models\RequestRenovasi;
use App\Models\MandorActivityHistory;
use App\Models\Material;

class MandorDashboardController extends Controller
{
    public function index()
    {
        // get currently authenticated mandor user
        $mandor = Auth::user();

        /**
         * =========================
         * PROYEK BANGUN AKTIF
         * =========================
         */
        $activeBangun = Proyek::with('detailBangun')
            ->where('mandor_id', $mandor->id)
            ->where('status_proyek', 'In Progress')
            ->where('jenis_proyek', 'Bangun Rumah')
            ->first();

        $activeBangunLabel = null;

        if ($activeBangun) {
            $nama = $activeBangun->detailBangun?->nama_proyek;

            $activeBangunLabel = $nama
                ? "Bangun - {$nama} (#{$activeBangun->id})"
                : "Bangun - Proyek #{$activeBangun->id}";
        }

        /**
         * =========================
         * PROYEK RENOVASI AKTIF
         * =========================
         */
        $activeRenovasi = \App\Models\PenawaranRenovasi::with('requestRenovasi')
            ->where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'diterima')
            ->whereHas('requestRenovasi', fn($q) =>
                $q->where('status_request', '!=', 'selesai')
            )
            ->latest()
            ->first();

        $activeRenovasiLabel = $activeRenovasi
            ? "Renovasi - #{$activeRenovasi->requestRenovasi->id}"
            : null;

        $activeProjectLabel = $activeBangunLabel ?? $activeRenovasiLabel ?? 'Tidak ada proyek aktif';

        /**
         * =========================
         * 2. REQUEST RENOVASI
         * =========================
         */
        $requests = Cache::remember('mandor:renovation_requests', now()->addMinutes(2), function () {
            return RequestRenovasi::latest()->take(10)->get();
        });

        $requestCount = $requests->count();

        $renovationRequests = $requests->map(function ($req) {
            return [
                'id' => $req->id,
                'applicant_name' => $req->nama_pengaju,
                'budget' => 'Rp ' . number_format($req->budget, 0, ',', '.'),
                'phone' => $req->no_hp,
                'photos' => $req->foto ? json_decode($req->foto, true) : [],
            ];
        });

        /**
         * =========================
         * 3. ACTIVITY HISTORY (FIX DI SINI)
         * =========================
         */
        $activityHistory = Cache::remember('mandor:activity_history:' . $mandor->id, now()->addMinutes(5), function () use ($mandor) {
            return MandorActivityHistory::where('mandor_id', $mandor->id)
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($act) {
                    return [
                        'title' => $act->description,
                        'timestamp' => $act->created_at->diffForHumans(),
                    ];
                })
                ->all();
        });

        /**
         * =========================
         * 4. REQUEST MAP (MODAL)
         * =========================
         */
        $requestMap = [];

        foreach ($requests as $req) {
            $requestMap[$req->id] = [
                'id' => $req->id,
                'applicant_name' => $req->nama_pengaju,
                'location' => $req->lokasi,
                'description' => $req->deskripsi,
                'phone' => $req->no_hp,
                'budget' => $req->budget,
                'photos' => $req->foto ? json_decode($req->foto, true) : [],
                'negotiations' => [],
            ];
        }

        /**
         * =========================
         * 5. MATERIAL
         * =========================
         */
        $materials = Cache::remember('mandor:material_catalog', now()->addHour(), function () {
            return Material::select('id', 'nama', 'harga')->get();
        });

        $materialCatalog = $materials->map(function ($mat) {
            return [
                'id' => $mat->id,
                'name' => $mat->nama,
                'price' => $mat->harga,
            ];
        });

        /**
         * =========================
         * RETURN
         * =========================
         */
        return view('mandor.dashboard', compact(
            'activeProjectLabel',
            'requestCount',
            'renovationRequests',
            'activityHistory',
            'requestMap',
            'materialCatalog'
        ));
    }
}