@extends('customer-layouts.main')
@push('styles')
    <link href="{{ asset('css/customer/user_tracking.css') }}" rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Manrope:wght@200;300;400;500;600;700;800&amp;family=Plus+Jakarta+Sans:wght@300;400;500;600;700&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap"
        rel="stylesheet" />
@endpush
@section('content')
    <main class="tracking-page">
        @if ($proyek->status_proyek === 'Selesai')
            <div
                style="background-color: #ecfdf5; color: #065f46; padding: 16px 24px; border-radius: 12px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; border: 1px solid #a7f3d0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                <span class="material-symbols-outlined"
                    style="font-size: 32px; font-variation-settings: 'FILL' 1;">check_circle</span>
                <div>
                    <h3 style="margin: 0 0 4px 0; font-size: 1.1rem; font-weight: 700;">Proyek Telah Selesai </h3>
                    <p style="margin: 0; font-size: 0.95rem; opacity: 0.9;">Seluruh tahapan pembangunan rumah Anda telah
                        selesai dikerjakan. Anda masih dapat melihat riwayat dan dokumentasi proyek di halaman ini.</p>
                </div>
            </div>
        @endif

        <div class="tracking-grid tracking-grid-hero">
            <div class="tracking-card tracking-hero-card">
                <div class="tracking-hero-blur"></div>
                <div class="tracking-hero-content">
                    <div class="tracking-hero-header">
                        <div>
                            <span class="tracking-kicker">Proyek</span>
                            <h1 class="tracking-page-title">
                                {{ $proyek->detailBangun->desainRumah->tipe_rumah ?? 'Proyek Pembangunan' }}</h1>
                        </div>
                        <span class="tracking-status-badge tracking-status-badge-active">{{ $proyek->status_proyek }}</span>
                    </div>

                    <div class="tracking-metrics">
                        <div class="tracking-metric">
                            <p class="tracking-label">Tanggal Mulai</p>
                            <p class="tracking-value">
                                {{ $proyek->tanggal_mulai ? \Carbon\Carbon::parse($proyek->tanggal_mulai)->format('d F Y') : '-' }}
                            </p>
                        </div>
                        <div class="tracking-metric">
                            <p class="tracking-label">Estimasi Selesai</p>
                            <p class="tracking-value">{{ $estimasiSelesai ?? '-' }}</p>
                        </div>
                        <div class="tracking-metric tracking-metric-progress">
                            <p class="tracking-label">Progress Pembangunan</p>
                            <div class="tracking-progress-row">
                                <div class="tracking-progress-track">
                                    <div class="tracking-progress-fill" style="width: {{ $persentase }}%"></div>
                                </div>
                                <span class="tracking-progress-text">{{ $persentase }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tracking-highlights">
                    <div class="tracking-highlight-card">
                        <span class="material-symbols-outlined tracking-icon-xl tracking-icon-primary"
                            style="font-variation-settings: 'FILL' 1;">foundation</span>
                        <div>
                            <p class="tracking-meta-label">Milestone Tercapai</p>
                            <p class="tracking-meta-value">{{ $milestoneSelesai }}</p>
                        </div>
                    </div>
                    <div class="tracking-highlight-card">
                        <span class="material-symbols-outlined tracking-icon-xl tracking-icon-primary"
                            style="font-variation-settings: 'FILL' 1;">calendar_today</span>
                        <div>
                            <p class="tracking-meta-label">Pengerjaan Saat Ini</p>
                            <p class="tracking-meta-value">{{ $milestoneAktif }}</p>
                        </div>
                    </div>
                    <div class="tracking-highlight-card">
                        <span class="material-symbols-outlined tracking-icon-xl tracking-icon-primary"
                            style="font-variation-settings: 'FILL' 1;">foundation</span>
                        <div>
                            <p class="tracking-meta-label">Milestone Berikutnya</p>
                            <p class="tracking-meta-value">{{ $milestoneBerikutnya }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tracking-card tracking-contacts-card">
                <h2 class="tracking-section-title">Kontak Staff</h2>
                <div class="tracking-contacts-list">
                    <div class="tracking-contact-item">
                        <div class="tracking-avatar-wrap">
                            <img alt="Project Manager" class="tracking-avatar-image"
                                src="{{ isset($mandorContactAvatar) ? asset('storage/' . $mandorContactAvatar) : 'https://ui-avatars.com/api/?name=' . urlencode($mandorContactName ?? 'Mandor') }}" />
                        </div>
                        <div>
                            <p class="tracking-contact-name">{{ $mandorContactName }}</p>
                            <p class="tracking-contact-role">Mandor Proyek</p>
                            <p class="tracking-contact-number">{{ $mandorContactNumber ?? 'Nomor belum tersedia' }}</p>
                        </div>
                        @if ($mandorContactWaUrl)
                            <a class="tracking-contact-chat material-symbols-outlined tracking-icon-md"
                                href="{{ $mandorContactWaUrl }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Hubungi {{ $mandorContactName }} via WhatsApp">chat_bubble</a>
                        @else
                            <span class="tracking-contact-chat material-symbols-outlined tracking-icon-md"
                                aria-hidden="true">chat_bubble</span>
                        @endif
                    </div>
                    <div class="tracking-contact-item">
                        <div class="tracking-avatar-wrap">
                            <img alt="Lead Architect" class="tracking-avatar-image"
                                data-alt="female lead architect with stylish spectacles looking confidently at the camera"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJA7JPAFI6ngjwmoFd6r7Yu2swUdR5t5sL_UCSZeKMxTC3nclnC07toxlq30o3BEd4q96HMaDK8wbHbE64tGe7iYP-YlmSC1Jrg8FHB0ggUUP10YKPnPntju5u-TpZyt_79JpNy7uuCiG5O1VbGLP_xqCnWs-emGL3WbMgvmDOsu7mfbhBv-qVosTFuoo_txYKPxRNMkofIFQfd7FElZ9NizwjdpoYWO-6dTPezw0CeH_gt61mzczLo1nlL89wyvQ-znZxS-8itDk" />
                        </div>
                        <div>
                            <p class="tracking-contact-name">{{ $adminMainContactName }}</p>
                            <p class="tracking-contact-role">Admin Utama</p>
                            <p class="tracking-contact-number">{{ $adminMainContactNumber }}</p>
                        </div>
                        <a class="tracking-contact-chat material-symbols-outlined tracking-icon-md"
                            href="{{ $adminMainContactWaUrl }}" target="_blank" rel="noopener noreferrer"
                            aria-label="Hubungi {{ $adminMainContactName }} via WhatsApp">chat_bubble</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="tracking-grid tracking-grid-middle">
            <div class="tracking-card tracking-activity-card">
                <h2 class="tracking-section-title">Aktivitas</h2>
                <div class="tracking-timeline">
                    <div class="tracking-timeline-line"></div>

                    @forelse($proyek->aktivitas->sortByDesc('created_at')->values() as $index => $aktivitas)
                        <div class="tracking-timeline-item {{ $loop->last ? 'tracking-timeline-item-last' : '' }}">
                            <div
                                class="tracking-timeline-dot {{ $index === 0 ? 'tracking-timeline-dot-active' : 'tracking-timeline-dot-inactive' }}">
                            </div>
                            <div>
                                <p class="tracking-timeline-date">
                                    {{ \Carbon\Carbon::parse($aktivitas->created_at)->format('d F Y') }}</p>
                                <h4 class="tracking-timeline-title">{{ $aktivitas->judul }}</h4>
                                <p class="tracking-timeline-desc">{{ $aktivitas->deskripsi }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="tracking-timeline-item tracking-timeline-item-last">
                            <div>
                                <p class="tracking-timeline-desc">Belum ada aktivitas.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="tracking-card tracking-milestone-card">
                <h2 class="tracking-section-title">Milestone Pencapaian</h2>
                <div class="tracking-milestone-wrap">
                    <div class="tracking-milestone-connector"></div>
                    <div class="tracking-milestone-grid">
                        @foreach ($milestones as $milestone)
                            <div class="tracking-milestone-item">
                                @if ($milestone['status'] === 'completed')
                                    <div class="tracking-milestone-icon tracking-milestone-icon-complete">
                                        <span class="material-symbols-outlined tracking-icon-sm"
                                            style="font-variation-settings: 'FILL' 1;">check</span>
                                    </div>
                                    <div>
                                        <p class="tracking-milestone-name">{{ $milestone['nama'] }}</p>
                                        <span class="tracking-milestone-state tracking-state-muted">Selesai</span>
                                    </div>
                                @elseif($milestone['status'] === 'in-progress')
                                    <div class="tracking-milestone-icon tracking-milestone-icon-active">
                                        <span class="tracking-milestone-dot"></span>
                                    </div>
                                    <div>
                                        <p class="tracking-milestone-name">{{ $milestone['nama'] }}</p>
                                        <span class="tracking-milestone-state tracking-state-primary">In Progress</span>
                                    </div>
                                @else
                                    <div class="tracking-milestone-icon tracking-milestone-icon-pending">
                                        <span class="material-symbols-outlined tracking-icon-sm">hourglass_empty</span>
                                    </div>
                                    <div>
                                        <p class="tracking-milestone-name tracking-state-pending">{{ $milestone['nama'] }}
                                        </p>
                                        <span class="tracking-milestone-state tracking-state-pending">Pending</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="tracking-subtable-wrap">
                    <table class="tracking-subtable">
                        <thead>
                            <tr class="tracking-subtable-head-row">
                                <th class="tracking-subtable-head-cell">Sub Milestone</th>
                                <th class="tracking-subtable-head-cell">Tanggal Selesai</th>
                                <th class="tracking-subtable-head-cell">Status</th>
                                <th class="tracking-subtable-head-cell tracking-text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="tracking-subtable-body">
                            @forelse($proyek->tasks as $task)
                                <tr class="tracking-subtable-row">
                                    <td class="tracking-subtable-cell tracking-subtable-cell-strong">
                                        {{ $task->nama_task }}</td>
                                    <td class="tracking-subtable-cell tracking-subtable-cell-muted">
                                        {{ $task->updated_at && $task->is_selesai ? \Carbon\Carbon::parse($task->updated_at)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="tracking-subtable-cell">
                                        <span
                                            class="tracking-pill {{ $task->is_selesai ? 'tracking-pill-complete' : ($task->nama_task === $milestoneAktif ? 'tracking-pill-progress' : '') }}">
                                            {{ $task->is_selesai ? 'Selesai' : ($task->nama_task === $milestoneAktif ? 'On Progress' : 'Menunggu') }}
                                        </span>
                                    </td>
                                    <td class="tracking-subtable-cell tracking-text-right"></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="tracking-subtable-cell text-center"
                                        style="text-align: center; padding: 20px;">Belum ada task.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="tracking-grid tracking-grid-docs">
            <div class="tracking-card tracking-docs-card">
                <div class="tracking-docs-header">
                    <h2 class="tracking-section-title tracking-section-title-no-margin">Dokumentasi Pengerjaan Pembangunan
                    </h2>
                </div>
                <div class="tracking-docs-grid">
                    @forelse($proyek->dokumentasi as $doc)
                        <div class="tracking-doc-item">
                            <img alt="Dokumentasi Proyek" class="tracking-doc-image"
                                src="{{ route('proyek.dokumentasi.url', $doc->id) }}" />
                            <div class="tracking-doc-overlay">
                                <span class="material-symbols-outlined tracking-doc-zoom">zoom_in</span>
                            </div>
                        </div>
                    @empty
                        <div style="grid-column: 1 / -1; text-align: center; color: #666; padding: 20px;">
                            Belum ada dokumentasi pengerjaan pembangunan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
@endsection
