@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/form_pembangunan_rumah.css') }}">
@endpush
@section('content')
    @php
        // Base URL Supabase public storage
        $supabaseBase = config('services.supabase.url', 'https://ovyjfudrdwrlyioygotq.supabase.co')
                      . '/storage/v1/object/public/';
        $isTester = auth()->user()?->is_tester;
        // Path aset dummy yang di-hardcode sama di BypassTesterRequest
        $dummyDocs = [
            'sertifikat_tanah' => $supabaseBase . 'public-assets/testing/dokumen/sertifikat_tanah.jpg',
            'ktp'              => $supabaseBase . 'public-assets/testing/dokumen/ktp.jpg',
            'imb'              => $supabaseBase . 'public-assets/testing/dokumen/imb.jpg',
            'surat_kuasa'      => $supabaseBase . 'public-assets/testing/dokumen/surat_kuasa.jpg',
        ];
    @endphp
    @php
        $imagePath = !empty($desain->path_gambar_desain)
            ? (preg_match('/^https?:\\/\\//', $desain->path_gambar_desain)
                ? $desain->path_gambar_desain
                : asset($desain->path_gambar_desain))
            : asset('images/rekomendasi/rekom1.jpg');

        $materialList = collect(explode(';', (string) $desain->material_digunakan))
            ->map(fn($item) => trim($item))
            ->filter()
            ->values();

        if ($materialList->isEmpty() && !empty($desain->material_utama)) {
            $materialList = collect([$desain->material_utama]);
        }
    @endphp

    <div class="form-wrapper">
        <!-- judul -->
        <div class="page-title">
            <h1>Form Pembangunan Rumah</h1>
            <p>Lengkapi detail proyek Anda untuk memulai proses pembangunan profesional bersama Way2Home.</p>
        </div>

        <!-- desain dipilih oleh user -->
        <section class="form-section">
            <div class="design-card">
                <div class="design-card-image">
                    <img alt="Rekomendasi Rumah" src="{{ $imagePath }}" />
                    <div class="design-badge">Desain Dipilih</div>
                </div>
                <div class="design-card-details">
                    <div class="detail-item">
                        <span class="detail-label">Tipe Rumah</span>
                        <span class="detail-value">{{ $desain->tipe_rumah }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estimasi Biaya</span>
                        <span class="detail-value">Rp {{ number_format($desain->estimasi_biaya, 0, ',', '.') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Area (Tanah/Bangunan)</span>
                        <span class="detail-value">{{ $desain->luas_tanah }}m² / {{ $desain->luas_bangunan }}m²</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estimasi Waktu</span>
                        <span class="detail-value">{{ $desain->estimasi_durasi }} Bulan</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Gaya Arsitektur</span>
                        <span class="detail-value">{{ $desain->gaya_arsitektur ?? '-' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Fasilitas</span>
                        <span class="detail-value">{{ $desain->fasilitas ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- estimasi kebutuhan material -->
        <section class="form-section-large">
            <div class="section-header">
                <h2>Estimasi Kebutuhan Material</h2>
                <div class="section-header-divider"></div>
            </div>
            <div class="material-grid">
                @forelse($materialList as $material)
                    @php
                        $parts = array_map('trim', explode(':', $material, 2));
                        $namaMaterial = $parts[0] ?? 'Material';
                        $qtyMaterial = $parts[1] ?? 'Sesuai RAB';
                    @endphp
                    <div class="material-card">
                        <div class="material-info">
                            <p>{{ $namaMaterial }}</p>
                            <p>{{ $qtyMaterial }}</p>
                        </div>
                    </div>
                @empty
                    <div class="material-card">
                        <div class="material-info">
                            <p>Material Utama</p>
                            <p>{{ $desain->material_utama ?? 'Sesuai RAB' }}</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </section>

        <!-- Choice of Package -->
        <div class="form-group">
            <label class="radio-group-title">Pilihan Paket Pembangunan</label>
            <div class="radio-options">
                <label class="radio-label">
                    <input checked="" name="package" value="paket-komplit" type="radio" />
                    <div class="radio-check-indicator"></div>
                    <div class="radio-content">
                        <span>Material + Jasa</span>
                        <span>Solusi lengkap &amp; terima kunci</span>
                    </div>
                </label>

                <label class="radio-label">
                    <input name="package" value="material-only" type="radio" />
                    <div class="radio-check-indicator"></div>
                    <div class="radio-content">
                        <span>Material Saja</span>
                        <span>Pengadaan bahan bangunan premium</span>
                    </div>
                </label>
            </div>
            <div id="package-info">
                <strong>Info:</strong> Anda perlu mengunggah dokumen pendukung dan alamat lengkap.
            </div>
        </div>

        <!-- form pembangunan -->
        <form>
            <input type="hidden" id="desain_id" value="{{ $desain->id }}">

            <!-- Address Input -->
            <div class="form-group" id="sectionAlamat">
                <label class="form-label" id="label-alamat">Alamat Lengkap Proyek</label>
                <textarea id="alamatProyek" placeholder="Masukkan alamat lengkap di wilayah Jawa Barat" rows="3">{{ old('alamat_proyek', $alamat ?? '') }}</textarea>
                <p class="field-error" id="alamat-error" aria-live="polite"></p>
            </div>
            @if(isset($old_proyek_id) && $old_proyek_id)
                <input type="hidden" id="old_proyek_id" name="old_proyek_id" value="{{ $old_proyek_id }}">
            @endif
        {{-- ── Banner Tester Mode ── --}}
        @if($isTester)
            <div style="background:#d1fae5;border:1.5px solid #059669;border-radius:10px;padding:12px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
                <span class="material-symbols-outlined" style="color:#059669;font-size:22px;">science</span>
                <div>
                    <strong style="color:#065f46;">Mode Tester Aktif</strong>
                    <p style="margin:2px 0 0;font-size:0.85rem;color:#065f46;">Dokumen sudah diisi otomatis dari aset uji coba. Setelah mengisi alamat, silakan langsung klik <b>Ajukan Pembangunan</b>.</p>
                </div>
            </div>
        @endif
        <!-- dokumen kebutuhan pembangunan rumah -->
            <div class="form-group" id="sectionDokumen">
                <label class="form-label">Dokumen Pendukung (1 file Max 2MB)</label>
                <div class="upload-grid">
                    <!-- dokumen sertif tanah -->
                    <label class="upload-item {{ $isTester ? 'is-valid' : '' }}" for="sertifikat_tanah" id="drop-zone-sertifikat">
                        <input type="file" id="sertifikat_tanah" name="sertifikat_tanah" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">

                        <!-- Preview -->
                        <div class="preview-container" style="display: {{ $isTester ? 'block' : 'none' }};">
                            <img src="{{ $isTester ? $dummyDocs['sertifikat_tanah'] : '' }}" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>

                        <p class="upload-title">Sertifikat Tanah (SHM/HGB)</p>
                        <p class="upload-subtitle" style="{{ $isTester ? 'color:#059669;' : '' }}">{{ $isTester ? 'Aset tester digunakan' : 'Pilih file atau drag & drop' }}</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>

                    <!-- dokumen ktp -->
                    <label class="upload-item {{ $isTester ? 'is-valid' : '' }}" for="ktp_pemilik" id="drop-zone-ktp">
                        <input type="file" id="ktp_pemilik" name="ktp_pemilik" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">
                        <!-- Preview -->
                        <div class="preview-container" style="display: {{ $isTester ? 'block' : 'none' }};">
                            <img src="{{ $isTester ? $dummyDocs['ktp'] : '' }}" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                        <p class="upload-title">KTP Pemilik</p>
                        <p class="upload-subtitle" id="label-ktp" style="{{ $isTester ? 'color:#059669;' : '' }}">{{ $isTester ? '✅ Aset tester digunakan' : 'Pilih file atau drag &amp; drop' }}</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>

                    <!-- dokumen imb/pbg -->
                    <label class="upload-item {{ $isTester ? 'is-valid' : '' }}" for="imb_pbg" id="drop-zone-imb-pbg">
                        <input type="file" id="imb_pbg" name="imb_pbg" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">
                        <!-- Preview -->
                        <div class="preview-container" style="display: {{ $isTester ? 'block' : 'none' }};">
                            <img src="{{ $isTester ? $dummyDocs['imb'] : '' }}" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                        <p class="upload-title">IMB/PBG</p>
                        <p class="upload-subtitle" style="{{ $isTester ? 'color:#059669;' : '' }}">{{ $isTester ? '✅ Aset tester digunakan' : 'Pilih file atau drag &amp; drop' }}</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>

                    <!-- dokumen surat kuasa -->
                    <label class="upload-item {{ $isTester ? 'is-valid' : '' }}" for="surat_kuasa" id="drop-zone-surat-kuasa">
                        <input type="file" id="surat_kuasa" name="surat_kuasa" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">
                        <!-- Preview -->
                        <div class="preview-container" style="display: {{ $isTester ? 'block' : 'none' }};">
                            <img src="{{ $isTester ? $dummyDocs['surat_kuasa'] : '' }}" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                        <p class="upload-title">Surat Kuasa (Jika Ada)</p>
                        <p class="upload-subtitle" style="{{ $isTester ? 'color:#059669;' : '' }}">{{ $isTester ? '✅ Aset tester digunakan' : 'Pilih file atau drag &amp; drop' }}</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>
                </div>
            </div>

            <!-- Info Box -->
            <div class="info-box">
                <p class="info-text">
                    <strong>Penting:</strong>
                    Platform Way2Home hanya melayani jasa konstruksi dan material. Segala bentuk pengurusan perizinan
                    (IMB/PBG) tidak termasuk dalam cakupan layanan platform.
                </p>
            </div>
            <!-- Submit Button -->
            <div class="submit-section">
                <button type="button" class="submit-button" id="mainSubmitBtn">
                    <span id="submitBtnText">Ajukan Pembangunan</span>
                </button>
                <p class="submit-message" id="submitMsgText">Tim spesialis kami akan menghubungi Anda dalam 1x24 jam
                    setelah verifikasi
                    dokumen.</p>
            </div>
        </form>
    </div>
@endsection

@push('head')
    <meta name="is-tester" content="{{ $isTester ? '1' : '0' }}">
@endpush

@push('scripts')
    <script>
        window.W2H_IS_TESTER = {{ $isTester ? 'true' : 'false' }};
    </script>
    <script src="{{ asset('js/customer/form_pembangunan_script.js') }}"></script>
@endpush
