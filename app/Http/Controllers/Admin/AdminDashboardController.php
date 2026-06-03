<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PembayaranProyek;
use App\Models\Proyek;
use App\Models\User;
use App\Models\OrderMaterial;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Proyek bangun rumah dengan mandor
        $proyekBangun = Proyek::query()
            ->with([
                'customer.user',
                'mandor.user',
                'detailBangun.desainRumah',
                'progress',
            ])
            ->where('jenis_proyek', 'Bangun Rumah')
            ->whereIn('status_proyek', ['In Progress', 'Selesai'])
            ->whereNotNull('mandor_id')
            ->get();

        // Proyek renovasi dengan mandor dari penawaran
        $proyekRenovasi = Proyek::query()
            ->with([
                'customer.user',
                'detailRenovasi.requestRenovasi.penawaran.mandor.user',
                'progress',
            ])
            ->where('jenis_proyek', 'Renovasi')
            ->whereIn('status_proyek', ['In Progress', 'Selesai'])
            ->get()
            ->filter(function ($proyek) {
                $penawaran = $proyek->detailRenovasi?->requestRenovasi?->penawaran?->first();
                return $penawaran && $penawaran->status_penawaran === 'diterima';
            });

        $activeProjects = $proyekBangun->concat($proyekRenovasi)
            ->sortByDesc('id')
            ->map(function (Proyek $proyek) {
                $isRenovasi = $proyek->jenis_proyek === 'Renovasi';

                if ($isRenovasi) {
                    $revId = $proyek->detailRenovasi?->request_renovasi_id ?? 'XXX';
                    $revCode = 'REV-' . str_pad((string) $revId, 3, '0', STR_PAD_LEFT);
                    $projectTitle = 'Renovasi ' . $revCode;
                    $mandor_name = $proyek->detailRenovasi?->requestRenovasi?->penawaran?->first()?->mandor?->user?->name ?? 'Belum ditugaskan';
                } else {
                    $tipeTipe = $proyek->detailBangun?->desainRumah?->tipe_rumah ?? 'Proyek Bangun Rumah';
                    $projectTitle = 'Pembangunan Rumah ' . $tipeTipe;
                    $mandor_name = $proyek->mandor?->user?->name ?? 'Belum ditugaskan';
                }

                $statusLabel = $proyek->status_proyek === 'Selesai' ? 'Selesai' : 'On Going';
                $statusClass = $proyek->status_proyek === 'Selesai' ? 'green' : 'blue';
                $categoryLabel = $isRenovasi ? 'Renovasi' : 'Bangun Rumah';
                $categoryClass = $isRenovasi ? 'warning' : 'primary';

                $progress = $isRenovasi
                    ? 100
                    : (int) ($proyek->progress?->persentase ?? 0);

                if ($proyek->status_proyek === 'Selesai') {
                    $progress = 100;
                }

                $progress = max(0, min(100, $progress));

                $photoLabel = sprintf('%s #%s', $projectTitle, $proyek->id);
                $thumbnailSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="96" viewBox="0 0 96 96" fill="none"><rect width="96" height="96" rx="20" fill="#EAF1FF"/><path d="M22 56.5L48 33l26 23.5V74a4 4 0 0 1-4 4H26a4 4 0 0 1-4-4V56.5Z" fill="#BCD0F2"/><path d="M34 74V54h28v20" fill="#FFFFFF"/><path d="M40 74V63h16v11" fill="#D9E6FB"/><text x="48" y="25" text-anchor="middle" font-family="Arial, sans-serif" font-size="10" font-weight="700" fill="#004796">NO FOTO</text></svg>';

                return [
                    'id' => $proyek->id,
                    'project_code' => 'PRJ-' . str_pad((string) $proyek->id, 5, '0', STR_PAD_LEFT),
                    'title' => $projectTitle,
                    'photo_alt' => $photoLabel,
                    'thumbnail_src' => 'data:image/svg+xml;charset=UTF-8,' . rawurlencode($thumbnailSvg),
                    'owner_name' => $proyek->customer?->user?->name ?? '-',
                    'mandor_name' => $mandor_name,
                    'category_label' => $categoryLabel,
                    'category_class' => $categoryClass,
                    'status_label' => $statusLabel,
                    'status_class' => $statusClass,
                    'progress' => $progress,
                    'progress_class' => $statusClass,
                ];
            })
            ->values();

        $totalUsers = User::count();
        $completedProjects = Proyek::where('status_proyek', 'Selesai')->count();
        $activeRenovationProjects = Proyek::where('jenis_proyek', 'Renovasi')
            ->where('status_proyek', 'In Progress')
            ->count();
        $activeBuildProjects = Proyek::where('jenis_proyek', 'Bangun Rumah')
            ->where('status_proyek', 'In Progress')
            ->count();
        // Revenue dari pembangunan proyek (DP + Cicilan)
        $proyekRevenue = PembayaranProyek::where('status_pembayaran', 'berhasil')
            ->whereHas('proyek', function ($query) {
                $query->where('jenis_proyek', 'Bangun Rumah');
            })
            ->sum('jumlah_bayar');

        // Revenue dari penjualan material di marketplace
        $materialRevenue = OrderMaterial::whereIn('status_order', ['paid', 'dikirim', 'selesai'])
            ->sum('total_harga');

        $totalRevenue = $proyekRevenue + $materialRevenue;

        return view('admin.dashboard', compact(
            'activeProjects',
            'totalUsers',
            'completedProjects',
            'activeRenovationProjects',
            'activeBuildProjects',
            'totalRevenue'
        ));
    }
}
