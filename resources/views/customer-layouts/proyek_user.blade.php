@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/proyek_user.css') }}">
@endpush
@section('content')
    <div class="page-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar-nav">

            <div class="sidebar-section">
                <p class="sidebar-title">List Project</p>
                <div class="sidebar-menu">
                    {{-- Item Proyek 1 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/1') ? 'active' : '' }}"
                        href="{{ url('user/projects/1') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/1') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Modern Villa Kemang
                    </a>

                    {{-- Item Proyek 2 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/2') ? 'active' : '' }}"
                        href="{{ url('user/projects/2') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/2') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Modern Villa Bandung
                    </a>
                    {{-- Item Proyek 3 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/3') ? 'active' : '' }}"
                        href="{{ url('user/projects/3') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/3') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Modern Villa Surabaya
                    </a>
                    {{-- Item Proyek 4 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/4') ? 'active' : '' }}"
                        href="{{ url('user/projects/4') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/4') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Rumah Minimalist Bali
                    </a>
                    {{-- Item Proyek 5 ceritanya --}}
                    <a class="sidebar-menu-item {{ request()->is('user/projects/5') ? 'active' : '' }}"
                        href="{{ url('user/projects/5') }}">
                        <span class="material-symbols-outlined {{ request()->is('user/projects/5') ? 'filled' : '' }}">
                            home_work
                        </span>
                        Rumah Minimalist Jatinangor
                    </a>
                    {{-- nanti kan dari database, nah pake foreach, biar ga hardcoded 1 1 kayak gini semangat back end --}}
                </div>
            </div>
        </aside>
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-wrapper">
                <!-- Header Section -->
                <header class="page-header">
                    <div class="header-title">
                        <h1>Proyek Saya</h1>
                        <p>Kelola dan pantau progres pembangunan hunian impian Anda.</p>
                    </div>
                    <div class="header-badge">
                        <span class="badge">
                            <span class="material-symbols-outlined" style="">info</span>
                            4 Proyek
                        </span>
                    </div>
                </header>
                <!-- Project Grid (Asymmetric Layout) -->
                <div class="project-grid">
                    @yield('project_content')
                </div>
            </div>
        </main>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/customer/payment.js') }}"></script>
@endpush
