@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/profile.css') }}" />
@endpush
@section('content')
    <main class="profile-page-shell">
        <div class="profile-bg-shape profile-bg-shape-one"></div>
        <div class="profile-bg-shape profile-bg-shape-two"></div>

        <div class="profile-page-content">
            <a class="profile-back-link" href="/dashboard">
                <span class="material-symbols-outlined">arrow_back</span>
                <span>Kembali ke Dashboard</span>
            </a>

            <div class="profile-card">
                <div class="profile-card-inner">
                    <header class="profile-header">
                        <h1 class="profile-title">Edit Profile</h1>
                        <p class="profile-lead">Perbarui informasi personal dan preferensi akun Anda.</p>
                    </header>

                    <form class="profile-form">
                        <div class="profile-avatar-block">
                            <div class="profile-avatar-wrap">
                                <div class="profile-avatar-frame">
                                    <img src="{{ asset('images/aset/user-dummy.jpg') }}" alt="User">
                                </div>
                                <button class="profile-avatar-edit-btn" type="button" aria-label="Edit foto profil">
                                    <span class="material-symbols-outlined">edit</span>
                                </button>
                                <div class="profile-avatar-overlay">
                                    <span>Ganti Foto</span>
                                </div>
                            </div>
                            <p class="profile-avatar-hint">FORMAT JPG ATAU PNG. MAKS 2MB.</p>
                        </div>

                        <div class="profile-fields">
                            <div class="profile-field">
                                <label class="profile-label">
                                    <span class="material-symbols-outlined profile-label-icon">person</span>
                                    Nama Lengkap
                                </label>
                                <input class="profile-input" type="text" value="Robby Azwan" />
                            </div>

                            <div class="profile-field-grid">
                                <div class="profile-field">
                                    <label class="profile-label">
                                        <span class="material-symbols-outlined profile-label-icon">phone</span>
                                        Nomor HP
                                    </label>
                                    <input class="profile-input" type="tel" value="0813-8431-0179" />
                                </div>
                                <div class="profile-field">
                                    <label class="profile-label">
                                        <span class="material-symbols-outlined profile-label-icon">mail</span>
                                        Email
                                    </label>
                                    <input class="profile-input" type="email" value="oby.azwan@gmail.com" />
                                </div>
                            </div>

                            <div class="profile-field">
                                <label class="profile-label">
                                    <span class="material-symbols-outlined profile-label-icon">location_on</span>
                                    Alamat
                                </label>
                                <textarea class="profile-textarea" placeholder="Masukkan alamat lengkap Anda di Jawa Barat" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button class="profile-submit-btn" type="submit">
                                <span>Ubah Profile</span>
                                <span class="material-symbols-outlined">save</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
@endsection