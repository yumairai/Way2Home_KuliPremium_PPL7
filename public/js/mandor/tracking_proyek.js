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
    const input = document.getElementById('renovation-doc-input');
    const dropzone = document.getElementById('renovation-dropzone');
    const previewGrid = document.getElementById('renovation-preview-grid');
    const dropzoneError = document.getElementById('renovation-dropzone-error');

    if (!input || !dropzone || !previewGrid || !dropzoneError) return;

    let previewCounter = 0;
    const MAX_FILE_SIZE = 2 * 1024 * 1024;

    const clearDropzoneError = () => {
        dropzone.classList.remove('is-error');
        dropzoneError.textContent = '';
    };

    const setDropzoneError = (message) => {
        dropzone.classList.add('is-error');
        dropzoneError.textContent = message;
    };

    const createPreviewCard = (file) => {
        const blobUrl = URL.createObjectURL(file);
        const previewId = `renovation-preview-${previewCounter++}`;

        const card = document.createElement('div');
        card.className = 'mandor-reno-preview-card';
        card.dataset.previewId = previewId;
        card.dataset.blobUrl = blobUrl;
        card.innerHTML = `
            <button type="button" class="mandor-reno-preview-remove" aria-label="Hapus foto dokumentasi">close</button>
            <img class="mandor-reno-preview-image" src="${blobUrl}" alt="Preview dokumentasi renovasi">
            <p class="mandor-reno-preview-name">${file.name}</p>
        `;

        const removeButton = card.querySelector('.mandor-reno-preview-remove');
        if (removeButton) {
            removeButton.addEventListener('click', () => {
                URL.revokeObjectURL(blobUrl);
                card.remove();
            });
        }

        previewGrid.appendChild(card);
    };

    const handleFiles = (fileList) => {
        const files = Array.from(fileList || []);
        if (!files.length) return;

        let hasInvalidSize = false;
        let addedCount = 0;

        files.forEach((file) => {
            if (!file.type.startsWith('image/')) {
                return;
            }

            if (file.size > MAX_FILE_SIZE) {
                hasInvalidSize = true;
                return;
            }

            addedCount += 1;
            createPreviewCard(file);
        });

        if (hasInvalidSize && !addedCount) {
            setDropzoneError('Ukuran foto melebihi 2 MB. Silakan pilih foto yang lebih kecil.');
            return;
        }

        if (hasInvalidSize) {
            setDropzoneError('Ada foto yang melebihi 2 MB, foto tersebut tidak ditampilkan ke preview.');
            return;
        }

        clearDropzoneError();
    };

    input.addEventListener('change', () => {
        handleFiles(input.files);
        input.value = '';
    });

    dropzone.addEventListener('click', () => input.click());
    dropzone.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            input.click();
        }
    });

    ['dragenter', 'dragover'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            clearDropzoneError();
            dropzone.classList.add('is-dragover');
        });
    });

    ['dragleave', 'drop'].forEach((eventName) => {
        dropzone.addEventListener(eventName, (event) => {
            event.preventDefault();
            if (eventName === 'drop') {
                const droppedFiles = event.dataTransfer?.files;
                if (droppedFiles && droppedFiles.length) {
                    handleFiles(droppedFiles);
                }
            }
            dropzone.classList.remove('is-dragover');
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

    // Update progress
    document.getElementById('persentase-text').innerText = `${data.persentase}%`;
    document.getElementById('persentase-fill').style.width = `${data.persentase}%`;
    document.getElementById('milestone-text').innerText = data.milestone_aktif;

    // Unlock tombol di milestone yang sekarang aktif
    unlockMilestone(data.milestone_aktif);

    // Reorder task list — completed pindah ke bawah
    reorderAndRenderTasks(isExpanded);

    if (data.persentase === 100) {
        await W2HDialog.success('Semua task selesai! Proyek telah selesai.');
        window.location.href = '/mandor/dashboard';
    }
}

function unlockMilestone(milestoneAktif) {
    const taskItems = document.querySelectorAll('.mandor-task-item');
    taskItems.forEach(item => {
        const btn = item.querySelector('.mandor-complete-btn');
        if (!btn || !btn.disabled) return;

        const taskMilestone = item.dataset.milestone;
        if (taskMilestone === milestoneAktif) {
            const taskId = item.id.replace('task-', '');
            btn.disabled = false;
            btn.onclick = function () {
                completeTask(taskId, btn);
            };
        }
    });
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