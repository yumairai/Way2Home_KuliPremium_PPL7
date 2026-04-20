@extends('admin.admin_page')
@section('title')
    Admin - Dashboard
@endsection

@section('header')
    <h2>Dashboard Overview</h2>
    <p>Welcome back. Monitoring structural integrity and project pipelines for Way2Home.</p>
@endsection
@section('stats')
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <p class="stat-title">Total User</p>
        <h3 class="stat-value">1,284</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">task_alt</span>
            </div>
        </div>
        <p class="stat-title">Proyek Selesai</p>
        <h3 class="stat-value">312</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">home_repair_service</span>
            </div>
        </div>
        <p class="stat-title">Permintaan Renovasi</p>
        <h3 class="stat-value">45</h3>
    </article>
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">add_business</span>
            </div>
        </div>
        <p class="stat-title">Pembangunan Rumah</p>
        <h3 class="stat-value">18</h3>
    </article>
    <article class="stat-card special">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(255, 255, 255, 0.2); color: #fff;">
                <span class="material-symbols-outlined">payments</span>
            </div>
        </div>
        <p class="stat-title" style="color: rgba(235,245,255,0.9);">Total Revenue</p>
        <h3 class="stat-value" style="font-size:1.25rem;">Rp 17.523.500.000</h3>
    </article>
