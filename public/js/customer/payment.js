document.addEventListener('DOMContentLoaded', () => {
    const cancelBtn = document.getElementById('cancelBtn');
    const dpBtn = document.getElementById('dpBtn');
    const progressBtn = document.getElementById('progressBtn'); // Tambahkan ini

    if (cancelBtn) {

        cancelBtn.addEventListener('click', () => {
            const isProjectActive = cancelBtn.getAttribute('data-proyek') === 'true';
            if (isProjectActive) {
                alert("Mohon maaf, proyek anda telah berhasil aktif dan tidak dapat dibatalkan. Terima kasih!");
            } else {
                alert("Project dibatalkan!");
            }
        });
    }

    // Cek jika tombol DP ada (User belum bayar)
    if (dpBtn) {
        dpBtn.addEventListener('click', () => {
            alert("BAYAR WOI PAKE MIDTRANS!");
        });
    }

    // Cek jika tombol Progress ada (User sudah bayar)
    if (progressBtn) {
        progressBtn.addEventListener('click', () => {
            // Ambil status mandor dari attribute data-mandor
            const isMandorAssigned = progressBtn.getAttribute('data-mandor') === 'true';

            if (isMandorAssigned) {
                alert("OTW ke page progress tracking!");
            } else {
                alert("Maaf, Mandor belum diassign. Mohon tunggu sebentar!");
            }
        });
    }
});


