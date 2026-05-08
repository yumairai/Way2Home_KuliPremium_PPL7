@extends('customer-layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/rekomendasi_rumah.css') }}">
@endpush

@section('content')
    {{-- Header --}}
    <div class="rekomendasi-rumah">
        <div class="rekom-header">
            <div>
                <span class="ai-badge">
                    Content-Based Filtering · Pure ML Engine
                </span>
            </div>
            <h1>REKOMENDASI RUMAH</h1>
        </div>

        {{-- Preferensi Summary Chips --}}
        @if (!empty($preferensi))
            <div class="preferensi-summary">
                <span class="pref-chip">Lokasi: {{ $preferensi['lokasi'] }}</span>
                <span class="pref-chip">Gaya: {{ $preferensi['gaya_arsitektur'] }}</span>
                <span class="pref-chip">Luas: {{ $preferensi['luas_area'] }} m²</span>
                <span class="pref-chip">Kamar: {{ $preferensi['jumlah_kamar'] }}</span>
                <span class="pref-chip">Budget: Rp {{ number_format($preferensi['budget'], 0, ',', '.') }}</span>
                <span class="pref-chip">Prioritas: {{ ucfirst($preferensi['prioritas']) }}</span>
            </div>
        @endif

        {{-- Kartu Hasil --}}
        @if (!empty($hasil))
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
                @foreach ($hasil as $index => $rumah)
                    @php
                        $skor = $rumah['skor'];
                        $skorClass = $skor >= 70 ? 'skor-high' : ($skor >= 50 ? 'skor-mid' : 'skor-low');
                        $harga = \App\Services\RekomendasiService::formatHarga($rumah['harga']);
                        $durasi = !empty($rumah['estimasi_durasi'])
                            ? (int) $rumah['estimasi_durasi'] . ' Bulan'
                            : \App\Services\RekomendasiService::estimasiDurasi($rumah['luas_tanah']);
                        $materials = collect(explode(';', $rumah['material_digunakan'] ?? ''))
                            ->map(fn($m) => trim($m))
                            ->filter()
                            ->take(6)
                            ->values();
                        $photo = !empty($rumah['path_gambar_desain'])
                            ? (preg_match('/^https?:\\/\\//', $rumah['path_gambar_desain'])
                                ? $rumah['path_gambar_desain']
                                : asset($rumah['path_gambar_desain']))
                            : $dummyPhotos[$index % count($dummyPhotos)];
                    @endphp

                    <div class="card">
                        <div class="card-photo">
                            <img src="{{ $photo }}" alt="Foto {{ $rumah['nama_rumah'] }}" loading="lazy">

                            <span class="rank-badge">Desain {{ $index + 1 }}</span>
                            <span class="skor-badge {{ $skorClass }}">{{ $skor }}% Match</span>

                            <div class="card-overlay">
                                <h2>{{ $rumah['nama_rumah'] }}</h2>

                                {{-- Progressive summary: show only duration & price; other details hidden until expanded --}}
                                <div class="detail-row">
                                    <span class="icon">Durasi:</span>
                                    <span><strong>{{ $durasi }}</strong></span>
                                </div>
                                <div class="detail-row">
                                    <span class="icon">Harga:</span>
                                    <span><strong>{{ $harga }}</strong></span>
                                </div>

                                <div class="card-extra" aria-hidden="true" style="display:none;">
                                    <div class="detail-row">
                                        <span class="icon">Lokasi:</span>
                                        <span>{{ $rumah['lokasi'] }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="icon">Gaya:</span>
                                        <span>{{ $rumah['gaya_arsitektur'] ?? '-' }}</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="icon">Luas:</span>
                                        <span><strong>{{ $rumah['luas_tanah'] }} m²</strong></span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="icon">Kamar:</span>
                                        <span>{{ $rumah['jumlah_kamar'] }} Kamar &nbsp;·&nbsp;
                                            {{ $rumah['jumlah_lantai'] }} Lantai</span>
                                    </div>
                                    <div class="detail-row">
                                        <span class="icon">Tahun:</span>
                                        <span><strong>{{ $rumah['tahun_bangun'] }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tombol aksi di bawah kartu --}}
                        <div class="card-footer-btns">
                            <button class="btn-pilih"
                                onclick="pilihRumah({{ $rumah['id'] }}, '{{ addslashes($rumah['nama_rumah']) }}')">
                                Pilih Desain
                            </button>
                            <button class="btn-detail btn-toggle-details" type="button">Detail</button>
                            @if ($materials->isNotEmpty())
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
                <h2>Tidak ada rekomendasi ditemukan.</h2>
                <p>Coba sesuaikan preferensi Anda.</p>
            </div>
        @endif

        <div class="back-btn-wrap">
            <button class="btn-back" onclick="window.location.href='/recommendation'">
                Ubah Preferensi
            </button>
        </div>

        {{-- Modal Material --}}
        <div class="modal-overlay" id="materialModal">
            <div class="modal-box">
                <h3 id="modalTitle">Material Digunakan</h3>
                <ul id="modalList"></ul>
                <button class="modal-close" onclick="tutupModal()">Tutup</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        async function pilihRumah(id, nama) {
            const confirmed = window.W2HDialog && typeof window.W2HDialog.confirm === 'function' ?
                await window.W2HDialog.confirm('Pilih desain "' + nama +
                    '"?\n\nAnda akan diarahkan ke halaman pembangunan.', {
                        title: 'Konfirmasi Pilihan Desain',
                        confirmText: 'Ya, pilih desain',
                        cancelText: 'Batal',
                        variant: 'warning',
                    }) :
                confirm('Pilih desain "' + nama + '"?\n\nAnda akan diarahkan ke halaman pembangunan.');

            if (confirmed) {
                window.location.href = '/house-build-form?desain_id=' + id;
            }
        }

        function lihatMaterial(materials, nama) {
            document.getElementById('modalTitle').textContent = 'Material: ' + nama;
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

        // Toggle progressive details per card
        document.addEventListener('click', function(e) {
            if (!e.target) return;
            if (e.target.classList && e.target.classList.contains('btn-toggle-details')) {
                var btn = e.target;
                var card = btn.closest('.card');
                if (!card) return;
                var extra = card.querySelector('.card-extra');
                if (!extra) return;
                var isHidden = extra.getAttribute('aria-hidden') === 'true' || extra.style.display === 'none' ||
                    getComputedStyle(extra).display === 'none';
                if (isHidden) {
                    extra.style.display = 'block';
                    extra.setAttribute('aria-hidden', 'false');
                } else {
                    extra.style.display = 'none';
                    extra.setAttribute('aria-hidden', 'true');
                }
            }
        });
    </script>
@endpush
