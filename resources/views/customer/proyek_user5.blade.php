@extends('customer.proyek_user_layouts')
@section('content')
    <!-- Left Column: Primary Project Card -->
    <div class="project-main">
        <section class="project-card">
            <div class="project-image-container">
                <img alt="Modern Villa Kemang" class="project-image"
                    data-alt="Contemporary luxury villa with large glass windows and minimalist white concrete architecture at dusk, surrounded by manicured lawn"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuA4ksDLDJyMvJlK1qi7FzNco1Xyp9nYVZJ7PcWeGMkIsPgGuo_SBxRjgQoXakT0ylXK-dclr4jD34smKE5vmSiIFTPD6oX93Z83QPeGqhn-x7-GHUG78hQW7ONCkPGcMtN9qyte3VI2roScfGgrqc0txkZkdMGlQwMN5P3O9Sfh2SssyYnaFZb_O1HNABQZO5vWNE3i8zsIhUoNLdh4uZ66a2luxvzxbgdegN0lso2FMcTL5fCx4B9r8mZ3gsV0gnad4KLn02SUj5w"
                    style="" />
                <div class="project-image-overlay"></div>
                <div class="project-header">
                    <h2>Rumah Minimalist Jatinangor</h2>
                    <p class="project-location">
                        <span class="material-symbols-outlined" style="">location_on</span>
                        Jatinangor, Sumedang
                    </p>
                </div>
                <div class="project-status verified">
                    <span class="status-badge verified">
                        <span class="material-symbols-outlined" style='font-variation-settings: "FILL" 1;'>check</span>
                        Terverifikasi
                    </span>
                </div>
            </div>
            <div class="project-content">
                <div class="info-grid">
                    <div class="info-item">
                        <p class="info-label">Nama Desain</p>
                        <p class="info-value">TMinimalist Nangorian</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Budget</p>
                        <p class="info-value">Rp 450.000.000</p>
                    </div>
                    <div class="info-item">
                        <p class="info-label">Estimasi Waktu</p>
                        <p class="info-value">6 Bulan</p>
                    </div>
                </div>
                <!-- Information Section -->
                <!-- di information-container ada state verified, revision, sama pending -->
                <div class="information-container verified">
                    <div class="information-icon-box verified">
                        <span class="material-symbols-outlined">check</span>
                    </div>
                    <div class="information-content verified">
                        <h3>Proyek Aktif</h3>
                        <p>Proyek sedang aktif dan progress dapat dilacak. Terima kasih telah menggunakan layanan <strong
                                style="color: #004796">Way2Home</strong></p>
                    </div>
                    <button class="btn-upload" style="display: none">
                        <span class="material-symbols-outlined">upload_file</span>
                        Upload Ulang
                    </button>
                </div>
                <!-- Action Buttons -->
                <div class="button-group">
                    <button class="btn-action btn-cancel" id="cancelBtn">
                        <span class="material-symbols-outlined">cancel</span>
                        Batalkan Proyek
                    </button>
                    <!-- button ini ada state 'disabled' biar gabisa diklik kalo user blm terverifikasi dok nya!-->
                    @php
                        $sudahBayar = true; // ceritanya udh bayar
                        $statusDokumen = 'approved';
                        $isMandor = true; // udh diassign juga sm admin, user baru bisa klik tombol pantau progress
                    @endphp

                    @if (!$sudahBayar)
                        {{-- Tampilan sebelum bayar --}}
                        <button class="btn-action btn-payments-action" id="dpBtn"
                            {{ $statusDokumen !== 'approved' ? 'disabled' : '' }}>
                            <span class="material-symbols-outlined">payments</span>
                            Bayar DP
                        </button>
                    @else
                        {{-- Tampilan sesudah bayar --}}
                        {{-- Kita kirim status mandor ke JS lewat data-attribute --}}
                        <button class="btn-action btn-progress-action" id="progressBtn"
                            data-mandor="{{ $isMandor ? 'true' : 'false' }}">
                            <span class="material-symbols-outlined">analytics</span>
                            Pantau Progress
                        </button>
                    @endif
                </div>
            </div>
        </section>
    </div>
    <!-- Right Column: Info & Status -->
    <div class="project-sidebar">
        <!-- Info Box -->
        <div class="info-box-gradient">
            <div class="info-box-content">
                <span class="material-symbols-outlined info-box-icon">lock_clock</span>
                <h3 class="info-box-title">Proses Pembayaran</h3>
                <p class="info-box-text">
                    Keamanan Anda adalah prioritas kami. Pembayaran Down Payment (DP) hanya dapat
                    dilakukan setelah seluruh dokumen administrasi Anda dinyatakan <b>Disetujui</b> oleh
                    tim verifikator kami.
                </p>
            </div>
            <!-- Decorative background element -->
            <div class="info-box-decoration">
                <span class="material-symbols-outlined" style="">verified_user</span>
            </div>
        </div>
        <!-- Progress Section -->
        <section class="milestone-section">
            <h3 class="milestone-title">Milestone Project</h3>
            <div class="milestone-list">
                <div class="milestone-item">
                    <!-- Milestone-icon ada state in-progress, completed, sama pending !-->
                    <!-- in-progress: user lagi ada di situ, completed: udah kelar, pending: user blm di situ (masi di atas) !-->
                    <!-- Milestone-line ada state ada inactive (berarti dari posisi itu, belum otw ke bawahnya), kalo proses udah selesai
                                                                                                                                                                                                                misalkan dokumen udah berhasil diverifikasi, nah itu berarti inactive
                                                                                                                                                                                                                nya diilangin biar jadi milestone-line aja, jangan dikasih inactive !-->
                    <div class="milestone-timeline">
                        <div class="milestone-icon completed">
                            <span class="material-symbols-outlined" style='font-variation-settings: "FILL" 1;'>check</span>
                        </div>
                        <div class="milestone-line"></div>
                    </div>
                    <div class="milestone-content">
                        <p class="milestone-label">Pengajuan</p>
                        <p class="milestone-date">12 Okt 2023</p>
                    </div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-timeline">
                        <div class="milestone-icon">
                            <span class="material-symbols-outlined" style="">check</span>
                        </div>
                        <div class="milestone-line"></div>
                    </div>
                    <div class="milestone-content">
                        <p class="milestone-label">Verifikasi Dokumen</p>
                        <p class="milestone-status pending">Berhasil direview</p>
                    </div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-timeline">
                        <div class="milestone-icon completed">
                            <span class="material-symbols-outlined" style="">check</span>
                        </div>
                        <!-- contoh ini, kan user udah bayar, maka nya ilang inactivenya !-->
                        <div class="milestone-line"></div>
                    </div>
                    <div class="milestone-content">
                        <p class="milestone-label">Pembayaran DP</p>
                        <p class="milestone-date">Pembayaran Berhasil</p>
                    </div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-timeline">
                        <div class="milestone-icon completed">
                            <span class="material-symbols-outlined" style="">check</span>
                        </div>
                        <div class="milestone-line"></div>
                    </div>
                    <div class="milestone-content">
                        <p class="milestone-label">Pengalokasian Mandor</p>
                        <p class="milestone-date">Mandor dialokasikan</p>
                    </div>
                </div>
                <div class="milestone-item">
                    <div class="milestone-timeline">
                        <div class="milestone-icon completed">
                            <span class="material-symbols-outlined" style="">start</span>
                        </div>
                    </div>
                    <div class="milestone-content">
                        <p class="milestone-label">Proyek Dibuat</p>
                        <p class="milestone-date">Pantau progress proyek Anda</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Support Card -->
        <div class="support-card">
            <div class="support-content">
                <div class="support-icon">
                    <span class="material-symbols-outlined">support_agent</span>
                </div>
                <div class="support-text">
                    <h4>Butuh Bantuan?</h4>
                    <p>Hubungi Admin Kami</p>
                </div>
            </div>
            <span class="material-symbols-outlined support-chevron">chevron_right</span>
        </div>
    </div>
@endsection()
