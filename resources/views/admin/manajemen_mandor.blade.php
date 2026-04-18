@extends('admin.admin_page')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/mandor_management.css') }}">
@endpush
@section('title')
    Admin - Manajemen Mandor
@endsection
@section('header')
    <h2> Manajemen Mandor</h2>
    <p> Kelola sumber daya lapangan Anda. Berikan
        penugasan proyek kepada mandor yang tersedia untuk efisiensi konstruksi maksimal.</p>
@endsection
@section('stats')
    <!-- Stats Overview (Asymmetric Layout) -->
    <div class="mandor-stat-card">
        <p class="mandor-stat-label">Total Mandor</p>
        <p class="mandor-stat-value">3</p>
    </div>
    <div class="mandor-stat-card mandor-stat-available">
        <p class="mandor-stat-label">Tersedia</p>
        <p class="mandor-stat-value">2</p>
    </div>
    <div class="mandor-stat-card mandor-stat-busy">
        <p class="mandor-stat-label">Sedang Bertugas</p>
        <p class="mandor-stat-value">1</p>
    </div>
@endsection
@section('content')
    <div class="mandor-container">
        <div class="mandor-list-section">
            <div class="mandor-list-header">
                <h3 class="mandor-list-title">Daftar Mandor Lapangan</h3>
                <div class="mandor-search">
                    <span class="material-symbols-outlined mandor-search-icon">search</span>
                    <input class="mandor-search-input" placeholder="Cari mandor atau proyek..." type="text" />
                </div>
            </div>
            <!-- Column List Entries -->
            <div class="mandor-entries">
                <!-- Entry 1: Available -->
                <div class="mandor-entry">
                    <div class="mandor-entry-info">
                        <div class="mandor-entry-avatar-container">
                            <img alt="Mandor Asep" class="mandor-entry-avatar"
                                data-alt="close-up portrait of an experienced male foreman wearing a yellow safety helmet and reflective vest, smiling at the construction site"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD7A1MuON_Qj2f-eHF_WycFlDWvtE86MFj8m0OgbDPzvvKoD-mOKqRCeR65je0zRCfxLhkiPurrysyf0Z8Jre2LjnXXFNxxX3d43QpsRxqKJB4_DiIZhYcVEqtrFO5A9tyRqDTr7pnsfE2walg8EtC8y8gSkB3uMIbJa-1wNny8vmoUZXHNbD8cKN3AsLW4Ui8Xow5jwb7Pdfr3r_aAqppY1snMFnj_qgKHxVq9QE27pfj9-QA5bdTBerbLfOVt-MJfBjeIG3ZaKO8" />
                            <span class="mandor-entry-status-dot"></span>
                        </div>
                        <div class="mandor-entry-details">
                            <h4>Mandor Asep</h4>
                            <p>M-001</p>
                        </div>
                    </div>
                    <div class="mandor-entry-status">
                        <span class="mandor-status available">Available</span>
                    </div>
                    <div class="mandor-entry-actions">
                        <div class="mandor-entry-project">
                            <p class="mandor-entry-project-label">Proyek Saat Ini</p>
                            <p class="mandor-entry-project-name">-</p>
                        </div>
                        <button class="mandor-assign-btn" onclick="openDocModal()">Assign Proyek</button>
                    </div>
                </div>
                <!-- Entry 2: Sibuk -->
                <div class="mandor-entry busy">
                    <div class="mandor-entry-info">
                        <div class="mandor-entry-avatar-container">
                            <img alt="Mandor Bambang" class="mandor-entry-avatar"
                                data-alt="serious foreman looking at blueprints on a construction site with structural steel in the background"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCmr0B_PBCWvCQfXPhsmL-UU_rCMXRoMHpwfDtTzxPhWdlXT-nuflt3_EycPqNk7zVkvJajzfx_IiavKszjnAi1rRBYSZ5z8eUNRbrmx8QLJSpMX2Es0ShmVYbSEUTNUeNZKLp8nYf0vg704VFlePUzlfIQIVaWl7Q6b25lHRyZCDgzpbLzblWlKLc_eTOwySZAcH3iUl3mbRLq8XIHYkF9-OubxF-O4pgWU04A6S0vl9UjcpwR3EbN4KkY0fByEIhp6LjP3WwIrCM" />
                            <span class="mandor-entry-status-dot"></span>
                        </div>
                        <div class="mandor-entry-details">
                            <h4>Mandor Bambang</h4>
                            <p>M-002</p>
                        </div>
                    </div>
                    <div class="mandor-entry-status">
                        <span class="mandor-status busy">Busy</span>
                    </div>
                    <div class="mandor-entry-actions">
                        <div class="mandor-entry-project">
                            <p class="mandor-entry-project-label">Proyek Saat Ini</p>
                            <p class="mandor-entry-project-name">Skyline Apartment B</p>
                        </div>
                        <button class="mandor-assign-btn disabled" disabled onclick="openDocModal()">Assign Proyek</button>
                    </div>
                </div>
                <!-- Entry 3: Available -->
                <div class="mandor-entry">
                    <div class="mandor-entry-info">
                        <div class="mandor-entry-avatar-container">
                            <img alt="Mandor Cahyo" class="mandor-entry-avatar"
                                data-alt="middle-aged foreman with graying hair wearing a white hardhat, professional and friendly expression"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCAetfovVS1ZJHAWs-ixawUhYb7-C4xVMn2F0llSqAInpLDxKDKx63Z4kl8usel2lDrnstVyhCZblUbTN94fQpla61Bis9upgGd1U_tu3a6AoX58JXQ_AzVwBhRKE0bRX0l_OQuvew7HVXDLd9QMjDjNirTBqHkAyZGbwfGfb7KztoMEv4yvLhHVT9jmem_oXnuyIQ6s43YP99HGD9jBPSTwlwkL2zcxxvHUGGROaiv51ah5vm5fK2OxrLtro6qfVY_uckPzPzzY_4" />
                            <span class="mandor-entry-status-dot"></span>
                        </div>
                        <div class="mandor-entry-details">
                            <h4>Mandor Cahyo</h4>
                            <p>M-003</p>
                        </div>
                    </div>
                    <div class="mandor-entry-status">
                        <span class="mandor-status available">Available</span>
                    </div>
                    <div class="mandor-entry-actions">
                        <div class="mandor-entry-project">
                            <p class="mandor-entry-project-label">Proyek Saat Ini</p>
                            <p class="mandor-entry-project-name">-</p>
                        </div>
                        <button class="mandor-assign-btn" onclick="openDocModal()">Assign Proyek</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="list-proyek-modal" class="modal-overlay" style="display:none;">
        <div class="modal-container">
            <div class="modal-close-wrapper">
                <button class="modal-close-btn" onclick="closeDocModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <!-- Modal Content -->
            <div class="modal-content">
                <div class="proyek-sidebar">
                    <h3 class="list-title">LIST PROYEK</h3>
                    <div class="proyek-list">
                        <button class="proyek-item active">
                            <div class="project-card">
                                <div class="project-thumb">
                                    <img data-alt="Modern minimalist luxury villa architectural render with pool and large windows"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBOmakkHuGbG_1TkrEKAD6jv7YTJX19KBBhkOJ-ARVr8-bmLgt85IGzTMblCmesPT2MooxRKgWJ94s-apEdBOxNX4PFIBiy9Pvqm97DQsM3yi8Ag8gQ4zXh8a3FmbDYDpUwFoKKqhqpageAbc3zsWlgNxJqj6jrEQxWCV43OmRyOqGzDu2-c1E65085F2XM8-M8qLwNIRL1qI9QhgpxPuh_iCrUt3r2ESIXre975nrD8MocpMXZQj3cdjwPMMYKR82e58abiaR029c" />
                                </div>
                                <div>
                                    <p class="project-title">Luxury Villa Kemang</p>
                                    <div class="title-wrapper">
                                        <p class="project-id"><strong>ID:</strong> W2H-88120</p>
                                        <p class="project-status"><strong>STATUS:</strong> Menunggu Mandor</p>
                                    </div>

                                </div>
                            </div>
                        </button>
                        <button class="proyek-item">
                            <div class="project-card">
                                <div class="project-thumb">
                                    <img data-alt="Interior renovation showing clean white walls and modern wooden flooring installation"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBuNVBvmYcRrwT_4FkMAq8oCXrfbGS_1JKCli5GwNxB6h2KrS8HpZqbmVPoPb7bzuUstPGfklMXR-zjk2VVk6-umkTpjfB-wqJFdQt9j4mob73wL-dv5bwOtlTdEEY2BxmrDTulsCZF2YpqRU4wq5pgQTewgTIUKGagnZFFPfPnWHnxKOu0JP9CGx-CKo9kd9FBerUqYR_CYbtj5MxeJBcEnUl2hwTDLtlKO2Wd6Ie0gUnvDrx_0P-w926SgLCBfZkvFaWuYGyK2Q" />
                                </div>
                                <div>
                                    <p class="project-title">Apartment BSD Unit 4B</p>
                                    <div class="title-wrapper">
                                        <p class="project-id"><strong>ID:</strong> W2H-88121</p>
                                        <p class="project-status"><strong>STATUS:</strong> Menunggu Mandor</p>
                                    </div>

                                </div>
                            </div>
                        </button>
                        <button class="proyek-item">
                            <div class="project-card">
                                <div class="project-thumb">
                                    <img data-alt="Exterior glass facade of a modern contemporary house in an urban setting"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAyW-pl942y86jgmNtBnCZeiZVlJZ2KPGVY3JmXZIADJvqzIMFu_XKplUPpsnPZUsp2JLZufN8vwre68_J5KpB0l0O5RJfJfMmSo1BdgnXcPvTvNrhAkEzJqgFCJy8ba6FlXUjiWa-NfazjoWxyLd662S874y2qg-ddwKzhsLI17nDl8q-xwfC_i9p3-foKMXMPZi4Pt9kXk8IbbpVIyy-QFWMR2Q7CR5_KmSVVPvlUfLDxI_kcfEH8TbsozjmDnagBasTXp-qUuyM" />
                                </div>
                                <div>
                                    <p class="project-title">Contemporary Menteng House</p>
                                    <div class="title-wrapper">
                                        <p class="project-id"><strong>ID:</strong> W2H-88122</p>
                                        <p class="project-status"><strong>STATUS:</strong> Menunggu Mandor</p>
                                    </div>

                                </div>
                            </div>
                        </button>
                        <button class="proyek-item">
                            <div class="project-card">
                                <div class="project-thumb">
                                    <img data-alt="High-end modern kitchen renovation with marble islands and premium cabinetry"
                                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTU4ilWBnRUFOPx5o8iSaQFokfqSO9iCd-uOOssv-tTUE5L5MMOjFbRR8eHwNUdO-uMfD4q6bA6WgBiTKpCqC5PbkFO_oseSkGFpUkYQoZlIKlDAuqguKV8rdoutEwUrSROdtPpSt4DjRbgGTl3AwUTWW_NPzMxgG_gh9Oqv6bscxHGkkungopwnhG6p5i4JZwLzoc2qkKAwITsAH8rXds8CiVthNVB1lonzY0Gi_TOJ-JMj4EYuBVzI6qAS7jhTFobvQrv0yDCuU" />
                                </div>
                                <div>
                                    <p class="project-title">Rumah Kota Bandung</p>
                                    <div class="title-wrapper">
                                        <p class="project-id"><strong>ID:</strong> W2H-88123</p>
                                        <p class="project-status"><strong>STATUS:</strong> Menunggu Mandor</p>
                                    </div>

                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <div class="modal-footer-buttons">
                    <button class="modal-btn modal-btn-submit">Assign Mandor</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/admin/assign_mandor.js') }}"></script>
@endpush
