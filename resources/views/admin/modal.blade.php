<head>
    <link rel="stylesheet" href="{{ asset('css/admin/verifikasi.css') }}">
</head>

<body>
    <div class="modal-container">
        <!-- Modal Header -->
        <div class="modal-header">
            <div>
                <h3 class="modal-title">Preview Dokumen</h3>
                <p class="modal-subtitle">Pemohon: Budi Pratama | ID: #WY2-9901</p>
            </div>
            <button class="modal-close-btn">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <!-- Modal Content -->
        <div class="modal-content">
            <div class="doc-sidebar">
                <p class="doc-label">Dokumen Pengaju:</p>
                <div class="doc-list">
                    <button class="doc-item active" data-src="{{ url('/preview-pdf/docs-dummy.pdf') }}">
                        <div class="doc-info">
                            <span class="doc-name">Sertifikat (SHM)</span>
                            <span class="doc-status status-check">Sudah Diupload</span>
                        </div>
                    </button>

                    <button class="doc-item" data-src="{{ url('/preview-pdf/docs.jpg') }}">
                        <div class="doc-info">
                            <span class="doc-name">KTP Pemilik</span>
                            <span class="doc-status">Sudah Diupload</span>
                        </div>
                    </button>

                    <button class="doc-item" data-src="{{ url('/preview-pdf/user-dummy.jpg') }}">
                        <div class="doc-info">
                            <span class="doc-name">IMB / PBG</span>
                            <span class="doc-status status-check">Sudah Diupload</span>
                        </div>
                    </button>

                    <button class="doc-item" data-src="{{ url('/preview-pdf/docs-dummy.pdf') }}">
                        <div class="doc-info">
                            <span class="doc-name">Surat Kuasa</span>
                            <span class="doc-status status-check">Sudah Diupload</span>
                        </div>
                    </button>
                </div>
            </div>
            <div class="doc-preview-container">
                <div id="preview-placeholder" class="preview-wrapper">

                    {{-- Untuk preview gambar --}}
                    <img id="image-preview" src="" style="display:none; max-width:100%; height:auto;" />

                    {{-- Untuk PDF: tampilkan info + tombol download --}}
                    <div id="pdf-info"
                        style="display:none; flex-direction:column; align-items:center; justify-content:center; gap:16px; height:100%;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="#e74c3c"
                            viewBox="0 0 24 24">
                            <path
                                d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z" />
                        </svg>
                        <span id="pdf-filename" style="font-size:16px; font-weight:600; color:#333;"></span>
                        <a id="pdf-download-btn" href="#" download
                            style="padding:10px 24px; background:#3490dc; color:white; border-radius:8px; text-decoration:none; font-weight:500;">
                            Download PDF
                        </a>
                    </div>

                </div>
            </div>
        </div>
        <div class="delivery-address-form">
            <div class="delivery-address-grid">
                <div class="delivery-address-field full-width">
                    <label class="delivery-address-label" for="alasan_penolakan">Alasan Penolakan</label>
                    <textarea class="delivery-address-textarea" id="alasan_penolakan" placeholder="Tuliskan alasan penolakan berkas"></textarea>
                </div>
            </div>
        </div>
        <!-- Modal Footer -->
        <div class="modal-footer">
            <div class="modal-footer-buttons">
                <button class="modal-btn modal-btn-reject">
                    Tolak Berkas
                </button>
                <button class="modal-btn modal-btn-approve">
                    Setujui &amp; Verifikasi
                </button>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.doc-item').forEach(item => {
            item.addEventListener('click', function() {
                document.querySelectorAll('.doc-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                const fileUrl = this.getAttribute('data-src');
                const fileName = this.querySelector('.doc-name').textContent;
                const isPdf = fileUrl.toLowerCase().endsWith('.pdf');

                const pdfInfo = document.getElementById('pdf-info');
                const imgPreview = document.getElementById('image-preview');

                pdfInfo.style.display = 'none';
                imgPreview.style.display = 'none';

                if (isPdf) {
                    pdfInfo.style.display = 'flex';
                    document.getElementById('pdf-filename').textContent = fileName;
                    document.getElementById('pdf-download-btn').href = fileUrl;
                } else {
                    imgPreview.style.display = 'block';
                    imgPreview.src = fileUrl;
                }
            });
        });

        const firstItem = document.querySelector('.doc-item.active');
        if (firstItem) firstItem.click();
    </script>
</body>
