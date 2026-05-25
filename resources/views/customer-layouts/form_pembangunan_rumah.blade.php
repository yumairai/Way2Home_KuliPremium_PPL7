@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/form_pembangunan_rumah.css') }}">
@endpush
@section('content')
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
            <!-- dokumen kebutuhan pembangunan rumah -->
            <div class="form-group" id="sectionDokumen">
                <label class="form-label">Dokumen Pendukung (1 file Max 2MB)</label>
                <div class="upload-grid">
                    <!-- dokumen sertif tanah -->
                    <label class="upload-item" for="sertifikat_tanah" id="drop-zone-sertifikat">
                        <input type="file" id="sertifikat_tanah" name="sertifikat_tanah" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">

                        <!-- Preview -->
                        <div class="preview-container" style="display: none;">
                            <img src="" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>

                        <p class="upload-title">Sertifikat Tanah (SHM/HGB)</p>
                        <p class="upload-subtitle">Pilih file atau drag & drop</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>

                    <!-- dokumen ktp -->
                    <label class="upload-item" for="ktp_pemilik" id="drop-zone-ktp">
                        <input type="file" id="ktp_pemilik" name="ktp_pemilik" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">
                        <!-- Preview -->
                        <div class="preview-container" style="display: none;">
                            <img src="" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                        <p class="upload-title">KTP Pemilik</p>
                        <p class="upload-subtitle" id="label-ktp">Pilih file atau drag &amp; drop</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>

                    <!-- dokumen imb/pbg -->
                    <label class="upload-item" for="imb_pbg" id="drop-zone-imb-pbg">
                        <input type="file" id="imb_pbg" name="imb_pbg" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">
                        <!-- Preview -->
                        <div class="preview-container" style="display: none;">
                            <img src="" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                        <p class="upload-title">IMB/PBG</p>
                        <p class="upload-subtitle">Pilih file atau drag &amp; drop</p>
                        <p class="field-error" aria-live="polite"></p>
                    </label>

                    <!-- dokumen surat kuasa -->
                    <label class="upload-item" for="surat_kuasa" id="drop-zone-surat-kuasa">
                        <input type="file" id="surat_kuasa" name="surat_kuasa" accept=".jpg,.jpeg,.png,.pdf"
                            class="file-input-hidden">
                        <!-- Preview -->
                        <div class="preview-container" style="display: none;">
                            <img src="" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>
                        <p class="upload-title">Surat Kuasa (Jika Ada)</p>
                        <p class="upload-subtitle">Pilih file atau drag &amp; drop</p>
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
@push('scripts')
    <script src="{{ asset('js/customer/form_pembangunan_script.js') }}"></script>
@endpush
