@extends('admin.admin_page')
@push('styles')
    <link href="{{ asset('css/admin/verifikasi.css') }}" rel="stylesheet" />
@endpush
@section('title')
    Admin - Verfikasi Dokumen
@endsection
@section('header')
    <h2>Verifikasi Dokumen</h2>
    <p>Periksa kelengkapan dokumen legalitas mandor dan proyek.</p>
@endsection
@section('content')
    <div class="verifikasi-card">
        <div class="table-scroll">
            <table class="verifikasi-table">
                <thead>
                    <tr class="table-head-row">
                        <th class="table-heading">ID Pengajuan</th>
                        <th class="table-heading">Pemohon</th>
                        <th class="table-heading table-heading-center">SHM</th>
                        <th class="table-heading table-heading-center">KTP</th>
                        <th class="table-heading table-heading-center">IMB</th>
                        <th class="table-heading table-heading-center">Surat Kuasa</th>
                        <th class="table-heading">Alasan Penolakan</th>
                        <th class="table-heading table-heading-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <tr class="table-row">
                        <td class="table-cell table-cell-strong">#1</td>
                        <td class="table-cell">
                            <div class="applicant-cell">
                                <span class="applicant-name">Adi Saputra</span>
                            </div>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="placeholder-text">-</span>
                        </td>
                        <td class="table-cell table-cell-note">-</td>
                        <td class="table-cell table-cell-right">
                            <button class="btn btn-primary reviewed">Verified</button>
                        </td>
                    </tr>
                    <tr class="table-row">
                        <td class="table-cell table-cell-strong">#2</td>
                        <td class="table-cell">
                            <div class="applicant-cell">
                                <span class="applicant-name">Bambang Kusuma</span>
                            </div>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-note">-</td>
                        <td class="table-cell table-cell-right">
                            <button class="btn btn-primary reviewed">Verified</button>
                        </td>
                    </tr>
                    <tr class="table-row">
                        <td class="table-cell table-cell-strong">#3</td>
                        <td class="table-cell">
                            <div class="applicant-cell">
                                <span class="applicant-name">Citra Dewi</span>
                            </div>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-danger">Ditolak</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-success">Terverifikasi</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="placeholder-text">-</span>
                        </td>
                        <td class="table-cell table-cell-note">
                            <div class="note-box">
                                <p class="note-text">Masa berlaku KTP telah habis. Harap unggah KTP yang masih berlaku
                                    atau e-KTP.</p>
                            </div>
                        </td>
                        <td class="table-cell table-cell-right">
                            <button class="btn btn-primary rejected">Rejected</button>
                        </td>
                    </tr>
                    <tr class="table-row">
                        <td class="table-cell table-cell-strong">#4</td>
                        <td class="table-cell">
                            <div class="applicant-cell">
                                <span class="applicant-name">Eko Prasetyo</span>
                            </div>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-note">-</td>
                        <td class="table-cell table-cell-right">
                            <button class="btn btn-primary" onclick="openDocModal()">Review</button>
                        </td>
                    </tr>
                    <tr class="table-row">
                        <td class="table-cell table-cell-strong">#5</td>
                        <td class="table-cell">
                            <div class="applicant-cell">
                                <span class="applicant-name">Fahri Mukti</span>
                            </div>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="status-pill status-warning">Menunggu</span>
                        </td>
                        <td class="table-cell table-cell-center">
                            <span class="placeholder-text">-</span>
                        </td>
                        <td class="table-cell table-cell-note">-</td>
                        <td class="table-cell table-cell-right">
                            <button class="btn btn-primary" onclick="openDocModal()">Review</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-footer">
            <span class="footer-text">Menampilkan 1-5 dari 42 pengajuan baru</span>
            <div class="pagination">
                <button class="pagination-button pagination-button-icon">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <button class="pagination-button pagination-button-active">1</button>
                <button class="pagination-button">2</button>
                <button class="pagination-button">3</button>
                <button class="pagination-button pagination-button-icon">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
    <!-- Modal Overlay -->
    <div id="doc-modal" class="modal-overlay" style="display:none;">
        <div class="modal-container">
            <!-- Modal Header -->
            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Preview Dokumen</h3>
                    <p class="modal-subtitle">Pemohon: Budi Pratama | ID: #WY2-9901</p>
                </div>
                <button class="modal-close-btn" onclick="closeDocModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <!-- Modal Content -->
            <div class="modal-content">
                <div class="doc-viewer">
                    <div class="doc-sidebar">
                        <p class="doc-label">Dokumen Pengaju:</p>
                        <div class="doc-list">
                            <button class="doc-item active"
                                data-src="{{ route('admin.preview', ['filename' => 'shm.png']) }}">
                                <div class="doc-info">
                                    <span class="doc-name">Sertifikat (SHM)</span>
                                    <span class="doc-status status-check">Sudah Diupload</span>
                                </div>
                            </button>
                            <button class="doc-item" data-src="{{ route('admin.preview', ['filename' => 'ktp.png']) }}">
                                <div class="doc-info">
                                    <span class="doc-name">KTP Pemilik</span>
                                    <span class="doc-status status-check">Sudah Diupload</span>
                                </div>
                            </button>
                            <button class="doc-item" data-src="{{ route('admin.preview', ['filename' => 'imb.jpg']) }}">
                                <div class="doc-info">
                                    <span class="doc-name">IMB / PBG</span>
                                    <span class="doc-status status-check">Sudah Diupload</span>
                                </div>
                            </button>
                            <button class="doc-item"
                                data-src="{{ route('admin.preview', ['filename' => 'surat-kuasa.pdf']) }}">
                                <div class="doc-info">
                                    <span class="doc-name">Surat Kuasa</span>
                                    <span class="doc-status status-check">Sudah Diupload</span>
                                </div>
                            </button>
                        </div>
                    </div>
                    <div class="doc-preview-container">
                        <div id="preview-placeholder" class="preview-wrapper">
                            <img id="image-preview" src="" style="display:none; max-width:100%; height:auto;" />
                            <div id="pdf-info"
                                style="display:none; flex-direction:column; align-items:center; justify-content:center; gap:16px; height:100%;">
                                <span class="material-symbols-outlined"
                                    style="font-size:64px; color:#e74c3c;">picture_as_pdf</span>
                                <span id="pdf-filename" style="font-size:16px; font-weight:600; color:#333;"></span>
                                <a id="pdf-download-btn" href="" download
                                    style="padding:10px 24px; background:#3490dc; color:white; border-radius:8px; text-decoration:none; font-weight:500;">
                                    Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="reason-reject-form">
                <div class="reason-reject-grid">
                    <div class="reason-reject-field full-width">
                        <label class="reason-reject-label" for="alasan_penolakan">Alasan Penolakan</label>
                        <textarea class="reason-reject-textarea" id="alasan_penolakan" placeholder="Tuliskan alasan penolakan berkas"></textarea>
                    </div>
                </div>
            </div>
            <!-- Modal Footer -->
            <div class="modal-footer">
                <div class="modal-footer-buttons-left">
                    <button class="modal-btn modal-btn-reject">Tolak</button>
                    <button class="modal-btn modal-btn-approve">Verifikasi</button>
                </div>
                <div class="modal-footer-buttons-right">
                    <button class="modal-btn modal-btn-submit disabled">Submit</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/admin/verifikasi.js') }}"></script>
@endpush
