@extends('mandor.mandor_main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/mandor/mandor_dashboard.css') }}" />
@endpush
@section('content')
    <header class="dashboard-header">
        <h1 class="dashboard-title">Ringkasan Operasional</h1>
        <p class="dashboard-lead">
            Pantau projek anda dan kelola pengajuan renovasi. Dapatkan update real-time
            tentang status proyek dan permintaan klien untuk memastikan semua berjalan lancar.
        </p>
    </header>
    <section class="dashboard-stats">
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon dashboard-stat-icon-primary">
                <span class="material-symbols-outlined">architecture</span>
            </div>
            <div class="dashboard-stat-copy">
                <p class="dashboard-stat-label">Project Saat Ini</p>
                <p class="dashboard-stat-value">-</p>
            </div>
        </div>
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon dashboard-stat-icon-secondary">
                <span class="material-symbols-outlined">task_alt</span>
            </div>
            <div class="dashboard-stat-copy">
                <p class="dashboard-stat-label">Project Diselesaikan</p>
                <p class="dashboard-stat-value">18</p>
            </div>
        </div>
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon dashboard-stat-icon-tertiary">
                <span class="material-symbols-outlined">assignment_late</span>
            </div>
            <div class="dashboard-stat-copy">
                <p class="dashboard-stat-label">Request Renovasi</p>
                <p class="dashboard-stat-value">3</p>
            </div>
        </div>
    </section>
    <section class="dashboard-list-section">
        <div class="dashboard-section-head">
            <div>
                <h2 class="dashboard-section-title">List Pengajuan Renovasi</h2>
                <div class="dashboard-section-underline"></div>
            </div>
        </div>
        <div class="dashboard-request-list">
            <div class="dashboard-request-card dashboard-request-card-primary">
                <div class="dashboard-request-image-wrap">
                    <img alt="House exterior"
                        data-alt="modern suburban house with clean white walls and large glass windows under a bright clear sky"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDRQjsSuGY7sFnINqjqCk2CuPKNYq_ZUqJQ3xGhEiWD3J1jyKukvSNfRBeoHx6CKBrZbFVfdUn9OgNU8jEFbJm6AUPNWbfenrqqtb3DM30kOaWZ5ENH4LIW8ERkJ9J_iL44s7Skl7JobFWA3_hfuiQ3UubBF0BsgLvoWB9ZjDSvZT1DQ841EtkLGRXbRqnfUkDrd0o7ocPCJynNPoTm_bgFaatDSehVyHKL3ObnBd9lRNdGQyIbxbNNithOMOdatFvkMKrzpcIKsv0" />
                </div>
                <div class="dashboard-request-meta">
                    <div>
                        <p class="dashboard-meta-label">Nama Pengaju</p>
                        <p class="dashboard-meta-value">Bapak Budi</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">ID Pengajuan</p>
                        <p class="dashboard-meta-code">#REV-001</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">Estimasi Budget</p>
                        <p class="dashboard-meta-value">Rp 10.000.000</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">Nomor HP</p>
                        <p class="dashboard-meta-muted">0812-3456-7890</p>
                    </div>
                </div>
                <div class="dashboard-request-action">
                    <button class="dashboard-review-btn" type="button" data-review-open>
                        Review
                    </button>
                </div>
            </div>
            <div class="dashboard-request-card dashboard-request-card-soft">
                <div class="dashboard-request-image-wrap">
                    <img alt="Bathroom renovation"
                        data-alt="luxurious modern bathroom interior with marble tiles and warm ambient lighting setup"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBRmlvaYdaX1wa3G8rqM_kUgCiApnNszotMRIgfH3POOWMJ93JoFF5aNVoT-I-GZ11J41_4iwbYGHCshkJMewLjgXhamGNt5jX0tfzMDk8FwSvE-DQyULZSRkzcvWNcj2haVQePyUjTgHCc-fCxyFvrHNtYsfQI3xSlXZcgNG4dpGoGBtiOCYZOh_FcOz9dMp5ghpP6aWZ79EmkJJyhwfzJBGnRJxR7u57hGNpgCbrD_UuOmF8SxUBZc5SdVZ62zQzFgnVFB0yHmq4" />
                </div>
                <div class="dashboard-request-meta">
                    <div>
                        <p class="dashboard-meta-label">Nama Pengaju</p>
                        <p class="dashboard-meta-value">Ibu Pertiwi</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">ID Pengajuan</p>
                        <p class="dashboard-meta-code">#REV-002</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">Estimasi Budget</p>
                        <p class="dashboard-meta-value">Rp 12.000.000</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">Nomor HP</p>
                        <p class="dashboard-meta-muted">0813-8877-2233</p>
                    </div>
                </div>
                <div class="dashboard-request-action">
                    <button class="dashboard-review-btn" type="button" data-review-open>
                        Review
                    </button>
                </div>
            </div>
            <div class="dashboard-request-card dashboard-request-card-primary">
                <div class="dashboard-request-image-wrap">
                    <img alt="Kitchen model"
                        data-alt="contemporary kitchen design featuring minimalist cabinets and stainless steel appliances with wooden accents"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDuGYmotysQKy8vRpXddi5QIPU4NxcFBJ4MQOZbt67cMhnvb3WyOClfE5sQKWtdssjUPm0BDydIUkxiDy5bxMWhSjdfuZ2SVqOUqVhXVGzPRscToRdoanX5lPKwPAyCouY08Ks_xqi9F4VLnk4JaJv8imsUUnI5mb4bX15-Tg1jVAHJOeBaxLKBHsX7aUpUA_havyLD8sNDLooVvxZVv0sm0HYNFTsYazi-GEAsZoi0LeFRTZfi7RxndmhM37QP8_xY-M2nK6Xf8Hw" />
                </div>
                <div class="dashboard-request-meta">
                    <div>
                        <p class="dashboard-meta-label">Nama Pengaju</p>
                        <p class="dashboard-meta-value">Bapak Santoso</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">ID Pengajuan</p>
                        <p class="dashboard-meta-code">#REV-003</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">Estimasi Budget</p>
                        <p class="dashboard-meta-value">Rp 50.000.000</p>
                    </div>
                    <div>
                        <p class="dashboard-meta-label">Nomor HP</p>
                        <p class="dashboard-meta-muted">0856-1122-3344</p>
                    </div>
                </div>
                <div class="dashboard-request-action">
                    <button class="dashboard-review-btn" type="button" data-review-open>
                        Review
                    </button>
                </div>
            </div>
        </div>
    </section>
    <div class="dashboard-review-modal" id="dashboard-review-modal" aria-hidden="true" hidden>
        <div class="dashboard-review-backdrop" data-review-close></div>
        <div class="dashboard-review-dialog" role="dialog" aria-modal="true" aria-labelledby="dashboard-review-title">
            <div class="dashboard-review-header">
                <div>
                    <h2 class="dashboard-review-title" id="dashboard-review-title">Review Permintaan Proyek</h2>
                    <p class="dashboard-review-subtitle">Detail pengajuan renovasi dari klien.</p>
                </div>
                <button class="dashboard-review-close-btn material-symbols-outlined" type="button" aria-label="Close modal"
                    data-review-close>close</button>
            </div>

            <div class="dashboard-review-body">
                <div class="dashboard-review-summary">
                    <div class="dashboard-review-summary-item">
                        <span class="dashboard-review-label">Nama Pemohon</span>
                        <div class="dashboard-review-inline">
                            <span class="material-symbols-outlined dashboard-review-icon">person</span>
                            <span class="dashboard-review-value">Bapak Budi</span>
                        </div>
                    </div>
                    <div class="dashboard-review-summary-item">
                        <span class="dashboard-review-label">Lokasi Renovasi</span>
                        <div class="dashboard-review-inline">
                            <span class="material-symbols-outlined dashboard-review-icon">location_on</span>
                            <span class="dashboard-review-value dashboard-review-value-small">Jl. Melati No. 45, Kebayoran
                                Baru, Jakarta Selatan</span>
                        </div>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Deskripsi Kerusakan</span>
                    <div class="dashboard-review-note">
                        <p>
                            "Atap bocor di area ruang tamu dan plafon sudah mulai berjamur. Kerusakan terlihat semakin parah
                            saat hujan deras semalam. Kami membutuhkan pengecekan struktur rangka atap dan penggantian
                            genteng serta pengecatan ulang plafon."
                        </p>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Foto Kerusakan (6)</span>
                    <div class="dashboard-review-gallery">
                        <div class="dashboard-review-photo">
                            <img alt="Water damage on ceiling"
                                data-alt="close-up of water damage on a white residential ceiling with brown stains and peeling paint texture"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKy-WBtF2cXvx-2ZIOh_GQaNLqm_Jp1Bydvg_Er3aLAKCQRLhberhaMNbn5_ps03No6rcnfaXqU0xgwaTK1WX0LWFrRLxvJpqU6ZVHoIX6-3VOy0F-AkrXeenCL6YCgURZfNINNTzftOqaDxj72tXhQJmVP5XGS7yZaXdIkGFgbuXE3XLFJ8dDHN099gkTYBCv5talcYnI72Or42Z7_HE2lpmLYD57ANBitS7r9Qe5q3G48KI4DyK5s0vEj0Fq9rayKORvpkVSRdM" />
                        </div>
                        <div class="dashboard-review-photo">
                            <img alt="Attic roof damage"
                                data-alt="wide shot of a dark attic space with visible wooden rafters and daylight peeking through broken roof tiles"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCRaVEAR22EFM9p7v_tVw9pbobNSGVzzf50oArJPQUUt-kwWoSo0nm-VWVcjNMAouZpm1x4al5CVZlrdJ3S5Jp3PmXG4oX4t9yUSwvtvZJlLhQLBeiGJtVUjJAspiFR05aQfFxBPPz_C1P3kKa-UnJsUZdlnof9EWdYFqP9IRkDnGV6_OWZnnkX_dnGMI0pf5F7aLV5KpNNoBZPbLHXrCzsh-JWBTUgCB5gPoPq5fVK_KXMgcsAG6viisAIkyRknOCJrG_BerCsGng" />
                        </div>
                        <div class="dashboard-review-photo">
                            <img alt="Cracked roof tiles"
                                data-alt="exterior view of a residential clay tile roof with several cracked and missing pieces near a gutter"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAttfMLxHpzbqlnZd0P4EbqBrOy2D6HF9Z_DHno6clDxQOpVEB5GGY1_orsdGJBGJqcafDfNvO7BjrRoxCy_lUD43CPXmXfzVRk2jzH-JhWJvptvVNvOIYMUL3IG6b37tMMY8ywQ-AIfXfqWwj-1nDaAdHa9FWnxFJvYEYtLHwZQNDJtzCW5B-BoqYeUzeoolBY6VeBiLgWsZ1RxgOwBwDgN47J21hti32vruypea9qDgLcOmZtygXOCSQHXjOhlMnpwmyN9_aAsv8" />
                        </div>
                        <div class="dashboard-review-photo">
                            <img alt="Cracked roof tiles"
                                data-alt="exterior view of a residential clay tile roof with several cracked and missing pieces near a gutter"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAttfMLxHpzbqlnZd0P4EbqBrOy2D6HF9Z_DHno6clDxQOpVEB5GGY1_orsdGJBGJqcafDfNvO7BjrRoxCy_lUD43CPXmXfzVRk2jzH-JhWJvptvVNvOIYMUL3IG6b37tMMY8ywQ-AIfXfqWwj-1nDaAdHa9FWnxFJvYEYtLHwZQNDJtzCW5B-BoqYeUzeoolBY6VeBiLgWsZ1RxgOwBwDgN47J21hti32vruypea9qDgLcOmZtygXOCSQHXjOhlMnpwmyN9_aAsv8" />
                        </div>
                        <div class="dashboard-review-photo">
                            <img alt="Cracked roof tiles"
                                data-alt="exterior view of a residential clay tile roof with several cracked and missing pieces near a gutter"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAttfMLxHpzbqlnZd0P4EbqBrOy2D6HF9Z_DHno6clDxQOpVEB5GGY1_orsdGJBGJqcafDfNvO7BjrRoxCy_lUD43CPXmXfzVRk2jzH-JhWJvptvVNvOIYMUL3IG6b37tMMY8ywQ-AIfXfqWwj-1nDaAdHa9FWnxFJvYEYtLHwZQNDJtzCW5B-BoqYeUzeoolBY6VeBiLgWsZ1RxgOwBwDgN47J21hti32vruypea9qDgLcOmZtygXOCSQHXjOhlMnpwmyN9_aAsv8" />
                        </div>
                        <div class="dashboard-review-photo">
                            <img alt="Cracked roof tiles"
                                data-alt="exterior view of a residential clay tile roof with several cracked and missing pieces near a gutter"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAttfMLxHpzbqlnZd0P4EbqBrOy2D6HF9Z_DHno6clDxQOpVEB5GGY1_orsdGJBGJqcafDfNvO7BjrRoxCy_lUD43CPXmXfzVRk2jzH-JhWJvptvVNvOIYMUL3IG6b37tMMY8ywQ-AIfXfqWwj-1nDaAdHa9FWnxFJvYEYtLHwZQNDJtzCW5B-BoqYeUzeoolBY6VeBiLgWsZ1RxgOwBwDgN47J21hti32vruypea9qDgLcOmZtygXOCSQHXjOhlMnpwmyN9_aAsv8" />
                        </div>
                    </div>
                </div>
                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Feedback Mandor</span>
                    <div class="dashboard-review-note">
                        <textarea class="dashboard-review-feedback"
                            placeholder="Tulis feedback dan material yang dibutuhkan secara lengkap untuk renovasi klien..."></textarea>
                    </div>
                </div>

                <div class="dashboard-review-block">
                    <span class="dashboard-review-label">Biaya Renovasi</span>
                    <div class="dashboard-review-cost-wrap">
                        <label class="dashboard-review-cost-field" for="dashboard-review-cost">
                            <span class="dashboard-review-cost-prefix">Rp</span>
                            <input class="dashboard-review-cost-input" id="dashboard-review-cost" type="number"
                                inputmode="numeric" min="0" step="1000" placeholder="Contoh: 12500000" />
                        </label>
                        <p class="dashboard-review-cost-hint">Masukkan total biaya renovasi.</p>
                    </div>
                </div>

                <div class="dashboard-review-actions">
                    <button class="dashboard-review-action-btn dashboard-review-action-btn-primary" type="button"
                        onclick="window.location.href='{{ route('mandor.tracking') }}'">
                        Ambil Renovasi
                    </button>
                </div>
            </div>
        </div>
    @endsection
    @push('scripts')
        <script src="{{ asset('js/mandor/review.js') }}"></script>
    @endpush
