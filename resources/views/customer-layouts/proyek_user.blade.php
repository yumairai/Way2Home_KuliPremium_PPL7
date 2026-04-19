@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/proyek_user.css') }}">
@endpush
@section('content')
    @php
        $projects = [
            [
                'id' => 1,
                'name' => 'Modern Villa Kemang',
                'location' => 'Jakarta Selatan, DKI Jakarta',
                'design_name' => 'The Minimalist V2',
                'budget' => 'Rp 2.450.000.000',
                'duration' => '12 Bulan',
                'image' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuA4ksDLDJyMvJlK1qi7FzNco1Xyp9nYVZJ7PcWeGMkIsPgGuo_SBxRjgQoXakT0ylXK-dclr4jD34smKE5vmSiIFTPD6oX93Z83QPeGqhn-x7-GHUG78hQW7ONCkPGcMtN9qyte3VI2roScfGgrqc0txkZkdMGlQwMN5P3O9Sfh2SssyYnaFZb_O1HNABQZO5vWNE3i8zsIhUoNLdh4uZ66a2luxvzxbgdegN0lso2FMcTL5fCx4B9r8mZ3gsV0gnad4KLn02SUj5w',
                'status' => ['class' => 'revision', 'label' => 'Perlu Revisi', 'icon' => 'error'],
                'information' => [
                    'container_class' => 'revision',
                    'icon_class' => '',
                    'icon' => 'rate_review',
                    'title' => 'Dokumen Ditolak',
                    'text' =>
                        'Scan dokumen IMB yang Anda lampirkan memiliki resolusi rendah dan tidak terbaca. Mohon unggah kembali dokumen dengan kualitas yang lebih baik agar dapat segera kami verifikasi.',
                    'show_upload' => true,
                ],
                'flow' => [
                    'sudah_bayar' => false,
                    'status_dokumen' => 'rejected',
                    'is_mandor' => false,
                    'is_proyek' => false,
                ],
                'milestones' => [
                    [
                        'label' => 'Pengajuan',
                        'meta' => '12 Okt 2023',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => true,
                    ],
                    [
                        'label' => 'Verifikasi Dokumen',
                        'meta' => 'Butuh Revisi',
                        'meta_type' => 'status',
                        'meta_class' => 'revision',
                        'icon_class' => 'in-progress',
                        'icon' => 'history',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pembayaran DP',
                        'meta' => 'Menunggu Verifikasi',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'payments',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pengalokasian Mandor',
                        'meta' => 'Menunggu',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'person',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Selesai',
                        'meta' => 'Progress dimulai',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'start',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                ],
                'installments' => [],
            ],
            [
                'id' => 2,
                'name' => 'Modern Villa Bandung',
                'location' => 'Antapani, Kota Bandung',
                'design_name' => 'The Modern',
                'budget' => 'Rp 1.500.000.000',
                'duration' => '9 Bulan',
                'image' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuA4ksDLDJyMvJlK1qi7FzNco1Xyp9nYVZJ7PcWeGMkIsPgGuo_SBxRjgQoXakT0ylXK-dclr4jD34smKE5vmSiIFTPD6oX93Z83QPeGqhn-x7-GHUG78hQW7ONCkPGcMtN9qyte3VI2roScfGgrqc0txkZkdMGlQwMN5P3O9Sfh2SssyYnaFZb_O1HNABQZO5vWNE3i8zsIhUoNLdh4uZ66a2luxvzxbgdegN0lso2FMcTL5fCx4B9r8mZ3gsV0gnad4KLn02SUj5w',
                'status' => ['class' => 'pending', 'label' => 'Menunggu', 'icon' => 'error'],
                'information' => [
                    'container_class' => 'pending',
                    'icon_class' => 'pending',
                    'icon' => 'rate_review',
                    'title' => 'Dokumen Sedang Direview',
                    'text' => 'Mohon tunggu 1x24 jam.',
                    'show_upload' => false,
                ],
                'flow' => [
                    'sudah_bayar' => false,
                    'status_dokumen' => 'pending',
                    'is_mandor' => false,
                    'is_proyek' => false,
                ],
                'milestones' => [
                    [
                        'label' => 'Pengajuan',
                        'meta' => '12 Okt 2023',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => true,
                    ],
                    [
                        'label' => 'Verifikasi Dokumen',
                        'meta' => 'Menunggu review',
                        'meta_type' => 'status',
                        'meta_class' => 'pending',
                        'icon_class' => 'in-progress',
                        'icon' => 'history',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pembayaran DP',
                        'meta' => 'Menunggu Verifikasi',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'payments',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pengalokasian Mandor',
                        'meta' => 'Menunggu',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'person',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Selesai',
                        'meta' => 'Progress dimulai',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'start',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                ],
                'installments' => [],
            ],
            [
                'id' => 3,
                'name' => 'Modern Villa Surabaya',
                'location' => 'Tunjungan Kota, Kota Surabaya',
                'design_name' => 'The Modern Tunjungan',
                'budget' => 'Rp 2.000.000.000',
                'duration' => '15 Bulan',
                'image' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuA4ksDLDJyMvJlK1qi7FzNco1Xyp9nYVZJ7PcWeGMkIsPgGuo_SBxRjgQoXakT0ylXK-dclr4jD34smKE5vmSiIFTPD6oX93Z83QPeGqhn-x7-GHUG78hQW7ONCkPGcMtN9qyte3VI2roScfGgrqc0txkZkdMGlQwMN5P3O9Sfh2SssyYnaFZb_O1HNABQZO5vWNE3i8zsIhUoNLdh4uZ66a2luxvzxbgdegN0lso2FMcTL5fCx4B9r8mZ3gsV0gnad4KLn02SUj5w',
                'status' => ['class' => 'verified', 'label' => 'Terverifikasi', 'icon' => 'check'],
                'information' => [
                    'container_class' => 'verified',
                    'icon_class' => 'verified',
                    'icon' => 'check',
                    'title' => 'Dokumen Terverifikasi',
                    'text' =>
                        'Mohon melakukan pembayaran DP dalam waktu 7x24 jam. Jika tidak melakukan pembayaran maka proyek otomatis akan dibatalkan.',
                    'show_upload' => false,
                ],
                'flow' => [
                    'sudah_bayar' => false,
                    'status_dokumen' => 'approved',
                    'is_mandor' => false,
                    'is_proyek' => false,
                ],
                'milestones' => [
                    [
                        'label' => 'Pengajuan',
                        'meta' => '12 Okt 2023',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => true,
                    ],
                    [
                        'label' => 'Verifikasi Dokumen',
                        'meta' => 'Berhasil direview',
                        'meta_type' => 'status',
                        'meta_class' => 'pending',
                        'icon_class' => '',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pembayaran DP',
                        'meta' => 'Menunggu Pembayaran',
                        'meta_type' => 'date',
                        'icon_class' => 'in-progress',
                        'icon' => 'payments',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pengalokasian Mandor',
                        'meta' => 'Menunggu',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'person',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Selesai',
                        'meta' => 'Progress dimulai',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'start',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                ],
                'installments' => [],
            ],
            [
                'id' => 4,
                'name' => 'Rumah Minimalist Bali',
                'location' => 'Kuta, Bali',
                'design_name' => 'The Modern Tunjungan',
                'budget' => 'Rp 2.000.000.000',
                'duration' => '15 Bulan',
                'image' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuA4ksDLDJyMvJlK1qi7FzNco1Xyp9nYVZJ7PcWeGMkIsPgGuo_SBxRjgQoXakT0ylXK-dclr4jD34smKE5vmSiIFTPD6oX93Z83QPeGqhn-x7-GHUG78hQW7ONCkPGcMtN9qyte3VI2roScfGgrqc0txkZkdMGlQwMN5P3O9Sfh2SssyYnaFZb_O1HNABQZO5vWNE3i8zsIhUoNLdh4uZ66a2luxvzxbgdegN0lso2FMcTL5fCx4B9r8mZ3gsV0gnad4KLn02SUj5w',
                'status' => ['class' => 'verified', 'label' => 'Terverifikasi', 'icon' => 'check'],
                'information' => [
                    'container_class' => 'verified',
                    'icon_class' => 'verified',
                    'icon' => 'check',
                    'title' => 'Pembayaran Berhasil',
                    'text' => 'Mohon tunggu 1x24 jam untuk pengalokasian mandor untuk proyek Anda.',
                    'show_upload' => false,
                ],
                'flow' => [
                    'sudah_bayar' => true,
                    'status_dokumen' => 'approved',
                    'is_mandor' => false,
                    'is_proyek' => false,
                ],
                'milestones' => [
                    [
                        'label' => 'Pengajuan',
                        'meta' => '12 Okt 2023',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => true,
                    ],
                    [
                        'label' => 'Verifikasi Dokumen',
                        'meta' => 'Berhasil direview',
                        'meta_type' => 'status',
                        'meta_class' => 'pending',
                        'icon_class' => '',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pembayaran DP',
                        'meta' => 'Pembayaran Berhasil',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pengalokasian Mandor',
                        'meta' => 'Menunggu',
                        'meta_type' => 'date',
                        'icon_class' => 'in-progress',
                        'icon' => 'person',
                        'line_inactive' => true,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Selesai',
                        'meta' => 'Progress dimulai',
                        'meta_type' => 'date',
                        'icon_class' => 'pending',
                        'icon' => 'start',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                ],
                'installments' => [],
            ],
            [
                'id' => 5,
                'name' => 'Rumah Minimalist Jatinangor',
                'location' => 'Jatinangor, Sumedang',
                'design_name' => 'Minimalist Nangorian',
                'budget' => 'Rp 450.000.000',
                'duration' => '6 Bulan',
                'image' =>
                    'https://lh3.googleusercontent.com/aida-public/AB6AXuA4ksDLDJyMvJlK1qi7FzNco1Xyp9nYVZJ7PcWeGMkIsPgGuo_SBxRjgQoXakT0ylXK-dclr4jD34smKE5vmSiIFTPD6oX93Z83QPeGqhn-x7-GHUG78hQW7ONCkPGcMtN9qyte3VI2roScfGgrqc0txkZkdMGlQwMN5P3O9Sfh2SssyYnaFZb_O1HNABQZO5vWNE3i8zsIhUoNLdh4uZ66a2luxvzxbgdegN0lso2FMcTL5fCx4B9r8mZ3gsV0gnad4KLn02SUj5w',
                'status' => ['class' => 'verified', 'label' => 'Terverifikasi', 'icon' => 'check'],
                'information' => [
                    'container_class' => 'verified',
                    'icon_class' => 'verified',
                    'icon' => 'check',
                    'title' => 'Proyek Aktif',
                    'text' =>
                        'Proyek sedang aktif dan progress dapat dilacak. Terima kasih telah menggunakan layanan Way2Home.',
                    'show_upload' => false,
                ],
                'flow' => [
                    'sudah_bayar' => true,
                    'status_dokumen' => 'approved',
                    'is_mandor' => true,
                    'is_proyek' => true,
                ],
                'milestones' => [
                    [
                        'label' => 'Pengajuan',
                        'meta' => '12 Okt 2023',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => true,
                    ],
                    [
                        'label' => 'Verifikasi Dokumen',
                        'meta' => 'Berhasil direview',
                        'meta_type' => 'status',
                        'meta_class' => 'pending',
                        'icon_class' => '',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pembayaran DP',
                        'meta' => 'Pembayaran Berhasil',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Pengalokasian Mandor',
                        'meta' => 'Mandor dialokasikan',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'check',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                    [
                        'label' => 'Proyek Dibuat',
                        'meta' => 'Pantau progress proyek Anda',
                        'meta_type' => 'date',
                        'icon_class' => 'completed',
                        'icon' => 'start',
                        'line_inactive' => false,
                        'fill_icon' => false,
                    ],
                ],
                'installments' => [
                    [
                        'periode' => 1,
                        'price' => 'Rp 25.000.000',
                        'badge' => 'Lunas',
                        'badge_class' => 'success',
                        'due_date' => 'Jatuh Tempo: 15 Okt 2023',
                        'card_class' => '',
                    ],
                    [
                        'periode' => 2,
                        'price' => 'Rp 25.000.000',
                        'badge' => 'Belum Bayar',
                        'badge_class' => 'warning',
                        'due_date' => 'Jatuh Tempo: 15 Nov 2023',
                        'card_class' => 'active',
                    ],
                    [
                        'periode' => 3,
                        'price' => 'Rp 25.000.000',
                        'badge' => 'Belum Bayar',
                        'badge_class' => 'neutral',
                        'due_date' => 'Jatuh Tempo: 15 Des 2023',
                        'card_class' => 'disabled',
                    ],
                ],
            ],
        ];

        $projectCount = count($projects);
        $selectedProjectId = (int) (request()->route('id') ?? (request()->segment(2) ?? request('project_id', 1)));
        $selectedProject = collect($projects)->firstWhere('id', $selectedProjectId) ?? $projects[0];

        $flow = $selectedProject['flow'];
        $sudahBayar = $flow['sudah_bayar'];
        $statusDokumen = $flow['status_dokumen'];
        $isMandor = $flow['is_mandor'];
        $isProyek = $flow['is_proyek'];
    @endphp

    <div class="page-container">
        <aside class="sidebar-nav">
            <div class="sidebar-section">
                <p class="sidebar-title">List Project</p>
                <div class="sidebar-menu">
                    @foreach ($projects as $project)
                        @php
                            $isSelected = $project['id'] === $selectedProject['id'];
                        @endphp
                        <a class="sidebar-menu-item {{ $isSelected ? 'active' : '' }}"
                            href="{{ url('/project/' . $project['id']) }}">
                            <span class="material-symbols-outlined {{ $isSelected ? 'filled' : '' }}">home_work</span>
                            {{ $project['name'] }}
                        </a>
                    @endforeach
                </div>
            </div>
        </aside>

        <main class="main-content">
            <div class="content-wrapper">
                <header class="page-header">
                    <div class="header-title">
                        <h1>Proyek Saya</h1>
                        <p>Kelola dan pantau progres pembangunan hunian impian Anda.</p>
                    </div>
                    <div class="header-badge">
                        <span class="badge">
                            <span class="material-symbols-outlined">info</span>
                            {{ $projectCount }} Proyek
                        </span>
                    </div>
                </header>

                <div class="project-grid">
                    <div class="project-main">
                        <section class="project-card">
                            <div class="project-image-container">
                                <img alt="{{ $selectedProject['name'] }}" class="project-image"
                                    src="{{ $selectedProject['image'] }}" />
                                <div class="project-image-overlay"></div>
                                <div class="project-header">
                                    <h2>{{ $selectedProject['name'] }}</h2>
                                    <p class="project-location">
                                        <span class="material-symbols-outlined">location_on</span>
                                        {{ $selectedProject['location'] }}
                                    </p>
                                </div>
                                <div class="project-status {{ $selectedProject['status']['class'] }}">
                                    <span class="status-badge {{ $selectedProject['status']['class'] }}">
                                        <span class="material-symbols-outlined"
                                            style='font-variation-settings: "FILL" 1;'>{{ $selectedProject['status']['icon'] }}</span>
                                        {{ $selectedProject['status']['label'] }}
                                    </span>
                                </div>
                            </div>

                            <div class="project-content">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <p class="info-label">Nama Desain</p>
                                        <p class="info-value">{{ $selectedProject['design_name'] }}</p>
                                    </div>
                                    <div class="info-item">
                                        <p class="info-label">Budget</p>
                                        <p class="info-value">{{ $selectedProject['budget'] }}</p>
                                    </div>
                                    <div class="info-item">
                                        <p class="info-label">Estimasi Waktu</p>
                                        <p class="info-value">{{ $selectedProject['duration'] }}</p>
                                    </div>
                                </div>

                                <div
                                    class="information-container {{ $selectedProject['information']['container_class'] }}">
                                    <div class="information-icon-box {{ $selectedProject['information']['icon_class'] }}">
                                        <span
                                            class="material-symbols-outlined">{{ $selectedProject['information']['icon'] }}</span>
                                    </div>
                                    <div
                                        class="information-content {{ $selectedProject['information']['container_class'] }}">
                                        <h3>{{ $selectedProject['information']['title'] }}</h3>
                                        <p>{{ $selectedProject['information']['text'] }}</p>
                                    </div>
                                    <button
                                        onclick="window.location.href='/project/{{ $selectedProject['id'] }}/pembangunan'"
                                        class="btn-upload {{ $selectedProject['information']['container_class'] }}"
                                        style="{{ $selectedProject['information']['show_upload'] ? '' : 'display: none' }}">
                                        <span class="material-symbols-outlined">upload_file</span>
                                        Upload Ulang
                                    </button>
                                </div>

                                <div class="button-group" id="buttonGroup" data-proyek="{{ $isProyek ? 'true' : 'false' }}"
                                    data-mandor="{{ $isMandor ? 'true' : 'false' }}">
                                    <button class="btn-action btn-cancel" id="cancelBtn">
                                        <span class="material-symbols-outlined">cancel</span>
                                        Batalkan Proyek
                                    </button>

                                    @if (!$sudahBayar)
                                        <button class="btn-action btn-payments-action" id="dpBtn"
                                            {{ $statusDokumen !== 'approved' ? 'disabled' : '' }}>
                                            <span class="material-symbols-outlined">payments</span>
                                            Bayar DP
                                        </button>
                                    @else
                                        <button class="btn-action btn-progress-action" id="progressBtn">
                                            <span class="material-symbols-outlined">analytics</span>
                                            Pantau Progress
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </section>

                        @if ($isProyek)
                            <div class="cicilan-section">
                                <h3 class="section-title">Periode Cicilan Rumah</h3>
                                <div class="card-grid">
                                    @foreach ($selectedProject['installments'] as $installment)
                                        <div class="card {{ $installment['card_class'] }}">
                                            <div class="card-header">
                                                <div>
                                                    <p
                                                        class="periode-label {{ $installment['card_class'] === 'active' ? 'highlight' : '' }}">
                                                        Periode {{ $installment['periode'] }}
                                                    </p>
                                                    <p class="price">{{ $installment['price'] }}</p>
                                                </div>
                                                <span
                                                    class="badge {{ $installment['badge_class'] }}">{{ $installment['badge'] }}</span>
                                            </div>

                                            <div
                                                class="date {{ $installment['card_class'] === 'active' ? 'highlight' : '' }}">
                                                <span class="material-symbols-outlined">calendar_today</span>
                                                {{ $installment['due_date'] }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="info-box">
                                    <span class="material-symbols-outlined">warning</span>
                                    <p><b>Informasi Penting:</b> Jika cicilan belum dibayar sesuai jadwal, pengerjaan proyek
                                        akan ditunda sementara.</p>
                                </div>

                                <button class="btn-primary" id="periodePayBtn">
                                    <span class="material-symbols-outlined">payments</span>
                                    Bayar Periode
                                </button>
                            </div>
                        @endif
                    </div>

                    <div class="project-sidebar">
                        <div class="info-box-gradient">
                            <div class="info-box-content">
                                <span class="material-symbols-outlined info-box-icon">lock_clock</span>
                                <h3 class="info-box-title">Proses Pembayaran</h3>
                                <p class="info-box-text">
                                    Keamanan Anda adalah prioritas kami. Pembayaran Down Payment (DP) hanya dapat dilakukan
                                    setelah seluruh dokumen administrasi Anda dinyatakan <b>Disetujui</b> oleh tim
                                    verifikator
                                    kami.
                                </p>
                            </div>
                            <div class="info-box-decoration">
                                <span class="material-symbols-outlined">verified_user</span>
                            </div>
                        </div>

                        <section class="milestone-section">
                            <h3 class="milestone-title">Milestone Project</h3>
                            <div class="milestone-list">
                                @foreach ($selectedProject['milestones'] as $milestone)
                                    <div class="milestone-item">
                                        <div class="milestone-timeline">
                                            <div class="milestone-icon {{ $milestone['icon_class'] }}">
                                                <span class="material-symbols-outlined"
                                                    style="{{ $milestone['fill_icon'] ? 'font-variation-settings: \"FILL\" 1;' : '' }}">{{ $milestone['icon'] }}</span>
                                            </div>
                                            @if (!$loop->last)
                                                <div
                                                    class="milestone-line {{ $milestone['line_inactive'] ? 'inactive' : '' }}">
                                                </div>
                                            @endif
                                        </div>
                                        <div class="milestone-content">
                                            <p class="milestone-label">{{ $milestone['label'] }}</p>
                                            @if ($milestone['meta_type'] === 'status')
                                                <p class="milestone-status {{ $milestone['meta_class'] }}">
                                                    {{ $milestone['meta'] }}</p>
                                            @else
                                                <p class="milestone-date">{{ $milestone['meta'] }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>

                        <div class="support-card">
                            <div class="support-content">
                                <div class="support-icon">
                                    <span class="material-symbols-outlined">support_agent</span>
                                </div>
                                <div class="support-text">
                                    <h4>Butuh Bantuan?</h4>
                                    <p>Hubungi Admin Kami</p>
                                </div>
                            </div>
                            <span class="material-symbols-outlined support-chevron">chevron_right</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/customer/payment.js') }}"></script>
@endpush
