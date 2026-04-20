@extends('admin.admin_page')
@section('title')
    Admin - Material Management
@endsection
@section('header')
    <h2>Kelola Material</h2>
    <p> Kelola stok dan informasi material proyek.</p>
@endsection
@section('stats')
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <p class="stat-title">Total Material</p>
        <h3 class="stat-value">90000</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">task_alt</span>
            </div>
        </div>
        <p class="stat-title">Material Terjual</p>
        <h3 class="stat-value">60023</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">home_repair_service</span>
            </div>
        </div>
        <p class="stat-title">Material Stok Habis</p>
        <h3 class="stat-value">200</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">add_business</span>
            </div>
        </div>
        <p class="stat-title">Material Baru</p>
        <h3 class="stat-value">18</h3>
    </article>
@endsection
@section('content')
    <div class="stats-grid">
        <p>Konten tabel kelola material akan muncul di sini...</p>
    </div>
@endsection
