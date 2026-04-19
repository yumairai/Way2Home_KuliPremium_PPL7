<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('css/admin/admin_page.css') }}" rel="stylesheet" />
    <link href="{{ asset('images/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    @stack('styles')
</head>

<body class="admin-page">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <img src="{{ asset('images/logo-w2h.png') }}" alt="Way2Home Logo">
            </div>
            <div>
                <h1 class="brand-title">Way2Home</h1>
                <p class="brand-subtitle">Construction Admin</p>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a class="sidebar-link {{ Route::is('admin.dashboard') ? 'active' : '' }}"
                href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined filled">dashboard</span>
                <span>Dashboard</span>
            </a>

            <a class="sidebar-link {{ Route::is('admin.verifikasi') ? 'active' : '' }}"
                href="{{ route('admin.verifikasi') }}">
                <span class="material-symbols-outlined filled">verified_user</span>
                <span>Verifikasi Dokumen</span>
            </a>

            <a class="sidebar-link {{ Route::is('admin.kelola_material') ? 'active' : '' }}"
                href="{{ route('admin.kelola_material') }}">
                <span class="material-symbols-outlined filled">construction</span>
                <span>Kelola Material</span>
            </a>

            <a class="sidebar-link {{ Route::is('admin.manajemen_mandor') ? 'active' : '' }}"
                href="{{ route('admin.manajemen_mandor') }}">
                <span class="material-symbols-outlined filled">engineering</span>
                <span>Manajemen Mandor</span>
            </a>

            <a class="sidebar-link {{ Route::is('admin.monitor_proyek') ? 'active' : '' }}"
                href="{{ route('admin.monitor_proyek') }}">
                <span class="material-symbols-outlined filled">monitoring</span>
                <span>Project Monitor</span>
            </a>
        </nav>
        <div class="sidebar-footer">
            <div class="profile-card">
                <img data-alt="professional male construction executive profile picture in clean formal attire"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuClqsZ14hBxsAXvMJcaikk4Ao-qbyDjaKB2OORFVvsvl1wh40BwE-GE1px1La-KsiFrMsiz3_C5Z0a4Sh03YVt6q2BgLhqW16dkXLxhfZ8oIH2p4CNefhlSW9-MM2Yk41gOlMBjzS6HNHCixpuMTTIoL68LQOLvPr3bPf--ERSaK4HPmcbmmWpgZ8HtpqwV5qQ6GA7Hy7jm9-xFmgKT0TehJBCRVf9mLozyRbZE0iMgWHOUDP2CnvZXx5-x_2k3UqOy4mJXK0jBkZI" />
                <div class="profile-meta">
                    <p class="profile-name">Admin Utama</p>
                    <p class="profile-role">Administrator</p>
                </div>
            </div>
        </div>
    </aside>
    <header class="topbar">
        <div class="topbar-actions">
            <button class="icon-button" type="button">
                <span class="material-symbols-outlined">notifications</span>
            </button>
            <div style="width:1px;height:1.5rem;background:rgba(194,198,213,0.3);"></div>
            <div class="user-summary">
                <p class="user-name">Admin Utama</p>
                <p class="user-role">System Access</p>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button class="logout-button" type="button" onclick="document.getElementById('logout-form').submit()">
                <span>Keluar</span>
                <span class="material-symbols-outlined">logout</span>
            </button>
        </div>
    </header>
    <main class="main-content">
        <header class="page-header">
            @yield('header')
        </header>
        <div class="stats-grid">
            <article class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                </div>
                <p class="stat-title">Total User</p>
                <h3 class="stat-value">1,284</h3>
            </article>
            <article class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                        <span class="material-symbols-outlined">task_alt</span>
                    </div>
                </div>
                <p class="stat-title">Proyek Selesai</p>
                <h3 class="stat-value">312</h3>
            </article>
            <article class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                        <span class="material-symbols-outlined">home_repair_service</span>
                    </div>
                </div>
                <p class="stat-title">Permintaan Renovasi</p>
                <h3 class="stat-value">45</h3>
            </article>
            <article class="stat-card">
                <div class="stat-head">
                    <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                        <span class="material-symbols-outlined">add_business</span>
                    </div>
                </div>
                <p class="stat-title">Pengajuan Proyek</p>
                <h3 class="stat-value">18</h3>
            </article>
            <article class="stat-card special">
                <div class="stat-head">
                    <div class="stat-icon" style="background: rgba(255, 255, 255, 0.2); color: #fff;">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                </div>
                <p class="stat-title" style="color: rgba(235,245,255,0.9);">Total Revenue</p>
                <h3 class="stat-value" style="font-size:1.25rem;">Rp 8.42B</h3>
            </article>
        </div>
        <section class="project-section">
            @yield('content')
        </section>
    </main>
    @stack('scripts')
</body>

</html>