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
@php
    $orders = [
        [
            'id' => 'ORD-20260419-001',
            'customer' => 'Budi Santoso',
            'date' => '19 April 2026',
            'address' => 'Jl. Melati No. 12, Bandung, Jawa Barat',
            'status' => 'persiapan',
            'total' => 'Rp 4.250.000',
            'materials' => [
                ['name' => 'Semen Portland 50kg', 'qty' => 10, 'price' => 'Rp 75.000'],
                ['name' => 'Bata Ringan', 'qty' => 120, 'price' => 'Rp 8.500'],
                ['name' => 'Pasir Bangunan', 'qty' => 3, 'price' => 'Rp 350.000'],
                ['name' => 'Besi Beton 10mm', 'qty' => 15, 'price' => 'Rp 92.000'],
            ],
        ],
        [
            'id' => 'ORD-20260418-014',
            'customer' => 'Rina Mahardika',
            'date' => '18 April 2026',
            'address' => 'Perumahan Citra Indah Blok B3, Cimahi',
            'status' => 'dikirim',
            'total' => 'Rp 1.980.000',
            'materials' => [
                ['name' => 'Keramik Lantai 40x40', 'qty' => 18, 'price' => 'Rp 120.000'],
                ['name' => 'Nat Keramik', 'qty' => 5, 'price' => 'Rp 28.000'],
                ['name' => 'Lem Keramik', 'qty' => 4, 'price' => 'Rp 85.000'],
            ],
        ],
        [
            'id' => 'ORD-20260417-022',
            'customer' => 'Dimas Pratama',
            'date' => '17 April 2026',
            'address' => 'Komplek Griya Asri C7, Cimahi',
            'status' => 'selesai',
            'total' => 'Rp 2.760.000',
            'materials' => [
                ['name' => 'Cat Tembok Interior', 'qty' => 8, 'price' => 'Rp 95.000'],
                ['name' => 'Cat Dasar', 'qty' => 4, 'price' => 'Rp 110.000'],
                ['name' => 'Roll Cat', 'qty' => 3, 'price' => 'Rp 45.000'],
                ['name' => 'Kuas', 'qty' => 6, 'price' => 'Rp 18.000'],
                ['name' => 'Lakban Kertas', 'qty' => 2, 'price' => 'Rp 25.000'],
            ],
        ],
    ];

    $statusLabels = [
        'persiapan' => 'Persiapan',
        'dikirim' => 'Dikirim',
        'selesai' => 'Pesanan Selesai',
    ];

    $statusCounts = [
        'persiapan' => collect($orders)->where('status', 'persiapan')->count(),
        'dikirim' => collect($orders)->where('status', 'dikirim')->count(),
        'selesai' => collect($orders)->where('status', 'selesai')->count(),
    ];
@endphp
@section('stats')
    <div class="order-stat-card order-stat-primary">
        <p class="order-stat-label">Total Pesanan</p>
        <p class="order-stat-value" id="stat-total">{{ count($orders) }}</p>
    </div>
    <div class="order-stat-card order-stat-prep">
        <p class="order-stat-label">Persiapan</p>
        <p class="order-stat-value" id="stat-persiapan">{{ $statusCounts['persiapan'] }}</p>
    </div>
    <div class="order-stat-card order-stat-ship">
        <p class="order-stat-label">Dikirim</p>
        <p class="order-stat-value" id="stat-dikirim">{{ $statusCounts['dikirim'] }}</p>
    </div>
    <div class="order-stat-card order-stat-done">
        <p class="order-stat-label">Pesanan Selesai</p>
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
            @foreach ($orders as $order)
                <article class="order-admin-card" data-order-card data-status="{{ $order['status'] }}">
                    <div class="order-admin-main">
                        <div class="order-admin-top">
                            <div>
                                <p class="order-admin-kicker">ID Order</p>
                                <h4 class="order-admin-id">{{ $order['id'] }}</h4>
                            </div>
                            <span class="order-admin-status {{ $order['status'] }}" data-status-badge>
                                {{ $statusLabels[$order['status']] }}
                            </span>
                        </div>

                        <div class="order-admin-meta-grid">
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Nama Customer</span>
                                <span class="meta-value">{{ $order['customer'] }}</span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Tanggal Order</span>
                                <span class="meta-value">{{ $order['date'] }}</span>
                            </div>
                            <div class="order-admin-meta-item order-admin-meta-wide">
                                <span class="meta-label">Alamat</span>
                                <span class="meta-value">{{ $order['address'] }}</span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Daftar Material</span>
                                <span class="meta-value">{{ count($order['materials']) }} item material</span>
                            </div>
                            <div class="order-admin-meta-item">
                                <span class="meta-label">Total Harga</span>
                                <span class="meta-value total">{{ $order['total'] }}</span>
                            </div>
                        </div>

                        <div class="order-admin-actions">
                            <button class="order-action-btn secondary" type="button" data-toggle-detail>
                                Lihat Detail Material
                            </button>
                            <button class="order-action-btn primary" type="button" data-update-status>
                                @if ($order['status'] === 'persiapan')
                                    Ubah ke Dikirim
                                @elseif ($order['status'] === 'dikirim')
                                    Ubah ke Pesanan Selesai
                                @else
                                    Status Final
                                @endif
                            </button>
                        </div>
                    </div>

                    <div class="order-admin-detail-shell">
                        <div class="order-admin-detail">
                            <div class="order-admin-detail-head">
                                <h5>Detail Material</h5>
                                <p>{{ count($order['materials']) }} item pada pesanan ini</p>
                            </div>
                            <div class="order-admin-material-list">
                                @foreach ($order['materials'] as $material)
                                    <div class="order-admin-material-row">
                                        <div>
                                            <p class="material-name">{{ $material['name'] }}</p>
                                            <p class="material-meta">Qty {{ $material['qty'] }}</p>
                                        </div>
                                        <span class="material-price">{{ $material['price'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/admin/order_management.js') }}"></script>
@endpush
