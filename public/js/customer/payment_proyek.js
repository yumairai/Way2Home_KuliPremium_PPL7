document.addEventListener('DOMContentLoaded', () => {
    const cancelBtn = document.getElementById('cancelBtn');
    const dpBtn = document.getElementById('dpBtn');
    const progressBtn = document.getElementById('progressBtn');

    if (cancelBtn) {
        cancelBtn.addEventListener('click', async () => {
            const isProjectActive = cancelBtn.getAttribute('data-proyek') === 'true';
            if (isProjectActive) {
                await W2HDialog.alert("Mohon maaf, proyek anda telah berhasil aktif dan tidak dapat dibatalkan. Terima kasih!");
            } else {
                await W2HDialog.alert("Project dibatalkan!");
            }
        });
    }

    if (dpBtn) {
        dpBtn.addEventListener('click', async () => {
            const hargaTotal = parseInt(dpBtn.dataset.harga) || 0;
            const proyekId = parseInt(dpBtn.dataset.proyekId) || 0;
            const jumlahDP = Math.round(hargaTotal * 0.30);

            const formatted = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0,
            }).format(jumlahDP);

            const konfirmasi = await W2HDialog.confirm(`Total DP yang harus dibayar: ${formatted}\n\nLanjutkan pembayaran?`);

            if (konfirmasi) {
                const btnText = dpBtn.innerText;
                dpBtn.disabled = true;
                dpBtn.innerText = "Memproses...";

                fetch('/proyek/bayar-dp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        proyek_id: proyekId
                    })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.snap_token) {
                            window.snap.pay(data.snap_token, {
                                onSuccess: function (result) {
                                    fetch('/proyek/payment-success', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                        },
                                        body: JSON.stringify({
                                            order_id: result.order_id
                                        })
                                    })
                                        .then(res => res.json())
                                        .then(() => {
                                            W2HDialog.success("Pembayaran Berhasil!");
                                            setTimeout(() => window.location.reload(), 1500);
                                        })
                                        .catch(() => {
                                            W2HDialog.error("Gagal update status di server");
                                        });
                                },
                                onPending: function (result) {
                                    W2HDialog.alert("Silahkan selesaikan pembayaran Anda.");
                                    setTimeout(() => window.location.reload(), 1500);
                                },
                                onError: function (result) {
                                    W2HDialog.error("Pembayaran Gagal.");
                                    dpBtn.disabled = false;
                                    dpBtn.innerText = btnText;
                                },
                                onClose: function () {
                                    dpBtn.disabled = false;
                                    dpBtn.innerText = btnText;
                                }
                            });
                        } else {
                            W2HDialog.error("Error: " + (data.message || "Gagal mendapatkan token"));
                            dpBtn.disabled = false;
                            dpBtn.innerText = btnText;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        W2HDialog.error("Terjadi kesalahan koneksi.");
                        dpBtn.disabled = false;
                        dpBtn.innerText = btnText;
                    });
            }
        });
    }

    if (progressBtn) {
        progressBtn.addEventListener('click', async () => {
            const isMandorAssigned = progressBtn.getAttribute('data-mandor') === 'true';
            const proyekId = progressBtn.getAttribute('data-proyek-id'); // tambah ini di blade

            if (isMandorAssigned) {
                window.location.href = `/proyek/${proyekId}/tracking`;
            } else {
                await W2HDialog.alert("Maaf, Mandor belum diassign. Mohon tunggu sebentar!");
            }
        });
    }
});