@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/order.css') }}">
@endpush

@section('content')
    @php
        $orders = [
            [
                'id' => 'ORD-20260419-001',
                'date' => '19 April 2026',
                'address' => 'Jl. Melati No. 12, Bandung, Jawa Barat',
                'status' => 'persiapan',
                'status_label' => 'Persiapan',
                'total' => 'Rp 4.250.000',
                'summary' => '4 item material',
                'materials' => [
                    ['name' => 'Semen Portland 50kg', 'qty' => 10, 'price' => 'Rp 75.000'],
                    ['name' => 'Bata Ringan', 'qty' => 120, 'price' => 'Rp 8.500'],
                    ['name' => 'Pasir Bangunan', 'qty' => 3, 'price' => 'Rp 350.000'],
                    ['name' => 'Besi Beton 10mm', 'qty' => 15, 'price' => 'Rp 92.000'],
                ],
            ],
            [
                'id' => 'ORD-20260418-014',
                'date' => '18 April 2026',
                'address' => 'Perumahan Citra Indah Blok B3, Cimahi',
                'status' => 'dikirim',
                'status_label' => 'Dikirim',
                'total' => 'Rp 1.980.000',
                'summary' => '3 item material',
                'materials' => [
                    ['name' => 'Keramik Lantai 40x40', 'qty' => 18, 'price' => 'Rp 120.000'],
                    ['name' => 'Nat Keramik', 'qty' => 5, 'price' => 'Rp 28.000'],
                    ['name' => 'Lem Keramik', 'qty' => 4, 'price' => 'Rp 85.000'],
                ],
            ],
            [
                'id' => 'ORD-20260417-022',
                'date' => '17 April 2026',
                'address' => 'Komplek Griya Asri C7, Cimahi',
                'status' => 'selesai',
                'status_label' => 'Pesanan Selesai',
                'total' => 'Rp 2.760.000',
                'summary' => '5 item material',
                'materials' => [
                    ['name' => 'Cat Tembok Interior', 'qty' => 8, 'price' => 'Rp 95.000'],
                    ['name' => 'Cat Dasar', 'qty' => 4, 'price' => 'Rp 110.000'],
                    ['name' => 'Roll Cat', 'qty' => 3, 'price' => 'Rp 45.000'],
                    ['name' => 'Kuas', 'qty' => 6, 'price' => 'Rp 18.000'],
                    ['name' => 'Lakban Kertas', 'qty' => 2, 'price' => 'Rp 25.000'],
                ],
            ],
        ];
    @endphp

    <main class="order-page">
        <section class="order-hero">
            <div>
                <p class="order-kicker">History Pesanan</p>
                <h1 class="order-title">Pesanan Material Saya</h1>
                <p class="order-subtitle">Ringkasan pesanan yang sudah dibayar dan status yang dikelola admin.</p>
            </div>
        </section>

        <section class="order-list" id="orderList">
            @foreach ($orders as $order)
                <article class="order-card" data-order-card>
                    <button class="order-card-toggle" type="button" aria-expanded="false">
                        <div class="order-card-summary">
                            <div class="order-card-top">
                                <div>
                                    <p class="order-label">ID Order</p>
                                    <h2 class="order-id">{{ $order['id'] }}</h2>
                                </div>
                                <span class="order-status {{ $order['status'] }}">{{ $order['status_label'] }}</span>
                            </div>

                            <div class="order-meta-grid">
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Tanggal Order</span>
                                    <span class="order-meta-value">{{ $order['date'] }}</span>
                                </div>
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Alamat</span>
                                    <span class="order-meta-value">{{ $order['address'] }}</span>
                                </div>
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Daftar Material</span>
                                    <span class="order-meta-value">{{ $order['summary'] }}</span>
                                </div>
                                <div class="order-meta-item">
                                    <span class="order-meta-label">Total Harga</span>
                                    <span class="order-meta-value order-total">{{ $order['total'] }}</span>
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
                                <p>{{ count($order['materials']) }} item di dalam pesanan ini</p>
                            </div>

                            <div class="order-material-list">
                                @foreach ($order['materials'] as $material)
                                    <div class="order-material-row">
                                        <div>
                                            <p class="order-material-name">{{ $material['name'] }}</p>
                                            <p class="order-material-meta">Qty {{ $material['qty'] }}</p>
                                        </div>
                                        <span class="order-material-price">{{ $material['price'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-order-card]').forEach((card) => {
            const button = card.querySelector('.order-card-toggle');
            const toggleText = card.querySelector('[data-toggle-text]');

            button.addEventListener('click', () => {
                const expanded = card.classList.toggle('is-expanded');
                button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
                toggleText.textContent = expanded ? 'Tutup detail' : 'Buka detail';
            });
        });
    </script>
@endpush
