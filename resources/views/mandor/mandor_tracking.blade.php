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
                             <span class="mandor-reno-subtitle">{{ count($renovationData['photos'] ?? []) }}
                                 Attachments</span>
                         </div>
                         <div class="mandor-reno-gallery-grid">
                             @foreach ($renovationData['photos'] ?? [] as $photoUrl)
                                 <div class="mandor-reno-photo-card">
                                     <img class="mandor-reno-photo" src="{{ $photoUrl }}"
                                         alt="Foto kerusakan renovasi" />
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
                             <h2 class="mandor-reno-title">Analisis Mandor
                             </h2>
                         </div>
                         <div class="mandor-reno-analysis-wrap">
                             <div class="mandor-reno-textarea">
                                 {{ $renovationData['analisis'] ?? '-' }}
                             </div>
                         </div>
                     </section>

                    <section class="mandor-reno-card mandor-reno-card-upload"
                        data-proyek-id="{{ $renovationData['proyek_id'] ?? '' }}">
                    
                        <div class="mandor-reno-head-row">
                            <div class="mandor-reno-head">
                                <div class="mandor-reno-icon-wrap mandor-reno-icon-wrap-primary">
                                    <span class="material-symbols-outlined" data-icon="upload">upload</span>
                                </div>
                                <h2 class="mandor-reno-title">Dokumentasi Renovasi</h2>
                            </div>
                            <label class="mandor-reno-upload-btn" for="renovation-doc-input">
                                <span class="material-symbols-outlined">add_photo_alternate</span>
                                Pilih Foto
                            </label>
                            <input type="file" id="renovation-doc-input" class="mandor-reno-file-input"
                                accept="image/jpg,image/jpeg,image/png" multiple>
                        </div>
                    
                        <div class="mandor-reno-dropzone" id="renovation-dropzone"
                            role="button" tabindex="0" aria-label="Area unggah dokumentasi renovasi">
                            <span class="material-symbols-outlined mandor-reno-dropzone-icon">cloud_upload</span>
                            <p class="mandor-reno-dropzone-title">Drag & Drop</p>
                            <p class="mandor-reno-dropzone-hint">Format JPG, PNG. Maks 5MB per foto</p>
                        </div>
                    
                        <p class="mandor-reno-dropzone-error" id="renovation-dropzone-error" aria-live="polite"></p>
                    
                        <div class="mandor-reno-preview-grid" id="renovation-preview-grid" aria-live="polite">
                            @foreach ($proyekRenovasi?->dokumentasi ?? [] as $dok)
                                <div class="mandor-doc-photo-card">
                                    <img src="{{ route('mandor.dokumentasi.url', $dok->id) }}"
                                        alt="Dokumentasi renovasi"
                                        class="mandor-doc-photo"
                                        loading="lazy">
                                    <div class="mandor-doc-overlay">
                                        <span class="mandor-doc-caption">{{ $dok->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            @endforeach
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
                     {{ $proyek->detailBangun?->desainRumah?->tipe_rumah ?? $proyek->jenis_proyek }}
                 </h1>
                 <div class="mandor-project-meta-row">
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">person</span>
                         <span>{{ $proyek->customer?->user?->name ?? '-' }}</span>
                     </div>
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">call</span>
                         <span>{{ $proyek->customer?->user?->phone_number ?? '-' }}</span>
                     </div>
                     <div class="mandor-project-meta-pill">
                         <span class="material-symbols-outlined mandor-icon-primary">location_on</span>
                         <span>{{ $proyek->alamat_proyek }}</span>
                     </div>
                 </div>
             </div>
             <div class="mandor-project-dates">
                 <div class="mandor-date-row mandor-date-row-divider">
                     <span class="mandor-date-label">Tanggal Mulai</span>
                     <span class="mandor-date-value">
                         {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d M Y') : '-' }}
                     </span>
                 </div>
                 <div class="mandor-date-row">
                     <span class="mandor-date-label">Estimasi Selesai</span>
                     <span class="mandor-date-value">
                         {{ $proyek->detailBangun?->desainRumah?->estimasi_durasi && $proyek->tanggal_mulai
                             ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->addMonths($proyek->detailBangun->desainRumah->estimasi_durasi)->format('d M Y')
                             : '-' }}
                     </span>
                 </div>
             </div>
         </header>

         <div class="mandor-main-grid">
             <section class="mandor-progress-column">
                 <div class="mandor-progress-card">
                     <div class="mandor-progress-bg-shape"></div>
                     <div class="mandor-progress-head">
                         <div>
                             <p class="mandor-progress-kicker">Overall Progress</p>
                             <h2 class="mandor-progress-title">Proyek Pembangunan</h2>
                         </div>
                         <span class="mandor-progress-number" id="persentase-text">{{ $persentase }}%</span>
                     </div>
                     <div class="mandor-progress-track">
                         <div class="mandor-progress-fill" id="persentase-fill" style="width: {{ $persentase }}%">
                         </div>
                     </div>
                     <div class="mandor-current-milestone">
                         <div class="mandor-current-milestone-icon-wrap">
                             <span class="material-symbols-outlined mandor-icon-tertiary">engineering</span>
                         </div>
                         <div>
                             <p class="mandor-current-milestone-label">Milestone Saat Ini</p>
                             <h3 class="mandor-current-milestone-title" id="milestone-text">{{ $milestoneAktif }}</h3>
                         </div>
                     </div>
                 </div>

                 <div class="mandor-task-block">
                     <h3 class="mandor-task-heading">
                         <span class="material-symbols-outlined mandor-icon-primary-container">assignment</span>
                         Task List
                     </h3>
                     <div class="mandor-task-list" id="task-list">
                         @foreach ($proyek->tasks->sortBy('is_selesai') as $task)
                             <div class="mandor-task-item {{ $task->is_selesai ? 'completed' : '' }}"
                                 id="task-{{ $task->id }}" data-milestone="{{ $task->milestone }}"
                                 style="{{ $loop->index >= 3 ? 'display:none' : '' }}">
                                 <div class="mandor-task-item-left">
                                     <span class="material-symbols-outlined mandor-icon-primary-container">
                                         {{ $task->is_selesai ? 'check_circle' : 'radio_button_unchecked' }}
                                     </span>
                                     <span class="mandor-task-item-text">{{ $task->nama_task }}</span>
                                 </div>
                                 @if (!$task->is_selesai)
                                     @if ($task->milestone === $milestoneAktif)
                                         <button class="mandor-complete-btn"
                                             onclick="completeTask({{ $task->id }}, this)">
                                             Complete
                                         </button>
                                     @else
                                         <button class="mandor-complete-btn" disabled>
                                             Complete
                                         </button>
                                     @endif
                                 @else
                                     <span class="mandor-task-done-label">Done</span>
                                 @endif
                             </div>
                         @endforeach
                     </div>
                     @if ($proyek->tasks->count() > 3)
                         <button class="mandor-show-more-btn" id="show-more-btn" onclick="toggleShowMore()">
                             <span class="material-symbols-outlined">expand_more</span>
                             Lihat Semua Task ({{ $proyek->tasks->count() }})
                         </button>
                     @endif
                 </div>
             </section>

             <section class="mandor-activity-column">
                 <div class="mandor-activity-card">
                     <h3 class="mandor-activity-heading">Aktivitas Sebelumnya</h3>
                     <div class="mandor-activity-list" id="aktivitas-list">
                         @forelse ($proyek->aktivitas as $aktivitas)
                             <div class="mandor-activity-item">
                                 <div class="mandor-activity-bar mandor-activity-bar-active"></div>
                                 <div>
                                     <p class="mandor-activity-title">{{ $aktivitas->judul }}</p>
                                     <p class="mandor-activity-desc">{{ $aktivitas->deskripsi }}</p>
                                 </div>
                             </div>
                         @empty
                             <p class="mandor-activity-empty">Belum ada aktivitas.</p>
                         @endforelse
                     </div>

                     <div class="mandor-new-activity">
                         <h4 class="mandor-new-activity-title">Tulis Aktivitas Baru</h4>
                         <div class="mandor-new-activity-fields">
                             <input class="mandor-input" id="input-judul" placeholder="Judul Aktivitas"
                                 type="text" />
                             <textarea class="mandor-textarea" id="input-deskripsi" placeholder="Isi Aktivitas" rows="3"></textarea>
                         </div>
                         <button class="mandor-add-activity-btn" onclick="tambahAktivitas({{ $proyek->id }})">
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
                 <label class="mandor-upload-btn" for="input-foto">
                     <span class="material-symbols-outlined">upload</span>
                     Unggah Foto
                 </label>
                 <input type="file" id="input-foto" accept="image/*" style="display:none"
                     onchange="uploadDokumentasi({{ $proyek->id }}, this)">
             </div>
             <div class="mandor-docs-grid" id="dokumentasi-grid">
                 <label class="mandor-upload-placeholder" for="input-foto">
                     <span class="material-symbols-outlined mandor-upload-icon">add_a_photo</span>
                     <span class="mandor-upload-text">Upload Foto Baru</span>
                     <p class="mandor-upload-hint">Format JPG, PNG (Max 5MB)</p>
                 </label>
                @foreach ($proyek->dokumentasi as $dok)
                    <div class="mandor-doc-photo-card">
                        <img src="{{ route('mandor.dokumentasi.url', $dok->id) }}" 
                            alt="Dokumentasi" class="mandor-doc-photo">
                        <div class="mandor-doc-overlay">
                            <span class="mandor-doc-caption">{{ $dok->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                @endforeach
             </div>
         </section>
     @endif
     </section>
 @endsection

 @push('scripts')
     <script src="{{ asset('js/mandor/tracking_proyek.js') }}"></script>
 @endpush
