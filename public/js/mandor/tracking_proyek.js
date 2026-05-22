const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';

// ── Renovasi done ──────────────────────────────
(function () {
    const doneButton = document.getElementById('mark-renovation-done-btn');
    if (!doneButton) return;
    doneButton.addEventListener('click', async function () {
        const requestId = doneButton.getAttribute('data-request-id');
        if (!requestId) return;
        const response = await fetch(`/mandor/renovation/${requestId}/done`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({})
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok) { await W2HDialog.error(data.message || 'Gagal menandai renovasi selesai.'); return; }
        await W2HDialog.success(data.message || 'Renovasi berhasil ditandai selesai.');
        window.location.reload();
    });
})();

// ── Renovasi documentation (frontend only) ───
(function () {
    var uploadSection  = document.querySelector('[data-proyek-id]');
    if (!uploadSection) return; // Bukan halaman renovasi (atau proyek_id tidak ada)
 
    var proyekId       = uploadSection.getAttribute('data-proyek-id');
    if (!proyekId) return;
 
    var input          = document.getElementById('renovation-doc-input');
    var dropzone       = document.getElementById('renovation-dropzone');
    var previewGrid    = document.getElementById('renovation-preview-grid');
    var dropzoneError  = document.getElementById('renovation-dropzone-error');
 
    if (!input || !dropzone || !previewGrid) return;
 
    var MAX_FILE_SIZE  = 5 * 1024 * 1024; // 5MB — sama dengan bangun
    var UPLOAD_URL     = '/mandor/proyek/' + proyekId + '/dokumentasi'; // route yang sama!
 
    var clearError = function () {
        if (dropzoneError) {
            dropzoneError.textContent = '';
            dropzone.classList.remove('is-error');
        }
    };
 
    var setError = function (msg) {
        if (dropzoneError) {
            dropzoneError.textContent = msg;
            dropzone.classList.add('is-error');
        }
    };
 
    var appendPhotoCard = function (fotoUrl, tanggal) {
        var card = document.createElement('div');
        card.className = 'mandor-doc-photo-card';
        card.innerHTML =
            '<img src="' + fotoUrl + '" alt="Dokumentasi renovasi" class="mandor-doc-photo" loading="lazy">' +
            '<div class="mandor-doc-overlay"><span class="mandor-doc-caption">' + tanggal + '</span></div>';
 
        // Sisipkan setelah foto existing (sebelum foto lain yang baru diupload)
        if (previewGrid.firstChild) {
            previewGrid.insertBefore(card, previewGrid.firstChild);
        } else {
            previewGrid.appendChild(card);
        }
    };
 
    var appendLoadingCard = function () {
        var card = document.createElement('div');
        card.className = 'mandor-doc-photo-card mandor-doc-loading';
        card.innerHTML = '<span class="material-symbols-outlined spinning">progress_activity</span>';
        previewGrid.insertBefore(card, previewGrid.firstChild);
        return card;
    };
 
    var uploadFile = function (file) {
        clearError();
 
        if (!file.type.startsWith('image/')) {
            setError('File harus berupa gambar.');
            return;
        }
 
        if (file.size > MAX_FILE_SIZE) {
            setError('Ukuran foto ' + file.name + ' melebihi 5MB.');
            return;
        }
 
        var loadingCard = appendLoadingCard();
        var formData = new FormData();
        formData.append('foto', file);
 
        fetch(UPLOAD_URL, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData,
        })
        .then(function (res) {
            return res.json().then(function (data) {
                return { ok: res.ok, data: data };
            });
        })
        .then(function (result) {
            loadingCard.remove();
            if (!result.ok) {
                setError(result.data.message || 'Gagal upload foto.');
                return;
            }
            // uploadDokumentasi di controller return { foto_url, tanggal }
            appendPhotoCard(result.data.foto_url, result.data.tanggal);
        })
        .catch(function () {
            loadingCard.remove();
            setError('Koneksi gagal. Coba lagi.');
        });
    };
 
    var handleFiles = function (fileList) {
        Array.from(fileList || []).forEach(uploadFile);
    };
 
    input.addEventListener('change', function () {
        handleFiles(this.files);
        this.value = '';
    });
 
    dropzone.addEventListener('click', function () { input.click(); });
    dropzone.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); input.click(); }
    });
 
    ['dragenter', 'dragover'].forEach(function (ev) {
        dropzone.addEventListener(ev, function (e) {
            e.preventDefault();
            clearError();
            dropzone.classList.add('is-dragover');
        });
    });
 
    ['dragleave', 'drop'].forEach(function (ev) {
        dropzone.addEventListener(ev, function (e) {
            e.preventDefault();
            dropzone.classList.remove('is-dragover');
            if (ev === 'drop' && e.dataTransfer && e.dataTransfer.files.length) {
                handleFiles(e.dataTransfer.files);
            }
        });
    });
})();

