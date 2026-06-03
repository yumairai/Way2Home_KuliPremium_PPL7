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

function refreshStats() {
    const cards  = Array.from(document.querySelectorAll('[data-order-card]'));
    const counts = cards.reduce(
        (acc, card) => {
            const status = card.dataset.status;
            acc[status] = (acc[status] || 0) + 1;
            return acc;
        },
        { paid: 0, persiapan: 0, dikirim: 0, selesai: 0 }
    );

    document.getElementById('stat-total').textContent   = cards.length;
    document.getElementById('stat-paid').textContent    = counts.paid;
    document.getElementById('stat-dikirim').textContent = counts.dikirim;
    document.getElementById('stat-selesai').textContent = counts.selesai;
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

            card.dataset.status = next;
            applyStatusUi();
            refreshStats();

        } catch (err) {
            console.error(err);
            alert('Gagal memperbarui status. Silakan coba lagi.');
            applyStatusUi();
        }
    });

    applyStatusUi();
});

refreshStats();