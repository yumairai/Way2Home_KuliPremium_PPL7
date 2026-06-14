@extends('admin.admin_page')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/order_management.css') }}" />
@endpush
@section('title')
    Admin - Order Management
@endsection
@section('header')
    <h2>Kelola Pesanan</h2>
    <p>Kelola pesanan customer yang sudah dibayar dan ubah status pengiriman secara bertahap.</p>
@endsection

@section('stats')
    <div class="order-stat-card order-stat-primary">
        <p class="order-stat-label">Total Pesanan</p>
        <p class="order-stat-value" id="stat-total">{{ $orders->total() }}</p>
    </div>
    <div class="order-stat-card order-stat-prep">
        <p class="order-stat-label">Menunggu Pengiriman</p>
        <p class="order-stat-value" id="stat-paid">{{ $statusCounts['paid'] }}</p>
    </div>
    <div class="order-stat-card order-stat-ship">
        <p class="order-stat-label">Dikirim</p>
        <p class="order-stat-value" id="stat-dikirim">{{ $statusCounts['dikirim'] }}</p>
    </div>
    <div class="order-stat-card order-stat-done">
        <p class="order-stat-label">Selesai</p>
        <p class="order-stat-value" id="stat-selesai">{{ $statusCounts['selesai'] }}</p>
    </div>
@endsection

@section('content')
    <div class="order-admin-wrap">
        <div class="order-admin-head">
            <h3>Daftar Pesanan User</h3>
            <p>Klik detail untuk melihat material. Ubah status secara berurutan agar alur pengiriman tetap konsisten.</p>
        </div>

        <div class="order-admin-list">
            @forelse ($orders as $order)
                <article
                    class="order-admin-card"
                    data-order-card
                    data-status="{{ $order->status_order }}"
                    data-order-id="{{ $order->id }}"
                >
                    <div class="order-admin-main">
                        <div class="order-admin-top">
                            <div>
                                <p class="order-admin-kicker">ID Order</p>
                                <h4 class="order-admin-id">{{ $order->order_id_midtrans }}</h4>
                            </div>
                            <span class="order-admin-status {{ $order->status_order }}" data-status-badge></span>
                        </div>

                        <div class="order-admin-meta-grid">
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Nama Customer</span>
                                <span class="meta-value">{{ $order->customer->user->name ?? '-' }}</span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Tanggal Order</span>
                                <span class="meta-value">
                                    {{ \Carbon\Carbon::parse($order->tanggal_order)->translatedFormat('d F Y') }}
                                </span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Alamat</span>
                                <span class="meta-value">{{ $order->alamat_pengiriman }}</span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Daftar Material</span>
                                <span class="meta-value">{{ $order->details->count() }} item material</span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Total Harga</span>
                                <span class="meta-value total">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <div class="order-admin-actions">
                            <button class="order-action-btn secondary" type="button" data-toggle-detail>
                                Lihat Detail Material
                            </button>
                            <button class="order-action-btn primary" type="button" data-update-status></button>
                        </div>
                    </div>

                    <div class="order-admin-detail-shell">
                        <div class="order-admin-detail">
                            <div class="order-admin-detail-head">
                                <h5>Detail Material</h5>
                                <p>{{ $order->details->count() }} item pada pesanan ini</p>
                            </div>
                            <div class="order-admin-material-list">
                                @foreach ($order->details as $detail)
                                    <div class="order-admin-material-row">
                                        <div>
                                            <p class="material-name">{{ $detail->material->nama_material ?? '-' }}</p>
                                            <p class="material-meta">Qty {{ $detail->jumlah }}</p>
                                        </div>
                                        <span class="material-price">
                                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div style="padding: 3rem; text-align: center; color: var(--text-muted);">
                    Belum ada pesanan masuk.
                </div>
            @endforelse
        </div>

        @if ($orders->total() > 6)
            <div class="pagination-container" style="margin-top: 2rem; display: flex; justify-content: flex-end; align-items: center; width: 100%;">
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($orders->onFirstPage())
                        <button class="pagination-button pagination-button-icon" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                    @else
                        <a href="{{ $orders->previousPageUrl() }}" class="pagination-button pagination-button-icon" style="text-decoration: none;">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($orders->getUrlRange(1, $orders->lastPage()) as $page => $url)
                        @if ($page == $orders->currentPage())
                            <button class="pagination-button pagination-button-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="pagination-button" style="text-decoration: none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($orders->hasMorePages())
                        <a href="{{ $orders->nextPageUrl() }}" class="pagination-button pagination-button-icon" style="text-decoration: none;">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </a>
                    @else
                        <button class="pagination-button pagination-button-icon" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin/order_management.js') }}"></script>
@endpush