// ── Show More Task ─────────────────────────────
let isExpanded = false;

function toggleShowMore() {
    isExpanded = !isExpanded;
    const btn = document.getElementById('show-more-btn');
    reorderAndRenderTasks(isExpanded);
    btn.innerHTML = isExpanded
        ? '<span class="material-symbols-outlined">expand_less</span> Sembunyikan'
        : '<span class="material-symbols-outlined">expand_more</span> Lihat Semua Task';
}

function reorderAndRenderTasks(expanded) {
    const list = document.getElementById('task-list');
    const items = Array.from(list.querySelectorAll('.mandor-task-item'));

    // Pisah completed dan belum
    const pending = items.filter(el => !el.classList.contains('completed'));
    const completed = items.filter(el => el.classList.contains('completed'));

    // Urutkan: pending dulu, completed di bawah
    const ordered = [...pending, ...completed];

    // Reappend sesuai urutan baru
    ordered.forEach((el, index) => {
        list.appendChild(el);
        el.style.display = (expanded || index < 3) ? '' : 'none';
    });
}

// ── Complete Task ──────────────────────────────
async function completeTask(taskId, btn) {
    btn.disabled = true;
    btn.innerText = 'Memproses...';
    const response = await fetch(`/mandor/task/${taskId}/complete`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({})
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) {
        await W2HDialog.error(data.message || 'Gagal menyelesaikan task.');
        btn.disabled = false;
        btn.innerText = 'Complete';
        return;
    }

    // Update UI task
    const taskEl = document.getElementById(`task-${taskId}`);
    taskEl.classList.add('completed');
    taskEl.querySelector('.material-symbols-outlined').innerText = 'check_circle';
    btn.replaceWith(Object.assign(document.createElement('span'), {
        className: 'mandor-task-done-label',
        innerText: 'Done'
    }));

    if (data.is_done) {
        await W2HDialog.success('Semua task selesai! Proyek telah selesai.');
        window.location.href = '/mandor/dashboard';
        return;
    }

    // Jika milestone berubah, reload halaman supaya peringatan cicilan terupdate dengan benar dari backend
    if (data.is_milestone_changed) {
        window.location.reload();
        return;
    }

    // Update progress
    document.getElementById('persentase-text').innerText = `${data.persentase}%`;
    document.getElementById('persentase-fill').style.width = `${data.persentase}%`;
    document.getElementById('milestone-text').innerText = data.milestone_aktif;

    // Reorder task list — completed pindah ke bawah
    reorderAndRenderTasks(isExpanded);
}



// ── Tambah Aktivitas ───────────────────────────
async function tambahAktivitas(proyekId) {
    const judul = document.getElementById('input-judul').value.trim();
    const deskripsi = document.getElementById('input-deskripsi').value.trim();
    if (!judul || !deskripsi) { await W2HDialog.alert('Judul dan isi aktivitas wajib diisi.'); return; }
    const response = await fetch(`/mandor/proyek/${proyekId}/aktivitas`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ judul, deskripsi })
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) { await W2HDialog.error(data.message || 'Gagal menambah aktivitas.'); return; }
    const list = document.getElementById('aktivitas-list');
    const empty = list.querySelector('.mandor-activity-empty');
    if (empty) empty.remove();
    const item = document.createElement('div');
    item.className = 'mandor-activity-item';
    item.innerHTML = `
        <div class="mandor-activity-bar mandor-activity-bar-active"></div>
        <div>
            <p class="mandor-activity-title">${data.aktivitas.judul}</p>
            <p class="mandor-activity-desc">${data.aktivitas.deskripsi}</p>
        </div>`;
    list.prepend(item);
    document.getElementById('input-judul').value = '';
    document.getElementById('input-deskripsi').value = '';
}

// ── Upload Dokumentasi ─────────────────────────
async function uploadDokumentasi(proyekId, input) {
    const file = input.files[0];
    if (!file) return;
    const formData = new FormData();
    formData.append('foto', file);
    const response = await fetch(`/mandor/proyek/${proyekId}/dokumentasi`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: formData
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) { await W2HDialog.error(data.message || 'Gagal upload foto.'); return; }
    const grid = document.getElementById('dokumentasi-grid');
    const card = document.createElement('div');
    card.className = 'mandor-doc-photo-card';
    card.innerHTML = `
        <img src="${data.foto_url}" alt="Dokumentasi" class="mandor-doc-photo">
        <div class="mandor-doc-overlay">
            <span class="mandor-doc-caption">${data.tanggal}</span>
        </div>`;
    grid.insertBefore(card, grid.children[1]);
    input.value = '';
}