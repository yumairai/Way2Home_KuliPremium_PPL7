@extends('customer-layouts.main')
@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer/proyek_user.css') }}">
@endpush

@section('content')
<div class="page-container">
    <aside class="sidebar-nav">
        <div class="sidebar-section">
            <p class="sidebar-title">List Proyek</p>
            <div class="sidebar-menu">
                @foreach ($proyeks as $item)
                <a class="sidebar-menu-item {{ request()->is('proyek/' . $item->id) ? 'active' : '' }}"
                    href="{{ url('proyek/' . $item->id) }}">
                    <span class="material-symbols-outlined {{ request()->is('proyek/' . $item->id) ? 'filled' : '' }}">
                        home_work
                    </span>
                    {{ $item->detailBangun?->desainRumah?->tipe_rumah ?? $item->jenis_proyek }}
                </a>
                @endforeach
            </div>
        </div>
    </aside>

    <main class="main-content">
        <div class="content-wrapper">
            <header class="page-header">
                <div class="header-title">
                    <h1>Proyek Saya</h1>
                    <p>Kelola dan pantau progres pembangunan hunian impian Anda.</p>
                </div>
                <div class="header-badge">
                    <span class="badge">
                        <span class="material-symbols-outlined">info</span>
                        {{ $proyeks->count() }} Proyek
                    </span>
                </div>
            </header>

            <div class="project-grid">
                @yield('project_content')
            </div>
        </div>
    </main>
</div>
@endsection

@push('scripts')
<script type="text/javascript"
    src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
</script>

<script
    src="{{ asset('js/customer/payment_proyek.js') }}">
</script>
@endpush