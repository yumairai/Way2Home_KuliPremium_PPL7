@extends('mandor.mandor_main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mandor/mandor_dashboard.css') }}" />
@endpush
@section('content')
    <header class="dashboard-header">
        <h1 class="dashboard-title">Ringkasan Operasional</h1>
        <p class="dashboard-lead">
            Pantau projek anda dan kelola pengajuan renovasi. Dapatkan update real-time
            tentang status proyek dan permintaan klien untuk memastikan semua berjalan lancar.
        </p>
    </header>
    <section class="dashboard-summary-wrapper">
        <section class="dashboard-stats">
            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon-primary">
                    <span class="material-symbols-outlined">architecture</span>
                </div>
                <div class="dashboard-stat-copy">
                    <p class="dashboard-stat-label">Project Saat Ini</p>
                    <p class="dashboard-stat-value">{{ $activeProjectLabel }}</p>
                </div>
            </div>
            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon-secondary">
                    <span class="material-symbols-outlined">task_alt</span>
                </div>
                <div class="dashboard-stat-copy">
                    <p class="dashboard-stat-label">Project Diselesaikan</p>
                    <p class="dashboard-stat-value">{{ $completedProjects }}</p>
                </div>
            </div>
            <div class="dashboard-stat-card">
                <div class="dashboard-stat-icon dashboard-stat-icon-tertiary">
                    <span class="material-symbols-outlined">assignment_late</span>
                </div>
                <div class="dashboard-stat-copy">
                    <p class="dashboard-stat-label">Request Renovasi</p>
                    <p class="dashboard-stat-value">{{ $requestCount }}</p>
                </div>
            </div>
        </section>
        <div class="dashboard-activity-history">
            <div class="dashboard-activity-header">
                <h3 class="dashboard-activity-title">History Aktivitas</h3>
            </div>
            <div class="dashboard-activity-list dashboard-activity-list-collapsed" id="dashboard-activity-list">
                <div class="dashboard-activity-track">
                    @forelse($activityHistory as $activity)
                        <article
                            class="dashboard-activity-item{{ $loop->index >= 4 ? ' dashboard-activity-item-hidden' : '' }}">
                            <div class="dashboard-activity-connector"></div>
                            <div class="dashboard-activity-content">
                                <p class="dashboard-activity-title">
                                    {{ data_get($activity, 'title') ?: (is_string($activity) ? $activity : '') }}
                                </p>
                                <span class="dashboard-activity-timestamp">
                                    {{ data_get($activity, 'timestamp') }}
                                </span>
                            </div>
                        </article>
                    @empty
                        <div class="dashboard-activity-empty">
                            <p>Belum ada aktivitas</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <button class="dashboard-activity-expand-btn" id="dashboard-activity-expand-btn" type="button">Lihat
                keseluruhan</button>
        </div>
    </section>
    <section class="dashboard-list-section">
        <div class="dashboard-section-head">
            <div>
                <h2 class="dashboard-section-title">List Pengajuan Renovasi</h2>
                <div class="dashboard-section-underline"></div>
            </div>
        </div>
        <div class="dashboard-request-list">
            @forelse ($renovationRequests as $request)
                <div
                    class="dashboard-request-card {{ $loop->odd ? 'dashboard-request-card-primary' : 'dashboard-request-card-soft' }}">
                    <div class="dashboard-request-image-wrap">
                        <img src="{{ $request['photos'][0] ?? asset('images/aset/user-dummy.jpg') }}"
                            alt="Foto Pengaju Renovasi">
                    </div>
                    <div class="dashboard-request-meta">
                        <div>
                            <p class="dashboard-meta-label">Nama Pengaju</p>
                            <p class="dashboard-meta-value">{{ $request['applicant_name'] }}</p>
                        </div>
                        <div>
                            <p class="dashboard-meta-label">ID Pengajuan</p>
                            <p class="dashboard-meta-code">#{{ $request['id'] }}</p>
                        </div>
                        <div>
                            <p class="dashboard-meta-label">Estimasi Budget</p>
                            <p class="dashboard-meta-value">{{ $request['budget'] }}</p>
                        </div>
                        <div>
                            <p class="dashboard-meta-label">Nomor HP</p>
                            <p class="dashboard-meta-muted">{{ $request['phone'] }}</p>
                        </div>
                    </div>
                    <div class="dashboard-request-action">
                        <button class="dashboard-review-btn" type="button" data-review-open
                            data-request-id="{{ $request['id'] }}">
                            Review
                        </button>
                    </div>
                </div>
            @empty
                <div class="dashboard-request-card dashboard-request-card-soft">
                    <div class="dashboard-request-meta">
                        <div>
                            <p class="dashboard-meta-label">Info</p>
                            <p class="dashboard-meta-value">Belum ada request renovasi yang perlu direview saat ini.
                            </p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        @if ($renovationRequests->total() > 6)
            <div class="pagination-container" style="margin-top: 2rem; display: flex; justify-content: flex-end; align-items: center; width: 100%;">
                <div class="pagination">
                    {{-- Previous Page Link --}}
                    @if ($renovationRequests->onFirstPage())
                        <button class="pagination-button pagination-button-icon" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                    @else
                        <a href="{{ $renovationRequests->previousPageUrl() }}" class="pagination-button pagination-button-icon" style="text-decoration: none;">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </a>
                    @endif

                    {{-- Pagination Elements --}}
                    @foreach ($renovationRequests->getUrlRange(1, $renovationRequests->lastPage()) as $page => $url)
                        @if ($page == $renovationRequests->currentPage())
                            <button class="pagination-button pagination-button-active">{{ $page }}</button>
                        @else
                            <a href="{{ $url }}" class="pagination-button" style="text-decoration: none;">{{ $page }}</a>
                        @endif
                    @endforeach

                    {{-- Next Page Link --}}
                    @if ($renovationRequests->hasMorePages())
                        <a href="{{ $renovationRequests->nextPageUrl() }}" class="pagination-button pagination-button-icon" style="text-decoration: none;">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </a>
                    @else
                        <button class="pagination-button pagination-button-icon" disabled style="opacity: 0.5; cursor: not-allowed;">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </section>
    <div class="dashboard-review-modal" id="dashboard-review-modal" aria-hidden="true" hidden>
        <div class="dashboard-review-backdrop" data-review-close></div>
        <div class="dashboard-review-dialog" role="dialog" aria-modal="true" aria-labelledby="dashboard-review-title">
            <div class="dashboard-review-header">
                <div>
                    <h2 class="dashboard-review-title" id="dashboard-review-title">Review Permintaan Proyek</h2>
                    <p class="dashboard-review-subtitle">Detail pengajuan renovasi <strong
                            id="dashboard-review-request-id">-</strong> dari klien.</p>
                </div>
                <button class="dashboard-review-close-btn material-symbols-outlined" type="button" aria-label="Close modal"
                    data-review-close>close</button>
            </div>

            <div class="dashboard-review-body">
                <div class="dashboard-review-summary">
                    <div class="dashboard-review-summary-item">
                        <span class="dashboard-review-label">Nama Pemohon</span>
                        <div class="dashboard-review-inline">
                            <span class="material-symbols-outlined dashboard-review-icon">person</span>
                            <span class="dashboard-review-value" id="dashboard-review-applicant">-</span>
                        </div>
                    </div>
                    <div class="dashboard-review-summary-item">
                        <span class="dashboard-review-label">Estimasi Budget</span>
                        <div class="dashboard-review-inline">
                            <span class="material-symbols-outlined dashboard-review-icon">monetization_on</span>
                            <span class="dashboard-review-value dashboard-review-value-small"
                                id="dashboard-review-budget">-</span>
                        </div>
                    </div>
                    <div class="dashboard-review-summary-item">
                        <span class="dashboard-review-label">Lokasi Renovasi</span>
                        <div class="dashboard-review-inline">
                            <span class="material-symbols-outlined dashboard-review-icon">location_on</span>
                            <span class="dashboard-review-value dashboard-review-value-small"
                                id="dashboard-review-location">-</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Deskripsi Kerusakan</span>
                    <div class="dashboard-review-note">
                        <p id="dashboard-review-description">-</p>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Feedback Mandor</span>
                    <div class="dashboard-review-note dashboard-review-feedback-wrap">
                        <textarea class="dashboard-review-feedback-readonly" placeholder="Tulis feedback mandor untuk customer..."></textarea>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Riwayat Negosiasi Customer</span>
                    <div class="dashboard-review-note">
                        <div id="dashboard-review-negotiation-list"></div>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Balas Negosiasi</span>
                    <div class="dashboard-review-note dashboard-review-negotiation-compose">
                        <textarea class="dashboard-review-negotiation-message" placeholder="Tulis balasan negosiasi untuk customer..."></textarea>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Foto Kerusakan (<span
                            id="dashboard-review-photo-count">0</span>)</span>
                    <div class="dashboard-review-gallery" id="dashboard-review-gallery">
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Material Renovasi</span>
                    <div class="dashboard-review-material-wrap">
                        <div class="dashboard-review-material-panel dashboard-review-material-panel-left">
                            <label class="dashboard-review-material-search-label"
                                for="dashboard-review-material-search">Cari
                                Material</label>
                            <input class="dashboard-review-material-search" id="dashboard-review-material-search"
                                type="text" placeholder="Cari nama material..." autocomplete="off" />
                            <div class="dashboard-review-material-source-list" id="dashboard-review-material-source-list">
                            </div>
                        </div>
                        <div class="dashboard-review-material-panel dashboard-review-material-panel-right">
                            <p class="dashboard-review-material-selected-title">Material Dipilih</p>
                            <p class="dashboard-review-material-empty" id="dashboard-review-material-empty">Belum ada
                                material
                                ditambah</p>
                            <div class="dashboard-review-material-selected-list"
                                id="dashboard-review-material-selected-list">
                            </div>
                            <div class="dashboard-review-material-total">
                                <p class="dashboard-review-material-total-label">Total Harga Material</p>
                                <p class="dashboard-review-material-total-value" id="dashboard-review-material-total">
                                    Rp 0
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Biaya Renovasi</span>
                    <div class="dashboard-review-cost-wrap">
                        <label class="dashboard-review-cost-field" for="dashboard-review-cost">
                            <span class="dashboard-review-cost-prefix">Rp</span>
                            <input class="dashboard-review-cost-input" id="dashboard-review-cost" type="text"
                                inputmode="numeric" autocomplete="off" placeholder="Contoh: 12.500.000" />
                        </label>
                        <p class="dashboard-review-cost-hint">Masukkan total biaya renovasi.</p>
                    </div>
                </div>

                <div class="dashboard-review-actions">
                    <button class="dashboard-review-action-btn dashboard-review-action-btn-primary" type="button"
                        id="dashboard-review-negotiate-btn">
                        Kirim Negosiasi
                    </button>
                    <button class="dashboard-review-action-btn dashboard-review-action-btn-secondary" type="button"
                        data-tracking-url="{{ route('mandor.tracking') }}" id="dashboard-review-take-btn" disabled
                        aria-disabled="true">
                        <span class="dashboard-review-action-btn__text">Ambil Renovasi</span>
                        <span class="dashboard-review-action-btn__spinner" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.renovationRequestMap = @json($requestMap);
        window.renovationMaterialCatalog = @json($materialCatalog);
    </script>
@endsection
@push('scripts')
    <script src="{{ asset('js/mandor/review.js') }}"></script>
    <script src="{{ asset('js/mandor/activity-history.js') }}"></script>
@endpush
