@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/renovation_form.css') }}">
@endpush
@section('content')
    <main class="rf-main">
        <!-- Abstract Background Decorative Elements -->
        <div class="rf-main-bg" aria-hidden="true">
            <div class="rf-bg-circle rf-bg-circle-blue"></div>
            <div class="rf-bg-circle rf-bg-circle-orange"></div>
        </div>
        <!-- Form Card Container -->
        <div class="rf-form-card">
            <!-- Form Header Section -->
            <div class="rf-form-header">
                <h1 class="rf-form-title">Form Pengajuan Renovasi</h1>
                <p class="rf-form-subtitle">Ajukan permohonan renovasi rumah Anda dengan mudah.</p>
            </div>
            <!-- The Form -->
            <form action="#" class="rf-form-body">
                <!-- Row 1: Personal Info -->
                <div class="rf-grid-2">
                    <div class="rf-field-group">
                        <label class="rf-label">Nama
                            Lengkap</label>
                        <input class="rf-input rf-input-readonly" readonly="" type="text" value="Budi Arsitek" />
                    </div>
                    <div class="rf-field-group">
                        <label class="rf-label">Nomor
                            HP</label>
                        <input class="rf-input rf-input-readonly" readonly="" type="text" value="0812-3456-7890" />
                    </div>
                </div>
                <!-- Row 2: Budget -->
                <div class="rf-field-group">
                    <label class="rf-label">Estimasi
                        Budget</label>
                    <div class="rf-input-wrap">
                        <div class="rf-input-prefix">
                            Rp</div>
                        <input class="rf-input rf-input-budget" placeholder="0" type="number" />
                    </div>
                </div>
                <!-- Row 3: Description -->
                <div class="rf-field-group">
                    <label class="rf-label">Deskripsi
                        Kerusakan / Keinginan</label>
                    <textarea class="rf-textarea" placeholder="Deskripsikan kerusakan atau keinginan renovasi Anda." rows="4"></textarea>
                </div>
                <!-- Row 4: Photo Upload -->
                <div class="rf-upload-section">
                    <label class="rf-label">Upload
                        Foto Kerusakan</label>
                    <div class="rf-upload-box">
                        <span class="material-symbols-outlined rf-upload-icon" data-icon="cloud_upload">cloud_upload</span>
                        <p class="rf-upload-title">Drag and drop files or <span class="rf-upload-link">browse</span></p>
                        <p class="rf-upload-subtitle">PNG, JPG up to 10MB</p>
                    </div>
                    <!-- Previews -->
                    <div class="rf-preview-grid">
                        <!-- Preview -->
                        <div class="rf-preview-item">
                            <img src="{{ asset('images/aset/user-dummy.jpg') }}" alt="Preview 1" class="rf-preview-image" />
                            <button class="rf-preview-remove-btn" type="button">
                                <span class="material-symbols-outlined rf-preview-remove-icon"
                                    data-icon="close">close</span>
                            </button>
                        </div>
                        <div class="rf-preview-item">
                            <img src="{{ asset('images/aset/user-dummy.jpg') }}" alt="Preview 2" class="rf-preview-image" />
                            <button class="rf-preview-remove-btn" type="button">
                                <span class="material-symbols-outlined rf-preview-remove-icon"
                                    data-icon="close">close</span>
                            </button>
                        </div>
                        <div class="rf-preview-item">
                            <img src="{{ asset('images/aset/user-dummy.jpg') }}" alt="Preview 3" class="rf-preview-image" />
                            <button class="rf-preview-remove-btn" type="button">
                                <span class="material-symbols-outlined rf-preview-remove-icon"
                                    data-icon="close">close</span>
                            </button>
                        </div>
                    </div>
                    <div class="rf-upload-note">
                        <span class="material-symbols-outlined rf-upload-note-icon" data-icon="info">info</span>
                        <p class="rf-upload-note-text">Minimal 1 gambar dan maksimal 6 gambar</p>
                    </div>
                </div>
                <!-- Submit Button -->
                <div class="rf-submit-wrap">
                    <button class="rf-submit-btn" type="submit">
                        <span>Submit Request</span>
                        <span class="material-symbols-outlined rf-submit-icon" data-icon="send">send</span>
                    </button>
                </div>
            </form>
        </div>
    </main>
@endsection
