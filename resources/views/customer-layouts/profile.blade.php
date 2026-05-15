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

                    <form class="profile-form" method="POST" action="/profile" enctype="multipart/form-data">
                        @csrf

                        <div class="profile-avatar-block">
                            <div class="profile-avatar-wrap">
                                <div class="profile-avatar-frame">
                                    <img alt="avatar" id="avatarPreview"
                                        src="{{ $user->avatar ?? asset('images/aset/avatar.jpg') }}" />

                                    <input type="file" name="avatar" id="avatarInput"
                                        accept="image/jpg,image/jpeg,image/png" style="display:none" />

                                    <button class="profile-avatar-edit-btn" type="button"
                                        onclick="document.getElementById('avatarInput').click()"
                                        aria-label="Edit foto profil">
                                        <span class="material-symbols-outlined">edit</span>
                                    </button>
                                    <div class="profile-avatar-overlay" onclick="document.getElementById('avatarInput').click()">
                                        <span>Ganti Foto</span>
                                    </div>
                                </div>
                                <p class="profile-avatar-hint">jpg/png maks 2mb</p>
                            </div>
                        </div>

                        <div class="profile-fields">
                            <div class="profile-field">
                                <label class="profile-label">
                                    <span class="material-symbols-outlined profile-label-icon">person</span>
                                    Nama Lengkap
                                </label>
                                <input class="profile-input" type="text" name="name" value="{{ $user->name }}">
                            </div>

                            <div class="profile-field-grid">
                                <div class="profile-field">
                                    <label class="profile-label">
                                        <span class="material-symbols-outlined profile-label-icon">phone</span>
                                        Nomor HP
                                    </label>
                                    <input class="profile-input" type="tel" name="phone" value="{{ $user->phone_number }}">
                                </div>
                                <div class="profile-field">
                                    <label class="profile-label">
                                        <span class="material-symbols-outlined profile-label-icon">mail</span>
                                        Email
                                    </label>
                                    <input class="profile-input" type="email" name="email" value="{{ $user->email }}">
                                </div>
                            </div>

                            <div class="profile-field">
                                <label class="profile-label">
                                    <span class="material-symbols-outlined profile-label-icon">location_on</span>
                                    Alamat
                                </label>
                                <textarea class="profile-textarea" name="address" rows="3">{{ $user->address }}</textarea>
                            </div>
                        </div>

                        <div class="profile-actions">
                            <button class="profile-submit-btn" type="submit" id="submitBtn" disabled>
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

@push('scripts')
<script>
    document.getElementById('avatarInput').addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => document.getElementById('avatarPreview').src = e.target.result;
        reader.readAsDataURL(file);
    });

    const submitBtn = document.getElementById('submitBtn');
    const form = document.querySelector('.profile-form');

    const initialValues = {};
    form.querySelectorAll('input, textarea').forEach(field => {
        if (field.name) initialValues[field.name] = field.value;
    });

    function checkChanges() {
        let hasChange = false;

        form.querySelectorAll('input, textarea').forEach(field => {
            if (field.type === 'file') {
                if (field.files.length > 0) hasChange = true;
            } else if (field.name && initialValues[field.name] !== field.value) {
                hasChange = true;
            }
        });

        submitBtn.disabled = !hasChange;
    }

    form.querySelectorAll('input, textarea').forEach(field => {
        field.addEventListener('input', checkChanges);
        field.addEventListener('change', checkChanges);
    });
</script>
@endpush
