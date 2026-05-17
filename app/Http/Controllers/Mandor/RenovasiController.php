<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\DetailProyekRenovasi;
use App\Models\Material;
use App\Models\MaterialRenovasi;
use App\Models\Mandor;
use App\Models\NegosiasiRenovasi;
use App\Models\PenawaranRenovasi;
use App\Models\RequestRenovasi;
use App\Models\Proyek;
use App\Models\MandorActivityHistory;
use App\Services\RenovasiService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\SupabaseStorageService;

class RenovasiController extends Controller
{
    public function __construct(private readonly RenovasiService $renovasiService) {}

    public function dashboard()
    {
        $this->renovasiService->expirePendingOffers();
        $mandor = $this->currentMandor();

        $activeProjectOffersQuery = PenawaranRenovasi::where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'diterima');

        $activeProjects = (clone $activeProjectOffersQuery)
            ->whereHas('requestRenovasi', fn($query) => $query->where('status_request', '!=', 'selesai'))
            ->count();

        $completedProjects = (clone $activeProjectOffersQuery)
            ->whereHas('requestRenovasi', fn($query) => $query->where('status_request', 'selesai'))
            ->count();

        $hasActiveProject = $activeProjects > 0;
        $isMandorAvailable = $mandor->status === 'aktif';

        $renovationRequests = RequestRenovasi::with([
            'customer.user',
            'penawaran' => fn($q) => $q->latest(),
            'penawaran.materialRenovasi.material',
            'penawaran.negosiasi' => fn($q) => $q->orderBy('created_at'),
        ])
            ->where('status_request', 'pending')
            ->where(function ($query) use ($mandor) {
                $query->whereDoesntHave('penawaran', function ($offerQuery) {
                    $offerQuery->whereIn('status_penawaran', ['pending', 'diterima']);
                });

                $query->orWhereHas('penawaran', function ($offerQuery) use ($mandor) {
                    $offerQuery->where('mandor_id', $mandor->id)
                        ->where('status_penawaran', 'pending');
                });
            })
            ->latest()
            ->get()
            ->map(function (RequestRenovasi $request) use ($isMandorAvailable) {
                $currentOffer = $request->penawaran->first();
                $offerMaterials = $currentOffer?->materialRenovasi ?? collect();
                $negotiationMessages = $currentOffer?->negosiasi?->values() ?? collect();
                $hasCustomerNegotiation = $negotiationMessages->contains(fn($message) => $message->pengirim === 'customer');

                return [
                    'id' => sprintf('REV-%03d', $request->id),
                    'db_id' => $request->id,
                    'applicant_name' => $request->customer?->user?->name ?? 'Customer',
                    'budget' => $this->renovasiService->formatRupiah((int) $request->budget_estimasi),
                    'phone' => $request->customer?->user?->phone_number ?? '-',
                    'location' => $request->alamat,
                    'description' => $request->deskripsi_renovasi,
                    'photos' => $request->getFotoDetailUrls()
                        ?: [asset('images/aset/user-dummy.jpg')],
                    'existing_offer_cost' => $currentOffer ? (int) $currentOffer->estimasi_biaya : 0,
                    'existing_offer_feedback' => $currentOffer?->analisis_dari_mandor,
                    'existing_offer_status' => $currentOffer?->status_penawaran,
                    'existing_offer_materials' => $offerMaterials->map(fn($item) => [
                        'material_id' => (string) $item->material_id,
                        'jumlah' => (int) $item->jumlah,
                    ])->values(),
                    'negotiation_messages' => $negotiationMessages->map(function ($message) {
                        return [
                            'pengirim' => $message->pengirim,
                            'tipe' => $message->tipe,
                            'pesan' => $message->pesan,
                            'nominal_tawaran' => $message->nominal_tawaran
                                ? $this->renovasiService->formatRupiah((int) $message->nominal_tawaran)
                                : null,
                            'waktu' => optional($message->created_at)->format('d M Y H:i'),
                        ];
                    })->values(),
                    'has_customer_negotiation' => $hasCustomerNegotiation,
                    'can_send_negotiation' => !$isMandorAvailable && $hasCustomerNegotiation,
                    'can_take_renovation' => $isMandorAvailable && !$hasCustomerNegotiation && !$currentOffer,
                ];
            })
            ->values();

        $requestMap = $renovationRequests->keyBy('id');
        $materialCatalog = Material::query()
            ->select(['id', 'nama_material', 'kategori', 'harga', 'satuan', 'stok', 'deskripsi', 'path_foto_material'])
            ->orderBy('nama_material')
            ->get()
            ->map(fn(Material $item) => [
                'id' => (string) $item->id,
                'nama_material' => $item->nama_material,
                'kategori' => $item->kategori,
                'harga' => (int) $item->harga,
                'satuan' => $item->satuan,
                'stok' => $item->stok,
                'deskripsi' => $item->deskripsi,
                'path_foto_material' => $item->path_foto_material,
            ])
            ->values();

        $activeProjects = '-';
        $completedProjects = 0;
        $requestCount = $renovationRequests->count();

