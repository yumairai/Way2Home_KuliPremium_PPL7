@extends('customer-layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/rekomendasi_rumah.css') }}">
    <style>
        .rekom-header {
            text-align: center;
            padding: 2rem 1rem 0.5rem;
        }
        .rekom-header h1 {
            font-size: 2rem;
            font-weight: 700;
            letter-spacing: 2px;
        }
        .ai-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #e8f4fd, #d0eaff);
            color: #1a6fb5;
            border-radius: 20px;
            padding: 4px 14px;
            font-size: 0.78rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
        }
        .preferensi-summary {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin: 1rem auto 2rem;
            max-width: 800px;
            padding: 0 1rem;
        }
        .pref-chip {
            background: #f0f4ff;
            border: 1px solid #c5d5f7;
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 0.8rem;
            color: #3a5ba8;
            font-weight: 500;
        }

        /* ── Card Container ── */
        .card-container {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            max-width: 1320px;
            margin: 0 auto 3rem;
            padding: 0 1.5rem;
            align-items: stretch;
        }
        @media (max-width: 1100px) {
            .card-container { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 760px) {
            .card-container { grid-template-columns: 1fr; }
        }

        /* ── Card ── */
        .card {
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 6px 28px rgba(0,0,0,0.13);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
            position: relative;
            display: flex;
            flex-direction: column;
            cursor: pointer;
        }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 14px 40px rgba(0,0,0,0.2);
        }

        /* ── Photo wrapper fills entire card ── */
        .card-photo {
            position: relative;
            width: 100%;
            /* Fixed height so info overlay sits consistently */
            height: 460px;
            flex-shrink: 0;
        }
        .card-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* ── Top badges (Desain N + skor) ── */
        .rank-badge {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            border-radius: 8px;
            padding: 5px 14px;
            font-size: 0.82rem;
            font-weight: 700;
            z-index: 3;
            letter-spacing: 0.5px;
            backdrop-filter: blur(4px);
        }
        .skor-badge {
            position: absolute;
            top: 14px;
            right: 14px;
            border-radius: 8px;
            padding: 5px 12px;
            font-size: 0.78rem;
            font-weight: 700;
            z-index: 3;
            backdrop-filter: blur(4px);
        }
        .skor-high { background: rgba(212,237,218,0.92); color: #155724; }
        .skor-mid  { background: rgba(255,243,205,0.92); color: #856404; }
        .skor-low  { background: rgba(248,215,218,0.92); color: #721c24; }

        /* ── Gradient overlay at the bottom of the photo ── */
        .card-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(
                to top,
                rgba(0,0,0,0.88) 0%,
                rgba(0,0,0,0.65) 55%,
                transparent 100%
            );
            padding: 24px 18px 16px;
            z-index: 2;
            color: #fff;
        }
        .card-overlay h2 {
            font-size: 1.05rem;
            font-weight: 700;
            margin: 0 0 10px;
            line-height: 1.3;
            color: #fff;
        }
        .detail-row {
            display: flex;
            align-items: flex-start;
            gap: 7px;
            font-size: 0.82rem;
            color: rgba(255,255,255,0.88);
            margin-bottom: 4px;
            line-height: 1.4;
        }
        .detail-row span.icon { flex-shrink: 0; }
        .detail-row strong { color: #fff; }

        /* ── Buttons below overlay ── */
        .card-footer-btns {
            display: flex;
            gap: 8px;
            padding: 12px 14px 14px;
            background: #fff;
            flex-wrap: wrap;
        }
        .btn-pilih {
            flex: 1;
            background: #1a4fa8;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            white-space: normal;
            line-height: 1.2;
        }
        .btn-pilih:hover { background: #153d86; }
        .btn-detail {
            background: #f0f4ff;
            color: #1a4fa8;
            border: 1px solid #c5d5f7;
            border-radius: 8px;
            padding: 9px 14px;
            font-size: 0.85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            white-space: normal;
            line-height: 1.2;
        }
        .btn-detail:hover { background: #dde8ff; }

        /* ── Back button ── */
        .back-btn-wrap {
            text-align: center;
            padding-bottom: 3rem;
        }
        .btn-back {
            background: transparent;
            border: 2px solid #1a4fa8;
            color: #1a4fa8;
            border-radius: 10px;
            padding: 10px 28px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-back:hover { background: #1a4fa8; color: #fff; }

        /* ── Modal ── */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.active { display: flex; }
        .modal-box {
            background: #fff;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            padding: 24px;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        }
        .modal-box h3 { margin: 0 0 12px; font-size: 1rem; color: #1a2340; }
        .modal-box ul { padding-left: 18px; font-size: 0.85rem; color: #444; }
        .modal-box ul li { margin-bottom: 5px; }
        .modal-close {
            display: block;
            margin-top: 18px;
            text-align: right;
            background: none;
            border: none;
            color: #1a4fa8;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
        }
        .empty-state {
            text-align: center;
            padding: 4rem 1rem;
            color: #888;
        }
    </style>
@endpush

@section('content')

{{-- Header --}}
<div class="rekom-header">
    <div>
        <span class="ai-badge">
            ⚙️ &nbsp;Content-Based Filtering · Pure ML Engine
        </span>
    </div>
    <h1>REKOMENDASI RUMAH</h1>
</div>

{{-- Preferensi Summary Chips --}}
@if(!empty($preferensi))
<div class="preferensi-summary">
    <span class="pref-chip">📍 {{ $preferensi['lokasi'] }}</span>
    <span class="pref-chip">🏠 {{ $preferensi['gaya_arsitektur'] }}</span>
    <span class="pref-chip">📐 {{ $preferensi['luas_area'] }} m²</span>
    <span class="pref-chip">🛏 {{ $preferensi['jumlah_kamar'] }} Kamar</span>
    <span class="pref-chip">💰 Rp {{ number_format($preferensi['budget'], 0, ',', '.') }}</span>
    <span class="pref-chip">🎯 Prioritas: {{ ucfirst($preferensi['prioritas']) }}</span>
</div>
@endif

{{-- Kartu Hasil --}}
@if(!empty($hasil))
@php
    /* Kumpulan foto dummy rumah dari Unsplash (bebas pakai) */
    $dummyPhotos = [
        'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=700&h=500&fit=crop',
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=700&h=500&fit=crop',
        'https://images.unsplash.com/photo-1570129477492-45c003edd2be?w=700&h=500&fit=crop',
        'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?w=700&h=500&fit=crop',
        'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=700&h=500&fit=crop',
        'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=700&h=500&fit=crop',
    ];
@endphp
<div class="card-container">
    @foreach($hasil as $index => $rumah)
    @php
        $skor      = $rumah['skor'];
        $skorClass = $skor >= 70 ? 'skor-high' : ($skor >= 50 ? 'skor-mid' : 'skor-low');
        $harga     = \App\Services\RekomendasiService::formatHarga($rumah['harga']);
        $durasi    = \App\Services\RekomendasiService::estimasiDurasi($rumah['luas_tanah']);
        $materials = collect(explode(';', $rumah['material_digunakan'] ?? ''))
                        ->map(fn($m) => trim($m))->filter()->take(6)->values();
        $photo     = $dummyPhotos[$index % count($dummyPhotos)];
    @endphp

    <div class="card">
        <div class="card-photo">
            {{-- Foto dummy rumah --}}
            <img src="{{ $photo }}"
                 alt="Foto {{ $rumah['nama_rumah'] }}"
                 loading="lazy">

            {{-- Badge kiri atas: Desain N --}}
            <span class="rank-badge">Desain {{ $index + 1 }}</span>

            {{-- Badge kanan atas: Skor --}}
            <span class="skor-badge {{ $skorClass }}">{{ $skor }}% Match</span>

            {{-- Overlay info di bawah foto --}}
            <div class="card-overlay">
                <h2>{{ $rumah['nama_rumah'] }}</h2>

                <div class="detail-row">
                    <span class="icon">📍</span>
                    <span>{{ $rumah['lokasi'] }}</span>
                </div>
                <div class="detail-row">
                    <span class="icon">📐</span>
                    <span>Luas: <strong>{{ $rumah['luas_tanah'] }} m²</strong></span>
                </div>
                <div class="detail-row">
                    <span class="icon">🛏</span>
                    <span>{{ $rumah['jumlah_kamar'] }} Kamar &nbsp;·&nbsp; {{ $rumah['jumlah_lantai'] }} Lantai</span>
                </div>
                <div class="detail-row">
                    <span class="icon">🏗</span>
                    <span>Tahun Bangun: <strong>{{ $rumah['tahun_bangun'] }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="icon">⏱</span>
                    <span>Est. Durasi: <strong>{{ $durasi }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="icon">💰</span>
                    <span>Harga: <strong>{{ $harga }}</strong></span>
                </div>
            </div>
        </div>

        {{-- Tombol aksi di bawah kartu --}}
        <div class="card-footer-btns">
            <button class="btn-pilih"
                onclick="pilihRumah({{ $rumah['id'] }}, '{{ addslashes($rumah['nama_rumah']) }}')">
                Pilih Rumah
            </button>
            @if($materials->isNotEmpty())
            <button class="btn-detail"
                onclick="lihatMaterial({{ $materials->toJson() }}, '{{ addslashes($rumah['nama_rumah']) }}')">
                Material
            </button>
            @endif
        </div>
    </div>
    @endforeach
</div>
@else
<div class="empty-state">
    <h2>😕 Tidak ada rekomendasi ditemukan.</h2>
    <p>Coba sesuaikan preferensi Anda.</p>
</div>
@endif

<div class="back-btn-wrap">
    <button class="btn-back" onclick="window.location.href='/recommendation'">
        ← Ubah Preferensi
    </button>
</div>

{{-- Modal Material --}}
<div class="modal-overlay" id="materialModal">
    <div class="modal-box">
        <h3 id="modalTitle">Material Digunakan</h3>
        <ul id="modalList"></ul>
        <button class="modal-close" onclick="tutupModal()">Tutup ✕</button>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function pilihRumah(id, nama) {
        if (confirm('Pilih rumah "' + nama + '"?\n\nAnda akan diarahkan ke halaman pembangunan.')) {
            window.location.href = '/house-build-form?rumah_id=' + id;
        }
    }

    function lihatMaterial(materials, nama) {
        document.getElementById('modalTitle').textContent = '📦 Material: ' + nama;
        const list = document.getElementById('modalList');
        list.innerHTML = '';
        materials.forEach(function(m) {
            const li = document.createElement('li');
            li.textContent = m;
            list.appendChild(li);
        });
        document.getElementById('materialModal').classList.add('active');
    }

    function tutupModal() {
        document.getElementById('materialModal').classList.remove('active');
    }

    document.getElementById('materialModal').addEventListener('click', function(e) {
        if (e.target === this) tutupModal();
    });
</script>
@endpush