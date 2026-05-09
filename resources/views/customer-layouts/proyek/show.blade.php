@extends('customer-layouts.proyek_user')
@section('project_content')

@php
$statusProyek = $proyek->status_proyek;
$detail       = $proyek->detailBangun;
$desain       = $detail?->desainRumah;
$catatanAdmin = $detail?->catatan_admin;

// Ambil semua pembayaran terurut by periode
$semuaPembayaran = $proyek->pembayaranProyek;

// DP = periode 0
$dp        = $semuaPembayaran->firstWhere('periode', 0);
$sudahBayarDP = $dp?->status_pembayaran === 'berhasil';

// Cicilan = periode 1-3
$cicilans = $semuaPembayaran->where('periode', '>', 0)->values();

// Cicilan aktif pertama yang belum lunas (berurutan)
$cicilanAktif = $cicilans->first(
    fn($c) => in_array($c->status_pembayaran, ['belum_bayar', 'pending', 'gagal', 'jatuh_tempo'])
);

$statusDokumen = match($statusProyek) {
    'Menunggu Verifikasi' => 'pending',
    'Revisi Dokumen'      => 'revision',
    default               => 'approved',
};

$isMandor = !in_array($statusProyek, [
    'Menunggu Verifikasi', 'Revisi Dokumen', 'Pembayaran DP', 'Pengalokasian Mandor'
]);
$isProyek = $isMandor;
@endphp

