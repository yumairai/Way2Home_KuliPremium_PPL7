// ─── Modal open/close ────────────────────────────────────────────────────────


// ─── Bulk Actions: Tolak Semua / Verifikasi Semua ────────────────────────────

function approveAllDocs() {
    const allItems = document.querySelectorAll('.doc-item');

    allItems.forEach(item => {
        item.classList.remove('rejected');
        item.classList.add('verified');

        const statusEl = item.querySelector('.doc-status');
        statusEl.textContent = 'Terverifikasi';
        statusEl.className = 'doc-status status-verified';

        item.querySelector('.doc-status-input').value = 'disetujui';
    });

    checkSubmitEligibility();
}

function rejectAllDocs() {
    const allItems = document.querySelectorAll('.doc-item');

    allItems.forEach(item => {
        item.classList.remove('verified');
        item.classList.add('rejected');

        const statusEl = item.querySelector('.doc-status');
        statusEl.textContent = 'Ditolak';
        statusEl.className = 'doc-status status-rejected';

        item.querySelector('.doc-status-input').value = 'ditolak';
    });

    checkSubmitEligibility();
}
function openDocModal(triggerBtn) {
    const proyekId = triggerBtn.dataset.id;
    const pemohon = triggerBtn.dataset.name;

    let documents;
    try {
        documents = JSON.parse(triggerBtn.dataset.documents);
    } catch (e) {
        console.error('Error parsing documents JSON:', e);
        W2HDialog.error('Gagal memuat data dokumen. Coba refresh halaman.');
        return;
    }

    const modal = document.getElementById('doc-modal');
    const subtitle = document.querySelector('.modal-subtitle');
    const form = document.getElementById('doc-form');

    if (!modal || !subtitle || !form) {
        console.error('Modal elements not found');
        W2HDialog.error('Modal tidak ditemukan. Coba refresh halaman.');
        return;
    }

    // Isi header modal
    subtitle.textContent = `Pemohon: ${pemohon} | ID: #${proyekId}`;

    // Arahkan form ke route update yang benar
    form.action = `/admin/verifikasi/${proyekId}`;

    // Render daftar dokumen secara dinamis dari database
    renderDocList(documents);

    // Tampilkan modal
    modal.style.display = 'flex';

    // Aktifkan dokumen pertama
    const firstItem = document.querySelector('.doc-item');
    if (firstItem) {
        firstItem.classList.add('active');
        firstItem.click();
    }
}

function closeDocModal() {
    document.getElementById('doc-modal').style.display = 'none';
    document.getElementById('alasan_penolakan').value = '';
}


// ─── Render daftar dokumen dari data controller ───────────────────────────────

function renderDocList(documents) {
    const docList = document.querySelector('.doc-list');
    docList.innerHTML = '';

    documents.forEach(doc => {
        // Tentukan label status awal berdasarkan status_verifikasi dari DB
        const statusLabel = {
            'disetujui': 'Terverifikasi',
            'ditolak': 'Ditolak',
            'pending': 'Menunggu',
        }[doc.status_verifikasi] ?? 'Menunggu';

        const statusClass = {
            'disetujui': 'status-verified',
            'ditolak': 'status-rejected',
            'pending': 'status-check',
        }[doc.status_verifikasi] ?? 'status-check';

        const isPdf = doc.file_url.toLowerCase().endsWith('.pdf');

        // Tambahkan hidden input agar status tiap dokumen ikut ter-submit
        const hiddenInput = `<input type="hidden" name="status_dokumen[${doc.id}]" class="doc-status-input" value="${doc.status_verifikasi}">`;
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'doc-item';
        btn.dataset.src = doc.file_url;
        btn.dataset.docId = doc.id;

        // Tandai class awal jika sudah ada status dari DB
        if (doc.status_verifikasi === 'disetujui') btn.classList.add('verified');
        if (doc.status_verifikasi === 'ditolak') btn.classList.add('rejected');

        btn.innerHTML = `
            <div class="doc-info">
                <span class="doc-name">${doc.nama_dokumen}</span>
                <span class="doc-status ${statusClass}">${statusLabel}</span>
            </div>
        `;

        // Simpan hidden input di dalam tombol agar mudah diakses
        btn.insertAdjacentHTML('beforeend', hiddenInput);

        docList.appendChild(btn);

        // Event: klik dokumen untuk preview
        btn.addEventListener('click', function () {
            document.querySelectorAll('.doc-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            previewFile(doc.file_url, doc.nama_dokumen, isPdf);
        });
    });
}


// ─── Preview file (gambar / PDF) ──────────────────────────────────────────────

function previewFile(fileUrl, fileName, isPdf) {
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
}


// ─── Event listeners (tombol Verifikasi / Tolak / Submit) ────────────────────

document.addEventListener('DOMContentLoaded', function () {

    // Tombol Verifikasi
    document.querySelector('.modal-btn-approve').addEventListener('click', function () {
        const activeItem = document.querySelector('.doc-item.active');
        if (!activeItem) return;

        activeItem.classList.remove('rejected');
        activeItem.classList.add('verified');

        const statusEl = activeItem.querySelector('.doc-status');
        statusEl.textContent = 'disetujui';
        statusEl.className = 'doc-status status-verified';

        // Update hidden input supaya nilai yang di-submit ke controller ikut berubah
        activeItem.querySelector('.doc-status-input').value = 'disetujui';

        checkSubmitEligibility();
    });

    // Tombol Tolak
    document.querySelector('.modal-btn-reject').addEventListener('click', function () {
        const activeItem = document.querySelector('.doc-item.active');
        if (!activeItem) return;

        activeItem.classList.remove('verified');
        activeItem.classList.add('rejected');

        const statusEl = activeItem.querySelector('.doc-status');
        statusEl.textContent = 'Ditolak';
        statusEl.className = 'doc-status status-rejected';

        activeItem.querySelector('.doc-status-input').value = 'ditolak';

        checkSubmitEligibility();
    });

    // Tombol Submit — kirim form ke controller
    document.querySelector('.modal-btn-submit').addEventListener('click', async function () {
        if (this.disabled) return;

        const hasRejected = document.querySelectorAll('.doc-item.rejected').length > 0;
        const alasan = document.getElementById('alasan_penolakan').value.trim();

        if (hasRejected && !alasan) {
            await W2HDialog.alert('Mohon isi alasan penolakan sebelum submit.');
            document.getElementById('alasan_penolakan').focus();
            return;
        }

        // Tentukan status_proyek otomatis:
        // Jika ada dokumen ditolak → 'Revisi Dokumen', jika semua oke → 'Pembayaran DP'
        const statusProyek = hasRejected ? 'Revisi Dokumen' : 'Pembayaran DP';
        document.getElementById('status-proyek-input').value = statusProyek;

        document.getElementById('doc-form').submit();
    });

    // Re-check saat admin mengetik alasan penolakan
    document.getElementById('alasan_penolakan').addEventListener('input', checkSubmitEligibility);
});


// ─── Cek apakah semua dokumen sudah di-review ────────────────────────────────

function checkSubmitEligibility() {
    const allItems = document.querySelectorAll('.doc-item');
    const submitBtn = document.querySelector('.modal-btn-submit');

    const allReviewed = [...allItems].every(item =>
        item.classList.contains('verified') || item.classList.contains('rejected')
    );

    const hasRejected = document.querySelectorAll('.doc-item.rejected').length > 0;
    const alasan = document.getElementById('alasan_penolakan').value.trim();
    const alasanOk = !hasRejected || alasan.length > 0;

    submitBtn.disabled = !(allReviewed && alasanOk);
}