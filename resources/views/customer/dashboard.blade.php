<!DOCTYPE html>

<html class="light" lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Way2Home</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

</head>

<body>
    <!-- NAVBAR -->
    <nav class="glass-nav">
        <div class="nav-container">
            <!-- Brand -->
            <div class="brand">
                <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                <span class="brand-text">Way2Home</span>
            </div>
            <!-- link -->
            <div class="nav-links">
                <a class="active" href="#">Beranda</a>
                <a href="/rekomendasi/input">Desain</a>
                <a href="/material">Material</a>
                <a href="/renovasi">Renovasi</a>
            </div>
            <!-- user bisa logout -->
            <div class="nav-actions">
                <button onclick="logout()" class="btn-nav primary">Logout</button>
            </div>
        </div>
    </nav>
    <!-- Main Content Canvas -->
    <main>
        <!-- Hero Section -->
        <section class="hero-section">
            <!-- Hero Content -->
            <div class="hero-content">
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    <span class="hero-badge-text">Platform Konstruksi Digital</span>
                </div>
                <h1 class="hero-title">
                    Bangun Hunian <br />
                    <span class="italic-gradient">Masa Depan</span> Anda.
                </h1>
                <p class="hero-description">
                    Way2Home adalah platform konstruksi digital terintegrasi yang dirancang untuk mempermudah proses
                    pembangunan hunian impian Anda di wilayah Jawa Barat. Kami menghubungkan Anda dengan pengadaan
                    material bangunan premium serta tenaga ahli profesional melalui sistem yang transparan dan efisien.
                </p>
                <!-- Action Hub -->
                <div class="action-buttons">
                    <a href="/material" class="btn-action primary">
                        Mulai Belanja
                        <img src="{{ asset('images/icon/shopping-cart.png') }}" alt="Shopping Cart">
                    </a>
                    <a href="/rekomendasi/input" class="btn-action secondary">
                        Rekomendasi Desain
                        <img src="{{ asset('images/icon/house.png') }}" alt="Rekomendasi Desain">
                    </a>
                    <a href="/material" class="btn-action outlined">
                        Renovasi
                        <img src="{{ asset('images/icon/renovation.png') }}" alt="Renovasi">
                    </a>
                </div>
            </div>
            <!-- Gambar Kanan -->
            <div class="hero-visual">
                <!-- Biar ada bg nya -->
                <div class="visual-background"></div>
                <!-- gambar 1 -->
                <div class="visual-image back">
                    <img alt="Modern architecture"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCMbK8UauEiIDTik98DtzjmaNyaTjNqS4LBR0tSHhRFFzy89fDk-qwTH4eCDFUZ3BsYohl4EW2ta5zd8qkt28Lq0EzyM3aK4iffhsHw4L3lXV7r-SJ9Vrb_9eQWt0jdiAkdPMjDovxOfH6U9PhWHokvHLyrKCee0JCBwZZzvc2VBpum1Cj6XG6-6asRGad3dtDZhFb40b8AxGUHJrhEnQ6jnG6o2UyhxDCu62-9iYzLTsF9hyf9wiAO2q3CkXOp_J0AYnYDAdtCtxA" />
                </div>
                <!-- gambar 2 -->
                <div class="visual-image front">
                    <img alt="Modern house detail"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCfmLEeisbpET-Lz2AiJaycDoH8dwnGAUcp3ghYim2R3p4K4NRu6do42oQTK4k3Eyz3RJDIfZ6woVUjFFrCOMY8xC4d8j-kBaw73vXuqJVg7ynuthNdbIbpv8fWOwj_OuAZBLPAlOZdTf7zOMmoJF8LL0AknDtnX1w-cFUcN9Fexiqy5A2eoa4n1osyRJilMalbrkjw31iz_THOyHHFIQ9NSglFgO8feRNOLuh_AJTSdXhSkmbz2PFjrpfE-1ZBDRaMSfJCtcZdaug" />
                </div>
                <!-- badge -->
                <div class="floating-badge">
                    <div class="badge-icon">
                        <img src="{{ asset('images/icon/verified.png') }}" alt="Verified Icon">
                    </div>
                    <div class="badge-content">
                        <h4>Jawa Barat</h4>
                        <p>Layanan Terintegrasi</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Trust & Efficiency Section (Bento Inspired) -->
        <section class="features-section">
            <div class="features-grid">
                <div class="feature-card low">
                    <img src="{{ asset('images/icon/bricks.png') }}" alt="Premium Material">
                    <h3>Material Bangunan Premium</h3>
                    <p>Akses langsung ke produsen material terbaik dengan standar
                        kualitas tinggi untuk daya tahan bangunan maksimal.</p>
                </div>
                <div class="feature-card medium">
                    <img src="{{ asset('images/icon/businessman.png') }}" alt="Professional Expert">
                    <h3>Tenaga Ahli Profesional</h3>
                    <p>Bekerja sama dengan arsitek dan kontraktor berlisensi yang
                        berpengalaman mewujudkan desain hunian kompleks.</p>
                </div>
                <div class="feature-card high">
                    <img src="{{ asset('images/icon/transparan.png') }}" alt="Transparency">
                    <h3>Sistem Transparan</h3>
                    <p>Lacak progres pembangunan Anda secara real-time dengan laporan
                        digital yang transparan dan akurat.</p>
                </div>
            </div>
        </section>
    </main>
    <!-- Footer Shell -->
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
    <script>
        // Cek apakah ada token di localStorage
        const token = localStorage.getItem('token');

        if (!token) {
            alert('Kamu belum login! Balik ke halaman login ya.');
            window.location.href = '/login';
        } else {
            document.getElementById('tokenDisplay').innerText = token;
        }

        function logout() {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    </script>
</body>

</html>