        // Ambil history aktivitas mandor
        $activityHistory = MandorActivityHistory::where('mandor_id', $mandor->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($activity) {
                return [
                    'title' => $activity->description,
                    'timestamp' => $activity->created_at->format('d M Y H:i'),
                ];
            });
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
            ->whereHas(
                'requestRenovasi',
                fn($q) =>
                $q->where('status_request', '!=', 'selesai')
            )
            ->latest()
            ->first();

        $activeRenovasiLabel = $activeRenovasi
            ? "Renovasi - #{$activeRenovasi->requestRenovasi->id}"
            : null;

        $activeProjectLabel = $activeBangunLabel ?? $activeRenovasiLabel ?? 'Tidak ada proyek aktif';

        return view('mandor.mandor_dashboard', compact(
            'renovationRequests',
            'requestMap',
            'materialCatalog',
            'activeProjects',
            'completedProjects',
            'requestCount',
            'activityHistory',
            'activeProjectLabel'
        ));
    }

    public function submitOffer(Request $request, RequestRenovasi $requestRenovasi, NotificationService $notif)
    {
        $this->renovasiService->expirePendingOffers();
        $mandor = $this->currentMandor();

        $validated = $request->validate([
            'feedback' => 'required|string|min:20',
            'estimasi_biaya' => 'required|integer|min:100000',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($requestRenovasi->status_request !== 'pending') {
            return response()->json([
                'status' => 'error',
                'message' => 'Request renovasi ini tidak dapat direview.',
            ], 422);
        }

        $existingPendingOffer = PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('status_penawaran', 'pending')
            ->first();

        if ($existingPendingOffer && $existingPendingOffer->mandor_id !== $mandor->id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Request ini sudah sedang direview mandor lain.',
            ], 422);
        }

        $isNewOffer = !PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'pending')
            ->exists();

        DB::transaction(function () use ($validated, $requestRenovasi, $mandor, $isNewOffer) {
            $offer = PenawaranRenovasi::updateOrCreate(
                [
                    'request_renovasi_id' => $requestRenovasi->id,
                    'mandor_id' => $mandor->id,
                    'status_penawaran' => 'pending',
                ],
                [
                    'analisis_dari_mandor' => $validated['feedback'],
                    'estimasi_biaya' => $validated['estimasi_biaya'],
                    'estimasi_durasi' => 14,
                ]
            );

            MaterialRenovasi::where('penawaran_renovasi_id', $offer->id)->delete();

            foreach ($validated['materials'] as $materialInput) {
                $material = Material::find($materialInput['material_id']);
                if (!$material) {
                    continue;
                }

                MaterialRenovasi::create([
                    'penawaran_renovasi_id' => $offer->id,
                    'material_id' => $material->id,
                    'jumlah' => $materialInput['jumlah'],
                    'satuan' => $material->satuan,
                ]);
            }

            NegosiasiRenovasi::create([
                'request_renovasi_id' => $requestRenovasi->id,
                'penawaran_renovasi_id' => $offer->id,
                'pengirim' => 'mandor',
                'tipe' => $isNewOffer ? 'penawaran' : 'tanggapan',
                'pesan' => $validated['feedback'],
                'nominal_tawaran' => $validated['estimasi_biaya'],
            ]);

            // Update status mandor menjadi busy saat submit offer pertama kali
            if ($isNewOffer) {
                $mandor->update(['status' => 'nonaktif']);
            }
        });

        // Log aktivitas submit offer
        if ($isNewOffer) {
            MandorActivityHistory::logOfferSubmitted($mandor, $requestRenovasi, $validated['estimasi_biaya']);

        // ✉️ Kirim notifikasi email ke customer
        $penawaranForNotif = PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('mandor_id', $mandor->id)
            ->with(['mandor.user', 'materialRenovasi.material'])
            ->latest()->first();
        if ($penawaranForNotif) {
            $notif->kirimPenawaranRenovasi(
                $requestRenovasi->load('customer.user'),
                $penawaranForNotif,
                $isNewOffer ? 'penawaran' : 'negosiasi'
            );
        }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Penawaran renovasi berhasil dikirim.',
        ]);
    }

    public function negotiate(Request $request, RequestRenovasi $requestRenovasi, NotificationService $notif)
    {
        $this->renovasiService->expirePendingOffers();
        $mandor = $this->currentMandor();

        $validated = $request->validate([
            'pesan' => 'required|string|min:5',
            'estimasi_biaya' => 'required|integer|min:100000',
            'materials' => 'required|array|min:1',
            'materials.*.material_id' => 'required|exists:materials,id',
            'materials.*.jumlah' => 'required|integer|min:1',
        ]);

        $offer = PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('mandor_id', $mandor->id)
            ->latest()
            ->first();

        if (!$offer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penawaran renovasi untuk request ini belum tersedia.',
            ], 422);
        }

        DB::transaction(function () use ($validated, $requestRenovasi, $mandor, $offer) {
            $offer->update([
                'estimasi_biaya' => $validated['estimasi_biaya'],
            ]);

            MaterialRenovasi::where('penawaran_renovasi_id', $offer->id)->delete();

            foreach ($validated['materials'] as $materialInput) {
                $material = Material::find($materialInput['material_id']);
                if (!$material) {
                    continue;
                }

                MaterialRenovasi::create([
                    'penawaran_renovasi_id' => $offer->id,
                    'material_id' => $material->id,
                    'jumlah' => $materialInput['jumlah'],
                    'satuan' => $material->satuan,
                ]);
            }

            NegosiasiRenovasi::create([
                'request_renovasi_id' => $requestRenovasi->id,
                'penawaran_renovasi_id' => $offer->id,
                'pengirim' => 'mandor',
                'tipe' => 'tanggapan',
                'pesan' => $validated['pesan'],
                'nominal_tawaran' => $validated['estimasi_biaya'],
            ]);
        });

        // ✉️ Kirim notifikasi negosiasi ke customer
        $offerForNotif = $offer->load('mandor.user', 'materialRenovasi.material');
        $notif->kirimPenawaranRenovasi(
            $requestRenovasi->load('customer.user'),
            $offerForNotif,
            'negosiasi'
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Negosiasi berhasil dikirim.',
            'negotiation' => [
                'pengirim' => 'mandor',
                'pesan' => $validated['pesan'],
                'nominal_tawaran' => $this->renovasiService->formatRupiah((int) $validated['estimasi_biaya']),
                'waktu' => now()->format('d M Y H:i'),
            ],
        ]);
    }

    public function tracking()
    {
        if ($offer && $offer->requestRenovasi) {
            $materialsTotal = $offer->materialRenovasi->sum(function ($item) {
                $price = (int) ($item->material?->harga ?? 0);
                return $price * (int) $item->jumlah;
            });
        
            // Ambil proyek_id dari DetailProyekRenovasi
            $detailProyek = \App\Models\DetailProyekRenovasi::where('request_renovasi_id', $offer->requestRenovasi->id)
                ->latest()
                ->first();
        
            $renovationData = [
                'request_id'     => sprintf('REV-%03d', $offer->requestRenovasi->id),
                'customer_name'  => $offer->requestRenovasi->customer?->user?->name ?? 'Customer',
                'customer_phone' => $offer->requestRenovasi->customer?->user?->phone_number ?? '-',
                'biaya_total'    => $this->renovasiService->formatRupiah((int) $offer->estimasi_biaya + $materialsTotal),
                'biaya_renovasi' => $this->renovasiService->formatRupiah((int) $offer->estimasi_biaya),
                'tanggal_mulai'  => optional($offer->updated_at ?? $offer->created_at)->format('d M Y'),
                'deskripsi'      => $offer->requestRenovasi->deskripsi_renovasi,
                'analisis'       => $offer->analisis_dari_mandor,
                'photos'         => $offer->requestRenovasi->getFotoDetailUrls()
                    ?: [asset('images/aset/user-dummy.jpg')],
                'request_db_id'  => $offer->requestRenovasi->id,
                'proyek_id'      => $detailProyek?->proyek_id, // ← TAMBAHAN INI
            ];
        }
        
        $proyekRenovasi = $detailProyek?->proyek_id
            ? \App\Models\Proyek::with('dokumentasi')->find($detailProyek->proyek_id)
            : null;
        
        return view('mandor.mandor_tracking', compact(
            'isHaveProject',
            'isHaveRenovation',
            'isAccepted',
            'renovationData',
            'proyekRenovasi',  
        ));
    }

    public function markDone(RequestRenovasi $requestRenovasi, NotificationService $notif)
    {
        $mandor = $this->currentMandor();

        // Cek apakah mandor memiliki akses ke renovasi ini
        $acceptedOffer = PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'diterima')
            ->exists();

        if (!$acceptedOffer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke renovasi ini.',
            ], 403);
        }

        // Update status renovasi dan bebaskan mandor dari proyek
        DB::transaction(function () use ($requestRenovasi, $mandor) {
            $requestRenovasi->update(['status_request' => 'selesai']);
            $mandor->update(['status' => 'aktif']);

            // Cari proyek renovasi yang terkait dan update status + bebaskan mandor
            $detailRenovasi = DetailProyekRenovasi::where('request_renovasi_id', $requestRenovasi->id)->first();
            if ($detailRenovasi && $detailRenovasi->proyek) {
                $detailRenovasi->proyek->update([
                    'status_proyek' => 'Selesai',
                    'mandor_id' => null
                ]);
            }

            // Log aktivitas renovasi selesai
            MandorActivityHistory::logRenovationCompleted($mandor, $requestRenovasi);
        });

        // ✉️ Kirim notifikasi renovasi selesai ke customer
        $notif->kirimRenovasiSelesai($requestRenovasi->load('customer.user', 'penawaran.mandor.user'));

        return response()->json([
            'status' => 'success',
            'message' => 'Renovasi berhasil ditandai selesai. Mandor telah dibebaskan dari proyek.',
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
