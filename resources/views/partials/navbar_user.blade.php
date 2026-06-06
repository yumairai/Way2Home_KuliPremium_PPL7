<nav class="glass-nav">
    <div class="nav-container">
        <!-- Brand -->
        <a href="/" class="brand">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
            <span class="brand-text">Way2Home</span>
        </a>
        <button class="nav-menu-toggle" type="button" aria-label="Buka navigasi" aria-controls="mobile-nav-drawer"
            aria-expanded="false" data-nav-drawer-toggle>
            <span class="material-symbols-outlined">menu</span>
        </button>
        <!-- link -->
        <div class="nav-links">
            <a href="/">Beranda</a>
            <a href="/recommendation">Desain</a>
            <a href="/material">Material</a>
            <a
                href="{{ Auth::user()?->role === 'mandor' ? route('mandor.dashboard') : route('customer.renovation') }}">Renovasi</a>
        </div>
        <!-- user bisa klik dropdown -->
        <div class="nav-actions">
            <div class="cart-icon" onclick="window.location.href='/material/cart'">
                <img src="{{ asset('images/icon/shopping-bag.png') }}" alt="">
                <span class="cart-badge" id="navCartBadge" style="display: none;">0</span>
            </div>
            <img alt="User profile avatar" class="profile-avatar" id="profileDropdown"
                src="{{ Auth::user()?->avatar ?? asset('images/aset/avatar.jpg') }}" />
            <!-- DROPDOWN PROFILE !-->
            <div class="profile-dropdown">
                <!-- Header dengan Background Foto (Sesuai request sebelumnya) -->
                <div class="dropdown-header">
                    <div class="header-background">
                        <img alt="Background" src="{{ asset('images/aset/construction.jpg') }}" />
                    </div>
                    <div class="header-content">
                        <div class="user-info">
                            <div class="user-avatar">
                                <img alt="avatar"
                                    src="{{ Auth::user()?->avatar ?? asset('images/aset/avatar.jpg') }}" />
                            </div>
                            <div class="user-details">
                                <h3>{{ auth()->user()?->name }}</h3>
                                <p>Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Links -->
                <div class="dropdown-body">
                    <a href="{{ route('customer.order') }}" class="nav-link">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span>Pesanan Saya</span>
                    </a>
                    <a href="{{ route('proyek.index') }}" class="nav-link">
                        <span class="material-symbols-outlined">house</span>
                        <span>Proyek Saya</span>
                    </a>
                    <a href="{{ Auth::user()?->role === 'mandor' ? route('mandor.dashboard') : route('customer.renovation') }}"
                        class="nav-link">
                        <span class="material-symbols-outlined">construction</span>
                        <span>Renovasi Saya</span>
                    </a>
                    <a href="{{ route('customer.profile') }}" class="nav-link">
                        <span class="material-symbols-outlined">person_edit</span>
                        <span>Edit Profile</span>
                    </a>
                </div>

                <div class="separator"></div>

                <!-- Footer / Logout -->
                <div class="dropdown-footer">
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>

                    <button type="button" class="dropdown-logout-btn"
                        onclick="window.W2HLogout.submit('logout-form', 'Yakin ingin keluar?')">
                        <span class="material-symbols-outlined">logout</span>
                        Logout
                    </button>
                </div>
            </div>
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
        <div class="nav-drawer-user-card">
            <div class="nav-drawer-user-avatar">
                <img alt="avatar" src="{{ Auth::user()?->avatar ?? asset('images/aset/avatar.jpg') }}" />
            </div>
            <div class="nav-drawer-user-meta">
                <p class="nav-drawer-user-label">Akun Anda</p>
                <h3 class="nav-drawer-user-name">{{ auth()->user()?->name }}</h3>
                <p class="nav-drawer-user-role">
                    <span class="material-symbols-outlined">person</span>
                    <span>Profil {{ ucfirst(auth()->user()?->role ?? 'user') }}</span>
                </p>
            </div>
        </div>

        <p class="nav-drawer-title">Navigasi</p>
        <div class="nav-links">
            <a href="/">Beranda</a>
            <a href="/recommendation">Desain</a>
            <a href="/material">Material</a>
            <a
                href="{{ Auth::user()?->role === 'mandor' ? route('mandor.dashboard') : route('customer.renovation') }}">Renovasi</a>
        </div>

        <div class="nav-drawer-actions nav-drawer-user-actions">
            <p class="nav-drawer-actions-title">Akun</p>
            <div class="nav-drawer-user-grid">
                <a href="/material/cart" class="btn-nav secondary nav-drawer-action-btn nav-drawer-action-secondary">
                    <span class="material-symbols-outlined">shopping_bag</span>
                    Keranjang
                </a>
                <a href="{{ route('customer.order') }}"
                    class="btn-nav secondary nav-drawer-action-btn nav-drawer-action-secondary">
                    <span class="material-symbols-outlined">shopping_cart</span>
                    Pesanan
                </a>
                <a href="{{ route('proyek.index') }}"
                    class="btn-nav secondary nav-drawer-action-btn nav-drawer-action-secondary">
                    <span class="material-symbols-outlined">home_work</span>
                    Proyek
                </a>
                <a href="{{ route('customer.profile') }}"
                    class="btn-nav secondary nav-drawer-action-btn nav-drawer-action-secondary">
                    <span class="material-symbols-outlined">person_edit</span>
                    Profil
                </a>
            </div>
            <button type="button" class="btn-nav primary nav-drawer-action-btn nav-drawer-action-primary"
                onclick="window.W2HLogout.submit('logout-form', 'Yakin ingin keluar?')">
                <span class="material-symbols-outlined">logout</span>
                Logout
            </button>
        </div>
    </div>
</aside>
@push('scripts')
    <script src="{{ asset('js/navbar-drawer.js') }}"></script>
    <script src="{{ asset('js/logout.js') }}"></script>
    <script src="{{ asset('js/dropdown.js') }}"></script>
    <script>
        // Fungsi global untuk update cart badge
        window.updateNavCartBadge = function() {
            const isLoggedIn = document.body.getAttribute('data-user-logged-in') === 'true';
            if (!isLoggedIn) return;

            fetch('/cart', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    const count = data.data ? data.data.reduce((sum, item) => sum + item.jumlah, 0) : 0;
                    const badge = document.getElementById('navCartBadge');
                    if (!badge) return;

                    if (count >= 1) {
                        badge.textContent = count;
                        badge.style.display = 'inline-flex';
                    } else {
                        badge.textContent = '0';
                        badge.style.display = 'none';
                    }
                })
                .catch(err => console.error('Error fetching cart:', err));
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.updateNavCartBadge();
        });
    </script>
@endpush
