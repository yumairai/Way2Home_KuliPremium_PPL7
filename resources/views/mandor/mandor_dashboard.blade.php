@extends('mandor.mandor_main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mandor/mandor_dashboard.css') }}" />
@endpush
@section('content')
    @php
        $renovationRequests = [
            [
                'id' => 'REV-001',
                'applicant_name' => 'Bapak Budi',
                'budget' => 'Rp 10.000.000',
                'phone' => '0812-3456-7890',
                'location' => 'Jl. Melati No. 45, Kebayoran Baru, Jakarta Selatan',
                'description' =>
                    'Atap bocor di area ruang tamu dan plafon sudah mulai berjamur. Kerusakan terlihat semakin parah saat hujan deras semalam. Kami membutuhkan pengecekan struktur rangka atap dan penggantian genteng serta pengecatan ulang plafon.',
                'photos' => [
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                ],
            ],
            [
                'id' => 'REV-002',
                'applicant_name' => 'Ibu Pertiwi',
                'budget' => 'Rp 12.000.000',
                'phone' => '0813-8877-2233',
                'location' => 'Jl. Cendana No. 12, Cimahi Tengah, Jawa Barat',
                'description' =>
                    'Dinding kamar retak memanjang dan ada rembesan air dari sisi samping rumah. Mohon dicek struktur dinding, plester ulang, dan pengecatan interior.',
                'photos' => [
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                ],
            ],
            [
                'id' => 'REV-003',
                'applicant_name' => 'Bapak Santoso',
                'budget' => 'Rp 50.000.000',
                'phone' => '0856-1122-3344',
                'location' => 'Jl. Kenanga No. 8, Setiabudi, Jakarta Selatan',
                'description' =>
                    'Renovasi dapur total untuk perluasan area dan perbaikan saluran air. Butuh pembongkaran kabinet lama, instalasi pipa baru, serta finishing keramik lantai.',
                'photos' => [
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                    asset('images/aset/user-dummy.jpg'),
                ],
            ],
            [
                'id' => 'REV-004',
                'applicant_name' => 'Bapak Fadhli',
                'budget' => 'Rp 40.000.000',
                'phone' => '0856-2222-1111',
                'location' => 'Jl. Jatinangor No. 8, Hegar, Sumedang ',
                'description' =>
                    'Renovasi dapur total untuk perluasan area dan perbaikan saluran air. Butuh pembongkaran kabinet lama, instalasi pipa baru, serta finishing keramik lantai.',
                'photos' => [asset('images/aset/user-dummy.jpg'), asset('images/aset/user-dummy.jpg')],
            ],
        ];

        $requestMap = collect($renovationRequests)->mapWithKeys(fn($request) => [$request['id'] => $request]);
        $activeProjects = '-';
        $completedProjects = 18; // data jumlah proyek yang sudah diselesaikan oleh suatu id mandor (mandor tertentu) nnti ambil dr db
        $requestCount = count($renovationRequests);
    @endphp

    <header class="dashboard-header">
        <h1 class="dashboard-title">Ringkasan Operasional</h1>
        <p class="dashboard-lead">
            Pantau projek anda dan kelola pengajuan renovasi. Dapatkan update real-time
            tentang status proyek dan permintaan klien untuk memastikan semua berjalan lancar.
        </p>
    </header>
    <section class="dashboard-stats">
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon dashboard-stat-icon-primary">
                <span class="material-symbols-outlined">architecture</span>
            </div>
            <div class="dashboard-stat-copy">
                <p class="dashboard-stat-label">Project Saat Ini</p>
                <p class="dashboard-stat-value">{{ $activeProjects }}</p>
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
    <section class="dashboard-list-section">
        <div class="dashboard-section-head">
            <div>
                <h2 class="dashboard-section-title">List Pengajuan Renovasi</h2>
                <div class="dashboard-section-underline"></div>
            </div>
        </div>
        <div class="dashboard-request-list">
            @foreach ($renovationRequests as $request)
                <div
                    class="dashboard-request-card {{ $loop->odd ? 'dashboard-request-card-primary' : 'dashboard-request-card-soft' }}">
                    <div class="dashboard-request-image-wrap">
                        <img src="{{ asset('images/aset/user-dummy.jpg') }}" alt="Foto Pengaju Renovasi">
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
            @endforeach
        </div>
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
                    <span class="dashboard-review-label">Foto Kerusakan (<span
                            id="dashboard-review-photo-count">0</span>)</span>
                    <div class="dashboard-review-gallery" id="dashboard-review-gallery">
                    </div>
                </div>
                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Feedback Mandor</span>
                    <div class="dashboard-review-note">
                        <textarea class="dashboard-review-feedback"
                            placeholder="Tulis feedback dan material yang dibutuhkan secara lengkap untuk renovasi klien..."></textarea>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Biaya Renovasi</span>
                    <div class="dashboard-review-cost-wrap">
                        <label class="dashboard-review-cost-field" for="dashboard-review-cost">
                            <span class="dashboard-review-cost-prefix">Rp</span>
                            <input class="dashboard-review-cost-input" id="dashboard-review-cost" type="number"
                                inputmode="numeric" min="0" step="1000" placeholder="Contoh: 12500000" />
                        </label>
                        <p class="dashboard-review-cost-hint">Masukkan total biaya renovasi.</p>
                    </div>
                </div>

                <div class="dashboard-review-actions">
                    <button class="dashboard-review-action-btn dashboard-review-action-btn-primary" type="button"
                        data-tracking-url="{{ route('mandor.tracking') }}" id="dashboard-review-take-btn">
                        Ambil Renovasi
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.renovationRequestMap = @json($requestMap);
    </script>
@endsection
@push('scripts')
    <script src="{{ asset('js/mandor/review.js') }}"></script>
@endpush
