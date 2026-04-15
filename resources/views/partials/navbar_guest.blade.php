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
        <!-- Aksi buat user blm login/masuk -->
        <div class="nav-actions">
            <a href="{{ route('login') }}" class="btn-nav secondary">Login</a>
            <a href="{{ route('register') }}" class="btn-nav primary">Daftar</a>
        </div>
    </div>
</nav>
