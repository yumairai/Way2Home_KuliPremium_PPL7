@extends('email.layouts.base')

@section('content')
@php
    $customerName = $requestRenovasi->customer?->user?->name ?? 'Pelanggan';
    $requestId    = sprintf('REV-%03d', $requestRenovasi->id);
    $alamat       = $requestRenovasi->alamat ?? '-';
    $deskripsi    = $requestRenovasi->deskripsi_renovasi ?? '-';
    $tanggal      = now()->isoFormat('D MMMM Y, HH:mm');

    $offer = $requestRenovasi->penawaran?->where('status_penawaran', 'diterima')->first()
           ?? $requestRenovasi->penawaran?->first();
    $mandorNama    = $offer?->mandor?->user?->name ?? 'Tim Mandor';
    $biayaRenovasi = $offer ? number_format($offer->estimasi_biaya, 0, ',', '.') : '-';
@endphp

<div class="body">
    <p class="greeting">Halo, <strong>{{ $customerName }}</strong> 👋</p>

    <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
        🎉 Kabar baik! Renovasi rumah Anda telah <strong>selesai dikerjakan</strong>
        oleh <strong>{{ $mandorNama }}</strong>. Terima kasih telah mempercayakan
        kebutuhan renovasi Anda kepada <strong>Way2Home</strong>.
    </p>

    <span class="status-badge status-done">✅ Renovasi Selesai</span>

    <!-- Info Renovasi -->
    <div class="info-card">
        <div class="info-row">
            <div>
                <div class="label">ID Renovasi</div>
                <div class="value">{{ $requestId }}</div>
            </div>
            <div style="text-align:right">
                <div class="label">Tanggal Selesai</div>
                <div class="value">{{ $tanggal }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="label">Alamat</div>
                <div class="value">{{ $alamat }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="label">Dikerjakan Oleh</div>
                <div class="value">{{ $mandorNama }}</div>
            </div>
            <div style="text-align:right">
                <div class="label">Total Biaya Jasa</div>
                <div class="value">Rp {{ $biayaRenovasi }}</div>
            </div>
        </div>
    </div>

    <!-- Checklist Selesai -->
    <div class="info-card" style="background:#f0fff4; border-color:#9ae6b4;">
        <div class="label" style="color:#276749; margin-bottom:12px;">Yang Telah Diselesaikan</div>
        @foreach(['Survei & Analisis Kebutuhan', 'Pengadaan Material', 'Pengerjaan Renovasi', 'Pengecekan Kualitas', 'Pembersihan Area Kerja'] as $item)
        <div style="display:flex; align-items:center; gap:10px; padding:7px 0; font-size:13px; color:#276749; border-bottom:1px solid #c6f6d5;">
            <span>✅</span> {{ $item }}
        </div>
        @endforeach
    </div>

    <div class="note-box" style="background:#fffbeb; border-color:#f6ad55;">
        <strong>📋 Langkah Selanjutnya:</strong><br>
        Silakan lakukan pengecekan hasil renovasi. Jika ada yang perlu ditindaklanjuti,
        hubungi kami melalui aplikasi Way2Home. Jangan lupa berikan ulasan untuk mandor Anda!
    </div>

    <hr class="divider">

    <div class="cta-wrap">
        <a href="{{ config('app.url') }}/customer/renovasi" class="cta-btn">
            Lihat Detail & Beri Ulasan
        </a>
    </div>
</div>
@endsection
