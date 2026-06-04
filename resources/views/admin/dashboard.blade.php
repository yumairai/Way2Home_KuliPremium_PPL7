@extends('admin.admin_page')
@section('title')
    Admin - Dashboard
@endsection

@section('header')
    <h2>Dashboard Overview</h2>
    <p>Selamat datang, Admin. Pantau proyek yang sedang aktif dan data terkini.</p>
@endsection
@section('stats')
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <p class="stat-title">Total User</p>
        <h3 class="stat-value">{{ number_format($totalUsers, 0, ',', '.') }}</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">task_alt</span>
            </div>
        </div>
        <p class="stat-title">Proyek Selesai</p>
        <h3 class="stat-value">{{ number_format($completedProjects, 0, ',', '.') }}</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">home_repair_service</span>
            </div>
        </div>
        <p class="stat-title">Renovasi Aktif</p>
        <h3 class="stat-value">{{ number_format($activeRenovationProjects, 0, ',', '.') }}</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">add_business</span>
            </div>
        </div>
        <p class="stat-title">Pembangunan Rumah Aktif</p>
        <h3 class="stat-value">{{ number_format($activeBuildProjects, 0, ',', '.') }}</h3>
    </article>
    <article class="stat-card special">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(255, 255, 255, 0.2); color: #fff;">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        <p class="stat-title" style="color: rgba(235,245,255,0.9);">Total Revenue</p>
        <h3 class="stat-value" style="font-size:1.25rem;">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
    </article>
@endsection
@section('content')
    <div class="project-section-header">
        <div>
            <h3>Daftar Proyek Aktif</h3>
            <p>Monitoring status dan progres pengerjaan dari proyek bangun rumah dan renovasi yang sedang berjalan.</p>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="project-table">
            <thead>
                <tr>
                    <th>Judul Proyek</th>
                    <th>Nama Pemilik</th>
                    <th>Nama Mandor</th>
                    <th>Kategori Proyek</th>
                    <th>Status</th>
                    <th>Progress</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($activeProjects as $project)
                    <tr>
                        <td>
                            <div class="project-card">
                                <div class="project-thumb">
                                    <img src="{{ $project['thumbnail_src'] }}" alt="{{ $project['photo_alt'] }}" />
                                </div>
                                <div>
                                    <p class="project-title">{{ $project['title'] }}</p>
                                    <p class="project-id">ID: {{ $project['project_code'] }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="project-owner">{{ $project['owner_name'] }}</span>
                        </td>
                        <td>
                            <span class="project-mandor">{{ $project['mandor_name'] }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $project['category_class'] }}">{{ $project['category_label'] }}</span>
                        </td>
                        <td>
                            <div class="status-pill {{ $project['status_class'] }}">
                                <span class="status-dot"></span>
                                <span>{{ $project['status_label'] }}</span>
                            </div>
                        </td>
                        <td>
                            <div class="progress-track">
                                <div class="progress-fill {{ $project['progress_class'] }}"
                                    style="width:{{ $project['progress'] }}%;"></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6"
                            style="text-align:center; padding: 2rem 1.5rem; color: var(--color-on-surface-variant);">
                            Belum ada proyek aktif yang bisa ditampilkan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="project-footer">
        <p class="pagination-text">Menampilkan {{ $activeProjects->count() }} proyek dari data aktif yang tersedia.</p>
    </div>
@endsection
