@extends('customer-layouts.proyek_user')
@section('project_content')

@php
$statusProyek = $proyek->status_proyek;
$detail = $proyek->detailBangun;
$desain = $detail?->desainRumah;
$dokumen = $detail?->dokumenProyek ?? collect();
$catatanAdmin = $detail?->catatan_admin;

// Cek sudah bayar dari relasi pembayaranDP
$sudahBayar = $proyek->pembayaranDP !== null;

$statusDokumen = match($statusProyek) {
'Menunggu Verifikasi' => 'pending',
'Revisi Dokumen' => 'revision',
default => 'approved',
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
                <button class="btn-upload" style="display:none">
                    <span class="material-symbols-outlined">upload_file</span>
                    Upload Ulang
                </button>
            </div>

            @elseif ($statusDokumen === 'approved' && !$sudahBayar)
            <div class="information-container verified">
                <div class="information-icon-box verified">
                    <span class="material-symbols-outlined">check</span>
                </div>
                <div class="information-content verified">
                    <h3>Dokumen Terverifikasi</h3>
                    <p>Mohon melakukan pembayaran DP dalam waktu 7×24 jam. Jika tidak, proyek akan otomatis dibatalkan.</p>
                </div>
                <button class="btn-upload" style="display:none"></button>
            </div>

            @elseif ($sudahBayar && !$isMandor)
            <div class="information-container verified">
                <div class="information-icon-box verified">
                    <span class="material-symbols-outlined">check</span>
                </div>
                <div class="information-content verified">
                    <h3>Pembayaran Berhasil</h3>
                    <p>Mohon tunggu 1×24 jam untuk pengalokasian mandor proyek Anda.</p>
                </div>
                <button class="btn-upload" style="display:none"></button>
            </div>

            @elseif ($sudahBayar && $isMandor)
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
                <button class="btn-upload" style="display:none"></button>
            </div>
            @endif

            <div class="button-group">
                <button class="btn-action btn-cancel" id="cancelBtn"
                    data-proyek="{{ $isProyek ? 'true' : 'false' }}">
                    <span class="material-symbols-outlined">cancel</span>
                    Batalkan Proyek
                </button>

                @if (!$sudahBayar)
                <button class="btn-action btn-payments-action" id="dpBtn"
                    data-harga="{{ $desain?->estimasi_biaya ?? 0 }}"
                    data-proyek-id="{{ $proyek->id }}"
                    {{ $statusDokumen !== 'approved' ? 'disabled' : '' }}>
                    <span class="material-symbols-outlined">payments</span>
                    Bayar DP
                </button>
                @else
                <button class="btn-action btn-progress-action" id="progressBtn"
                    data-mandor="{{ $isMandor ? 'true' : 'false' }}">
                    <span class="material-symbols-outlined">analytics</span>
                    Pantau Progress
                </button>
                @endif
            </div>

        </div>
    </section>
</div>

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
                    @if ($sudahBayar)
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
                    @if ($sudahBayar)
                    <p class="milestone-date">Pembayaran Berhasil</p>
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
                    @elseif ($sudahBayar)
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
                    @if ($isProyek)
                    <div class="milestone-icon completed">
                        <span class="material-symbols-outlined">start</span>
                    </div>
                    @else
                    <div class="milestone-icon pending">
                        <span class="material-symbols-outlined">start</span>
                    </div>
                    @endif
                </div>
                <div class="milestone-content">
                    <p class="milestone-label">{{ $isProyek ? 'Proyek Dibuat' : 'Mulai' }}</p>
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