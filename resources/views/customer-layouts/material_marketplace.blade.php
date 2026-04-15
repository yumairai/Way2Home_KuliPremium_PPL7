@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/material_marketplace.css') }}">
@endpush
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <!-- floating button (kaya grab kalo kata yumi) buat notalin barang !-->
        @auth
            <div class="checkout-btn" onclick="window.location.href='/material/cart'">
                <div class="checkout-left"><img src="{{ asset('images/icon/shopping-cart.png') }}" alt="Cart"> • <span
                        id="checkoutCount">3</span>Items</div>
                <div class="checkout-right">Rp<span id="checkoutTotal">6.495.000</span></div>
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
                <!-- Category Quick Links -->
                <div class="quick-links">
                    <span class="quick-links-label">Populer:</span>
                    <a class="quick-link-tag" href="#">Semen Merah Putih</a>
                    <a class="quick-link-tag" href="#">Besi Beton 10mm</a>
                    <a class="quick-link-tag" href="#">Bata Ringan</a>
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
                                <input checked class="checkbox-input" type="checkbox" />
                                <span>Semen</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" />
                                <span>Bata & Mortar</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" />
                                <span>Pasir & Kerikil</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" />
                                <span>Besi & Logam</span>
                            </label>
                            <label class="checkbox-label">
                                <input class="checkbox-input" type="checkbox" />
                                <span>Cat & Finishing</span>
                            </label>
                        </div>
                    </div>
                    <!-- Harga Range -->
                    <div class="price-range-wrapper">
                        <p class="filter-label">Rentang Harga (Rp)</p>
                        <div class="current-price-display">
                            <span id="currentPriceLabel">Rp 0</span>
                        </div>
                        <input class="price-range-slider" id="priceRange" max="10000000" min="0" step="100000"
                            value="0" type="range" />
                        <div class="price-range-labels">
                            <span>Rp 0</span>
                            <span>Rp 10jt+</span>
                        </div>
                    </div>
                    <!-- Ketersediaan -->
                    <div class="filter-section">
                        <p class="filter-label">Ketersediaan</p>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input checked class="radio-input" name="stock" type="radio" />
                                <span>Stok Tersedia</span>
                            </label>
                            <label class="radio-label">
                                <input class="radio-input" name="stock" type="radio" />
                                <span>Pre-order</span>
                            </label>
                        </div>
                    </div>
                </div>
                <button class="reset-filter-btn">Apply Filter</button>
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
                        <option>Terpopuler</option>
                        <option>Terbaru</option>
                        <option>Harga Terendah</option>
                        <option>Harga Tertinggi</option>
                    </select>
                </div>
            </div>
            <div class="product-grid" id="productGrid">
            </div>

            <div class="pagination" id="paginationContainer">
            </div>
            <!-- Pagination -->
            <div class="pagination">
                <button class="pagination-btn">
                    <img src="{{ asset('images/icon/chevron-left.png') }}" alt="Chevron Left">
                </button>
                <button class="pagination-btn active">1</button>
                <button class="pagination-btn">2</button>
                <button class="pagination-btn">3</button>
                <span class="pagination-dots">...</span>
                <button class="pagination-btn">
                    <img src="{{ asset('images/icon/chevron-right.png') }}" alt="Chevron Right">
                </button>
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
