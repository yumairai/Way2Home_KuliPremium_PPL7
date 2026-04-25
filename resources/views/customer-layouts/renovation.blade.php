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
                    'materials' => [],
                ],
                [
                    'id' => 'REV-2',
                    'status' => 'on-progress',
                    'location' => 'Bandung, Jawa Barat',
                    'budget_user' => 'Rp 150.000.000',
                    'feedback' =>
                        'Berdasarkan survei visual dan data teknis, area renovasi memerlukan perkuatan struktur pada bagian atap. Penggunaan material baja ringan standar SNI sangat disarankan. Kami telah menghitung efisiensi tenaga kerja lokal untuk menekan biaya jasa tanpa mengurangi kualitas finishing.',
                    'budget_needed' => 'Rp 190.000.000',
                    'mandor_contact' => '6281384310179',
                    'mandor_name' => 'Mandor Budi',
                    'materials' => [
                        [
                            'nama_material' => 'Besi Beton 19mm',
                            'harga' => 320000,
                            'satuan' => 'btg',
                            'jumlah' => 4,
                            'deskripsi' => 'Perwira • 19mm • 27kg',
                        ],
                        [
                            'nama_material' => 'Semen Portland 50kg',
                            'harga' => 76000,
                            'satuan' => 'zak',
                            'jumlah' => 8,
                            'deskripsi' => 'Mutu K-225 • Kuat tekan stabil',
                        ],
                    ],
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
                    'materials' => [
                        [
                            'nama_material' => 'Semen Portland 50kg',
                            'harga' => 76000,
                            'satuan' => 'zak',
                            'jumlah' => 5,
                            'deskripsi' => 'Tahan lembab • Mutu K-225',
                        ],
                        [
                            'nama_material' => 'Pasir Cor Halus',
                            'harga' => 285000,
                            'satuan' => 'm3',
                            'jumlah' => 2,
                            'deskripsi' => 'Butiran halus • Bersih dari lumpur',
                        ],
                        [
                            'nama_material' => 'Batu Split 1/2',
                            'harga' => 340000,
                            'satuan' => 'm3',
                            'jumlah' => 1,
                            'deskripsi' => 'Agregat struktur • Kering',
                        ],
                    ],
                ],
                [
                    'id' => 'REV-4',
                    'status' => 'completed',
                    'location' => 'Jakarta Selatan, DKI Jakarta',
                    'budget_user' => 'Rp 250.000.000',
                    'feedback' =>
                        'Berdasarkan survei visual dan data teknis, area renovasi memerlukan perkuatan struktur pada bagian atap. Penggunaan material baja ringan standar SNI sangat disarankan. Kami telah menghitung efisiensi tenaga kerja lokal untuk menekan biaya jasa tanpa mengurangi kualitas finishing.',
                    'budget_needed' => 'Rp 274.500.000',
                    'mandor_contact' => '6281384310179',
                    'mandor_name' => 'Mandor Budi',
                    'materials' => [
                        [
                            'nama_material' => 'Cat Eksterior Weatherproof',
                            'harga' => 295000,
                            'satuan' => 'pail',
                            'jumlah' => 5,
                            'deskripsi' => '20L • Tahan UV & hujan',
                        ],
                        [
                            'nama_material' => 'Acrylic Sealer Primer',
                            'harga' => 185000,
                            'satuan' => 'pail',
                            'jumlah' => 2,
                            'deskripsi' => 'Daya rekat tinggi • Anti jamur',
                        ],
                    ],
                ],
            ];
        @endphp
        @if (!$isHaveRequest)
            <p class="rv-no-requests">Anda belum memiliki request renovasi. Silahkan ajukan request renovasi baru.</p>
            <button type="button" class="rv-add-btn" onclick="window.location.href='{{ route('customer.renovation_form') }}'">
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
                        $isReviewedState = $status === 'reviewed';
                        $isProgressLikeState = in_array($status, ['on-progress', 'completed'], true);
                        $isWaitingStatus = $status === 'waiting';
                        $showMaterialInfo = in_array($status, ['reviewed', 'on-progress', 'completed'], true);
                        $materials = $request['materials'] ?? [];
                        $materialTotalPrice = 0;
                        $materialAlertLines = [];

                        foreach ($materials as $materialItem) {
                            $itemPrice = (int) ($materialItem['harga'] ?? 0);
                            $itemQty = (int) ($materialItem['jumlah'] ?? 0);
                            $itemSubtotal = $itemPrice * $itemQty;
                            $materialTotalPrice += $itemSubtotal;
                            $materialAlertLines[] = sprintf(
                                '- %s | %d %s | Rp %s',
                                (string) ($materialItem['nama_material'] ?? '-'),
                                $itemQty,
                                (string) ($materialItem['satuan'] ?? '-'),
                                number_format($itemSubtotal, 0, ',', '.'),
                            );
                        }

                        $materialAlertMessage = "Detail Material:\n";
                        $materialAlertMessage .= implode("\n", $materialAlertLines);
                        $materialAlertMessage .=
                            "\n\nTotal Harga Material: Rp " . number_format($materialTotalPrice, 0, ',', '.');

                        $mandorContactDigits = preg_replace('/\D+/', '', (string) ($request['mandor_contact'] ?? ''));
                        $mandorContactForWa = str_starts_with($mandorContactDigits, '0')
                            ? '62' . ltrim($mandorContactDigits, '0')
                            : $mandorContactDigits;
                        $mandorContactForDisplay = str_starts_with($mandorContactDigits, '62')
                            ? '0' . substr($mandorContactDigits, 2)
                            : $mandorContactDigits;
                        $mandorContactForDisplay = preg_replace(
                            '/(\d{4})(\d{4})(\d+)/',
                            '$1-$2-$3',
                            $mandorContactForDisplay,
                        );
                    @endphp
                    <article class="rv-request-card" data-request-status="{{ $status }}">
                        <div class="rv-request-content">
                            <div class="rv-request-top">
                                <div>
                                    <span class="rv-request-kicker">Request Renovasi</span>
                                    <h2 class="rv-request-id">#{{ $request['id'] }}</h2>
                                </div>
                                <div class="rv-status-pill rv-status {{ $status }}" data-state-pill>
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

                            <details class="rv-review-details {{ $isWaitingStatus ? 'rv-review-details-disabled' : '' }}">
                                <summary class="rv-review-summary"
                                    aria-disabled="{{ $isWaitingStatus ? 'true' : 'false' }}">
                                    <span class="material-symbols-outlined rv-review-summary-icon">notes</span>
                                    <span>Detail Review Mandor</span>
                                </summary>

                                @if ($showMandorSection)
                                    <div class="mandor-section">
                                        <div class="rv-feedback-section">
                                            <label class="rv-section-label">Feedback Mandor</label>
                                            <div class="rv-feedback-box {{ $config['feedbackClass'] }}" data-feedback-box>
                                                {{ $request['feedback'] }}
                                            </div>
                                        </div>

                                        @if ($showMaterialInfo && count($materials) > 0)
                                            <div class="rv-material-box">
                                                <div class="rv-material-title-wrap">
                                                    <span
                                                        class="material-symbols-outlined rv-material-icon">inventory_2</span>
                                                    <span class="rv-material-label">Kebutuhan Material</span>
                                                </div>
                                                @foreach ($materials as $material)
                                                    <div class="rv-material-content">
                                                        <p class="rv-material-name">{{ $material['nama_material'] }}</p>
                                                        <p class="rv-material-meta">Kebutuhan: {{ $material['jumlah'] }}
                                                            {{ $material['satuan'] }}</p>
                                                        <p class="rv-material-meta">{{ $material['deskripsi'] }}</p>
                                                        <p class="rv-material-price">Harga Satuan: Rp
                                                            {{ number_format((int) $material['harga'], 0, ',', '.') }}</p>
                                                    </div>
                                                @endforeach
                                                <div class="rv-material-total">
                                                    <p class="rv-material-total-label">Total Harga Material</p>
                                                    <p class="rv-material-total-value">Rp
                                                        {{ number_format($materialTotalPrice, 0, ',', '.') }}</p>
                                                </div>
                                                <div class="rv-material-actions">
                                                    <button type="button"
                                                        class="rv-action-btn rv-action-btn-primary js-alert-btn"
                                                        data-alert-message="{{ e($materialAlertMessage) }}">Material
                                                        Saja</button>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="rv-budget-box rv-budget">
                                            <div class="rv-budget-title-wrap">
                                                <span class="material-symbols-outlined rv-budget-icon"
                                                    data-budget-icon>{{ $config['budgetIcon'] }}</span>
                                                <span class="rv-budget-label">Informasi Budget Dibutuhkan</span>
                                            </div>
                                            <div class="rv-budget-value">{{ $request['budget_needed'] }}</div>
                                        </div>

                                        <div class="rv-actions rv-state-panel {{ $isReviewedState ? '' : 'rv-hidden' }}"
                                            data-state-panel="reviewed">
                                            <button type="button" class="rv-action-btn rv-action-btn-primary"
                                                data-transition-state="on-progress">Jasa Renovasi</button>
                                            <div class="rv-actions-spacer"></div>
                                            <button type="button" class="rv-done-btn"
                                                data-transition-state="completed">Selesai</button>
                                        </div>

                                        <div class="rv-actions rv-state-panel {{ $isProgressLikeState ? '' : 'rv-hidden' }}"
                                            data-state-panel="progress">
                                            <div class="rv-info-item">
                                                <div class="rv-info-icon-wrap">
                                                    <span class="material-symbols-outlined rv-info-icon">call</span>
                                                </div>
                                                <div>
                                                    <p class="rv-info-label">Kontak Mandor</p>
                                                    <p class="rv-info-value">{{ $request['mandor_name'] }} -
                                                        {{ $mandorContactForDisplay }}</p>
                                                </div>
                                            </div>
                                            <div class="rv-actions-spacer"></div>
                                            <button type="button" class="rv-action-btn rv-action-btn-primary"
                                                onclick="window.location.href= 'https://wa.me/{{ $mandorContactForWa }}'">Hubungi
                                                Mandor</button>
                                        </div>

                                    </div>
                                @endif
                            </details>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
        <!-- Add Request Button Section -->
        <section class="rv-add-section">
            @if ($isHaveRequest)
                <button type="button" class="rv-add-btn"
                    onclick="window.location.href='{{ route('customer.renovation_form') }}'">
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
@push('scripts')
    <script src="{{ asset('js/customer/renovasi.js') }}"></script>
@endpush