@endsection
@section('content')
    <div class="project-section-header">
        <div>
            <h3>Daftar Proyek Terbaru</h3>
            <p>Monitoring status dan progres pengerjaan di seluruh wilayah.</p>
        </div>
        <div class="project-actions">
            <button class="btn btn-secondary" type="button">Filter</button>
            <button class="btn btn-primary" type="button">Export Data</button>
        </div>
    </div>
    <div class="table-wrapper">
        <table class="project-table">
            <thead>
                <tr>
                    <th>Judul Proyek</th>
                    <th>Nama Pemilik</th>
                    <th>Kategori Proyek</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th style="text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="project-card">
                            <div class="project-thumb">
                                <img data-alt="Modern minimalist luxury villa architectural render with pool and large windows"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBOmakkHuGbG_1TkrEKAD6jv7YTJX19KBBhkOJ-ARVr8-bmLgt85IGzTMblCmesPT2MooxRKgWJ94s-apEdBOxNX4PFIBiy9Pvqm97DQsM3yi8Ag8gQ4zXh8a3FmbDYDpUwFoKKqhqpageAbc3zsWlgNxJqj6jrEQxWCV43OmRyOqGzDu2-c1E65085F2XM8-M8qLwNIRL1qI9QhgpxPuh_iCrUt3r2ESIXre975nrD8MocpMXZQj3cdjwPMMYKR82e58abiaR029c" />
                            </div>
                            <div>
                                <p class="project-title">Luxury Villa Kemang</p>
                                <p class="project-id">ID: W2H-99210</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="project-owner">ROBBY GANTENG</span>
                    </td>
                    <td>
                        <span class="badge primary">Bangun Rumah</span>
                    </td>
                    <td>
                        <div class="status-pill blue">
                            <span class="status-dot"></span>
                            <span>On Going</span>
                        </div>
                    </td>
                    <td>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:65%;"></div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <button class="icon-button" type="button">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="project-card">
                            <div class="project-thumb">
                                <img data-alt="Interior renovation showing clean white walls and modern wooden flooring installation"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBuNVBvmYcRrwT_4FkMAq8oCXrfbGS_1JKCli5GwNxB6h2KrS8HpZqbmVPoPb7bzuUstPGfklMXR-zjk2VVk6-umkTpjfB-wqJFdQt9j4mob73wL-dv5bwOtlTdEEY2BxmrDTulsCZF2YpqRU4wq5pgQTewgTIUKGagnZFFPfPnWHnxKOu0JP9CGx-CKo9kd9FBerUqYR_CYbtj5MxeJBcEnUl2hwTDLtlKO2Wd6Ie0gUnvDrx_0P-w926SgLCBfZkvFaWuYGyK2Q" />
                            </div>
                            <div>
                                <p class="project-title">Apartment BSD Unit 4B</p>
                                <p class="project-id">ID: W2H-88312</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="project-owner">ROBBY GANTENG</span>
                    </td>
                    <td>
                        <span class="badge warning">Renovasi</span>
                    </td>
                    <td>
                        <div class="status-pill green">
                            <span class="status-dot"></span>
                            <span>Selesai</span>
                        </div>
                    </td>
                    <td>
                        <div class="progress-track">
                            <div class="progress-fill green" style="width:100%;"></div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <button class="icon-button" type="button">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="project-card">
                            <div class="project-thumb">
                                <img data-alt="Exterior glass facade of a modern contemporary house in an urban setting"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAyW-pl942y86jgmNtBnCZeiZVlJZ2KPGVY3JmXZIADJvqzIMFu_XKplUPpsnPZUsp2JLZufN8vwre68_J5KpB0l0O5RJfJfMmSo1BdgnXcPvTvNrhAkEzJqgFCJy8ba6FlXUjiWa-NfazjoWxyLd662S874y2qg-ddwKzhsLI17nDl8q-xwfC_i9p3-foKMXMPZi4Pt9kXk8IbbpVIyy-QFWMR2Q7CR5_KmSVVPvlUfLDxI_kcfEH8TbsozjmDnagBasTXp-qUuyM" />
                            </div>
                            <div>
                                <p class="project-title">Contemporary Menteng House</p>
                                <p class="project-id">ID: W2H-77441</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="project-owner">ROBBY GANTENG</span>
                    </td>
                    <td>
                        <span class="badge primary">Bangun Rumah</span>
                    </td>
                    <td>
                        <div class="status-pill red">
                            <span class="status-dot"></span>
                            <span>Canceled</span>
                        </div>
                    </td>
                    <td>
                        <div class="progress-track">
                            <div class="progress-fill red" style="width:15%;"></div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <button class="icon-button" type="button">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="project-card">
                            <div class="project-thumb">
                                <img data-alt="High-end modern kitchen renovation with marble islands and premium cabinetry"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBTU4ilWBnRUFOPx5o8iSaQFokfqSO9iCd-uOOssv-tTUE5L5MMOjFbRR8eHwNUdO-uMfD4q6bA6WgBiTKpCqC5PbkFO_oseSkGFpUkYQoZlIKlDAuqguKV8rdoutEwUrSROdtPpSt4DjRbgGTl3AwUTWW_NPzMxgG_gh9Oqv6bscxHGkkungopwnhG6p5i4JZwLzoc2qkKAwITsAH8rXds8CiVthNVB1lonzY0Gi_TOJ-JMj4EYuBVzI6qAS7jhTFobvQrv0yDCuU" />
                            </div>
                            <div>
                                <p class="project-title">Kitchen Redesign - Pak Ali</p>
                                <p class="project-id">ID: W2H-66550</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="project-owner">ROBBY GANTENG</span>
                    </td>
                    <td>
                        <span class="badge warning">Renovasi</span>
                    </td>
                    <td>
                        <div class="status-pill blue">
                            <span class="status-dot"></span>
                            <span>On Going</span>
                        </div>
                    </td>
                    <td>
                        <div class="progress-track">
                            <div class="progress-fill" style="width:42%;"></div>
                        </div>
                    </td>
                    <td style="text-align:right;">
                        <button class="icon-button" type="button">
                            <span class="material-symbols-outlined">more_vert</span>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="project-footer">
        <p class="pagination-text">Showing 4 of 312 Projects</p>
        <div class="pagination-group">
            <button class="pagination-button" type="button">
                <span class="material-symbols-outlined">chevron_left</span>
            </button>
            <button class="pagination-button active" type="button">1</button>
            <button class="pagination-button" type="button">2</button>
            <button class="pagination-button" type="button">
                <span class="material-symbols-outlined">chevron_right</span>
            </button>
        </div>
    </div>
@endsection
