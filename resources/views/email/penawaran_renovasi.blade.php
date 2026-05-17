@extends('email.layouts.base')

@section('content')
@php
    $customerName  = $requestRenovasi->customer?->user?->name ?? 'Pelanggan';
    $requestId     = sprintf('REV-%03d', $requestRenovasi->id);
    $mandorNama    = $penawaran->mandor?->user?->name ?? 'Tim Mandor';
    $estimasiBiaya = number_format($penawaran->estimasi_biaya, 0, ',', '.');
    $estimasiDurasi = $penawaran->estimasi_durasi ?? 14;
    $analisis      = $penawaran->analisis_dari_mandor ?? '-';
    $deskripsi     = $requestRenovasi->deskripsi_renovasi ?? '-';
    $alamat        = $requestRenovasi->alamat ?? '-';
    $tanggal       = now()->isoFormat('D MMMM Y, HH:mm');
    $isPenawaran   = $tipe === 'penawaran';

    // Hitung total material
    $materialTotal = $penawaran->materialRenovasi?->sum(fn($m) => ($m->material?->harga ?? 0) * $m->jumlah) ?? 0;
    $totalBiaya    = $penawaran->estimasi_biaya + $materialTotal;
@endphp

<div class="body">
    <p class="greeting">Halo, <strong>{{ $customerName }}</strong> 👋</p>

    @if($isPenawaran)
    <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
        Mandor <strong>{{ $mandorNama }}</strong> telah mengirimkan <strong>penawaran</strong>
        untuk permintaan renovasi Anda. Silakan tinjau dan berikan tanggapan Anda.
    </p>
    <span class="status-badge status-inprogress">📋 Penawaran Baru</span>
    @else
    <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
        Mandor <strong>{{ $mandorNama }}</strong> telah merespons negosiasi Anda.
        Berikut adalah tanggapan terbaru:
    </p>
    <span class="status-badge status-revision">💬 Tanggapan Negosiasi</span>
    @endif

    <!-- Info Renovasi -->
    <div class="info-card">
        <div class="info-row">
            <div>
                <div class="label">ID Request</div>
                <div class="value">{{ $requestId }}</div>
            </div>
            <div style="text-align:right">
                <div class="label">Tanggal</div>
                <div class="value">{{ $tanggal }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="label">Alamat Renovasi</div>
                <div class="value">{{ $alamat }}</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="label">Deskripsi Kebutuhan</div>
                <div class="value" style="font-size:13px; font-weight:400; color:#4a5568;">{{ Str::limit($deskripsi, 120) }}</div>
            </div>
        </div>
    </div>

    <!-- Detail Penawaran -->
    <div class="info-card" style="background:#f0f7ff; border-color:#bee3f8;">
        <div class="label" style="margin-bottom:12px; color:#2b6cb0;">Detail Penawaran Mandor</div>

        <div class="info-row">
            <div>
                <div class="label">Mandor</div>
                <div class="value">{{ $mandorNama }}</div>
            </div>
            <div style="text-align:right">
                <div class="label">Estimasi Durasi</div>
                <div class="value">{{ $estimasiDurasi }} hari</div>
            </div>
        </div>
        <div class="info-row">
            <div>
                <div class="label">Biaya Jasa</div>
                <div class="value" style="color:#2B3361;">Rp {{ $estimasiBiaya }}</div>
            </div>
            @if($materialTotal > 0)
            <div style="text-align:right">
                <div class="label">Biaya Material</div>
                <div class="value" style="color:#2B3361;">Rp {{ number_format($materialTotal, 0, ',', '.') }}</div>
            </div>
            @endif
        </div>
        @if($materialTotal > 0)
        <div class="info-row" style="background:#dbeafe; border-radius:8px; padding:10px; border:none; margin-top:4px;">
            <div>
                <div class="label">Total Estimasi</div>
                <div class="value" style="color:#1a365d; font-size:17px;">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</div>
            </div>
        </div>
        @endif
    </div>

    <!-- Analisis Mandor -->
    <div class="note-box">
        <strong>💬 Pesan dari {{ $mandorNama }}:</strong><br>
        {{ $analisis }}
    </div>

    <!-- Material List -->
    @if($penawaran->materialRenovasi && $penawaran->materialRenovasi->count() > 0)
    <div class="info-card" style="margin-top:0;">
        <div class="label" style="margin-bottom:10px;">Material yang Diajukan</div>
        @foreach($penawaran->materialRenovasi as $item)
        <div style="display:flex; justify-content:space-between; align-items:center; padding:8px 0; border-bottom:1px solid #edf2f7; font-size:13px;">
            <span style="color:#2d3748;">{{ $item->material?->nama_material ?? '-' }}</span>
            <span style="color:#718096;">{{ $item->jumlah }} {{ $item->satuan }}</span>
            <span style="color:#2B3361; font-weight:600;">Rp {{ number_format(($item->material?->harga ?? 0) * $item->jumlah, 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div>
    @endif

    <p style="font-size:13px; color:#718096; text-align:center; margin-bottom:20px;">
        Buka aplikasi Way2Home untuk menerima penawaran, melakukan negosiasi, atau menolak.
    </p>

    <hr class="divider">

    <div class="cta-wrap">
        <a href="{{ config('app.url') }}/customer/renovasi" class="cta-btn">
            Tinjau Penawaran
        </a>
    </div>
</div>
@endsection
