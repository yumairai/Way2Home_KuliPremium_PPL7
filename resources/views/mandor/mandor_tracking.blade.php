 @extends('mandor.mandor_main')
 @push('styles')
     <link rel="stylesheet" href="{{ asset('css/mandor/mandor_tracking.css') }}" />
 @endpush
 @section('content')
     @php
         $isHaveProject = true; // Ubah ke false untuk simulasi kondisi tanpa proyek aktif
         $isHaveRenovation = false; // Ubah ke false untuk simulasi kondisi tanpa renovasi aktif
     @endphp
     @if (!$isHaveProject)
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
                         <span class="mandor-doc-caption">12 Mei 2024 - Pengecoran Deck</span>
                     </div>
                 </div>

                 <div class="mandor-doc-photo-card">
                     <img src="{{ asset('images/aset/construction.jpg') }}" alt="Documentation 2"
                         class="mandor-doc-photo">

                     <div class="mandor-doc-overlay">
                         <span class="mandor-doc-caption">10 Mei 2024 - Perakitan Besi</span>
                     </div>
                 </div>

                 <div class="mandor-doc-photo-card">
                     <img src="{{ asset('images/aset/construction.jpg') }}" alt="Documentation 3"
                         class="mandor-doc-photo">

                     <div class="mandor-doc-overlay">
                         <span class="mandor-doc-caption">08 Mei 2024 - Kedatangan Material</span>
                     </div>
                 </div>
             </div>
     @endif
     </section>
 @endsection
