<!DOCTYPE html>

<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Material Marketplace</title>
    <link rel="stylesheet" href="{{ asset('css/customer/material_marketplace.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>

<body>
    <div class="checkout-btn" onclick="window.location.href='/material/cart'">
        <div class="checkout-left"><img src="{{ asset('images/icon/shopping-cart.png') }}" alt="Cart"> • <span
                id="checkoutCount">3</span>Items</div>
        <div class="checkout-right">Rp<span id="checkoutTotal">6.495.000</span></div>

    </div>
    <main>
        <!-- TopNavBar -->
        <nav>
            <div class="nav-container">
                <div class="brand">
                    <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                    <span class="brand-text">Way2Home</span>
                </div>
                <div class="nav-links">
                    <a href="/">Beranda</a>
                    <a href="/rekomendasi/input">Desain</a>
                    <a href="/material" class="active">Material</a>
                    <a href="/renovasi">Renovasi</a>
                </div>
                <div class="nav-actions">
                    <div class="cart-icon" onclick="window.location.href='/material/cart'">
                        <img src="{{ asset('images/icon/shopping-bag.png') }}" alt="">
                        <span class="cart-badge">3</span>
                    </div>
                    <img alt="User profile avatar" class="profile-avatar"
                        src="{{ asset('images/aset/user-dummy.jpg') }}" />
                </div>
            </div>
        </nav>
        <main>
            <!-- Hero Section -->
            <section class="hero-section">
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
                                <input class="price-range-slider" id="priceRange" max="10000000" min="0"
                                    step="100000" value="0" type="range" />
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
        </main>
        <!-- Footer -->
        <footer>
            <div class="footer-container">
                <div class="footer-brand">
                    <div class="footer-brand-info">
                        <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                        <span class="footer-brand-name">Way2Home</span>
                    </div>
                    <p class="footer-brand-text">© 2026 Way2Home Construction
                        Platform. Architectural Excellence.</p>
                </div>
                <div class="footer-links">
                    <a href="#">Tentang Kami</a>
                    <a href="#">Proyek</a>
                    <a href="#">Karir</a>
                    <a href="#">Kontak</a>
                    <a href="#">Privasi</a>
                </div>
                <div class="footer-actions">
                    <div class="footer-icon-btn">
                        <img src="{{ asset('images/icon/whatsapp.png') }}" alt="WhatsApp">
                    </div>
                </div>
            </div>
        </footer>
        <script src="{{ asset('js/customer/material_marketplace.js') }}"></script>
</body>

</html>
