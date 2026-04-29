<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DetailProyekRenovasi;
use App\Models\NegosiasiRenovasi;
use App\Models\PenawaranRenovasi;
use App\Models\Proyek;
use App\Models\RequestRenovasi;
use App\Services\RenovasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RenovasiController extends Controller
{
    public function __construct(private readonly RenovasiService $renovasiService) {}

    public function index()
    {
        $this->renovasiService->expirePendingOffers();

        $customer = Auth::user()?->customer;
        abort_if(!$customer, 403, 'Akun customer tidak ditemukan.');

        $requestRenovasiList = RequestRenovasi::with([
            'penawaran' => fn($query) => $query->latest(),
            'penawaran.mandor.user',
            'penawaran.materialRenovasi.material',
            'penawaran.negosiasi' => fn($query) => $query->orderBy('created_at'),
        ])
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        $requests = $requestRenovasiList->map(function (RequestRenovasi $requestRenovasi) {
            $latestOffer = $requestRenovasi->penawaran->first();

            $status = $this->resolveFrontendStatus($requestRenovasi, $latestOffer);
            $materials = $latestOffer?->materialRenovasi?->map(function ($item) {
                return [
                    'nama_material' => $item->material?->nama_material ?? '-',
                    'harga' => (int) ($item->material?->harga ?? 0),
                    'satuan' => $item->satuan ?: ($item->material?->satuan ?? '-'),
                    'jumlah' => (int) $item->jumlah,
                    'deskripsi' => $item->material?->deskripsi ?? '-',
                ];
            })->values()->toArray() ?? [];

            return [
                'id' => sprintf('REV-%03d', $requestRenovasi->id),
                'db_id' => $requestRenovasi->id,
                'status' => $status,
                'location' => $requestRenovasi->alamat,
                'budget_user' => $this->renovasiService->formatRupiah((int) $requestRenovasi->budget_estimasi),
                'damage_description' => $requestRenovasi->deskripsi_renovasi,
                'damage_photos' => $requestRenovasi->path_foto_detail
                    ? [asset('storage/' . $requestRenovasi->path_foto_detail)]
                    : [],
                'feedback' => $latestOffer?->analisis_dari_mandor
                    ?? 'Pengajuan Anda sedang dalam antrean review mandor.',
                'budget_needed' => $latestOffer
                    ? $this->renovasiService->formatRupiah((int) $latestOffer->estimasi_biaya)
                    : '-',
                'mandor_contact' => $latestOffer?->mandor?->user?->phone_number,
                'mandor_name' => $latestOffer?->mandor?->user?->name,
                'materials' => $materials,
                'negotiation_messages' => $latestOffer?->negosiasi?->map(function ($message) {
                    return [
                        'pengirim' => $message->pengirim,
                        'tipe' => $message->tipe,
                        'pesan' => $message->pesan,
                        'nominal_tawaran' => $message->nominal_tawaran
                            ? $this->renovasiService->formatRupiah((int) $message->nominal_tawaran)
                            : null,
                        'waktu' => optional($message->created_at)->format('d M Y H:i'),
                    ];
                })->values()->toArray() ?? [],
                'offer_expires_at' => $latestOffer ? optional($this->renovasiService->offerExpiresAt($latestOffer))->format('d M Y H:i') : null,
                'is_offer_expired' => $latestOffer ? $this->renovasiService->isOfferExpired($latestOffer) : false,
                'is_service_actionable' => $latestOffer
                    && $latestOffer->status_penawaran === 'pending'
                    && !$this->renovasiService->isOfferExpired($latestOffer),
            ];
        })->values();

        return view('customer-layouts.renovation', [
            'requests' => $requests,
            'isHaveRequest' => $requests->isNotEmpty(),
        ]);
    }

    public function create()
    {
        $customer = Auth::user()?->customer;
        abort_if(!$customer, 403, 'Akun customer tidak ditemukan.');

        return view('customer-layouts.renovation_form');
    }

    public function store(Request $request)
    {
        try {
            $customer = Auth::user()?->customer;
            abort_if(!$customer, 403, 'Akun customer tidak ditemukan.');

            $validated = $request->validate([
                'budget_estimasi' => 'required|integer|min:100000',
                'deskripsi_renovasi' => 'required|string|min:20',
                'alamat' => 'required|string|min:10',
                'foto_detail' => 'nullable|image|max:2048',
            ], [
                'foto_detail.uploaded' => 'Ukuran foto kerusakan terlalu besar. Maksimal 2 MB per file.',
                'foto_detail.image' => 'Foto kerusakan harus berupa gambar yang valid.',
                'foto_detail.max' => 'Ukuran foto kerusakan terlalu besar. Maksimal 2 MB per file.',
            ]);

            $photoPath = $request->hasFile('foto_detail')
                ? $request->file('foto_detail')->store('renovasi/request', 'public')
                : null;

            RequestRenovasi::create([
                'customer_id' => $customer->id,
                'deskripsi_renovasi' => $validated['deskripsi_renovasi'],
                'budget_estimasi' => $validated['budget_estimasi'],
                'alamat' => $validated['alamat'],
                'path_foto_detail' => $photoPath,
                'tanggal_request' => now()->toDateString(),
                'status_request' => 'pending',
            ]);

            return redirect()
                ->route('customer.renovation')
                ->with('success', 'Request renovasi berhasil dikirim.');
        } catch (\Throwable $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mengirim request renovasi: ' . $e->getMessage());
        }
    }

    public function acceptOffer(RequestRenovasi $requestRenovasi)
    {
        $this->renovasiService->expirePendingOffers();

        $customer = Auth::user()?->customer;
        abort_if(!$customer || $customer->id !== $requestRenovasi->customer_id, 403);

        $offer = PenawaranRenovasi::with('mandor')
            ->where('request_renovasi_id', $requestRenovasi->id)
            ->where('status_penawaran', 'pending')
            ->latest()
            ->first();

        if (!$offer || $this->renovasiService->isOfferExpired($offer)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penawaran sudah tidak tersedia.',
            ], 422);
        }

        DB::transaction(function () use ($requestRenovasi, $offer) {
            PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
                ->where('id', '!=', $offer->id)
                ->where('status_penawaran', 'pending')
                ->update(['status_penawaran' => 'ditolak']);

            $offer->update(['status_penawaran' => 'diterima']);
            $requestRenovasi->update(['status_request' => 'disetujui']);
            NegosiasiRenovasi::create([
                'request_renovasi_id' => $requestRenovasi->id,
                'penawaran_renovasi_id' => $offer->id,
                'pengirim' => 'customer',
                'tipe' => 'setuju',
                'pesan' => 'Customer menyetujui penawaran jasa dan material.',
            ]);

            $offer->mandor?->update(['status' => 'nonaktif']);

            $proyek = Proyek::create([
                'customer_id' => $requestRenovasi->customer_id,
                'mandor_id' => $offer->mandor_id,
                'jenis_proyek' => 'Renovasi',
                'alamat_proyek' => $requestRenovasi->alamat,
                'tanggal_mulai' => now()->toDateString(),
                'status_proyek' => 'In Progress',
                'jumlah_cicilan' => 0,
            ]);

            DetailProyekRenovasi::create([
                'proyek_id' => $proyek->id,
                'request_renovasi_id' => $requestRenovasi->id,
                'penawaran_renovasi_id' => $offer->id,
            ]);
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Jasa renovasi berhasil diambil.',
        ]);
    }

    public function negotiate(Request $request, RequestRenovasi $requestRenovasi)
    {
        $customer = Auth::user()?->customer;
        abort_if(!$customer || $customer->id !== $requestRenovasi->customer_id, 403);

        $validated = $request->validate([
            'pesan' => 'required|string|min:5',
            'nominal_tawaran' => 'nullable|integer|min:100000',
        ]);

        $offer = PenawaranRenovasi::where('request_renovasi_id', $requestRenovasi->id)
            ->where('status_penawaran', 'pending')
            ->latest()
            ->first();

        if (!$offer || $this->renovasiService->isOfferExpired($offer)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Penawaran tidak bisa dinegosiasikan.',
            ], 422);
        }

        NegosiasiRenovasi::create([
            'request_renovasi_id' => $requestRenovasi->id,
            'penawaran_renovasi_id' => $offer->id,
            'pengirim' => 'customer',
            'tipe' => 'negosiasi',
            'pesan' => $validated['pesan'],
            'nominal_tawaran' => $validated['nominal_tawaran'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Negosiasi berhasil dikirim ke mandor.',
        ]);
    }

    public function rejectOffer(Request $request, RequestRenovasi $requestRenovasi)
    {
        $customer = Auth::user()?->customer;
        abort_if(!$customer || $customer->id !== $requestRenovasi->customer_id, 403);

        $validated = $request->validate([
            'pesan' => 'nullable|string|max:1000',
        ]);

        $offer = PenawaranRenovasi::with('mandor')
            ->where('request_renovasi_id', $requestRenovasi->id)
            ->where('status_penawaran', 'pending')
            ->latest()
            ->first();

        if (!$offer) {
            return response()->json([
                'status' => 'error',
                'message' => 'Tidak ada penawaran aktif untuk ditolak.',
            ], 422);
        }

        DB::transaction(function () use ($offer, $requestRenovasi, $validated) {
            $offer->update(['status_penawaran' => 'ditolak']);
            NegosiasiRenovasi::create([
                'request_renovasi_id' => $requestRenovasi->id,
                'penawaran_renovasi_id' => $offer->id,
                'pengirim' => 'customer',
                'tipe' => 'tolak',
                'pesan' => $validated['pesan'] ?? 'Customer menolak penawaran saat ini.',
            ]);

            if ($offer->mandor) {
                $this->renovasiService->syncMandorStatus($offer->mandor);
            }
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Penawaran berhasil ditolak.',
        ]);
    }

    private function resolveFrontendStatus(RequestRenovasi $request, ?PenawaranRenovasi $offer): string
    {
        if ($request->status_request === 'selesai') {
            return 'completed';
        }

        if (!$offer) {
            return 'waiting';
        }

        if ($offer->status_penawaran === 'diterima') {
            return 'on-progress';
        }

        if ($offer->status_penawaran === 'pending' && !$this->renovasiService->isOfferExpired($offer)) {
            return 'reviewed';
        }

        return 'waiting';
    }
}
