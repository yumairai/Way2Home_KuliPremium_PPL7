let selectedMandorId = null;
let selectedProyekId = null;

function openDocModal(element) {
    selectedMandorId = element.dataset.mandorId; // ambil dari data attribute
    selectedProyekId = null;

    document.getElementById('list-proyek-modal').style.display = 'flex';

    const items = document.querySelectorAll('.proyek-item');
    items.forEach(i => i.classList.remove('active'));
    if (items.length > 0) {
        items[0].classList.add('active');
        selectedProyekId = items[0].dataset.proyekId;
    }
}

function closeDocModal() {
    document.getElementById('list-proyek-modal').style.display = 'none';
    selectedMandorId = null;
    selectedProyekId = null;
}

async function unassignMandor(element) {
    const mandorId = element.dataset.mandorId;
    const mandorName = element.dataset.mandorName;

    const confirmed = await W2HDialog.confirm(`Yakin ingin melepas ${mandorName} dari proyeknya?`);
    if (!confirmed) return;

    fetch('/admin/manajemen-mandor/unassign', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ mandor_id: mandorId }),
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                W2HDialog.success(data.message);
                setTimeout(() => location.reload(), 1500);
            } else {
                W2HDialog.error(data.message);
            }
        })
        .catch(() => W2HDialog.error('Terjadi kesalahan server.'));
}

document.addEventListener('DOMContentLoaded', function () {
    // Klik item proyek
    document.querySelectorAll('.proyek-item').forEach(item => {
        item.addEventListener('click', function () {
            document.querySelectorAll('.proyek-item').forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            selectedProyekId = this.dataset.proyekId;
        });
    });

    // Search mandor (filter by name)
    document.getElementById('searchInput').addEventListener('input', function () {
        const keyword = this.value.toLowerCase();
        document.querySelectorAll('.mandor-entry').forEach(entry => {
            const name = entry.dataset.name || '';
            entry.style.display = name.includes(keyword) ? '' : 'none';
        });
    });

    // Tombol assign
    document.querySelector('.modal-btn-submit').addEventListener('click', async function () {
        if (!selectedMandorId || !selectedProyekId) {
            await W2HDialog.alert('Pilih proyek terlebih dahulu!');
            return;
        }

        const submitBtn = this;
        submitBtn.classList.add('is-loading');
        submitBtn.disabled = true;

        fetch('/admin/manajemen-mandor/assign', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({
                mandor_id: selectedMandorId,
                proyek_id: selectedProyekId,
            }),
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    W2HDialog.success(data.message);
                    setTimeout(() => {
                        closeDocModal();
                        location.reload();
                    }, 1500);
                } else {
                    W2HDialog.error(data.message);
                    submitBtn.classList.remove('is-loading');
                    submitBtn.disabled = false;
                }
            })
            .catch(() => {
                submitBtn.classList.remove('is-loading');
                submitBtn.disabled = false;
                W2HDialog.error('Terjadi kesalahan server.');
            });
    });
});