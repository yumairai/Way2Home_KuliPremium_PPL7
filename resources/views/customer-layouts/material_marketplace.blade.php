@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/material_marketplace.css') }}">
@endpush
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <!-- floating button (kaya grab kalo kata yumi) buat notalin barang !-->
        @auth
            <div class="checkout-btn" id="floatingCart" style="display: none;" onclick="window.location.href='/material/cart'">
                <div class="checkout-left"><img src="{{ asset('images/icon/shopping-cart.png') }}" alt="Cart"> • <span
                        id="checkoutCount"></span>Items</div>
                <div class="checkout-right">Rp<span id="checkoutTotal"></span></div>
            </div>
        @endauth
        <div class="hero-container">
            <div class="hero-background">
                <img alt="Construction Background" src="{{ asset('images/aset/construction.jpg') }}" />
            </div>
            <div class="hero-content">
                <h1 class="hero-title">
                    Bangun Impian Anda dengan <br /><span class="hero-title-accent">Material Berkualitas</span>.
                </h1>
                <p class="hero-description">
                    Pilihan material konstruksi premium untuk hasil akhir yang presisi dan tahan lama. Way2Home
                    menyiapkan kebutuhan material konstruksi Anda.
                </p>
                <!-- Search Bar -->
                <div class="search-bar">
                    <div class="search-input-wrapper">
                        <img src="{{ asset('images/icon/search-interface-symbol.png') }}" alt="">
                        <input class="search-input" placeholder="Cari material . . ." type="text" />
                    </div>
                    <button class="search-btn">Cari Sekarang</button>
                </div>
            </div>
        </div>
    </section>
    <!-- Main Content Area -->
    <div class="main-layout">
        <!-- Sidebar Filters -->
        <aside class="sidebar">
            <div class="filter-wrapper">
                <div>
                    <h3 class="filter-header">
                        <img src="{{ asset('images/icon/sort.png') }}" alt="">
                        Filter Produk
                    </h3>
                    <!-- Kategori -->
                    <div class="filter-section">
                        <p class="filter-label">Kategori</p>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Struktur" />
                                <span>Struktur</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Dinding" />
                                <span>Dinding</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Atap" />
                                <span>Atap</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Sanitasi" />
                                <span>Sanitasi</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Finishing" />
                                <span>Finishing</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Material Dasar" />
                                <span>Material Dasar</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Elektrikal" />
                                <span>Elektrikal</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" value="Kusen & Pintu" />
                                <span>Kusen & Pintu</span>
                            </label>
                        </div>
                    </div>
                    <!-- Harga Range -->
                    <div class="price-range-wrapper">
                        <p class="filter-label">Rentang Harga (Rp)</p>
                        <div class="current-price-display">
                            <span id="currentPriceLabel">Rp 0</span>
                        </div>
                        <input class="price-range-slider" id="priceRange" max="1000000" min="0" step="25000"
                            value="1000000" type="range" />
                        <div class="price-range-labels">
                            <span>Rp 0</span>
                            <span>Rp 1jt++</span>
                        </div>
                    </div>
                </div>
                <div class="filter-action-group">
                    <button class="reset-filter-btn" type="button">Apply Filter</button>
                    <button class="clear-filter-btn" type="button">Reset Filter</button>
                </div>
            </div>
        </aside>
        <!-- Product Grid -->
        <section class="product-section">
            <div class="product-header">
                <div class="product-header-content">
                    <h2>Semua Material</h2>
                    <p>Menampilkan produk utama</p>
                </div>
                <div class="product-sort">
                    <span>Urutkan:</span>
                    <select class="sort-select">
                        <option value="">Harga</option>
                        <option value="harga_rendah">Harga Terendah</option>
                        <option value="harga_tinggi">Harga Tertinggi</option>
                    </select>
                </div>
            </div>
            <div class="product-grid" id="productGrid">
            </div>

            <!-- Pagination - akan di-render oleh JS -->
            <div class="pagination" id="paginationContainer">
            </div>
        </section>
    </div>
@endsection
@push('scripts')
    <script>
        window.loginUrl = "{{ route('login') }}";
    </script>
    <script src="{{ asset('js/customer/material_marketplace.js') }}"></script>
@endpush
