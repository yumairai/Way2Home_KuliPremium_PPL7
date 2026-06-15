const statusLabelMap = {
    paid:      'Menunggu Pengiriman',
    persiapan: 'Diproses Admin',
    dikirim:   'Dalam Pengiriman',
    selesai:   'Pesanan Selesai',
};

const nextStatusMap = {
    paid:      'persiapan',
    persiapan: 'dikirim',
    dikirim:   'selesai',
    selesai:   null,
};

const actionLabelMap = {
    paid:      'Proses Pesanan',
    persiapan: 'Ubah ke Dikirim',
    dikirim:   'Ubah ke Selesai',
    selesai:   'Status Final',
};

// Read server-provided baseline counts from data attributes.
// These reflect the real totals across ALL pages, not just the current page.
function getBaseline(el) {
    return parseInt(el.dataset.baseline ?? el.textContent.trim(), 10) || 0;
}

function initBaselines() {
    ['stat-total', 'stat-paid', 'stat-dikirim', 'stat-selesai'].forEach(id => {
        const el = document.getElementById(id);
        if (el && el.dataset.baseline === undefined) {
            el.dataset.baseline = el.textContent.trim();
        }
    });
}

// Delta-based update: only adjust counts when a status changes on this page.
function adjustStats(fromStatus, toStatus) {
    const statIdMap = {
        paid:      'stat-paid',
        dikirim:   'stat-dikirim',
        selesai:   'stat-selesai',
        persiapan: null, // persiapan has no dedicated stat card
    };

    const fromId = statIdMap[fromStatus];
    const toId   = statIdMap[toStatus];

    if (fromId) {
        const el = document.getElementById(fromId);
        el.textContent = Math.max(0, parseInt(el.textContent, 10) - 1);
    }
    if (toId) {
        const el = document.getElementById(toId);
        el.textContent = parseInt(el.textContent, 10) + 1;
    }
}

document.querySelectorAll('[data-order-card]').forEach((card) => {
    const toggleDetailBtn = card.querySelector('[data-toggle-detail]');
    const updateStatusBtn = card.querySelector('[data-update-status]');
    const statusBadge     = card.querySelector('[data-status-badge]');

    const applyStatusUi = () => {
        const status = card.dataset.status;
        statusBadge.classList.remove('persiapan', 'dikirim', 'selesai');
        statusBadge.classList.add(status);
        statusBadge.textContent = statusLabelMap[status] ?? status;

        updateStatusBtn.textContent = actionLabelMap[status] ?? 'Status Final';
        updateStatusBtn.disabled    = status === 'selesai';
        updateStatusBtn.classList.toggle('disabled', status === 'selesai');
    };

    toggleDetailBtn.addEventListener('click', () => {
        const expanded = card.classList.toggle('expanded');
        toggleDetailBtn.textContent = expanded ? 'Tutup Detail Material' : 'Lihat Detail Material';
    });

    updateStatusBtn.addEventListener('click', async () => {
        const current = card.dataset.status;
        const next    = nextStatusMap[current];
        if (!next) return;

        const orderId = card.dataset.orderId;

        updateStatusBtn.disabled    = true;
        updateStatusBtn.textContent = 'Menyimpan...';

        try {
            const response = await fetch(`/admin/order-management/${orderId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ status: next }),
            });

            if (!response.ok) throw new Error('Gagal');

            adjustStats(current, next);
            card.dataset.status = next;
            applyStatusUi();

        } catch (err) {
            console.error(err);
            alert('Gagal memperbarui status. Silakan coba lagi.');
            applyStatusUi();
        }
    });

    applyStatusUi();
});

initBaselines();