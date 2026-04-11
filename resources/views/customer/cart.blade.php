<!DOCTYPE html>

<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Material Marketplace | Cart</title>
    <link rel="stylesheet" href="{{ asset('css/customer/cart.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
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
                    <div class="cart-icon">
                        <img src="{{ asset('images/icon/shopping-bag.png') }}" alt="">
                        <span class="cart-badge">3</span>
                    </div>
                    <img alt="User profile avatar" class="profile-avatar"
                        src="{{ asset('images/aset/user-dummy.jpg') }}" />
                </div>
            </div>
        </nav>
        <!-- Cart Section -->
        <section class="cart-section">
            <main class="cart-main">
                <!-- nanti kalo belanjaannya kosong (sesuaikan sama DB nanti), ubah cart title ini jadi belanjaan masi kosong,
                    sama cart subtitlenya silakan lanjut berbelanja + tombol balik ke rute /marketplace -->
                <div class="cart-header">
                    <h1 class="cart-title">Shopping Cart</h1>
                    <p class="cart-subtitle">Periksa dan konfirmasikan pesanan material Anda.</p>
                </div>

                <div class="cart-layout">
                    <div class="cart-left">
                        <div class="delivery-address-form">
                            <div class="delivery-address-header">
                                <img src="{{ asset('images/icon/location.png') }}" alt="Location Icon">
                                <h2 class="delivery-address-title">Alamat Pengiriman</h2>
                            </div>
                            <div class="delivery-address-grid">
                                <div class="delivery-address-field">
                                    <!-- seharusnya ini udah terisi otomatis dari data user yang login -->
                                    <label class="delivery-address-label" for="nama_lengkap">Nama
                                        Lengkap</label>
                                    <input class="delivery-address-input" id="nama_lengkap"
                                        placeholder="Contoh: Budi Santoso" type="text" />
                                </div>
                                <div class="delivery-address-field">
                                    <!-- seharusnya ini juga udah terisi otomatis dari data user yang login -->
                                    <label class="delivery-address-label" for="nomor_telepon">Nomor Telepon</label>
                                    <input class="delivery-address-input" id="nomor_telepon"
                                        placeholder="Contoh: 08123456789" type="tel" />
                                </div>
                                <div class="delivery-address-field full-width">
                                    <!-- nah, kalo ini tergantung, user ini generate pake desain atau memang dr marketplace langsung
                                    kalo dari marketplace, user ngisi alamatnya di sini, kalo dari desain, alamatnya udah terisi otomatis -->
                                    <label class="delivery-address-label" for="alamat_lengkap">Alamat Lengkap</label>
                                    <textarea class="delivery-address-textarea" id="alamat_lengkap"
                                        placeholder="Tuliskan alamat detail seperti nama jalan, nomor rumah, RT/RW, dan kelurahan/kecamatan"></textarea>
                                </div>
                            </div>
                            <div class="delivery-address-checkbox-container">
                                <input class="delivery-address-checkbox" id="save_primary" type="checkbox" />
                                <label class="delivery-address-checkbox-label" for="save_primary">Simpan sebagai
                                    alamat utama</label>
                            </div>
                        </div>
                        <div class="cart-items">
                            <!-- Cart Item 1 -->
                            <div class="cart-item">
                                <div class="cart-item-image-container">
                                    <img class="cart-item-image" src="{{ asset('images/material/beton.jpg') }}"
                                        alt="Besi Beton">
                                </div>
                                <div class="cart-item-content">
                                    <div>
                                        <div class="cart-item-header">
                                            <h3 class="cart-item-title">Besi Beton 19mm</h3>
                                            <button class="cart-item-delete">
                                                <img src="{{ asset('images/icon/bin.png') }}" alt="Delete">
                                            </button>
                                        </div>
                                        <div class="cart-item-description">
                                            Perwira • 19mm • 27kg
                                        </div>
                                    </div>
                                    <div class="cart-item-footer">
                                        <span class="cart-item-price">Rp 320.000</span>
                                        <div class="cart-quantity">
                                            <button class="cart-quantity-btn">
                                                <span class="material-symbols-outlined" data-icon="remove">-</span>
                                            </button>
                                            <span class="cart-quantity-value">10</span>
                                            <button class="cart-quantity-btn">
                                                <span class="material-symbols-outlined" data-icon="add">+</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Cart Item 2 -->
                            <div class="cart-item">
                                <div class="cart-item-image-container">
                                    <img class="cart-item-image" src="{{ asset('images/material/hebel.jpeg') }}"
                                        alt="Bata Ringan">
                                </div>
                                <div class="cart-item-content">
                                    <div>
                                        <div class="cart-item-header">
                                            <h3 class="cart-item-title">Bata Ringan</h3>
                                            <button class="cart-item-delete">
                                                <img src="{{ asset('images/icon/bin.png') }}" alt="Delete">
                                            </button>
                                        </div>
                                        <div class="cart-item-description">
                                            Walbric • 60x30x10cm • 83pcs
                                        </div>
                                    </div>
                                    <div class="cart-item-footer">
                                        <span class="cart-item-price">Rp 740.000</span>
                                        <div class="cart-quantity">
                                            <button class="cart-quantity-btn">
                                                <span class="material-symbols-outlined" data-icon="remove">-</span>
                                            </button>
                                            <span class="cart-quantity-value">3</span>
                                            <button class="cart-quantity-btn">
                                                <span class="material-symbols-outlined" data-icon="add">+</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Cart Item 3 -->
                            <div class="cart-item">
                                <div class="cart-item-image-container">
                                    <img class="cart-item-image" src="{{ asset('images/material/granite.jpg') }}"
                                        alt="Granite">
                                </div>
                                <div class="cart-item-content">
                                    <div>
                                        <div class="cart-item-header">
                                            <h3 class="cart-item-title">Granite Tile</h3>
                                            <button class="cart-item-delete">
                                                <img src="{{ asset('images/icon/bin.png') }}" alt="Delete">
                                            </button>
                                        </div>
                                        <div class="cart-item-description">
                                            Niro • 60x60cm • 34kg
                                        </div>
                                    </div>
                                    <div class="cart-item-footer">
                                        <span class="cart-item-price">Rp 350.000</span>
                                        <div class="cart-quantity">
                                            <button class="cart-quantity-btn">
                                                <span class="material-symbols-outlined" data-icon="remove">-</span>
                                            </button>
                                            <span class="cart-quantity-value">3</span>
                                            <button class="cart-quantity-btn">
                                                <span class="material-symbols-outlined" data-icon="add">+</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Right Column: Order Summary (40%) -->
                    <aside class="cart-right">
                        <div class="cart-summary">
                            <h2 class="cart-summary-title">Ringkasan Belanja</h2>
                            <div class="cart-summary-items">
                                <div class="cart-summary-item">
                                    <div class="cart-summary-item-info">
                                        <span class="cart-summary-item-name">Besi Beton 19mm</span>
                                        <span class="cart-summary-item-detail">10 units x Rp 320.000</span>
                                    </div>
                                    <span class="cart-summary-item-price">Rp 3.200.000</span>
                                </div>
                                <div class="cart-summary-item">
                                    <div class="cart-summary-item-info">
                                        <span class="cart-summary-item-name">Bata Ringan</span>
                                        <span class="cart-summary-item-detail">3 units x Rp 740.000</span>
                                    </div>
                                    <span class="cart-summary-item-price">Rp 2.220.000</span>
                                </div>
                                <div class="cart-summary-item">
                                    <div class="cart-summary-item-info">
                                        <span class="cart-summary-item-name">Granite Tile</span>
                                        <span class="cart-summary-item-detail">3 units x Rp 350.000</span>
                                    </div>
                                    <span class="cart-summary-item-price">Rp 1.050.000</span>
                                </div>
                            </div>
                            <div class="cart-summary-totals">
                                <div class="cart-summary-total-row">
                                    <span class="cart-summary-total-label">Subtotal</span>
                                    <span class="cart-summary-total-value">Rp 6.470.000</span>
                                </div>
                                <div class="cart-summary-total-row">
                                    <span class="cart-summary-total-label">Biaya Layanan</span>
                                    <span class="cart-summary-total-value">Rp 25.000</span>
                                </div>
                                <div class="cart-summary-total-row">
                                    <span class="cart-summary-total-label">Ongkos Kirim</span>
                                    <span class="cart-summary-total-value free">Gratis</span>
                                </div>
                            </div>
                            <div class="cart-summary-grand-total">
                                <span class="cart-summary-grand-label">Total Harga</span>
                                <span class="cart-summary-grand-value">Rp 6.495.000</span>
                            </div>
                            <button class="cart-checkout-btn" id="checkoutBtn">
                                Konfirmasi &amp; Bayar
                                <img src="{{ asset('images/icon/chevron-right.png') }}" alt="Next Icon">
                            </button>
                            <div class="cart-secure-note">
                                <img src="{{ asset('images/icon/lock.png') }}" alt="Secure Icon"
                                    class="cart-secure-icon">
                                Secure Checkout Guarantee
                            </div>
                        </div>
                    </aside>
            </main>
        </section>
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
