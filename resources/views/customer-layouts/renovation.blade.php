@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/renovation.css') }}">
@endpush
@section('content')
    <main class="rv-main rv-container">
        <!-- Header Section -->
        <header class="rv-header">
            <h1 class="rv-page-title">Request Renovasi Saya</h1>
            <p class="rv-page-subtitle">Pantau status pengajuan renovasi dan tinjau analisis anggaran
                dari tim ahli kami.</p>
        </header>
        <!-- Requests List -->
        @php
            $isHaveRequest = true;

            $statusConfig = [
                'waiting' => [
                    'label' => 'Menunggu Review',
                    'icon' => 'schedule',
                    'feedbackClass' => 'rv-feedback-pending',
                    'budgetIcon' => 'calculate',
                ],
                'on-progress' => [
                    'label' => 'Renovasi Aktif',
                    'icon' => 'schedule',
                    'feedbackClass' => 'rv-feedback-pending',
                    'budgetIcon' => 'calculate',
                ],
                'reviewed' => [
                    'label' => 'Sudah Direview',
                    'icon' => 'verified',
                    'feedbackClass' => 'rv-feedback-reviewed',
                    'budgetIcon' => 'analytics',
                ],
                'completed' => [
                    'label' => 'Request Renovasi Selesai',
                    'icon' => 'verified',
                    'feedbackClass' => 'rv-feedback-reviewed',
                    'budgetIcon' => 'analytics',
                ],
            ];

            // Dummy data sementara untuk simulasi 4 state.
            $requests = [
                [
                    'id' => 'REV-1',
                    'status' => 'waiting',
                    'location' => 'Bandung, Jawa Barat',
                    'budget_user' => 'Rp 150.000.000',
                    'feedback' =>
                        'Pengajuan Anda sedang dalam antrean tim verifikasi teknis. Kami akan segera memberikan analisis material dan jasa berdasarkan spesifikasi lokasi di Bandung.',
                    'budget_needed' => '-',
                    'mandor_contact' => null,
                    'mandor_name' => null,
                ],
                [
                    'id' => 'REV-2',
                    'status' => 'on-progress',
                    'location' => 'Bandung, Jawa Barat',
                    'budget_user' => 'Rp 150.000.000',
                    'feedback' =>
                        'Proyek renovasi Anda sedang berjalan. Silahkan hubungi mandor untuk update harian dan koordinasi kebutuhan material.',
                    'budget_needed' => 'Rp 190.000.000',
                    'mandor_contact' => '6281384310179',
                    'mandor_name' => 'Mandor Budi',
                ],
                [
                    'id' => 'REV-3',
                    'status' => 'reviewed',
                    'location' => 'Jakarta Selatan, DKI Jakarta',
                    'budget_user' => 'Rp 250.000.000',
                    'feedback' =>
                        'Berdasarkan survei visual dan data teknis, area renovasi memerlukan perkuatan struktur pada bagian atap. Penggunaan material baja ringan standar SNI sangat disarankan. Kami telah menghitung efisiensi tenaga kerja lokal untuk menekan biaya jasa tanpa mengurangi kualitas finishing.',
                    'budget_needed' => 'Rp 274.500.000',
                    'mandor_contact' => '6281384310179',
                    'mandor_name' => 'Mandor Budi',
                ],
                [
                    'id' => 'REV-4',
                    'status' => 'completed',
                    'location' => 'Jakarta Selatan, DKI Jakarta',
                    'budget_user' => 'Rp 250.000.000',
                    'feedback' => 'Request renovasi telah selesai. Terima kasih sudah menggunakan layanan kami.',
                    'budget_needed' => 'Rp 274.500.000',
                    'mandor_contact' => '6281384310179',
                    'mandor_name' => 'Mandor Budi',
                ],
            ];
        @endphp
        @if (!$isHaveRequest)
            <p class="rv-no-requests">Anda belum memiliki request renovasi. Silahkan ajukan request renovasi baru.</p>
            <button class="rv-add-btn" onclick="window.location.href='{{ route('customer.renovation_form') }}'">
                <span class="material-symbols-outlined rv-add-btn-icon">add_circle</span>
                <span>Buat Request Renovasi</span>
            </button>
        @else
            <div class="rv-request-list">
                @foreach ($requests as $request)
                    @php
                        $status = $request['status'];
                        $config = $statusConfig[$status] ?? $statusConfig['waiting'];
                        $showMandorSection = $status !== 'waiting';
                    @endphp
                    <article class="rv-request-card">
                        <div class="rv-request-content">
                            <div class="rv-request-top">
                                <div>
                                    <span class="rv-request-kicker">Request Renovasi</span>
                                    <h2 class="rv-request-id">#{{ $request['id'] }}</h2>
                                </div>
                                <div class="rv-status-pill rv-status {{ $status }}">
                                    <span class="material-symbols-outlined rv-status-icon">{{ $config['icon'] }}</span>
                                    <span class="rv-status-text">{{ $config['label'] }}</span>
                                </div>
                            </div>

                            <div class="rv-info-grid">
                                <div class="rv-info-item">
                                    <div class="rv-info-icon-wrap">
                                        <span class="material-symbols-outlined rv-info-icon">location_on</span>
                                    </div>
                                    <div>
                                        <p class="rv-info-label">Lokasi Proyek</p>
                                        <p class="rv-info-value">{{ $request['location'] }}</p>
                                    </div>
                                </div>
                                <div class="rv-info-item">
                                    <div class="rv-info-icon-wrap">
                                        <span class="material-symbols-outlined rv-info-icon">payments</span>
                                    </div>
                                    <div>
                                        <p class="rv-info-label">Estimasi Budget User</p>
                                        <p class="rv-info-value">{{ $request['budget_user'] }}</p>
                                    </div>
                                </div>
                            </div>

                            @if ($showMandorSection)
                                <div class="mandor-section">
                                    <div class="rv-feedback-section">
                                        <label class="rv-section-label">Feedback Mandor</label>
                                        <div class="rv-feedback-box {{ $config['feedbackClass'] }}">
                                            {{ $request['feedback'] }}
                                        </div>
                                    </div>

                                    <div class="rv-budget-box rv-budget">
                                        <div class="rv-budget-title-wrap">
                                            <span
                                                class="material-symbols-outlined rv-budget-icon">{{ $config['budgetIcon'] }}</span>
                                            <span class="rv-budget-label">Informasi Budget Dibutuhkan</span>
                                        </div>
                                        <div class="rv-budget-value">{{ $request['budget_needed'] }}</div>
                                    </div>

                                    @if ($status === 'reviewed')
                                        <div class="rv-actions">
                                            <button class="rv-action-btn rv-action-btn-primary">Material Saja</button>
                                            <button class="rv-action-btn rv-action-btn-primary">Jasa Renovasi</button>
                                            <div class="rv-actions-spacer"></div>
                                            <button class="rv-done-btn">Selesai</button>
                                        </div>
                                    @elseif ($status === 'on-progress')
                                        <div class="rv-actions">
                                            <div class="rv-info-item">
                                                <div class="rv-info-icon-wrap">
                                                    <span class="material-symbols-outlined rv-info-icon">call</span>
                                                </div>
                                                <div>
                                                    <p class="rv-info-label">Kontak Mandor</p>
                                                    <p class="rv-info-value">{{ $request['mandor_name'] }} -
                                                        {{ $request['mandor_contact'] }}</p>
                                                </div>
                                            </div>
                                            <div class="rv-actions-spacer"></div>
                                            <button class="rv-action-btn rv-action-btn-primary"
                                                onclick="window.location.href= 'https://wa.me/{{ $request['mandor_contact'] }}'">Hubungi
                                                Mandor</button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
        <!-- Add Request Button Section -->
        <section class="rv-add-section">
            @if ($isHaveRequest)
                <button class="rv-add-btn" onclick="window.location.href='{{ route('customer.renovation_form') }}'">
                    <span class="material-symbols-outlined rv-add-btn-icon">add_circle</span>
                    <span>Tambah Request Renovasi</span>
                </button>
            @endif

        </section>
    </main>
    <!-- Floating Background Decorative Elements -->
    <div class="rv-background" aria-hidden="true">
        <div class="rv-bg-orb rv-bg-orb-right"></div>
        <div class="rv-bg-orb rv-bg-orb-left"></div>
    </div>
@endsection
