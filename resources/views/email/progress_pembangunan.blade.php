@extends('email.layouts.base')

@section('content')
    @php
        $customerName = $proyek->customer?->user?->name ?? 'Pelanggan';
        $proyekId = '#' . $proyek->id;
        $namaProyek = $proyek->detailBangun?->nama_proyek ?? 'Proyek ' . $proyekId;
        $mandorNama = $proyek->mandor?->user?->name ?? 'Tim Mandor';
        $tanggalUpdate = now()->isoFormat('D MMMM Y, HH:mm');

        // Warna progress
        $progressColor = $persentase >= 80 ? '#38a169' : ($persentase >= 50 ? '#d69e2e' : '#4a6cf7');
    @endphp

    <div class="body">
        <p class="greeting">Halo, <strong>{{ $customerName }}</strong> 👋</p>

        @if ($isSelesai)
            <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
                🎉 <strong>Selamat!</strong> Pembangunan rumah Anda telah <strong>100% selesai</strong>!
                Tim mandor kami telah menyelesaikan seluruh tahapan pembangunan dengan baik.
            </p>
            <span class="status-badge status-done">✅ Pembangunan Selesai</span>
        @else
            <p style="font-size:15px; color:#4a5568; line-height:1.7; margin-bottom:16px;">
                Tim mandor baru saja memperbarui progress pembangunan rumah Anda.
                Berikut laporan terkini dari lapangan:
            </p>
        @endif

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
                    <div class="label">Milestone Aktif</div>
                    <div class="value">{{ $milestoneAktif }}</div>
                </div>
                <div style="text-align:right">
                    <div class="label">Dikerjakan Oleh</div>
                    <div class="value">{{ $mandorNama }}</div>
                </div>
            </div>
            <div class="info-row">
                <div>
                    <div class="label">Tanggal Update</div>
                    <div class="value">{{ $tanggalUpdate }}</div>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-wrap">
            <div class="progress-label">
                <span>Progress Pembangunan</span>
                <span style="font-weight:700; color:{{ $progressColor }}">{{ $persentase }}%</span>
            </div>
            <div class="progress-track">
                <div class="progress-fill"
                    style="width:{{ $persentase }}%; background: linear-gradient(90deg, #2B3361, {{ $progressColor }});">
                </div>
            </div>
            <div style="margin-top:8px; font-size:12px; color:#a0aec0; text-align:center;">
                @if ($isSelesai)
                    Semua milestone telah selesai!
                @else
                    Tahap selanjutnya: <strong>{{ $milestoneAktif }}</strong>
                @endif
            </div>
        </div>

        <!-- Milestone Overview -->
        <div class="info-card" style="margin-top:0;">
            <div class="label" style="margin-bottom:12px;">Tahapan Pembangunan</div>
            @php
                $milestones = ['Fondasi' => 15, 'Struktur' => 35, 'Atap' => 15, 'MEP' => 15, 'Finishing' => 20];
                $kumulatif = 0;
            @endphp
            @foreach ($milestones as $ms => $bobot)
                @php
                    $kumulatif += $bobot;
                    $isDone = $persentase >= $kumulatif;
                    $isActive = !$isDone && $milestoneAktif === $ms;
                @endphp
                <div style="display:flex; align-items:center; gap:10px; padding:8px 0; border-bottom:1px solid #edf2f7;">
                    <span style="font-size:18px;">
                        @if ($isDone)
                            ✅
                        @elseif($isActive)
                            🔄
                        @else
                            ⬜
                        @endif
                    </span>
                    <div style="flex:1;">
                        <div
                            style="font-size:13px; font-weight:{{ $isActive ? '700' : '500' }}; color:{{ $isDone ? '#38a169' : ($isActive ? '#2B3361' : '#a0aec0') }}">
                            {{ $ms }}
                        </div>
                    </div>
                    <span style="font-size:11px; color:#a0aec0;">Bobot {{ $bobot }}%</span>
                </div>
            @endforeach
        </div>

        @if ($isSelesai)
            <div class="note-box" style="background:#f0fff4; border-color:#38a169; color:#276749;">
                🏠 Rumah Anda siap! Silakan lakukan pengecekan akhir bersama tim mandor dan hubungi admin Way2Home untuk
                proses serah terima.
            </div>
        @endif

        <hr class="divider">

        <div class="cta-wrap">
            <a href="{{ route('proyek.tracking', $proyek->id) }}" class="cta-btn">
                Pantau Progress Proyek
            </a>
        </div>
    </div>
@endsection
