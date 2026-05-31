<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Mandor;
use App\Models\MandorActivityHistory;
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
            'pembayaranProyek',
            'customer.user',
        ])
            ->where('customer_id', $customer->id)
            ->where('id', $id)
            ->whereIn('status_proyek', ['In Progress', 'Selesai'])
            ->where('jenis_proyek', 'Bangun Rumah')
            ->first();

        abort_if(!$proyek, 404);

        $totalTask      = $proyek->tasks->count();
        $selesaiTask    = $proyek->tasks->where('is_selesai', true)->count();
        $daftarMilestone = ['Fondasi', 'Struktur', 'Atap', 'MEP', 'Finishing'];

        // ── Tester: gunakan progress_proyek, bukan tasks ──────────────────
        $isTesterProyek = $proyek->customer?->user?->is_tester;

        if ($isTesterProyek && $proyek->progress) {
            $syaratPembayaran = [
                'Fondasi'   => 0,
                'Struktur'  => 0,
                'Atap'      => 1,
                'MEP'       => 2,
                'Finishing' => 3,
            ];
            $bobotMilestone = [
                'Fondasi'   => 15,
                'Struktur'  => 35,
                'Atap'      => 15,
                'MEP'       => 15,
                'Finishing' => 20,
            ];

            // Cari periode tertinggi yang sudah berhasil dibayar
            $maxPeriodeBayar = $proyek->pembayaranProyek
                ->where('status_pembayaran', 'berhasil')
                ->max('periode') ?? -1;

            $persentase = 0;
            $statusMilestone = [];
            $milestoneAktif = 'Semua Selesai';
            $milestoneSelesai = '-';
            $milestoneBerikutnya = '-';

            foreach ($daftarMilestone as $nama) {
                $syarat = $syaratPembayaran[$nama];
                if ($maxPeriodeBayar >= $syarat) {
                    $statusMilestone[$nama] = 'completed';
                    $persentase += $bobotMilestone[$nama];
                    $milestoneSelesai = $nama;
                } else {
                    $statusMilestone[$nama] = 'pending';
                }
            }

            // Cari milestone aktif (pending pertama setelah completed)
            $foundAktif = false;
            foreach ($daftarMilestone as $i => $nama) {
                if ($statusMilestone[$nama] === 'pending' && !$foundAktif) {
                    $statusMilestone[$nama] = 'in-progress';
                    $milestoneAktif = $nama;
                    $foundAktif = true;
                    // Cari berikutnya
                    foreach (array_slice($daftarMilestone, $i + 1) as $next) {
                        $milestoneBerikutnya = $next;
                        break;
                    }
                    break;
                }
            }

            if (!$foundAktif) {
                $milestoneAktif = 'Semua Selesai';
            }

            $persentase = round($persentase);

            $milestones = collect($daftarMilestone)->map(fn($nama) => [
                'nama'   => $nama,
                'status' => $statusMilestone[$nama],
            ]);

        } else {
            // ── Non-tester: logika existing dari tasks ────────────────────
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

            $statusMilestone = [];
            foreach ($daftarMilestone as $nama) {
                $tasks   = $proyek->tasks->where('milestone', $nama);
                $total   = $tasks->count();
                $selesai = $tasks->where('is_selesai', true)->count();

                $statusMilestone[$nama] = match (true) {
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
        }

        $estimasiSelesai = null;
        if ($proyek->tanggal_mulai && $proyek->detailBangun?->desainRumah?->estimasi_durasi) {
            $estimasiSelesai = \Carbon\Carbon::parse($proyek->tanggal_mulai)
                ->addMonths($proyek->detailBangun->desainRumah->estimasi_durasi)
                ->format('d M Y');
        }

        $normalizePhoneNumber = function (?string $phoneNumber): ?string {
            if (!$phoneNumber) return null;
            $digits = preg_replace('/\D+/', '', $phoneNumber);
            if (!$digits) return null;
            if (str_starts_with($digits, '0')) {
                $digits = '62' . substr($digits, 1);
            } elseif (!str_starts_with($digits, '62')) {
                $digits = '62' . ltrim($digits, '0');
            }
            return $digits;
        };

        $mandorForContact = $proyek->mandor;
        if (!$mandorForContact || !$mandorForContact->user) {
            $mandorHistory = MandorActivityHistory::where('reference_type', 'proyek')
                ->where('reference_id', $proyek->id)
                ->whereIn('activity_type', ['assigned_project', 'completed_project'])
                ->latest()
                ->first();
            if ($mandorHistory) {
                $mandorForContact = Mandor::with('user')->find($mandorHistory->mandor_id);
            }
        }

        $mandorContactName   = $mandorForContact?->user?->name ?? 'Belum Ditentukan';
        $mandorContactNumber = $mandorForContact?->user?->phone_number;
        $mandorContactWaUrl  = ($normalizedMandorNumber = $normalizePhoneNumber($mandorContactNumber))
            ? "https://wa.me/{$normalizedMandorNumber}"
            : null;
        $mandorContactAvatar = $mandorForContact?->user?->avatar;

        $adminMainContactName   = 'Admin Utama';
        $adminMainContactNumber = '081384310179';
        $adminMainContactWaUrl  = 'https://wa.me/6281384310179';

        return view('customer-layouts.customer_tracking', compact(
            'proyek',
            'persentase',
            'milestoneAktif',
            'milestoneSelesai',
            'milestoneBerikutnya',
            'estimasiSelesai',
            'milestones',
            'mandorContactName',
            'mandorContactNumber',
            'mandorContactWaUrl',
            'mandorContactAvatar',
            'adminMainContactName',
            'adminMainContactNumber',
            'adminMainContactWaUrl',
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
