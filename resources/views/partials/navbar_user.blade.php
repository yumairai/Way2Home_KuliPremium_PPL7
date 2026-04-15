<nav class="glass-nav">
    <div class="nav-container">
        <!-- Brand -->
        <div class="brand">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
            <span class="brand-text">Way2Home</span>
        </div>
        <!-- link -->
        <div class="nav-links">
            <a href="#">Beranda</a>
            <a href="/rekomendasi/input">Desain</a>
            <a href="/material">Material</a>
            <a href="/renovasi">Renovasi</a>
        </div>
        <!-- user bisa klik dropdown -->
        <div class="nav-actions">
            <div class="cart-icon" onclick="window.location.href='/material/cart'">
                <img src="{{ asset('images/icon/shopping-bag.png') }}" alt="">
                <span class="cart-badge">3</span>
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
                    <a href="/user/orders" class="nav-link">
                        <span class="material-symbols-outlined">shopping_cart</span>
                        <span>Pesanan Saya</span>
                    </a>
                    <a href="/user/projects" class="nav-link">
                        <span class="material-symbols-outlined">construction</span>
                        <span>Proyek Saya</span>
                    </a>
                    <a href="/user/profile" class="nav-link">
                        <span class="material-symbols-outlined">person_edit</span>
                        <span>Edit Profile</span>
                    </a>
                </div>

                <div class="separator"></div>

                <!-- Footer / Logout -->
                <div class="dropdown-footer">
                    <button class="dropdown-logout-btn" onclick="AuthApp.logout()">
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
@endpush
