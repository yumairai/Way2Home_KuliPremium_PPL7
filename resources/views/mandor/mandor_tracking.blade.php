 @extends('mandor.mandor_main')
 @push('styles')
     <link rel="stylesheet" href="{{ asset('css/mandor/mandor_tracking.css') }}" />
 @endpush
 @section('content')
     @if (!$isHaveProject && !$isHaveRenovation)
         <header class="mandor-project-head">
             <div class="mandor-project-main">
                 <nav class="mandor-project-breadcrumb">
                     <span class="material-symbols-outlined mandor-icon-sm">home_work</span>
                     <span class="mandor-project-label">Project Tidak Aktif</span>
                 </nav>
                 <h1 class="mandor-project-title">
                     Tidak Ada Proyek Aktif Saat Ini
                 </h1>
                 <p>Silahkan kontak admin untuk informasi lebih lanjut atau cek request renovasi customer.</p>
                 <a href="{{ route('mandor.dashboard') }}" class="mandor-request-btn">Request Renovasi</a>
             </div>
         </header>
     @elseif ($isHaveRenovation)
         <header class="mandor-project-head">
             <div class="mandor-project-main">
                 <nav class="mandor-project-breadcrumb">
                     <span class="material-symbols-outlined mandor-icon-sm">home_work</span>
                     <span class="mandor-project-label">Active Project</span>
                 </nav>
                 <h1 class="mandor-project-title">
                     Renovasi {{ $renovationData['request_id'] ?? '-' }}
                 </h1>
                 <div class="mandor-project-meta-row">
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">person</span>
                         <span>{{ $renovationData['customer_name'] ?? '-' }}</span>
                     </div>
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">call</span>
                         <span>{{ $renovationData['customer_phone'] ?? '-' }}</span>
                     </div>
                     <div class="mandor-project-meta-pill hightlight">
                         <span class="material-symbols-outlined mandor-icon-primary">payments</span>
                         <span>Biaya Renovasi: {{ $renovationData['biaya_renovasi'] ?? '-' }}</span>
                     </div>
                 </div>
             </div>
             <div class="mandor-project-dates">
                 <div class="mandor-date-row mandor-date-row-divider">
                     <span class="mandor-date-label">Tanggal Mulai</span>
                     <span class="mandor-date-value">{{ $renovationData['tanggal_mulai'] ?? '-' }}</span>
                 </div>
                 <p class="mandor-date-value">Harap komunikasi dengan klien untuk informasi lebih lanjut dan kenyamanan
                     renovasi.</p>
             </div>
         </header>
         @if (!$isAccepted)
             <h1 class="mandor-project-title">
                 Renovasi Masih Menunggu Persetujuan Klien.
             </h1>
             <p class="mandor-date-value">Mohon tunggu 1x24 jam atau hubungi klien terkait.</p>
         @else
             <section>
                 <div class="mandor-reno-grid">
                     <!-- Deskripsi Kerusakan User (Large Span) -->
                     <section class="mandor-reno-card mandor-reno-card-main">
                         <div class="mandor-reno-head">
                             <div class="mandor-reno-icon-wrap mandor-reno-icon-wrap-primary">
                                 <span class="material-symbols-outlined" data-icon="description">description</span>
                             </div>
                             <h2 class="mandor-reno-title">Deskripsi Kerusakan User</h2>
                         </div>
                          <div class="mandor-reno-body mandor-reno-description">
                             "{{ $renovationData['deskripsi'] ?? '-' }}"
                          </div>
                      </section>
                     <!-- Foto Kerusakan (Gallery Grid) -->
                     <section class="mandor-reno-card mandor-reno-card-gallery">
                         <div class="mandor-reno-head-row">
                             <div class="mandor-reno-head">
                                 <div class="mandor-reno-icon-wrap mandor-reno-icon-wrap-secondary">
                                     <span class="material-symbols-outlined" data-icon="photo_library">photo_library</span>
                                 </div>
                                 <h2 class="mandor-reno-title">Foto Kerusakan</h2>
                             </div>
                             <span class="mandor-reno-subtitle">{{ count($renovationData['photos'] ?? []) }} Attachments</span>
                          </div>
                          <div class="mandor-reno-gallery-grid">
                             @foreach ($renovationData['photos'] ?? [] as $photoUrl)
                                 <div class="mandor-reno-photo-card">
                                     <img class="mandor-reno-photo" src="{{ $photoUrl }}" alt="Foto kerusakan renovasi" />
                                     <div class="mandor-reno-photo-overlay">
                                         <p class="mandor-reno-photo-caption">Dokumentasi Customer</p>
                                     </div>
                                 </div>
                             @endforeach
                          </div>
                      </section>
                     <!-- Analisis Mandor (Technical Assessment) -->
                     <section class="mandor-reno-card mandor-reno-card-analysis">
                         <div class="mandor-reno-head">
                             <div class="mandor-reno-icon-wrap mandor-reno-icon-wrap-tertiary">
                                 <span class="material-symbols-outlined" data-icon="engineering">engineering</span>
                             </div>
                             <h2 class="mandor-reno-title">Analisis Mandor (Technical Assessment)
                             </h2>
                         </div>
                          <div class="mandor-reno-analysis-wrap">
                              <div class="mandor-reno-textarea">
                                 {{ $renovationData['analisis'] ?? '-' }}
                              </div>
                          </div>
                     </section>
                 </div>
                 <!-- Footer Action -->
                  <div class="mandor-reno-footer-action">
                      <div class="mandor-reno-btn-wrap">
                         <button class="mandor-reno-action-btn" id="mark-renovation-done-btn"
                             data-request-id="{{ $renovationData['request_db_id'] ?? '' }}">
                              Tandai Renovasi Selesai
                          </button>
                      </div>
                  </div>
              </section>
         @endif
     @else
         <header class="mandor-project-head">
             <div class="mandor-project-main">
                 <nav class="mandor-project-breadcrumb">
                     <span class="material-symbols-outlined mandor-icon-sm">home_work</span>
                     <span class="mandor-project-label">Active Project</span>
                 </nav>
                 <h1 class="mandor-project-title">
                     Modern Villa Kemang
                 </h1>
                 <div class="mandor-project-meta-row">
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">person</span>
                         <span>Budi Doremi</span>
                     </div>
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">call</span>
                         <span>+62 812-3456-7890</span>
                     </div>

                 </div>
             </div>

             <div class="mandor-project-dates">
                 <div class="mandor-date-row mandor-date-row-divider">
                     <span class="mandor-date-label">Tanggal Mulai</span>
                     <span class="mandor-date-value">12 Jan 2024</span>
                 </div>
                 <div class="mandor-date-row">
                     <span class="mandor-date-label">Estimasi Selesai</span>
                     <span class="mandor-date-value">15 Nov 2024</span>
                 </div>
             </div>
         </header>

         <div class="mandor-main-grid">
             <section class="mandor-progress-column">
                 <div class="mandor-progress-card">
                     <div class="mandor-progress-bg-shape"></div>
                     <div class="mandor-progress-head">
                         <div>
                             <p class="mandor-progress-kicker">Overall Progress
                             </p>
                             <h2 class="mandor-progress-title">Proyek Pembangunan</h2>
                         </div>
                         <span class="mandor-progress-number">50%</span>
                     </div>
                     <div class="mandor-progress-track">
                         <div class="mandor-progress-fill">
                         </div>
                     </div>
                     <div class="mandor-current-milestone">
                         <div class="mandor-current-milestone-icon-wrap">
                             <span class="material-symbols-outlined mandor-icon-tertiary">engineering</span>
                         </div>
                         <div>
                             <p class="mandor-current-milestone-label">Milestone Saat Ini</p>
                             <h3 class="mandor-current-milestone-title">Pekerjaan Struktur Lantai 1</h3>
                         </div>
                     </div>
                 </div>

                 <div class="mandor-task-block">
                     <h3 class="mandor-task-heading">
                         <span class="material-symbols-outlined mandor-icon-primary-container">assignment</span>
                         Task List
                     </h3>
                     <div class="mandor-task-list">
                         <div class="mandor-task-item">
                             <div class="mandor-task-item-left">
                                 <span class="material-symbols-outlined mandor-icon-primary-container"
                                     data-weight="fill">radio_button_unchecked</span>
                                 <span class="mandor-task-item-text">Pemasangan Bekisting Kolom</span>
                             </div>
                             <button class="mandor-complete-btn">Complete</button>
                         </div>
                         <div class="mandor-task-item">
                             <div class="mandor-task-item-left">
                                 <span class="material-symbols-outlined mandor-icon-primary-container"
                                     data-weight="fill">radio_button_unchecked</span>
                                 <span class="mandor-task-item-text">Fabrikasi Besi Tulangan Utama</span>
                             </div>
                             <button class="mandor-complete-btn">Complete</button>
                         </div>
                         <div class="mandor-task-item">
                             <div class="mandor-task-item-left">
                                 <span class="material-symbols-outlined mandor-icon-primary-container"
                                     data-weight="fill">radio_button_unchecked</span>
                                 <span class="mandor-task-item-text">Leveling Lantai Kerja</span>
                             </div>
                             <button class="mandor-complete-btn">Complete</button>
                         </div>
                     </div>
                 </div>
             </section>

             <section class="mandor-activity-column">
                 <div class="mandor-activity-card">
                     <h3 class="mandor-activity-heading">Aktivitas Sebelumnya</h3>
                     <div class="mandor-activity-list">
                         <div class="mandor-activity-item">
                             <div class="mandor-activity-bar mandor-activity-bar-active"></div>
                             <div>
                                 <p class="mandor-activity-title">Pengecoran Dak</p>
                                 <p class="mandor-activity-desc">Mengecor dak dalam rumah</p>
                             </div>
                         </div>
                         <div class="mandor-activity-item">
                             <div class="mandor-activity-bar"></div>
                             <div>
                                 <p class="mandor-activity-title">Material Bata Tiba</p>
                                 <p class="mandor-activity-desc">Persiapan bata untuk konstruksi
                                 </p>
                             </div>
                         </div>
                         <div class="mandor-activity-item">
                             <div class="mandor-activity-bar"></div>
                             <div>
                                 <p class="mandor-activity-title">Cek Instalasi Listrik</p>
                                 <p class="mandor-activity-desc">Memeriksa instalasi listrik di
                                     lokasi</p>
                             </div>
                         </div>
                     </div>

                     <div class="mandor-new-activity">
                         <h4 class="mandor-new-activity-title">Tulis Aktivitas Baru</h4>
                         <div class="mandor-new-activity-fields">
                             <input class="mandor-input" placeholder="Judul Aktivitas" type="text" />
                             <textarea class="mandor-textarea" placeholder="Isi Aktivitas" rows="3"></textarea>
                         </div>
                         <button class="mandor-add-activity-btn">
                             <span class="material-symbols-outlined">add_circle</span>
                             Tambah Aktivitas
                         </button>
                     </div>
                 </div>
             </section>
         </div>

         <section class="mandor-docs-section">
             <div class="mandor-docs-head">
                 <h2 class="mandor-docs-title">Dokumentasi Lapangan</h2>
                 <button class="mandor-upload-btn">
                     <span class="material-symbols-outlined">upload</span>
                     Unggah Foto
                 </button>
             </div>
             <div class="mandor-docs-grid">
                 <div class="mandor-upload-placeholder">
                     <span class="material-symbols-outlined mandor-upload-icon">add_a_photo</span>
                     <span class="mandor-upload-text">Upload Foto Baru</span>
                     <p class="mandor-upload-hint">Format JPG, PNG (Max 5MB)</p>
                 </div>

                 <div class="mandor-doc-photo-card">
                     <img src="{{ asset('images/aset/construction.jpg') }}" alt="Documentation 1"
                         class="mandor-doc-photo">

                     <div class="mandor-doc-overlay">
                         <span class="mandor-doc-caption">12 Mei 2024</span>
                     </div>
                 </div>

                 <div class="mandor-doc-photo-card">
                     <img src="{{ asset('images/aset/construction.jpg') }}" alt="Documentation 2"
                         class="mandor-doc-photo">

                     <div class="mandor-doc-overlay">
                         <span class="mandor-doc-caption">10 Mei 2024</span>
                     </div>
                 </div>

                 <div class="mandor-doc-photo-card">
                     <img src="{{ asset('images/aset/construction.jpg') }}" alt="Documentation 3"
                         class="mandor-doc-photo">

                     <div class="mandor-doc-overlay">
                         <span class="mandor-doc-caption">08 Mei 2024</span>
                     </div>
                 </div>
             </div>
     @endif
     </section>
 @endsection
 @push('scripts')
     <script>
         (function() {
             const doneButton = document.getElementById('mark-renovation-done-btn');
             const tokenMeta = document.querySelector('meta[name="csrf-token"]');
             const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

             if (!doneButton) {
                 return;
             }

             doneButton.addEventListener('click', async function() {
                 const requestId = doneButton.getAttribute('data-request-id');
                 if (!requestId) {
                     return;
                 }

                 const response = await fetch(`/mandor/renovation/${requestId}/done`, {
                     method: 'POST',
                     headers: {
                         'Accept': 'application/json',
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': csrfToken
                     },
                     body: JSON.stringify({})
                 });

                 const data = await response.json().catch(() => ({}));

                 if (!response.ok) {
                     alert(data.message || 'Gagal menandai renovasi selesai.');
                     return;
                 }

                 alert(data.message || 'Renovasi berhasil ditandai selesai.');
                 window.location.reload();
             });
         })();
     </script>
 @endpush
