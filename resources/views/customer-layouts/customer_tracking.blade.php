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
        <div class="tracking-grid tracking-grid-hero">
            <div class="tracking-card tracking-hero-card">
                <div class="tracking-hero-blur"></div>
                <div class="tracking-hero-content">
                    <div class="tracking-hero-header">
                        <div>
                            <span class="tracking-kicker">Proyek</span>
                            <h1 class="tracking-page-title">Rumah Modern Minimalist Bandung</h1>
                        </div>
                        <span class="tracking-status-badge tracking-status-badge-active">Proyek Aktif</span>
                    </div>

                    <div class="tracking-metrics">
                        <div class="tracking-metric">
                            <p class="tracking-label">Tanggal Mulai</p>
                            <p class="tracking-value">12 Oktober 2023</p>
                        </div>
                        <div class="tracking-metric">
                            <p class="tracking-label">Estimasi Selesai</p>
                            <p class="tracking-value">28 Agustus 2024</p>
                        </div>
                        <div class="tracking-metric tracking-metric-progress">
                            <p class="tracking-label">Progress Pembangunan</p>
                            <div class="tracking-progress-row">
                                <div class="tracking-progress-track">
                                    <div class="tracking-progress-fill"></div>
                                </div>
                                <span class="tracking-progress-text">50%</span>
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
                            <p class="tracking-meta-value">Struktur</p>
                        </div>
                    </div>
                    <div class="tracking-highlight-card">
                        <span class="material-symbols-outlined tracking-icon-xl tracking-icon-primary"
                            style="font-variation-settings: 'FILL' 1;">calendar_today</span>
                        <div>
                            <p class="tracking-meta-label">Pengerjaan Saat Ini</p>
                            <p class="tracking-meta-value">Pemasangan Atap</p>
                        </div>
                    </div>
                    <div class="tracking-highlight-card">
                        <span class="material-symbols-outlined tracking-icon-xl tracking-icon-primary"
                            style="font-variation-settings: 'FILL' 1;">foundation</span>
                        <div>
                            <p class="tracking-meta-label">Milestone Berikutnya</p>
                            <p class="tracking-meta-value">Instalasi MEP</p>
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
                                data-alt="professional male project manager with a calm expression wearing a navy polo shirt"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDCmXrJn6unRVBl_IqApiWBt273puoByO6mlWpIcr113y09EyWIOUyrCdAFmRmsFrkxswDOId9qDG5VFZ6t5tm5zcMaFRIyGM09yWvH9EJP-WNFtsGHoOP2b_FV8WCE2C4T65jpeA_iQNTjJNG1wfxCKcaF64SiUg8VyaYWgZV9M02-4MiQ1YmqHtH47k2-hHJ9cijKtyVqkvxRUYrlIq0fCDlmhBBNinUH4IIYs04t7GAyw2cIhu6JRA8rBgrMkXy1UaPqVBj-6Js" />
                        </div>
                        <div>
                            <p class="tracking-contact-name">Asep Jalaluddin</p>
                            <p class="tracking-contact-role">Mandor Proyek</p>
                        </div>
                        <button
                            class="tracking-contact-chat material-symbols-outlined tracking-icon-md">chat_bubble</button>
                    </div>
                    <div class="tracking-contact-item">
                        <div class="tracking-avatar-wrap">
                            <img alt="Lead Architect" class="tracking-avatar-image"
                                data-alt="female lead architect with stylish spectacles looking confidently at the camera"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDJA7JPAFI6ngjwmoFd6r7Yu2swUdR5t5sL_UCSZeKMxTC3nclnC07toxlq30o3BEd4q96HMaDK8wbHbE64tGe7iYP-YlmSC1Jrg8FHB0ggUUP10YKPnPntju5u-TpZyt_79JpNy7uuCiG5O1VbGLP_xqCnWs-emGL3WbMgvmDOsu7mfbhBv-qVosTFuoo_txYKPxRNMkofIFQfd7FElZ9NizwjdpoYWO-6dTPezw0CeH_gt61mzczLo1nlL89wyvQ-znZxS-8itDk" />
                        </div>
                        <div>
                            <p class="tracking-contact-name">Admin</p>
                            <p class="tracking-contact-role">Admin Utama</p>
                        </div>
                        <button
                            class="tracking-contact-chat material-symbols-outlined tracking-icon-md">chat_bubble</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="tracking-grid tracking-grid-middle">
            <div class="tracking-card tracking-activity-card">
                <h2 class="tracking-section-title">Aktivitas</h2>
                <div class="tracking-timeline">
                    <div class="tracking-timeline-line"></div>

                    <div class="tracking-timeline-item">
                        <div class="tracking-timeline-dot tracking-timeline-dot-active"></div>
                        <div>
                            <p class="tracking-timeline-date">14 April 2024</p>
                            <h4 class="tracking-timeline-title">Pengerjaan Rangka Atap</h4>
                            <p class="tracking-timeline-desc">Memasang rangka atap bagian kamar, ruang tengah, dan kamar
                                mandi.</p>
                        </div>
                    </div>

                    <div class="tracking-timeline-item">
                        <div class="tracking-timeline-dot tracking-timeline-dot-inactive"></div>
                        <div>
                            <p class="tracking-timeline-date">10 April 2024</p>
                            <h4 class="tracking-timeline-title">Pengiriman Material Atap</h4>
                            <p class="tracking-timeline-desc">Pengiriman atap dari gudang sampai di tempat pengerjaan proyek
                                dan siap dipakai</p>
                        </div>
                    </div>

                    <div class="tracking-timeline-item tracking-timeline-item-last">
                        <div class="tracking-timeline-dot tracking-timeline-dot-inactive"></div>
                        <div>
                            <p class="tracking-timeline-date">1 Maret 2024</p>
                            <h4 class="tracking-timeline-title">Pembangunan Struktur Selesai</h4>
                            <p class="tracking-timeline-desc">Struktur rumah selesai, seperti tiang penyangga, bla bla bla.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tracking-card tracking-milestone-card">
                <h2 class="tracking-section-title">Milestone Pencapaian</h2>
                <div class="tracking-milestone-wrap">
                    <div class="tracking-milestone-connector"></div>
                    <div class="tracking-milestone-grid">
                        <div class="tracking-milestone-item">
                            <div class="tracking-milestone-icon tracking-milestone-icon-complete">
                                <span class="material-symbols-outlined tracking-icon-sm"
                                    style="font-variation-settings: 'FILL' 1;">check</span>
                            </div>
                            <div>
                                <p class="tracking-milestone-name">Fondasi</p>
                                <span class="tracking-milestone-state tracking-state-muted">Selesai</span>
                            </div>
                        </div>
                        <div class="tracking-milestone-item">
                            <div class="tracking-milestone-icon tracking-milestone-icon-complete">
                                <span class="material-symbols-outlined tracking-icon-sm"
                                    style="font-variation-settings: 'FILL' 1;">check</span>
                            </div>
                            <div>
                                <p class="tracking-milestone-name">Struktur</p>
                                <span class="tracking-milestone-state tracking-state-muted">Selesai</span>
                            </div>
                        </div>
                        <div class="tracking-milestone-item">
                            <div class="tracking-milestone-icon tracking-milestone-icon-active">
                                <span class="tracking-milestone-dot"></span>
                            </div>
                            <div>
                                <p class="tracking-milestone-name">Atap</p>
                                <span class="tracking-milestone-state tracking-state-primary">In Progress</span>
                            </div>
                        </div>
                        <div class="tracking-milestone-item">
                            <div class="tracking-milestone-icon tracking-milestone-icon-pending">
                                <span class="material-symbols-outlined tracking-icon-sm">hourglass_empty</span>
                            </div>
                            <div>
                                <p class="tracking-milestone-name tracking-state-pending">MEP</p>
                                <span class="tracking-milestone-state tracking-state-pending">Pending</span>
                            </div>
                        </div>
                        <div class="tracking-milestone-item">
                            <div class="tracking-milestone-icon tracking-milestone-icon-pending">
                                <span class="material-symbols-outlined tracking-icon-sm">hourglass_empty</span>
                            </div>
                            <div>
                                <p class="tracking-milestone-name tracking-state-pending">Finishing</p>
                                <span class="tracking-milestone-state tracking-state-pending">Pending</span>
                            </div>
                        </div>
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
                            <tr class="tracking-subtable-row">
                                <td class="tracking-subtable-cell tracking-subtable-cell-strong">Rangka Atap</td>
                                <td class="tracking-subtable-cell tracking-subtable-cell-muted">-</td>
                                <td class="tracking-subtable-cell"><span class="tracking-pill tracking-pill-progress">On
                                        Progress</span></td>
                            </tr>
                            <tr class="tracking-subtable-row">
                                <td class="tracking-subtable-cell tracking-subtable-cell-strong">Pemasangan Genteng</td>
                                <td class="tracking-subtable-cell tracking-subtable-cell-muted">-</td>
                                <td class="tracking-subtable-cell"><span
                                        class="tracking-pill tracking-pill-progress">Menunggu</span></td>
                            </tr>
                            <tr>
                                <td class="tracking-subtable-cell tracking-subtable-cell-strong">Plafon & Lipslang</td>
                                <td class="tracking-subtable-cell tracking-subtable-cell-muted">-</td>
                                <td class="tracking-subtable-cell"><span
                                        class="tracking-pill tracking-pill-progress">Menunggu</span></td>
                            </tr>
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
                    <div class="tracking-doc-item">
                        <img alt="Construction Site" class="tracking-doc-image"
                            data-alt="construction workers pouring concrete for a large foundation slab at dawn with dramatic orange sky"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDQoJEqI8qrZxzjHMzg6kmtWDapqTnFZ_ztCqRoj0E-vAtxO21i8eWTVMNs_0CXVN3aSPRKCND7GnnCQtkLWPux7-6oNEz6avX6LL5f33lFSSshRG0wlQpYMY8zZViDkALnN7Grv-ve0_fW0JxEGM8xFD7WhuBiDEYhWU7jStjArbEZWAAzV8TpnxUpHoR2tmwnDw1Zv9crptLPWbgOHnXFvLkxnChS64KPsxIn4XrtKTt6jL0sQE8i1o6GScSyex3QjOSoFZL2mPI" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Architecture Detail" class="tracking-doc-image"
                            data-alt="low angle shot of modern geometric building architecture with sharp edges and clean white concrete walls"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuACz2ROLQvZ0aZ8oeZLelVa2tZBOwHjheogBvMOf9rPkSnJPLdaLjR3UapFUH8OUT6LahJBuBSMuZw_GQ9v3wK2DuPlOjAqXMKsQ7lBZStnLX4QNnx6v3zRKjZ2P5ZlLUvRgt8corGiZ55F7pkpb9WFAtfKNcg9FemucMYeJx9ZqAwk3dvWtltxoRs0dXNCBlPWNhDZ5ikQFjF9PhOxVNE3OBuy1qk_rSOkx1LnHqX9i0-TIiu_NfysyzgzpfmrSLE23L8xiUxeptA" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Framing Progress" class="tracking-doc-image"
                            data-alt="detailed view of precise wooden house framing with blue sky visible through the structural beams"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQhsLJxCfR7sXXy2fFMkaG2JyE9M-SU2LlrDP_l8im6PZNfCFbd4JZRN8mvybXY5TPn5uAOn4zdB_LcFIVZrdYr0UqbjuvkhMTSPjY0CrfBae9IikcOzCl8rDSQwiMLdp__w7y5Fi2J7t0IO2vYXy1-vDbfkkJ7N6aSG-u6cIKLlUdAmctk3ly4jfnJqT3irfSqHCfs8kHvZDt6nlBXTsAOsl75_UYQyFoYN1ZzQDgOi-nYVWj8PpXHN6m3yIYdACA6QDAPHNq3gQ" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Framing Progress" class="tracking-doc-image"
                            data-alt="detailed view of precise wooden house framing with blue sky visible through the structural beams"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQhsLJxCfR7sXXy2fFMkaG2JyE9M-SU2LlrDP_l8im6PZNfCFbd4JZRN8mvybXY5TPn5uAOn4zdB_LcFIVZrdYr0UqbjuvkhMTSPjY0CrfBae9IikcOzCl8rDSQwiMLdp__w7y5Fi2J7t0IO2vYXy1-vDbfkkJ7N6aSG-u6cIKLlUdAmctk3ly4jfnJqT3irfSqHCfs8kHvZDt6nlBXTsAOsl75_UYQyFoYN1ZzQDgOi-nYVWj8PpXHN6m3yIYdACA6QDAPHNq3gQ" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Framing Progress" class="tracking-doc-image"
                            data-alt="detailed view of precise wooden house framing with blue sky visible through the structural beams"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQhsLJxCfR7sXXy2fFMkaG2JyE9M-SU2LlrDP_l8im6PZNfCFbd4JZRN8mvybXY5TPn5uAOn4zdB_LcFIVZrdYr0UqbjuvkhMTSPjY0CrfBae9IikcOzCl8rDSQwiMLdp__w7y5Fi2J7t0IO2vYXy1-vDbfkkJ7N6aSG-u6cIKLlUdAmctk3ly4jfnJqT3irfSqHCfs8kHvZDt6nlBXTsAOsl75_UYQyFoYN1ZzQDgOi-nYVWj8PpXHN6m3yIYdACA6QDAPHNq3gQ" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Framing Progress" class="tracking-doc-image"
                            data-alt="detailed view of precise wooden house framing with blue sky visible through the structural beams"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQhsLJxCfR7sXXy2fFMkaG2JyE9M-SU2LlrDP_l8im6PZNfCFbd4JZRN8mvybXY5TPn5uAOn4zdB_LcFIVZrdYr0UqbjuvkhMTSPjY0CrfBae9IikcOzCl8rDSQwiMLdp__w7y5Fi2J7t0IO2vYXy1-vDbfkkJ7N6aSG-u6cIKLlUdAmctk3ly4jfnJqT3irfSqHCfs8kHvZDt6nlBXTsAOsl75_UYQyFoYN1ZzQDgOi-nYVWj8PpXHN6m3yIYdACA6QDAPHNq3gQ" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Framing Progress" class="tracking-doc-image"
                            data-alt="detailed view of precise wooden house framing with blue sky visible through the structural beams"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQhsLJxCfR7sXXy2fFMkaG2JyE9M-SU2LlrDP_l8im6PZNfCFbd4JZRN8mvybXY5TPn5uAOn4zdB_LcFIVZrdYr0UqbjuvkhMTSPjY0CrfBae9IikcOzCl8rDSQwiMLdp__w7y5Fi2J7t0IO2vYXy1-vDbfkkJ7N6aSG-u6cIKLlUdAmctk3ly4jfnJqT3irfSqHCfs8kHvZDt6nlBXTsAOsl75_UYQyFoYN1ZzQDgOi-nYVWj8PpXHN6m3yIYdACA6QDAPHNq3gQ" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                    <div class="tracking-doc-item">
                        <img alt="Framing Progress" class="tracking-doc-image"
                            data-alt="detailed view of precise wooden house framing with blue sky visible through the structural beams"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuBQhsLJxCfR7sXXy2fFMkaG2JyE9M-SU2LlrDP_l8im6PZNfCFbd4JZRN8mvybXY5TPn5uAOn4zdB_LcFIVZrdYr0UqbjuvkhMTSPjY0CrfBae9IikcOzCl8rDSQwiMLdp__w7y5Fi2J7t0IO2vYXy1-vDbfkkJ7N6aSG-u6cIKLlUdAmctk3ly4jfnJqT3irfSqHCfs8kHvZDt6nlBXTsAOsl75_UYQyFoYN1ZzQDgOi-nYVWj8PpXHN6m3yIYdACA6QDAPHNq3gQ" />
                        <div class="tracking-doc-overlay"><span
                                class="material-symbols-outlined tracking-doc-zoom">zoom_in</span></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
