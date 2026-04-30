<?php

namespace App\Http\Controllers\Mandor;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\MaterialRenovasi;
use App\Models\Mandor;
use App\Models\NegosiasiRenovasi;
use App\Models\PenawaranRenovasi;
use App\Models\RequestRenovasi;
use App\Services\RenovasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RenovasiController extends Controller
{
    public function __construct(private readonly RenovasiService $renovasiService) {}

    public function dashboard()
    {
        $this->renovasiService->expirePendingOffers();
        $mandor = $this->currentMandor();

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
            ->map(function (RequestRenovasi $request) {
                $currentOffer = $request->penawaran->first();
                $offerMaterials = $currentOffer?->materialRenovasi ?? collect();
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
                    'existing_offer_materials' => $offerMaterials->map(fn($item) => [
                        'material_id' => (string) $item->material_id,
                        'jumlah' => (int) $item->jumlah,
                    ])->values(),
                    'negotiation_messages' => $currentOffer?->negosiasi?->map(function ($message) {
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

        return view('mandor.mandor_dashboard', compact(
            'renovationRequests',
            'requestMap',
            'materialCatalog',
            'activeProjects',
            'completedProjects',
            'requestCount'
        ));
    }

    public function submitOffer(Request $request, RequestRenovasi $requestRenovasi)
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

        DB::transaction(function () use ($validated, $requestRenovasi, $mandor) {
            $isNewOffer = !PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
                ->where('mandor_id', $mandor->id)
                ->where('status_penawaran', 'pending')
                ->exists();

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
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Penawaran renovasi berhasil dikirim.',
        ]);
    }

    public function negotiate(Request $request, RequestRenovasi $requestRenovasi)
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
        $this->renovasiService->expirePendingOffers();
        $mandor = $this->currentMandor();

        $acceptedOffer = PenawaranRenovasi::with(['requestRenovasi.customer.user', 'materialRenovasi.material'])
            ->where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'diterima')
            ->whereHas('requestRenovasi', fn($query) => $query->where('status_request', '!=', 'selesai'))
            ->latest()
            ->first();

        $pendingOffer = PenawaranRenovasi::with('requestRenovasi.customer.user')
            ->where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'pending')
            ->latest()
            ->first();

        $isHaveProject = false;
        $isHaveRenovation = (bool) ($acceptedOffer || $pendingOffer);
        $isAccepted = (bool) $acceptedOffer;

        $offer = $acceptedOffer ?? $pendingOffer;
        $renovationData = null;

        if ($offer && $offer->requestRenovasi) {
            $materialsTotal = $offer->materialRenovasi->sum(function ($item) {
                $price = (int) ($item->material?->harga ?? 0);
                return $price * (int) $item->jumlah;
            });

            $renovationData = [
                'request_id' => sprintf('REV-%03d', $offer->requestRenovasi->id),
                'customer_name' => $offer->requestRenovasi->customer?->user?->name ?? 'Customer',
                'customer_phone' => $offer->requestRenovasi->customer?->user?->phone_number ?? '-',
                'biaya_total' => $this->renovasiService->formatRupiah((int) $offer->estimasi_biaya + $materialsTotal),
                'tanggal_mulai' => optional($offer->updated_at ?? $offer->created_at)->format('d M Y'),
                'deskripsi' => $offer->requestRenovasi->deskripsi_renovasi,
                'analisis' => $offer->analisis_dari_mandor,
                'photos' => $offer->requestRenovasi->getFotoDetailUrls()
                    ?: [asset('images/aset/user-dummy.jpg')],
                'request_db_id' => $offer->requestRenovasi->id,
            ];
        }

        return view('mandor.mandor_tracking', compact(
            'isHaveProject',
            'isHaveRenovation',
            'isAccepted',
            'renovationData'
        ));
    }

    public function markDone(RequestRenovasi $requestRenovasi)
    {
        $mandor = $this->currentMandor();

        $acceptedOffer = PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('mandor_id', $mandor->id)
            ->where('status_penawaran', 'diterima')
            ->latest()
            ->first();

        if (!$acceptedOffer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda tidak memiliki akses ke renovasi ini.',
            ], 403);
        }

        DB::transaction(function () use ($requestRenovasi, $mandor) {
            $requestRenovasi->update(['status_request' => 'selesai']);
            $this->renovasiService->syncMandorStatus($mandor);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Renovasi berhasil ditandai selesai.',
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