<div class="project-main">
    <section class="project-card">

        <div class="project-image-container">
            <img class="project-image"
                alt="{{ $desain->tipe_rumah ?? $proyek->jenis_proyek }}"
                src="{{ $desain?->path_gambar_desain ? asset($desain->path_gambar_desain) : asset('images/default-project.jpg') }}" />
            <div class="project-image-overlay"></div>

            <div class="project-header">
                <h2>{{ $desain->tipe_rumah ?? $proyek->jenis_proyek }}</h2>
                <p class="project-location">
                    <span class="material-symbols-outlined">location_on</span>
                    {{ $proyek->alamat_proyek }}
                </p>
            </div>

            <div class="project-status {{ $statusDokumen }}">
                @if ($statusDokumen === 'revision')
                <span class="status-badge revision">
                    <span class="material-symbols-outlined" style='font-variation-settings:"FILL" 1'>error</span>
                    Perlu Revisi
                </span>
                @elseif ($statusDokumen === 'pending')
                <span class="status-badge pending">
                    <span class="material-symbols-outlined" style='font-variation-settings:"FILL" 1'>error</span>
                    Menunggu
                </span>
                @elseif ($statusDokumen === 'approved')
                <span class="status-badge verified">
                    <span class="material-symbols-outlined" style='font-variation-settings:"FILL" 1'>check</span>
                    Terverifikasi
                </span>
                @endif
            </div>
        </div>

        <div class="project-content">

            <div class="info-grid">
                <div class="info-item">
                    <p class="info-label">Nama Desain</p>
                    <p class="info-value">{{ $desain->tipe_rumah ?? '-' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Budget</p>
                    <p class="info-value">Rp {{ $desain ? number_format($desain->estimasi_biaya, 0, ',', '.') : '-' }}</p>
                </div>
                <div class="info-item">
                    <p class="info-label">Estimasi Waktu</p>
                    <p class="info-value">{{ $desain->estimasi_durasi ?? '-' }} Bulan</p>
                </div>
            </div>

            {{-- ── Info Banner ── --}}
            @if ($statusDokumen === 'revision')
            <div class="information-container">
                <div class="information-icon-box">
                    <span class="material-symbols-outlined">rate_review</span>
                </div>
                <div class="information-content">
                    <h3>Dokumen Ditolak</h3>
                    <p>{{ $catatanAdmin ?? 'Dokumen Anda memerlukan perbaikan. Mohon unggah ulang dokumen yang sesuai.' }}</p>
                </div>
                <button class="btn-upload revision">
                    <span class="material-symbols-outlined">upload_file</span>
                    Upload Ulang
                </button>
            </div>

            @elseif ($statusDokumen === 'pending')
            <div class="information-container pending">
                <div class="information-icon-box pending">
                    <span class="material-symbols-outlined">rate_review</span>
                </div>
                <div class="information-content pending">
                    <h3>Dokumen Sedang Direview</h3>
                    <p>Mohon tunggu 1×24 jam.</p>
                </div>
            </div>

            @elseif ($statusDokumen === 'approved' && !$sudahBayarDP)
            <div class="information-container verified">
                <div class="information-icon-box verified">
                    <span class="material-symbols-outlined">check</span>
                </div>
                <div class="information-content verified">
                    <h3>Dokumen Terverifikasi</h3>
                    <p>Mohon melakukan pembayaran DP dalam waktu 7×24 jam. Jika tidak, proyek akan otomatis dibatalkan.</p>
                </div>
            </div>

            @elseif ($sudahBayarDP && !$isMandor)
            <div class="information-container verified">
                <div class="information-icon-box verified">
                    <span class="material-symbols-outlined">check</span>
                </div>
                <div class="information-content verified">
                    <h3>Pembayaran DP Berhasil</h3>
                    <p>Mohon tunggu 1×24 jam untuk pengalokasian mandor proyek Anda.</p>
                </div>
            </div>

            @elseif ($sudahBayarDP && $isMandor)
            <div class="information-container verified">
                <div class="information-icon-box verified">
                    <span class="material-symbols-outlined">check</span>
                </div>
                <div class="information-content verified">
                    <h3>Proyek Aktif</h3>
                    <p>Proyek sedang aktif dan progress dapat dilacak. Terima kasih telah menggunakan layanan
                        <strong style="color:#004796">Way2Home</strong>.
                    </p>
                </div>
            </div>
            @endif

            {{-- ── Action Buttons ── --}}
            <div class="button-group">
                <button class="btn-action btn-cancel" id="cancelBtn"
                    data-proyek="{{ $isProyek ? 'true' : 'false' }}">
                    <span class="material-symbols-outlined">cancel</span>
                    Batalkan Proyek
                </button>

                @if (!$sudahBayarDP)
                {{-- Tombol Bayar DP --}}
                <button class="btn-action btn-payments-action" id="dpBtn"
                    data-pembayaran-id="{{ $dp?->id }}"
                    data-nominal="{{ $dp?->jumlah_bayar ?? 0 }}"
                    data-label="Down Payment"
                    {{ $statusDokumen !== 'approved' ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined">payments</span>
                    Bayar DP
                </button>

                @else
                {{-- Tombol Pantau Progress --}}
                <button class="btn-action btn-progress-action" id="progressBtn"
                    data-mandor="{{ $isMandor ? 'true' : 'false' }}"
                    data-proyek-id="{{ $proyek->id }}">
                    <span class="material-symbols-outlined">analytics</span>
                    Pantau Progress
                </button>
                @endif
            </div>

        </div>
    </section>

    {{-- ── Cicilan Section (hanya tampil jika proyek aktif) ── --}}
    @if ($isProyek)
    <div class="cicilan-section">
        <h3 class="section-title">Periode Cicilan Rumah</h3>
        <div class="card-grid">

            @forelse ($cicilans as $cicilan)
            @php
                $isAktifCard = $cicilanAktif && $cicilanAktif->id === $cicilan->id;
                $cardClass   = $isAktifCard ? 'active' : $cicilan->cardClass();
            @endphp
            <div class="card {{ $cardClass }}">
                <div class="card-header">
                    <div>
                        <p class="periode-label {{ $isAktifCard ? 'highlight' : '' }}">
                            Periode {{ $cicilan->periode }}
                        </p>
                        <p class="price">Rp {{ number_format($cicilan->jumlah_bayar, 0, ',', '.') }}</p>
                    </div>
                    <span class="badge {{ $cicilan->badgeClass() }}">
                        {{ $cicilan->badgeLabel() }}
                    </span>
                </div>
                <div class="date {{ $isAktifCard ? 'highlight' : '' }}">
                    <span class="material-symbols-outlined">calendar_today</span>
                    {{ $cicilan->tanggal_jatuh_tempo?->format('d M Y') ?? '-' }}
                </div>
            </div>
            @empty
            <p style="color: var(--text-muted); font-size: 0.9rem;">Cicilan belum tersedia.</p>
            @endforelse

        </div>

        <div class="info-box">
            <span class="material-symbols-outlined">warning</span>
            <p><b>Informasi Penting:</b> Jika cicilan belum dibayar sesuai jadwal, pengerjaan proyek
                akan ditunda sementara.</p>
        </div>

        <button class="btn-primary" id="periodePayBtn"
            data-pembayaran-id="{{ $cicilanAktif?->id }}"
            data-nominal="{{ $cicilanAktif?->jumlah_bayar }}"
            data-periode="{{ $cicilanAktif?->periode }}"
            {{ !$cicilanAktif ? 'disabled' : '' }}>
            <span class="material-symbols-outlined">payments</span>
            @if (!$cicilanAktif)
                Semua Cicilan Lunas 🎉
            @elseif ($cicilanAktif->status_pembayaran === 'pending')
                Selesaikan Pembayaran Periode {{ $cicilanAktif->periode }}
            @else
                Bayar Periode {{ $cicilanAktif->periode }}
            @endif
        </button>
    </div>
    @endif

</div>

{{-- ── Sidebar ── --}}
<div class="project-sidebar">

    <div class="info-box-gradient">
        <div class="info-box-content">
            <span class="material-symbols-outlined info-box-icon">lock_clock</span>
            <h3 class="info-box-title">Proses Pembayaran</h3>
            <p class="info-box-text">
                Keamanan Anda adalah prioritas kami. Pembayaran Down Payment (DP) hanya dapat
                dilakukan setelah seluruh dokumen administrasi Anda dinyatakan <b>Disetujui</b>
                oleh tim verifikator kami.
            </p>
        </div>
        <div class="info-box-decoration">
            <span class="material-symbols-outlined">verified_user</span>
        </div>
    </div>

    <section class="milestone-section">
        <h3 class="milestone-title">Milestone Proyek</h3>
        <div class="milestone-list">

            {{-- 1. Pengajuan --}}
            <div class="milestone-item">
                <div class="milestone-timeline">
                    <div class="milestone-icon completed">
                        <span class="material-symbols-outlined" style='font-variation-settings:"FILL" 1'>check</span>
                    </div>
                    <div class="milestone-line"></div>
                </div>
                <div class="milestone-content">
                    <p class="milestone-label">Pengajuan</p>
                    <p class="milestone-date">{{ $proyek->created_at->format('d M Y') }}</p>
                </div>
            </div>

            {{-- 2. Verifikasi Dokumen --}}
            <div class="milestone-item">
                <div class="milestone-timeline">
                    @if ($statusDokumen === 'approved')
                    <div class="milestone-icon completed">
                        <span class="material-symbols-outlined">check</span>
                    </div>
                    <div class="milestone-line"></div>
                    @else
                    <div class="milestone-icon in-progress">
                        <span class="material-symbols-outlined">history</span>
                    </div>
                    <div class="milestone-line inactive"></div>
                    @endif
                </div>
                <div class="milestone-content">
                    <p class="milestone-label">Verifikasi Dokumen</p>
                    @if ($statusDokumen === 'approved')
                    <p class="milestone-status pending">Berhasil direview</p>
                    @elseif ($statusDokumen === 'revision')
                    <p class="milestone-status revision">Butuh Revisi</p>
                    @else
                    <p class="milestone-status pending">Menunggu review</p>
                    @endif
                </div>
            </div>

            {{-- 3. Pembayaran DP --}}
            <div class="milestone-item">
                <div class="milestone-timeline">
                    @if ($sudahBayarDP)
                    <div class="milestone-icon completed">
                        <span class="material-symbols-outlined">check</span>
                    </div>
                    <div class="milestone-line"></div>
                    @elseif ($statusDokumen === 'approved')
                    <div class="milestone-icon in-progress">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="milestone-line inactive"></div>
                    @else
                    <div class="milestone-icon pending">
                        <span class="material-symbols-outlined">payments</span>
                    </div>
                    <div class="milestone-line inactive"></div>
                    @endif
                </div>
                <div class="milestone-content">
                    <p class="milestone-label">Pembayaran DP</p>
                    @if ($sudahBayarDP)
                    <p class="milestone-date">{{ $dp->tanggal_bayar?->format('d M Y') }}</p>
                    @elseif ($statusDokumen === 'approved')
                    <p class="milestone-date">Menunggu Pembayaran</p>
                    @else
                    <p class="milestone-date">Menunggu Verifikasi</p>
                    @endif
                </div>
            </div>

            {{-- 4. Pengalokasian Mandor --}}
            <div class="milestone-item">
                <div class="milestone-timeline">
                    @if ($isMandor)
                    <div class="milestone-icon completed">
                        <span class="material-symbols-outlined">check</span>
                    </div>
                    <div class="milestone-line"></div>
                    @elseif ($sudahBayarDP)
                    <div class="milestone-icon in-progress">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div class="milestone-line inactive"></div>
                    @else
                    <div class="milestone-icon pending">
                        <span class="material-symbols-outlined">person</span>
                    </div>
                    <div class="milestone-line inactive"></div>
                    @endif
                </div>
                <div class="milestone-content">
                    <p class="milestone-label">Pengalokasian Mandor</p>
                    <p class="milestone-date">{{ $isMandor ? 'Mandor dialokasikan' : 'Menunggu' }}</p>
                </div>
            </div>

            {{-- 5. Proyek Mulai --}}
            <div class="milestone-item">
                <div class="milestone-timeline">
                    <div class="milestone-icon {{ $isProyek ? 'completed' : 'pending' }}">
                        <span class="material-symbols-outlined">start</span>
                    </div>
                </div>
                <div class="milestone-content">
                    <p class="milestone-label">{{ $isProyek ? 'Proyek Aktif' : 'Mulai' }}</p>
                    <p class="milestone-date">{{ $isProyek ? 'Pantau progress proyek Anda' : 'Progress dimulai' }}</p>
                </div>
            </div>

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

@endsection