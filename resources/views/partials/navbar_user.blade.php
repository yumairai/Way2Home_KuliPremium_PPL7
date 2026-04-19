<nav class="glass-nav">
    <div class="nav-container">
        <!-- Brand -->
        <div class="brand">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
            <span class="brand-text">Way2Home</span>
        </div>
        <!-- link -->
        <div class="nav-links">
            <a href="/">Beranda</a>
            <a href="/recommendation">Desain</a>
            <a href="/material">Material</a>
            <a href="/renovation">Renovasi</a>
        </div>
        <!-- user bisa klik dropdown -->
        <div class="nav-actions">
            <div class="cart-icon" onclick="window.location.href='/material/cart'">
                <img src="{{ asset('images/icon/shopping-bag.png') }}" alt="">
                <span class="cart-badge" id="navCartBadge" style="display: none;">0</span>
            </div>
            <img alt="User profile avatar" class="profile-avatar" id="profileDropdown"
                src="{{ asset('images/aset/user-dummy.jpg') }}" />
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
                                <img alt="avatar" src="{{ asset('images/aset/user-dummy.jpg') }}" />
                            </div>
                            <div class="user-details">
                                <h3>Robby Azwan</h3>
                                <p>Customer</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Navigation Links -->
                <div class="dropdown-body">
                    <a href="/order" class="nav-link">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span>Pesanan Saya</span>
                    </a>
                    <a href="/project" class="nav-link">
                        <span class="material-symbols-outlined">house</span>
                        <span>Proyek Saya</span>
                    </a>
                    <a href="/renovation" class="nav-link">
                        <span class="material-symbols-outlined">construction</span>
                        <span>Renovasi Saya</span>
                    </a>
                    <a href="/profile" class="nav-link">
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
                        onclick="event.preventDefault(); if(confirm('Yakin ingin keluar?')) { document.getElementById('logout-form').submit(); }">
                        <span class="material-symbols-outlined">logout</span>
                        Logout
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
@push('scripts')
    <script src="{{ asset('js/dropdown.js') }}"></script>
    <script>
        // Fungsi global untuk update cart badge
        window.updateNavCartBadge = function() {
            const isLoggedIn = document.body.getAttribute('data-user-logged-in') === 'true';
            if (!isLoggedIn) return;

            fetch('/api/cart')
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
