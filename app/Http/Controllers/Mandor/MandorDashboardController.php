<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Models\Proyek;
use App\Models\RequestRenovasi;
use App\Models\MandorActivityHistory;
use App\Models\Material;

class MandorDashboardController extends Controller
{
    public function index()
    {
        $mandor = $this->currentMandor();

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
        $requests = RequestRenovasi::latest()->take(10)->get();

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
        $activities = MandorActivityHistory::where('mandor_id', $mandorId)
            ->latest()
            ->take(10)
            ->get();

        $activityHistory = $activities->map(function ($act) {
            return [
                'title' => $act->description,
                'timestamp' => $act->created_at->diffForHumans(),
            ];
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
        $materials = Material::all();

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
            'completedProjects',
            'requestCount',
            'renovationRequests',
            'activityHistory',
            'requestMap',
            'materialCatalog'
        ));
    }
}