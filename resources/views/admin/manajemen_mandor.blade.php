@extends('admin.admin_page')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/mandor_management.css') }}">
@endpush
@section('title')
    Admin - Manajemen Mandor
@endsection
@section('header')
    <h2> Manajemen Mandor</h2>
    <p> Kelola sumber daya lapangan Anda. Berikan
        penugasan proyek kepada mandor yang tersedia untuk efisiensi konstruksi maksimal.</p>
@endsection
@section('stats')
    <!-- Stats Overview (Asymmetric Layout) -->
    <div class="mandor-stat-card">
        <p class="mandor-stat-label">Total Mandor</p>
        <p class="mandor-stat-value">{{ $stats['total'] }}</p>
    </div>
    <div class="mandor-stat-card mandor-stat-available">
        <p class="mandor-stat-label">Tersedia</p>
        <p class="mandor-stat-value">{{ $stats['available'] }}</p>
    </div>
    <div class="mandor-stat-card mandor-stat-busy">
        <p class="mandor-stat-label">Sedang Bertugas</p>
        <p class="mandor-stat-value">{{ $stats['busy'] }}</p>
    </div>
@endsection
@section('content')
    <div class="mandor-container">
        <div class="mandor-list-section">
            <div class="mandor-list-header">
                <h3 class="mandor-list-title">Daftar Mandor Lapangan</h3>
                <div class="mandor-search">
                    <span class="material-symbols-outlined mandor-search-icon">search</span>
                    <input class="mandor-search-input" placeholder="Cari mandor atau proyek..." type="text"
                        id="searchInput" />
                </div>
            </div>

            <div class="mandor-entries">
                @forelse($mandors as $mandor)
                    @php
                        $isBusy = $mandor->proyekAktif !== null;
                    @endphp
                    <div class="mandor-entry {{ $isBusy ? 'busy' : '' }}" data-name="{{ strtolower($mandor->user->name) }}">
                        <div class="mandor-entry-info">
                            <div class="mandor-entry-avatar-container">
                                <img class="mandor-entry-avatar"
                                    src="{{ $mandor->path_foto_profil ? asset('storage/' . $mandor->path_foto_profil) : asset('images/default-avatar.png') }}"
                                    alt="{{ $mandor->user->name }}" />
                                <span class="mandor-entry-status-dot"></span>
                            </div>
                            <div class="mandor-entry-details">
                                <h4>{{ $mandor->user->name }}</h4>
                                <p>{{ $mandor->user->email }}</p>
                            </div>
                        </div>

                        <div class="mandor-entry-status">
                            <span class="mandor-status {{ $isBusy ? 'busy' : 'available' }}">
                                {{ $isBusy ? 'Busy' : 'Available' }}
                            </span>
                        </div>

                        <div class="mandor-entry-actions">
                            <div class="mandor-entry-project">
                                <p class="mandor-entry-project-label">Proyek Saat Ini</p>
                                <p class="mandor-entry-project-name">
                                    {{ $mandor->proyekAktif ? $mandor->proyekAktif->jenis_proyek : '-' }}
                                </p>
                            </div>

                            @if (!$isBusy)
                                <button class="mandor-assign-btn" data-mandor-id="{{ $mandor->id }}"
                                    onclick="openDocModal(this)">
                                    Assign Proyek
                                </button>
                            @else
                                <button class="mandor-assign-btn disabled" disabled>
                                    Assign Proyek
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="padding:1rem;color:gray;">Belum ada data mandor.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Modal --}}
    <div id="list-proyek-modal" class="modal-overlay" style="display:none;">
        <div class="modal-container">
            <div class="modal-close-wrapper">
                <button class="modal-close-btn" onclick="closeDocModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="modal-content">
                <div class="proyek-sidebar">
                    <h3 class="list-title">LIST PROYEK</h3>
                    <div class="proyek-list">
                        @forelse($proyeksAvailable as $proyek)
                            @php
                                $desain = $proyek->detailBangun?->desainRumah;
                            @endphp
                            <button class="proyek-item" data-proyek-id="{{ $proyek->id }}">
                                <div class="project-card">
                                    <div class="project-thumb">
                                        <img src="{{ $desain ? asset($desain->path_gambar_desain) : asset('images/project-placeholder.png') }}"
                                            alt="{{ $desain?->tipe_rumah ?? $proyek->jenis_proyek }}" />
                                    </div>
                                    <div>
                                        <p class="project-title">{{ $desain?->tipe_rumah ?? $proyek->jenis_proyek }}</p>
                                        <div class="title-wrapper">
                                            <p class="project-id"><strong>ID:</strong> {{ $proyek->id }}</p>
                                            <p class="project-status"><strong>Alamat:</strong> {{ $proyek->alamat_proyek }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        @empty
                            <p style="padding:1rem;color:gray;">Tidak ada proyek yang menunggu mandor.</p>
                        @endforelse
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="modal-footer-buttons">
                    <button class="modal-btn modal-btn-submit">Assign Mandor</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/admin/assign_mandor.js') }}"></script>
@endpush
