<nav class="glass-nav">
    <div class="nav-container">
        <!-- Brand -->
        <div class="brand">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
            <span class="brand-text">Way2Home</span>
        </div>
        <button class="nav-menu-toggle" type="button" aria-label="Buka navigasi" aria-controls="mobile-nav-drawer"
            aria-expanded="false" data-nav-drawer-toggle>
            <span class="material-symbols-outlined">menu</span>
        </button>
        <!-- link -->
        <div class="nav-links">
            <a href="/">Beranda</a>
            <a href="/recommendation">Desain</a>
            <a href="/material">Material</a>
            <a href="/renovation">Renovasi</a>
        </div>
        <!-- Aksi buat user blm login/masuk -->
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-nav secondary">Login</a>
            <a href="{{ route('register') }}" class="btn-nav primary">Daftar</a>
        </div>
    </div>
</nav>
<div class="nav-drawer-backdrop" data-nav-drawer-backdrop></div>
<aside class="nav-drawer" id="mobile-nav-drawer" aria-hidden="true" aria-label="Navigasi utama" data-nav-drawer>
    <div class="nav-drawer-header">
        <div class="brand">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
            <span class="brand-text">Way2Home</span>
        </div>
        <button class="nav-drawer-close" type="button" aria-label="Tutup navigasi" data-nav-drawer-close>
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="nav-drawer-body">
        <p class="nav-drawer-title">Navigasi</p>
        <div class="nav-links">
            <a href="/">Beranda</a>
            <a href="/recommendation">Desain</a>
            <a href="/material">Material</a>
            <a href="/renovation">Renovasi</a>
        </div>
    </div>
</aside>
@push('scripts')
    <script src="{{ asset('js/navbar-drawer.js') }}"></script>
@endpush
