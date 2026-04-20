const statusLabelMap = {
    persiapan: 'Persiapan',
    dikirim: 'Dikirim',
    selesai: 'Pesanan Selesai',
};

const nextStatusMap = {
    persiapan: 'dikirim',
    dikirim: 'selesai',
    selesai: null,
};

const actionLabelMap = {
    persiapan: 'Ubah ke Dikirim',
    dikirim: 'Ubah ke Pesanan Selesai',
    selesai: 'Status Final',
};

function refreshStats() {
    const cards = Array.from(document.querySelectorAll('[data-order-card]'));
    const counts = cards.reduce((acc, card) => {
        const status = card.dataset.status;
        acc[status] = (acc[status] || 0) + 1;
        return acc;
    }, {
        persiapan: 0,
        dikirim: 0,
        selesai: 0,
    });

    document.getElementById('stat-total').textContent = cards.length;
    document.getElementById('stat-persiapan').textContent = counts.persiapan;
    document.getElementById('stat-dikirim').textContent = counts.dikirim;
    document.getElementById('stat-selesai').textContent = counts.selesai;
}

document.querySelectorAll('[data-order-card]').forEach((card) => {
    const toggleDetailBtn = card.querySelector('[data-toggle-detail]');
    const updateStatusBtn = card.querySelector('[data-update-status]');
    const statusBadge = card.querySelector('[data-status-badge]');

    const applyStatusUi = () => {
        const status = card.dataset.status;
        statusBadge.classList.remove('persiapan', 'dikirim', 'selesai');
        statusBadge.classList.add(status);
        statusBadge.textContent = statusLabelMap[status];

        updateStatusBtn.textContent = actionLabelMap[status];
        updateStatusBtn.disabled = status === 'selesai';
        updateStatusBtn.classList.toggle('disabled', status === 'selesai');
    };

    toggleDetailBtn.addEventListener('click', () => {
        const expanded = card.classList.toggle('expanded');
        toggleDetailBtn.textContent = expanded ? 'Tutup Detail Material' : 'Lihat Detail Material';
    });

    updateStatusBtn.addEventListener('click', () => {
        const current = card.dataset.status;
        const next = nextStatusMap[current];
        if (!next) {
            return;
        }

        card.dataset.status = next;
        applyStatusUi();
        refreshStats();
    });

    applyStatusUi();
});

refreshStats();