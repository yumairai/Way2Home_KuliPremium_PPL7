document.addEventListener('DOMContentLoaded', () => {
    const cancelBtn     = document.getElementById('cancelBtn');
    const dpBtn         = document.getElementById('dpBtn');
    const progressBtn   = document.getElementById('progressBtn');
    const periodePayBtn = document.getElementById('periodePayBtn');

    // ─── Helper: inisiasi pembayaran ke backend ───────────────────
    async function initiateBayar(pembayaranId) {
        const response = await fetch('/proyek/bayar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ pembayaran_id: pembayaranId }),
        });
        return response.json();
    }

    // ─── Helper: verifikasi setelah Midtrans onSuccess ───────────
    async function verifyPayment(orderId) {
        const response = await fetch('/proyek/payment-success', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ order_id: orderId }),
        });
        return response.json();
    }

    // ─── Helper: buka Snap dan handle semua callback ─────────────
    function openSnap(snapToken, { onSettled, onClose }) {
        window.snap.pay(snapToken, {
            onSuccess: async (result) => {
                try {
                    await verifyPayment(result.order_id);
                    W2HDialog.success('Pembayaran berhasil!');
                    setTimeout(() => window.location.reload(), 1500);
                } catch {
                    W2HDialog.error('Gagal update status di server.');
                }
            },
            onPending: () => {
                W2HDialog.alert('Silahkan selesaikan pembayaran Anda.');
                setTimeout(() => window.location.reload(), 1500);
            },
            onError: () => {
                W2HDialog.error('Pembayaran gagal.');
                onSettled?.();
            },
            onClose: () => {
                onClose?.();
            },
        });
    }

    // ─── Batalkan Proyek ─────────────────────────────────────────
    if (cancelBtn) {
        cancelBtn.addEventListener('click', async () => {
            const isActive = cancelBtn.getAttribute('data-proyek') === 'true';
            if (isActive) {
                await W2HDialog.alert('Mohon maaf, proyek Anda telah aktif dan tidak dapat dibatalkan.');
            } else {
                await W2HDialog.alert('Proyek dibatalkan!');
            }
        });
    }

    // ─── Bayar DP ────────────────────────────────────────────────
    if (dpBtn) {
        dpBtn.addEventListener('click', async () => {
            const pembayaranId = dpBtn.dataset.pembayaranId;
            const nominal      = parseInt(dpBtn.dataset.nominal) || 0;

            const formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
            }).format(nominal);

            const konfirmasi = await W2HDialog.confirm(
                `Down Payment yang harus dibayar: ${formatted}\n\nLanjutkan pembayaran?`
            );
            if (!konfirmasi) return;

            const originalHTML = dpBtn.innerHTML;
            dpBtn.disabled = true;
            dpBtn.innerHTML = '<span class="material-symbols-outlined">hourglass_top</span> Memproses...';

            const resetBtn = () => {
                dpBtn.disabled = false;
                dpBtn.innerHTML = originalHTML;
            };

            try {
                const data = await initiateBayar(pembayaranId);
                if (data.snap_token) {
                    openSnap(data.snap_token, { onSettled: resetBtn, onClose: resetBtn });
                } else {
                    W2HDialog.error(data.message || 'Gagal mendapatkan token pembayaran.');
                    resetBtn();
                }
            } catch {
                W2HDialog.error('Terjadi kesalahan koneksi.');
                resetBtn();
            }
        });
    }

    // ─── Pantau Progress ─────────────────────────────────────────
    if (progressBtn) {
        progressBtn.addEventListener('click', async () => {
            const isMandorAssigned = progressBtn.getAttribute('data-mandor') === 'true';
            const proyekId         = progressBtn.getAttribute('data-proyek-id');

            if (isMandorAssigned) {
                window.location.href = `/proyek/${proyekId}/tracking`;
            } else {
                await W2HDialog.alert('Mandor belum diassign. Mohon tunggu sebentar!');
            }
        });
    }

    // ─── Bayar Cicilan ───────────────────────────────────────────
    if (periodePayBtn) {
        periodePayBtn.addEventListener('click', async () => {
            const pembayaranId = periodePayBtn.dataset.pembayaranId;
            const nominal      = parseInt(periodePayBtn.dataset.nominal) || 0;
            const periode      = periodePayBtn.dataset.periode;

            if (!pembayaranId) return;

            const formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0,
            }).format(nominal);

            const konfirmasi = await W2HDialog.confirm(
                `Cicilan Periode ${periode}\nNominal: ${formatted}\n\nLanjutkan pembayaran?`
            );
            if (!konfirmasi) return;

            const originalHTML = periodePayBtn.innerHTML;
            periodePayBtn.disabled = true;
            periodePayBtn.innerHTML = '<span class="material-symbols-outlined">hourglass_top</span> Memproses...';

            const resetBtn = () => {
                periodePayBtn.disabled = false;
                periodePayBtn.innerHTML = originalHTML;
            };

            try {
                const data = await initiateBayar(pembayaranId);
                if (data.snap_token) {
                    openSnap(data.snap_token, { onSettled: resetBtn, onClose: resetBtn });
                } else {
                    W2HDialog.error(data.message || 'Gagal mendapatkan token pembayaran.');
                    resetBtn();
                }
            } catch {
                W2HDialog.error('Terjadi kesalahan koneksi.');
                resetBtn();
            }
        });
    }
});