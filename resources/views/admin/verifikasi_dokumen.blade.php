@extends('admin.admin_page')
@push('styles')
    <link href="{{ asset('css/admin/verifikasi.css') }}" rel="stylesheet" />
@endpush
@section('title')
    Admin - Verifikasi Dokumen
@endsection
@section('header')
    <h2>Verifikasi Dokumen</h2>
    <p>Periksa kelengkapan dokumen legalitas mandor dan proyek.</p>
@endsection
@section('stats')
    <div class="verifikasi-stat-card">
        <p class="verifikasi-stat-label">Total Pengajuan Verifikasi</p>
        <p class="verifikasi-stat-value">3</p>
    </div>
    <div class="verifikasi-stat-card waiting">
        <p class="verifikasi-stat-label">Pending</p>
        <p class="verifikasi-stat-value">3</p>
    </div>
    <div class="verifikasi-stat-card verified">
        <p class="verifikasi-stat-label">Disetujui</p>
        <p class="verifikasi-stat-value">0</p>
    </div>
    <div class="verifikasi-stat-card rejected">
        <p class="verifikasi-stat-label">Ditolak</p>
        <p class="verifikasi-stat-value">0</p>
    </div>
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
                    @forelse($proyek as $item)
                        @php
                            $dokumen = $item->detailBangun->dokumenProyek ?? collect();

                            $hasRejected = $dokumen->contains('status_verifikasi', 'ditolak');

                            $isFinal = $dokumen->every(function ($doc) {
                                return in_array($doc->status_verifikasi, ['disetujui', 'ditolak']);
                            });
                        @endphp
                        <tr class="table-row">
                            <td class="table-cell table-cell-strong">#{{ $item->id }}</td>
                            <td class="table-cell">{{ $item->customer->user->name }}</td>

                            {{-- Status Pills --}}
                            @foreach (['Sertifikat Tanah', 'KTP Pemilik', 'IMB/PBG', 'Surat Kuasa'] as $jenis)
                                @php $doc = $dokumen->firstWhere('jenis_dokumen', $jenis); @endphp
                                <td class="table-cell table-cell-center">
                                    @if ($doc)
                                        <span
                                            class="status-pill {{ $doc->status_verifikasi == 'disetujui' ? 'status-success' : ($doc->status_verifikasi == 'ditolak' ? 'status-danger' : 'status-warning') }}">
                                            {{ ucfirst($doc->status_verifikasi) }}
                                        </span>
                                    @else
                                        <span class="placeholder-text">-</span>
                                    @endif
                                </td>
                            @endforeach

                            <td class="table-cell table-cell-note">{{ $item->detailBangun->catatan_admin ?? '-' }}</td>
                            <td class="table-cell table-cell-right">
                                @if ($isFinal)
                                    <button class="btn btn-secondary" disabled>
                                        {{ $hasRejected ? 'Rejected' : 'Verified' }}
                                    </button>
                                @else
                                    <button class="btn btn-primary" onclick="openDocModal(this)"
                                        data-id="{{ $item->id }}" data-name="{{ $item->customer->user->name }}"
                                        data-documents="{{ json_encode(
                                            $dokumen->map(function ($d) {
                                                return [
                                                    'id' => $d->id,
                                                    'nama_dokumen' => $d->jenis_dokumen,
                                                    'file_url' => $d->signed_url, // ← ubah ini
                                                    'status_verifikasi' => $d->status_verifikasi,
                                                ];
                                            }),
                                        ) }}">
                                        Review
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    @endforelse
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
        <form id="doc-form" method="POST" class="modal-container">
            @csrf
            @method('PUT')

            <input type="hidden" name="status_proyek" id="status-proyek-input" value="">

            <div class="modal-header">
                <div>
                    <h3 class="modal-title">Preview Dokumen</h3>
                    <p class="modal-subtitle">Memuat data...</p>
                </div>
                <button type="button" class="modal-close-btn" onclick="closeDocModal()">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div class="modal-content">
                <div class="doc-viewer">
                    <div class="doc-sidebar">
                        <p class="doc-label">Dokumen Pengaju:</p>
                        <div class="doc-list">
                        </div>
                        <div class="doc-bulk-actions">
                            <button type="button" class="doc-bulk-btn doc-bulk-btn-reject" onclick="rejectAllDocs()">
                                Tolak Semua
                            </button>
                            <button type="button" class="doc-bulk-btn doc-bulk-btn-approve" onclick="approveAllDocs()">
                                Verifikasi Semua
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
                        <textarea name="catatan_admin" class="reason-reject-textarea" id="alasan_penolakan"
                            placeholder="Tuliskan alasan penolakan berkas jika ada dokumen yang ditolak"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="modal-footer-buttons-left">
                    <button type="button" class="modal-btn modal-btn-reject">Tolak</button>
                    <button type="button" class="modal-btn modal-btn-approve">Verifikasi</button>
                </div>
                <div class="modal-footer-buttons-right">
                    <button type="submit" class="modal-btn modal-btn-submit" disabled>Submit</button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/admin/verifikasi.js') }}"></script>
@endpush
