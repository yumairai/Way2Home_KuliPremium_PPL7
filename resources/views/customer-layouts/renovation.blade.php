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
                'cancelled' => [
                    'label' => 'Request Dibatalkan',
                    'icon' => 'cancel',
                    'feedbackClass' => 'rv-feedback-cancelled',
                    'budgetIcon' => 'calculate',
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
                        $isCancelledState = $status === 'cancelled';
                        $isWaitingStatus = $status === 'waiting';
                        $showMaterialInfo = in_array(
                            $status,
                            ['reviewed', 'on-progress', 'completed', 'cancelled'],
                            true,
                        );
                        $hideNegotiationForm = in_array($status, ['on-progress', 'completed', 'cancelled'], true);
                        $damageDescription = trim(
                            (string) ($request['damage_description'] ?? 'Deskripsi belum tersedia.'),
                        );
                        $damagePhotos = array_values(array_filter((array) ($request['damage_photos'] ?? [])));
                        $previewPhotos = array_slice($damagePhotos, 0, 2);
                        $remainingPhotosCount = max(count($damagePhotos) - count($previewPhotos), 0);
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
                    <article class="rv-request-card" data-request-status="{{ $status }}"
                        data-request-id="{{ $request['db_id'] ?? '' }}">
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

                            <section class="rv-user-detail-preview">
                                <div class="rv-user-detail-head">
                                    <h3 class="rv-user-detail-title">Deskripsi Kerusakan</h3>
                                    <button type="button" class="js-open-request-detail"
                                        data-request-id="{{ $request['id'] }}"
                                        data-request-description="{{ e($damageDescription) }}"
                                        data-request-photos='@json($damagePhotos)'
                                        data-request-materials='@json($materials)'><span
                                            class="material-symbols-outlined">gallery_thumbnail</span></button>
                                </div>
                                <p class="rv-user-desc-clamp">{{ $damageDescription }}</p>
                                <div class="rv-damage-photo-preview">
                                    @if (count($previewPhotos) > 0)
                                        @foreach ($previewPhotos as $photoIndex => $photoPath)
                                            <div class="rv-damage-photo-thumb-wrap">
                                                <img src="{{ $photoPath }}"
                                                    alt="Foto kerusakan {{ $photoIndex + 1 }} request {{ $request['id'] }}"
                                                    class="rv-damage-photo-thumb">
                                                @if ($loop->last && $remainingPhotosCount > 0)
                                                    <div class="rv-damage-photo-more">+{{ $remainingPhotosCount }}</div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="rv-damage-photo-empty">Belum ada foto kerusakan yang diunggah.</p>
                                    @endif
                                </div>
                            </section>

                            <details class="rv-review-details {{ $isWaitingStatus ? 'rv-review-details-disabled' : '' }}">
                                <summary class="rv-review-summary"
                                    aria-disabled="{{ $isWaitingStatus ? 'true' : 'false' }}">
                                    <span class="material-symbols-outlined rv-review-summary-icon">notes</span>
                                    <span>Lihat Hasil Review & Detail Renovasi</span>
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

                                                    <button type="button"
                                                        class="rv-action-btn rv-action-btn-outline js-toggle-material-details"
                                                        aria-expanded="false"><span
                                                            class="material-symbols-outlined rv-material-icon">list</span>
                                                        <span class="rv-material-label">Lihat Kebutuhan
                                                            Material</span></button>
                                                </div>

                                                <div class="rv-material-details" style="display: none;">
                                                    @foreach ($materials as $material)
                                                        <div class="rv-material-content">
                                                            <p class="rv-material-name">{{ $material['nama_material'] }}
                                                            </p>
                                                            <p class="rv-material-meta">Kebutuhan:
                                                                {{ $material['jumlah'] }}
                                                                {{ $material['satuan'] }}</p>
                                                            <p class="rv-material-meta">{{ $material['deskripsi'] }}</p>
                                                            <p class="rv-material-price">Harga Satuan: Rp
                                                                {{ number_format((int) $material['harga'], 0, ',', '.') }}
                                                            </p>
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
                                                            data-alert-message="{{ e($materialAlertMessage) }}">Pesan
                                                            Material Saja</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

                                        <div class="rv-budget-box rv-budget">
                                            <div class="rv-budget-title-wrap">
                                                <span class="material-symbols-outlined rv-budget-icon"
                                                    data-budget-icon>{{ $config['budgetIcon'] }}</span>
                                                <div class="rv-budget-wrap">
                                                    <span class="rv-budget-label">Biaya Renovasi</span>
                                                    <span class="rv-budget-description">Biaya renovasi tidak termasuk biaya
                                                        material.</span>
                                                </div>
                                            </div>
                                            <div class="rv-budget-value">{{ $request['budget_needed'] }}</div>
                                        </div>
                                        <div class="rv-material-box">
                                            <div class="rv-material-title-wrap">
                                                <span class="material-symbols-outlined rv-material-icon">forum</span>
                                                <span class="rv-material-label">Riwayat Negosiasi</span>
                                            </div>
                                            @if (!empty($request['negotiation_messages']) && count($request['negotiation_messages']) > 0)
                                                <div class="rv-negotiation-history">
                                                    @foreach ($request['negotiation_messages'] as $message)
                                                        @php
                                                            $messageSender = strtolower(
                                                                (string) ($message['pengirim'] ?? 'customer'),
                                                            );
                                                            $messageType = strtolower(
                                                                (string) ($message['tipe'] ?? ''),
                                                            );
                                                            $messageClass =
                                                                $messageSender === 'mandor'
                                                                    ? 'rv-negotiation-message-mandor'
                                                                    : 'rv-negotiation-message-customer';
                                                            $messageText =
                                                                $messageSender === 'mandor' &&
                                                                $messageType === 'penawaran'
                                                                    ? 'Penawaran awal dari mandor.'
                                                                    : (string) ($message['pesan'] ?? '-');
                                                        @endphp
                                                        <div class="rv-negotiation-message {{ $messageClass }}">
                                                            <div class="rv-negotiation-message-head">
                                                                <span class="rv-negotiation-message-badge">
                                                                    {{ ucfirst($message['pengirim']) }}
                                                                </span>
                                                                @if (!empty($message['waktu']))
                                                                    <span class="rv-negotiation-message-time">
                                                                        {{ $message['waktu'] }}
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            <p class="rv-negotiation-message-text">
                                                                {{ $messageText }}</p>
                                                            @if (!empty($message['nominal_tawaran']))
                                                                <p class="rv-negotiation-message-price">Nominal Tawaran:
                                                                    {{ $message['nominal_tawaran'] }}</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="rv-material-meta rv-negotiation-empty">Belum ada riwayat
                                                    negosiasi.</p>
                                            @endif

                                            <div class="rv-negotiation-form {{ $hideNegotiationForm ? 'rv-hidden' : '' }}"
                                                data-negotiation-form>
                                                <div class="rv-negotiation-fields">
                                                    <label class="rv-negotiation-label"
                                                        for="rv-negotiation-price-{{ $request['id'] }}">
                                                        Nominal Nego
                                                    </label>
                                                    <input id="rv-negotiation-price-{{ $request['id'] }}" type="number"
                                                        class="rf-input js-negotiation-price rv-negotiation-input"
                                                        placeholder="Nominal nego (opsional)">
                                                    <label class="rv-negotiation-label"
                                                        for="rv-negotiation-message-{{ $request['id'] }}">
                                                        Pesan Negosiasi
                                                    </label>
                                                    <div class="rv-textarea-wrapper">
                                                        <textarea id="rv-negotiation-message-{{ $request['id'] }}"
                                                            class="rf-textarea js-negotiation-message rv-negotiation-textarea" rows="3"
                                                            placeholder="Tulis pesan negosiasi ke mandor"></textarea>
                                                        <button type="button"
                                                            class="rv-action-btn rv-action-btn-outline js-negotiate-btn rv-negotiation-submit">
                                                            <span
                                                                class="material-symbols-outlined rv-negotiation-submit-icon">send</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if (!empty($request['offer_expires_at']) && $status === 'reviewed')
                                            <p class="rv-info-label" style="margin-top:0.75rem;">
                                                Penawaran berlaku sampai {{ $request['offer_expires_at'] }}
                                            </p>
                                        @endif
                                        <div class="rv-actions rv-state-panel {{ $isReviewedState ? '' : 'rv-hidden' }}"
                                            data-state-panel="reviewed">
                                            <div
                                                style="display:flex;gap:0.5rem;align-items:center;justify-content:flex-start">
                                                <button type="button" class="rv-action-btn rv-action-btn-primary"
                                                    data-transition-state="on-progress"
                                                    data-service-action="{{ !empty($request['is_service_actionable']) ? '1' : '0' }}"
                                                    {{ !empty($request['is_service_actionable']) ? '' : 'disabled' }}>Terima
                                                    Tawaran</button>
                                                <button type="button" class="rv-done-btn js-reject-offer-btn">Tolak
                                                    Penawaran & Batalkan Renovasi</button>
                                            </div>
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

    <div class="rv-modal" id="requestDetailModal" aria-hidden="true">
        <div class="rv-modal-backdrop js-close-request-detail"></div>
        <div class="rv-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="rvDetailModalTitle">
            <div class="rv-modal-header">
                <div>
                    <p class="rv-modal-kicker">Detail Request Renovasi</p>
                    <h2 class="rv-modal-title" id="rvDetailModalTitle">Request #<span data-modal-request-id>-</span></h2>
                </div>
                <button type="button" class="rv-modal-close-btn js-close-request-detail"
                    aria-label="Tutup detail request">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="rv-modal-body">
                <section class="rv-modal-section">
                    <h3 class="rv-modal-section-title">Deskripsi Kerusakan / Keinginan</h3>
                    <p class="rv-modal-description" data-modal-description>-</p>
                </section>
                <section class="rv-modal-section">
                    <h3 class="rv-modal-section-title">Foto Kerusakan</h3>
                    <div class="rv-modal-gallery">
                        <button type="button" class="rv-gallery-nav rv-gallery-prev" data-gallery-nav="prev"
                            aria-label="Foto sebelumnya">
                            <span class="material-symbols-outlined">chevron_left</span>
                        </button>
                        <img src="" alt="Foto detail kerusakan" class="rv-modal-main-photo"
                            data-modal-main-photo>
                        <button type="button" class="rv-gallery-nav rv-gallery-next" data-gallery-nav="next"
                            aria-label="Foto berikutnya">
                            <span class="material-symbols-outlined">chevron_right</span>
                        </button>
                    </div>
                    <p class="rv-gallery-counter">Foto <span data-gallery-index>0</span>/<span data-gallery-total>0</span>
                    </p>
                    <div class="rv-gallery-thumbs" data-gallery-thumbs></div>
                </section>
                <section class="rv-modal-section">
                    <h3 class="rv-modal-section-title">List Material & Harga</h3>
                    <div data-modal-material-list></div>
                </section>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/customer/renovasi.js') }}"></script>
@endpush
