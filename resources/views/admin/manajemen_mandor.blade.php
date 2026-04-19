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
@php
    $mandors = [
        [
            'id' => 'M-001',
            'name' => 'Mandor Asep',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-002',
            'name' => 'Mandor Bambang',
            'status' => 'busy',
            'current_project' => 'Skyline Apartment B',
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-003',
            'name' => 'Mandor Cahyo',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-004',
            'name' => 'Mandor Dedi',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-005',
            'name' => 'Mandor Eka',
            'status' => 'busy',
            'current_project' => 'Rumah Mewah Senayan',
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-006',
            'name' => 'Mandor Fajri',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-007',
            'name' => 'Mandor Gede',
            'status' => 'busy',
            'current_project' => 'Renovasi Hotel Blok M',
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-008',
            'name' => 'Mandor Hendra',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-009',
            'name' => 'Mandor Indra',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
        [
            'id' => 'M-010',
            'name' => 'Mandor Joko',
            'status' => 'available',
            'current_project' => null,
            'image' => asset('images/aset/user-dummy.jpg'),
        ],
    ];

    $projects_pending = [
        ['id' => 'W2H-88120', 'name' => 'Luxury Villa Kemang', 'image' => asset('images/aset/construction.jpg')],
        ['id' => 'W2H-88121', 'name' => 'Apartment BSD Unit 4B', 'image' => asset('images/aset/construction2.jpg')],
        ['id' => 'W2H-88122', 'name' => 'Contemporary Menteng House', 'image' => asset('images/aset/construction.jpg')],
        ['id' => 'W2H-88123', 'name' => 'Rumah Kota Bandung', 'image' => asset('images/aset/construction2.jpg')],
    ];

    $total_mandors = count($mandors);
    $available_mandors = collect($mandors)->where('status', 'available')->count();
    $busy_mandors = collect($mandors)->where('status', 'busy')->count();
    $pending_projects = count($projects_pending);
@endphp
@section('stats')
    <!-- Stats Overview (Asymmetric Layout) -->
    <div class="mandor-stat-card">
        <p class="mandor-stat-label">Total Mandor</p>
        <p class="mandor-stat-value">{{ $total_mandors }}</p>
    </div>
    <div class="mandor-stat-card mandor-stat-available">
        <p class="mandor-stat-label">Tersedia</p>
        <p class="mandor-stat-value">{{ $available_mandors }}</p>
    </div>
    <div class="mandor-stat-card mandor-stat-busy">
        <p class="mandor-stat-label">Sedang Bertugas</p>
        <p class="mandor-stat-value">{{ $busy_mandors }}</p>
    </div>
    <div class="mandor-stat-card mandor-stat-pending">
        <p class="mandor-stat-label">Proyek Menunggu Mandor</p>
        <p class="mandor-stat-value">{{ $pending_projects }}</p>
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
                @foreach ($mandors as $mandor)
                    <div class="mandor-entry {{ $mandor['status'] === 'busy' ? 'busy' : '' }}">
                        <div class="mandor-entry-info">
                            <div class="mandor-entry-avatar-container">
                                <img alt="{{ $mandor['name'] }}" class="mandor-entry-avatar" src="{{ $mandor['image'] }}" />
                                <span class="mandor-entry-status-dot"></span>
                            </div>
                            <div class="mandor-entry-details">
                                <h4>{{ $mandor['name'] }}</h4>
                                <p>{{ $mandor['id'] }}</p>
                            </div>
                        </div>
                        <div class="mandor-entry-status">
                            <span class="mandor-status {{ $mandor['status'] }}">
                                {{ ucfirst($mandor['status'] === 'available' ? 'Available' : 'Busy') }}
                            </span>
                        </div>
                        <div class="mandor-entry-actions">
                            <div class="mandor-entry-project">
                                <p class="mandor-entry-project-label">Proyek Saat Ini</p>
                                <p class="mandor-entry-project-name">{{ $mandor['current_project'] ?? '-' }}</p>
                            </div>
                            <button class="mandor-assign-btn {{ $mandor['status'] === 'busy' ? 'disabled' : '' }}"
                                {{ $mandor['status'] === 'busy' ? 'disabled' : '' }}
                                onclick="openDocModal('{{ $mandor['id'] }}')">
                                Assign Proyek
                            </button>
                        </div>
                    </div>
                @endforeach
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
                        @foreach ($projects_pending as $index => $project)
                            <button class="proyek-item {{ $index === 0 ? 'active' : '' }}" onclick="selectProject(this)">
                                <div class="project-card">
                                    <div class="project-thumb">
                                        <img src="{{ $project['image'] }}" alt="{{ $project['name'] }}">
                                    </div>
                                    <div>
                                        <p class="project-title">{{ $project['name'] }}</p>
                                        <div class="title-wrapper">
                                            <p class="project-id"><strong>ID:</strong> {{ $project['id'] }}</p>
                                            <p class="project-status"><strong>STATUS:</strong> Menunggu Mandor</p>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <div class="modal-footer-buttons">
                    <button class="modal-btn modal-btn-submit" onclick="assignMandor()">Assign Mandor</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        window.projectsPending = @json($projects_pending);
    </script>
    <script src="{{ asset('js/admin/assign_mandor.js') }}"></script>
@endpush
@push('scripts')
    <script src="{{ asset('js/admin/assign_mandor.js') }}"></script>
@endpush
