@extends('email.layouts.base')

@section('content')
@php
    $customerName  = $proyek->customer?->user?->name ?? 'Pelanggan';
    $proyekId      = '#' . $proyek->id;
    $namaProyek    = $proyek->detailBangun?->nama_proyek ?? ('Proyek ' . $proyekId);
    $mandorNama    = $proyek->mandor?->user?->name ?? 'Tim Mandor';
    $tanggal       = now()->isoFormat('D MMMM Y, HH:mm');
    $progress      = $proyek->progress;
    $persentase    = $progress?->persentase ?? 0;
    $milestone     = $progress?->milestone_aktif ?? '-';
@endphp

<div class="body">
    <p class="greeting">Halo, <strong>{{ $customerName }}</strong> 👋</p>

    <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
        Mandor <strong>{{ $mandorNama }}</strong> baru saja menambahkan laporan aktivitas
        untuk proyek Anda. Berikut update dari lapangan:
    </p>

    <span class="status-badge status-inprogress">📋 Laporan Aktivitas</span>

    <!-- Info Proyek -->
    <div class="info-card">
        <div class="info-row">
            <div>
                <div class="label">Nama Proyek</div>
                <div class="value">{{ $namaProyek }}</div>
            </div>
            <div style="text-align:right">
                <div class="label">ID Proyek</div>
                <div class="value">{{ $proyekId }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="label">Mandor</div>
                <div class="value">{{ $mandorNama }}</div>
            </div>
            <div style="text-align:right">
                <div class="label">Tanggal Laporan</div>
                <div class="value">{{ $tanggal }}</div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Card -->
    <div class="info-card" style="background:#f0f7ff; border-color:#bee3f8; border-left: 4px solid #4a6cf7;">
        <div class="label" style="color:#2b6cb0; margin-bottom:8px;">Judul Aktivitas</div>
        <div style="font-size:16px; font-weight:700; color:#1a365d; margin-bottom:12px;">
            {{ $judulAktivitas }}
        </div>
        <div class="label" style="color:#2b6cb0; margin-bottom:6px;">Laporan</div>
        <div style="font-size:14px; color:#2d3748; line-height:1.7;">
            {{ $deskripsiAktivitas }}
        </div>
    </div>

    <!-- Progress Info -->
    @if($persentase > 0)
    <div class="progress-wrap">
        <div class="progress-label">
            <span>Progress Keseluruhan</span>
            <span style="font-weight:700; color:#4a6cf7;">{{ $persentase }}%</span>
        </div>
        <div class="progress-track">
            <div class="progress-fill" style="width:{{ $persentase }}%;"></div>
        </div>
        <div style="margin-top:6px; font-size:12px; color:#a0aec0; text-align:center;">
            Milestone aktif: <strong>{{ $milestone }}</strong>
        </div>
    </div>
    @endif

    <p style="font-size:13px; color:#718096; text-align:center;">
        Buka aplikasi Way2Home untuk melihat foto dokumentasi dan detail lengkap.
    </p>

    <hr class="divider">

    <div class="cta-wrap">
        <a href="{{ config('app.url') }}/customer/tracking" class="cta-btn">
            Lihat Progress Proyek
        </a>
    </div>
</div>
@endsection
