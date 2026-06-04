<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/customer/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui/dialog.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ui/verify-notice.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
</head>

<body>
    <div class="verify-notice-page">
        <div class="dashboard-backdrop" aria-hidden="true">
            <div class="dashboard-dim"></div>

            <section class="dashboard-page">
                <section class="hero-section">
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
                            Way2Home adalah platform konstruksi digital terintegrasi yang dirancang untuk mempermudah
                            proses pembangunan hunian impian Anda di wilayah Jawa Barat.
                        </p>
                        <div class="action-buttons">
                            <span class="btn-action primary">Mulai Belanja</span>
                            <span class="btn-action secondary">Rekomendasi Desain</span>
                            <span class="btn-action outlined">Renovasi</span>
                        </div>
                    </div>

                    <div class="hero-visual">
                        <div class="visual-background"></div>
                        <div class="visual-image back">
                            <img alt="Modern architecture"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCMbK8UauEiIDTik98DtzjmaNyaTjNqS4LBR0tSHhRFFzy89fDk-qwTH4eCDFUZ3BsYohl4EW2ta5zd8qkt28Lq0EzyM3aK4iffhsHw4L3lXV7r-SJ9Vrb_9eQWt0jdiAkdPMjDovxOfH6U9PhWHokvHLyrKCee0JCBwZZzvc2VBpum1Cj6XG6-6asRGad3dtDZhFb40b8AxGUHJrhEnQ6jnG6o2UyhxDCu62-9iYzLTsF9hyf9wiAO2q3CkXOp_J0AYnYDAdtCtxA" />
                        </div>
                        <div class="visual-image front">
                            <img alt="Modern house detail"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCfmLEeisbpET-Lz2AiJaycDoH8dwnGAUcp3ghYim2R3p4K4NRu6do42oQTK4k3Eyz3RJDIfZ6woVUjFFrCOMY8xC4d8j-kBaw73vXuqJVg7ynuthNdbIbpv8fWOwj_OuAZBLPAlOZdTf7zOMmoJF8LL0AknDtnX1w-cFUcN9Fexiqy5A2eoa4n1osyRJilMalbrkjw31iz_THOyHHFIQ9NSglFgO8feRNOLuh_AJTSdXhSkmbz2PFjrpfE-1ZBDRaMSfJCtcZdaug" />
                        </div>
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

                <section class="features-section">
                    <div class="features-grid">
                        <div class="feature-card low">
                            <img src="{{ asset('images/icon/bricks.png') }}" alt="Premium Material">
                            <h3>Material Bangunan Premium</h3>
                            <p>Akses langsung ke produsen material terbaik dengan standar kualitas tinggi.</p>
                        </div>
                        <div class="feature-card medium">
                            <img src="{{ asset('images/icon/businessman.png') }}" alt="Professional Expert">
                            <h3>Tenaga Ahli Profesional</h3>
                            <p>Bekerja sama dengan arsitek dan kontraktor berlisensi yang berpengalaman.</p>
                        </div>
                        <div class="feature-card high">
                            <img src="{{ asset('images/icon/transparan.png') }}" alt="Transparency">
                            <h3>Sistem Transparan</h3>
                            <p>Lacak progres pembangunan Anda secara real-time dengan laporan digital.</p>
                        </div>
                    </div>
                </section>
            </section>
        </div>

        <div class="card-shell">
            <div class="card">
                <h2>Verifikasi Email</h2>

                <p>
                    Kami telah mengirimkan link verifikasi ke:
                    <br>
                    <span class="email">{{ auth()->user()->email }}</span>
                </p>

                <p>
                    Silakan cek inbox atau folder spam, lalu klik link untuk mengaktifkan akun kamu.
                </p>

                <div class="button">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit">
                            Kirim Ulang Email
                        </button>
                    </form>
                </div>

                <div class="button-logout">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn-logout">
                            Keluar
                        </button>
                    </form>
                </div>


                <p class="helper-text">
                    Jika tidak menerima email, tunggu beberapa detik lalu klik tombol di atas.
                </p>
            </div>
        </div>
    </div>

    @include('partials.w2h-dialog')
    @include('partials.w2h-flash')
    <script src="{{ asset('js/ui/dialog.js') }}"></script>
    <script src="{{ asset('js/ui/dropdown.js') }}"></script>

</body>

</html>
