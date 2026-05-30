@extends('email.layouts.base')

@section('content')
    @php
        $customerName = $proyek->customer?->user?->name ?? 'Pelanggan';
        $proyekId = '#' . $proyek->id;
        $jenisProyek = $proyek->jenis_proyek ?? 'Pembangunan Rumah';
        $alamat = $proyek->alamat_proyek ?? '-';
        $tanggalMulai = $proyek->tanggal_mulai
            ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->isoFormat('D MMMM Y')
            : '-';
        $tanggalUpdate = now()->isoFormat('D MMMM Y, HH:mm');

        $statusMap = [
            'Pengajuan' => ['class' => 'status-default', 'label' => 'Pengajuan Diterima'],
            'Verifikasi Dokumen' => ['class' => 'status-inprogress', 'label' => 'Sedang Diverifikasi'],
            'Revisi Dokumen' => ['class' => 'status-revision', 'label' => 'Perlu Revisi Dokumen'],
            'Pembayaran DP' => ['class' => 'status-payment', 'label' => 'Menunggu Pembayaran DP'],
            'In Progress' => ['class' => 'status-inprogress', 'label' => 'Proyek Berjalan'],
            'Selesai' => ['class' => 'status-done', 'label' => 'Proyek Selesai'],
            'Ditolak' => ['class' => 'status-rejected', 'label' => 'Proyek Ditolak'],
        ];
        $statusInfo = $statusMap[$statusBaru] ?? ['class' => 'status-default', 'label' => $statusBaru];

        $pesanStatus = [
            'Pengajuan' => 'Pengajuan proyek Anda telah kami terima dan sedang menunggu proses verifikasi.',
            'Verifikasi Dokumen' =>
                'Dokumen proyek Anda sedang dalam proses verifikasi oleh admin. Mohon tunggu konfirmasi selanjutnya.',
            'Revisi Dokumen' =>
                'Terdapat dokumen yang perlu direvisi. Silakan cek catatan admin dan upload ulang dokumen yang diperlukan.',
            'Pembayaran DP' =>
                'Dokumen proyek Anda telah disetujui! Silakan lakukan pembayaran DP untuk memulai proses pembangunan.',
            'In Progress' =>
                'Proyek Anda kini sedang dalam pengerjaan oleh tim mandor kami. Pantau progressnya di aplikasi Way2Home.',
            'Selesai' =>
                'Selamat! Proyek pembangunan rumah Anda telah selesai dikerjakan. Terima kasih telah mempercayakan kepada Way2Home.',
            'Ditolak' =>
                'Mohon maaf, pengajuan proyek Anda tidak dapat kami proses. Silakan hubungi tim kami untuk informasi lebih lanjut.',
        ];
        $pesan = $pesanStatus[$statusBaru] ?? 'Status proyek Anda telah diperbarui oleh admin.';
    @endphp

    <div class="body">
        <p class="greeting">Halo, <strong>{{ $customerName }}</strong> 👋</p>

        <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
            Ada pembaruan status untuk proyek Anda di <strong>Way2Home</strong>.
            Berikut adalah informasi terbaru:
        </p>

        <!-- Status Badge -->
        <span class="status-badge {{ $statusInfo['class'] }}">
            {{ $statusInfo['label'] }}
        </span>

        <!-- Info Proyek -->
        <div class="info-card">
            <div class="info-row">
                <div>
                    <div class="label">ID Proyek</div>
                    <div class="value">{{ $proyekId }}</div>
                </div>
                <div style="text-align:right">
                    <div class="label">Jenis</div>
                    <div class="value">{{ $jenisProyek }}</div>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <div class="label">Alamat Proyek</div>
                    <div class="value">{{ $alamat }}</div>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <div class="label">Tanggal Mulai</div>
                    <div class="value">{{ $tanggalMulai }}</div>
                </div>
                <div style="text-align:right">
                    <div class="label">Diperbarui</div>
                    <div class="value">{{ $tanggalUpdate }}</div>
                </div>
            </div>
        </div>

        <!-- Pesan Status -->
        <p style="font-size:14px; color:#4a5568; line-height:1.7;">
            {{ $pesan }}
        </p>

        <!-- Catatan Admin -->
        @if ($catatanAdmin)
            <div class="note-box" style="margin-top:16px;">
                <strong>📝 Catatan Admin:</strong><br>
                {{ $catatanAdmin }}
            </div>
        @endif

        <hr class="divider">

        <div class="cta-wrap">
            <a href="{{ route('proyek.show', $proyek->id) }}" class="cta-btn">
                Lihat Detail Proyek
            </a>
        </div>
    </div>
@endsection
