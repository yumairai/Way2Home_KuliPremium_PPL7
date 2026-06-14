<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proyek;
use App\Models\DokumenProyek;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VerifikasiProyekController extends Controller
{
    public function index(\App\Services\SupabaseStorageService $storageService)
    {
        // Calculate stats on all projects
        $allProyeks = Proyek::with(['detailBangun.dokumenProyek'])->get();
        
        $totalPengajuan = $allProyeks->count();
        $pendingCount = 0;
        $verifiedCount = 0;
        $rejectedCount = 0;

        foreach ($allProyeks as $item) {
            $dokumen = $item->detailBangun->dokumenProyek ?? collect();
            if ($dokumen->isEmpty()) {
                $pendingCount++;
                continue;
            }

            $isFinal = $dokumen->every(function ($doc) {
                return in_array($doc->status_verifikasi, ['disetujui', 'ditolak']);
            });

            if ($isFinal) {
                $hasRejected = $dokumen->contains('status_verifikasi', 'ditolak');
                if ($hasRejected) {
                    $rejectedCount++;
                } else {
                    $verifiedCount++;
                }
            } else {
                $pendingCount++;
            }
        }

        $stats = [
            'total'    => $totalPengajuan,
            'pending'  => $pendingCount,
            'verified' => $verifiedCount,
            'rejected' => $rejectedCount,
        ];

        // Sort with latest() to show newest at the top
        $proyek = Proyek::with(['customer.user', 'detailBangun.dokumenProyek'])
            ->latest()
            ->paginate(6);

        foreach ($proyek as $item) {
            if ($item->detailBangun) {
                $paths = $item->detailBangun->dokumenProyek->pluck('file_path')->toArray();
                if (!empty($paths)) {
                    $signedUrls = $storageService->getAdminSignedUrls($paths);
                    $item->detailBangun->dokumenProyek->transform(function ($dokumen) use ($signedUrls, $storageService) {
                        if (str_starts_with($dokumen->file_path, 'https://')) {
                            $dokumen->signed_url = $dokumen->file_path;
                        } elseif (str_starts_with($dokumen->file_path, 'public-assets/')) {
                            $dokumen->signed_url = $storageService->getSignedUrl($dokumen->file_path);
                        } else {
                            $dokumen->signed_url = $signedUrls[$dokumen->file_path] ?? null;
                        }
                        return $dokumen;
                    });
                }
            }
        }

        return view('admin.verifikasi_dokumen', compact('proyek', 'stats'));
    }

    public function show($id, \App\Services\SupabaseStorageService $storageService)
    {
        $proyek = Proyek::with(['customer.user', 'detailBangun.dokumenProyek'])->findOrFail($id);

        if ($proyek->detailBangun) {
            $paths = $proyek->detailBangun->dokumenProyek->pluck('file_path')->toArray();
            if (!empty($paths)) {
                $signedUrls = $storageService->getAdminSignedUrls($paths);
                $proyek->detailBangun->dokumenProyek->transform(function ($dokumen) use ($signedUrls, $storageService) {
                    if (str_starts_with($dokumen->file_path, 'https://')) {
                        $dokumen->signed_url = $dokumen->file_path;
                    } elseif (str_starts_with($dokumen->file_path, 'public-assets/')) {
                        $dokumen->signed_url = $storageService->getPublicUrl($dokumen->file_path);
                    } else {
                        $dokumen->signed_url = $signedUrls[$dokumen->file_path] ?? null;
                    }
                    return $dokumen;
                });
            }
        }

        return view('admin.verifikasi.show', compact('proyek'));
    }

    public function update(Request $request, $id, NotificationService $notif)
    {
        $request->validate([
            'status_proyek'  => 'required',
            'catatan_admin'  => 'nullable|string',
            'status_dokumen' => 'required|array',
        ]);

        $proyek = Proyek::with(['customer.user'])->findOrFail($id);

        if ($proyek->detailBangun) {
            $allFinal = $proyek->detailBangun->dokumenProyek
                ->every(fn($doc) => in_array($doc->status_verifikasi, ['disetujui', 'ditolak']));

            if ($allFinal && $proyek->detailBangun->dokumenProyek->isNotEmpty()) {
                return back()->with('error', 'Dokumen sudah final, tidak bisa diubah lagi.');
            }
        }

        $statusFinal = null;

        DB::transaction(function () use ($request, $proyek, &$statusFinal) {
            $adaDitolak = false;

            foreach ($request->status_dokumen as $docId => $status) {
                DokumenProyek::where('id', $docId)->update(['status_verifikasi' => $status]);
                if ($status == 'ditolak') $adaDitolak = true;
            }

            if ($proyek->detailBangun) {
                $proyek->detailBangun()->update(['catatan_admin' => $request->catatan_admin]);
            }

            $statusFinal = $request->status_proyek;
            if ($statusFinal == 'Pembayaran DP' && $adaDitolak) {
                $statusFinal = 'Revisi Dokumen';
            }

            $proyek->update(['status_proyek' => $statusFinal]);
        });

        // ✉️ Kirim notifikasi email ke customer
        // Ensure parameters are strings to satisfy type expectations (avoid null)
        $notif->kirimStatusProyek($proyek, (string) $statusFinal, (string) ($request->catatan_admin ?? ''));

        return redirect()->back()->with('success', 'Verifikasi berhasil diperbarui dan notifikasi telah dikirim ke customer.');
    }
}