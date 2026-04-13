<!DOCTYPE html>

<html class="light" lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Proyek Saya - Way2Home</title>
    <link rel="stylesheet" href="{{ asset('css/customer/proyek_user.css') }}">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
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
                <a href="#">Beranda</a>
                <a href="/rekomendasi/input">Desain</a>
                <a href="/material">Material</a>
                <a href="/renovasi">Renovasi</a>
            </div>
            <!-- user bisa klik dropdown -->
            <div class="nav-actions">
                <img alt="User profile avatar" class="profile-avatar" src="{{ asset('images/aset/user-dummy.jpg') }}" />
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
                        <button class="dropdown-logout-btn" onclick="logout()">
                            <span class="material-symbols-outlined">logout</span>
                            Logout
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    <div class="page-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar-nav">

            <div class="sidebar-section">
                <p class="sidebar-title">List Project</p>
                <div class="sidebar-menu">
                    {{-- Item Proyek 1 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/1') ? 'active' : '' }}"
                        href="{{ url('user/projects/1') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/1') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Modern Villa Kemang
                    </a>

                    {{-- Item Proyek 2 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/2') ? 'active' : '' }}"
                        href="{{ url('user/projects/2') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/2') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Modern Villa Bandung
                    </a>
                    {{-- Item Proyek 3 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/3') ? 'active' : '' }}"
                        href="{{ url('user/projects/3') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/3') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Modern Villa Surabaya
                    </a>
                    {{-- Item Proyek 4 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/4') ? 'active' : '' }}"
                        href="{{ url('user/projects/4') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/4') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Rumah Minimalist Bali
                    </a>
                    {{-- Item Proyek 5 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/5') ? 'active' : '' }}"
                        href="{{ url('user/projects/5') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/5') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Rumah Minimalist Jatinangor
                    </a>
                    {{-- nanti kan dari database, nah pake foreach, biar ga hardcoded 1 1 kayak gini semangat back end --}}
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Header Section -->
                <header class="page-header">
                    <div class="header-title">
                        <h1>Proyek Saya</h1>
                        <p>Kelola dan pantau progres pembangunan hunian impian Anda.</p>
                    </div>
                    <div class="header-badge">
                        <span class="badge">
                            <span class="material-symbols-outlined" style="">info</span>
                            4 Proyek
                        </span>
                    </div>
                </header>
                <!-- Project Grid (Asymmetric Layout) -->
                <div class="project-grid">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    <script src="{{ asset('js/dropdown.js') }}"></script>
    <script src="{{ asset('js/customer/payment.js') }}"></script>
</body>

</html>
