<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pembangunan Rumah</title>
    <link rel="stylesheet" href="{{ asset('css/form_pembangunan_rumah.css') }}">
    <link href="{{ asset('images/aset/logo-w2h.png') }}" type="image" rel="icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- NAVBAR -->
    <nav class="glass-nav">
        <div class="nav-container">
            <!-- Brand -->
            <div class="brand">
                <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                <span class="brand-text">Way2Home</span>
            </div>
            <!-- link -->
            <div class="nav-links">
                <a href="#">Beranda</a>
                <a href="#">Desain</a>
                <a href="#">Material</a>
                <a href="#">Renovasi</a>
            </div>
            <!-- user bisa logout -->
            <div class="nav-actions">
                <button onclick="logout()" class="btn-nav primary">Logout</button>
            </div>
        </div>
    </nav>
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
                    <img alt="Rekomendasi Rumah" src="{{ asset('images/rekomendasi/rekom1.jpg') }}" />
                    <div class="design-badge">Desain Dipilih</div>
                </div>
                <div class="design-card-details">
                    <div class="detail-item">
                        <span class="detail-label">Desain 1</span>
                        <span class="detail-value">Modern Minimalist</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estimasi Biaya</span>
                        <span class="detail-value">Rp 400.000.000</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Area</span>
                        <span class="detail-value">50m²</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Estimasi Waktu</span>
                        <span class="detail-value">6 Bulan</span>
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
                <div class="material-card">
                    <div class="material-info">
                        <p>Semen Portland</p>
                        <p>250 Sak</p>
                    </div>
                </div>
                <div class="material-card">
                    <div class="material-info">
                        <p>Beton Ready Mix</p>
                        <p>45 m³</p>
                    </div>
                </div>
                <div class="material-card">
                    <div class="material-info">
                        <p>Pasir Pasang</p>
                        <p>30 m³</p>
                    </div>
                </div>
                <div class="material-card">
                    <div class="material-info">
                        <p>Batu Belah</p>
                        <p>20 m³</p>
                    </div>
                </div>
                <div class="material-card">
                    <div class="material-info">
                        <p>Kaca Polos 5mm</p>
                        <p>15 Lembar</p>
                    </div>
                </div>
                <div class="material-card">
                    <div class="material-info">
                        <p>Besi Beton (8/10mm)</p>
                        <p>120 Lonjor</p>
                    </div>
                </div>
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
            <input type="hidden" id="desain_id" value="1">

            <!-- Address Input -->
            <div class="form-group">
                <label class="form-label" id="label-alamat">Alamat Lengkap Proyek</label>
                <textarea id="alamatProyek" placeholder="Masukkan alamat lengkap di wilayah Jawa Barat" rows="3"></textarea>
            </div>
            <!-- dokumen kebutuhan pembangunan rumah -->
            <div class="form-group" id="sectionDokumen">
                <label class="form-label">Dokumen Pendukung</label>
                <div class="upload-grid">
                    <!-- dokumen sertif tanah -->
                    <label class="upload-item" for="sertifikat_tanah" id="drop-zone-sertifikat">
                        <input type="file" id="sertifikat_tanah" name="sertifikat_tanah"
                            accept=".jpg,.jpeg,.png,.pdf" class="file-input-hidden">

                        <!-- Preview -->
                        <div class="preview-container" style="display: none;">
                            <img src="" class="img-preview"
                                style="max-width: 100px; border-radius: 5px; margin-bottom: 10px;">
                        </div>

                        <p class="upload-title">Sertifikat Tanah (SHM/HGB)</p>
                        <p class="upload-subtitle">Pilih file atau drag & drop</p>
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
    <footer>
        <div class="footer-container">
            <div class="footer-brand">
                <div class="footer-brand-info">
                    <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                    <span class="footer-brand-name">Way2Home</span>
                </div>
                <p class="footer-brand-text">© 2026 Way2Home Construction
                    Platform. Architectural Excellence.</p>
            </div>
            <div class="footer-links">
                <a href="#">Tentang Kami</a>
                <a href="#">Proyek</a>
                <a href="#">Karir</a>
                <a href="#">Kontak</a>
                <a href="#">Privasi</a>
            </div>
            <div class="footer-actions">
                <div class="footer-icon-btn">
                    <img src="{{ asset('images/icon/whatsapp.png') }}" alt="WhatsApp">
                </div>
            </div>
        </div>
    </footer>
    <script>
        // Cek apakah ada token di localStorage
        const token = localStorage.getItem('token');

        if (!token) {
            alert('Kamu belum login! Balik ke halaman login ya.');
            window.location.href = '/login';
        } else {
            document.getElementById('tokenDisplay').innerText = token;
        }

        function logout() {
            localStorage.removeItem('token');
            window.location.href = '/login';
        }
    </script>
    <script src="{{ asset('js/form_pembangunan_script.js') }}"></script>
</body>

</html>
