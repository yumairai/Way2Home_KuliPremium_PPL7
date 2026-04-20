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
        .card-container {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
            max-width: 1320px;
            margin: 0 auto 3rem;
            padding: 0 1.5rem;
            align-items: stretch;
        }
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            position: relative;
            display: flex;
            flex-direction: column;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.13);
        }
        .rank-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background: rgba(0,0,0,0.55);
            color: #fff;
            border-radius: 8px;
            padding: 3px 10px;
            font-size: 0.73rem;
            font-weight: 600;
            z-index: 2;
        }
        .skor-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            border-radius: 8px;
            padding: 3px 10px;
            font-size: 0.73rem;
            font-weight: 700;
            z-index: 2;
        }
        .skor-high { background: #d4edda; color: #155724; }
        .skor-mid  { background: #fff3cd; color: #856404; }
        .skor-low  { background: #f8d7da; color: #721c24; }
        .card-img-placeholder {
            width: 100%;
            height: 190px;
            background: linear-gradient(135deg, #e8edf7 0%, #c9d6f0 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }
        @media (max-width: 1100px) {
            .card-container {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
        @media (max-width: 760px) {
            .card-container {
                grid-template-columns: 1fr;
            }
        }
        .details {
            padding: 14px 16px 10px;
            flex: 1;
        }
        .details h2 {
            font-size: 1rem;
            font-weight: 700;
            margin: 0 0 10px;
            color: #1a2340;
            line-height: 1.35;
            overflow-wrap: anywhere;
        }
        .detail-row {
            display: grid;
            grid-template-columns: auto 1fr;
            align-items: start;
            gap: 6px;
            font-size: 0.83rem;
            color: #555;
            margin-bottom: 5px;
            line-height: 1.35;
        }
        .detail-row span:last-child { min-width: 0; overflow-wrap: anywhere; }
        .detail-row strong { color: #222; }
        .card-footer-btns {
            display: flex;
            gap: 8px;
            padding: 0 16px 16px;
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
        /* Modal */
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
<div class="card-container">
    @foreach($hasil as $index => $rumah)
    @php
        $skor      = $rumah['skor'];
        $skorClass = $skor >= 70 ? 'skor-high' : ($skor >= 50 ? 'skor-mid' : 'skor-low');
        $harga     = \App\Services\RekomendasiService::formatHarga($rumah['harga']);
        $durasi    = \App\Services\RekomendasiService::estimasiDurasi($rumah['luas_tanah']);
        $materials = collect(explode(';', $rumah['material_digunakan'] ?? ''))
                        ->map(fn($m) => trim($m))->filter()->take(6)->values();
    @endphp
    <div class="card">
        <span class="rank-badge">#{{ $index + 1 }}</span>
        <span class="skor-badge {{ $skorClass }}">{{ $skor }}% Match</span>

        <div class="card-img-placeholder">🏠</div>

        <div class="details">
            <h2>{{ $rumah['nama_rumah'] }}</h2>
            <div class="detail-row">
                <span>📍</span>
                <span>{{ $rumah['lokasi'] }}</span>
            </div>
            <div class="detail-row">
                <span>📐</span>
                <span>Luas: <strong>{{ $rumah['luas_tanah'] }} m²</strong></span>
            </div>
            <div class="detail-row">
                <span>🛏</span>
                <span>{{ $rumah['jumlah_kamar'] }} Kamar &nbsp;·&nbsp; {{ $rumah['jumlah_lantai'] }} Lantai</span>
            </div>
            <div class="detail-row">
                <span>🏗</span>
                <span>Tahun Bangun: <strong>{{ $rumah['tahun_bangun'] }}</strong></span>
            </div>
            <div class="detail-row">
                <span>⏱</span>
                <span>Est. Durasi: <strong>{{ $durasi }}</strong></span>
            </div>
            <div class="detail-row">
                <span>💰</span>
                <span>Harga: <strong>{{ $harga }}</strong></span>
            </div>
        </div>

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
