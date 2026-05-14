@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
@endpush

@section('content')
    <main class="order-page">
        <section class="order-hero">
            <div>
                <p class="order-kicker">History Pesanan</p>
                <h1 class="order-title">Pesanan Material Saya</h1>
                <p class="order-subtitle">Ringkasan pesanan yang sudah dibayar dan status yang dikelola admin.</p>
            </div>
        </section>

        <section class="order-list" id="orderList">
            @forelse ($orders as $order)
                <article class="order-card" data-order-card>
                    <button class="order-card-toggle" type="button" aria-expanded="false">
                        <div class="order-card-summary">
                            <div class="order-card-top">
                                <div>
                                    <p class="order-label">ID Order</p>
                                    <h2 class="order-id">{{ $order->order_id_midtrans }}</h2>
                                </div>
                                <span class="order-status {{ $order->status_order }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>

                            <div class="order-meta-grid">
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Tanggal Order</span>
                                    <span class="order-meta-value">
                                        {{ \Carbon\Carbon::parse($order->tanggal_order)->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Alamat</span>
                                    <span class="order-meta-value">{{ $order->alamat_pengiriman }}</span>
                                </div>
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Daftar Material</span>
                                    <span class="order-meta-value">{{ $order->details->count() }} item material</span>
                                </div>
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Total Harga</span>
                                    <span class="order-meta-value order-total">
                                        Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div class="order-card-footer">
                                <span class="order-detail-hint">Klik untuk lihat detail material</span>
                                <span class="order-toggle-text" data-toggle-text>Buka detail</span>
                            </div>
                        </div>
                        <span class="order-chevron" aria-hidden="true"></span>
                    </button>

                    <div class="order-details-shell">
                        <div class="order-details">
                            <div class="order-details-head">
                                <h3>Daftar Material</h3>
                                <p>{{ $order->details->count() }} item di dalam pesanan ini</p>
                            </div>
                            <div class="order-material-list">
                                @foreach ($order->details as $detail)
                                    <div class="order-material-row">
                                        <div>
                                            <p class="order-material-name">
                                                {{ $detail->material->nama_material ?? '-' }}
                                            </p>
                                            <p class="order-material-meta">Qty {{ $detail->jumlah }}</p>
                                        </div>
                                        <span class="order-material-price">
                                            Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div style="padding: 3rem; text-align: center;">
                    <p>Belum ada pesanan. Yuk belanja material!</p>
                </div>
            @endforelse
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-order-card]').forEach((card) => {
            const button     = card.querySelector('.order-card-toggle');
            const toggleText = card.querySelector('[data-toggle-text]');
            button.addEventListener('click', () => {
                const expanded = card.classList.toggle('is-expanded');
                button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                toggleText.textContent = expanded ? 'Tutup detail' : 'Buka detail';
            });
        });
    </script>
@endpush