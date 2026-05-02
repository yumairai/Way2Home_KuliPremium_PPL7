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
        if (!response.ok) { alert(data.message || 'Gagal menandai renovasi selesai.'); return; }
        alert(data.message || 'Renovasi berhasil ditandai selesai.');
        window.location.reload();
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
    const list    = document.getElementById('task-list');
    const items   = Array.from(list.querySelectorAll('.mandor-task-item'));

    // Pisah completed dan belum
    const pending   = items.filter(el => !el.classList.contains('completed'));
    const completed = items.filter(el => el.classList.contains('completed'));

    // Urutkan: pending dulu, completed di bawah
    const ordered = [...pending, ...completed];

    // Reappend sesuai urutan baru
    ordered.forEach((el, index) => {
        list.appendChild(el);
        el.style.display = (expanded || index < 4) ? '' : 'none';
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
        alert(data.message || 'Gagal menyelesaikan task.');
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
        innerText: 'Selesai'
    }));

    // Update progress
    document.getElementById('persentase-text').innerText = `${data.persentase}%`;
    document.getElementById('persentase-fill').style.width = `${data.persentase}%`;
    document.getElementById('milestone-text').innerText = data.milestone_aktif;

    // Reorder task list — completed pindah ke bawah
    reorderAndRenderTasks(isExpanded);

    if (data.persentase === 100) {
        alert('Semua task selesai! Proyek telah selesai.');
        window.location.reload();
    }
}

// ── Tambah Aktivitas ───────────────────────────
async function tambahAktivitas(proyekId) {
    const judul     = document.getElementById('input-judul').value.trim();
    const deskripsi = document.getElementById('input-deskripsi').value.trim();
    if (!judul || !deskripsi) { alert('Judul dan isi aktivitas wajib diisi.'); return; }
    const response = await fetch(`/mandor/proyek/${proyekId}/aktivitas`, {
        method: 'POST',
        headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ judul, deskripsi })
    });
    const data = await response.json().catch(() => ({}));
    if (!response.ok) { alert(data.message || 'Gagal menambah aktivitas.'); return; }
    const list  = document.getElementById('aktivitas-list');
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
    if (!response.ok) { alert(data.message || 'Gagal upload foto.'); return; }
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