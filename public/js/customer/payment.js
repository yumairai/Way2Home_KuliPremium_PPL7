const btnGroup = document.getElementById('buttonGroup')
const cancelBtn = document.getElementById('cancelBtn');
const dpBtn = document.getElementById('dpBtn');
const progressBtn = document.getElementById('progressBtn');
const periodePayBtn = document.getElementById('periodePayBtn');
document.addEventListener('DOMContentLoaded', () => {
    if (btnGroup) {
        const isProjectActive = btnGroup.getAttribute('data-proyek') === 'true';
        const isMandorAssigned = btnGroup.getAttribute('data-mandor') === 'true';
        if (isProjectActive) {
            cancelBtn.style.display = 'none';
        } else {
            cancelBtn.style.display = 'block';
            cancelBtn.addEventListener('click', () => {
                if (isProjectActive) {
                    alert("Mohon maaf, proyek anda telah berhasil aktif dan tidak dapat dibatalkan. Terima kasih!");
                } else {
                    alert("Project dibatalkan!");
                }
            });
        }
        if (progressBtn) {
            progressBtn.addEventListener('click', () => {
                if (isMandorAssigned) {
                    alert("OTW ke page progress tracking!");
                    // di sini kasih route sesuai id proyek user, sekarang masih dummy dlu ke user tracking dummy
                    window.location.href = "/customer"
                } else {
                    alert("Maaf, Mandor belum diassign. Mohon tunggu sebentar!");
                }
            });
        }
    }

    // Cek jika tombol DP ada (User belum bayar)
    if (dpBtn) {
        dpBtn.addEventListener('click', () => {
            alert("BAYAR DP WOI PAKE MIDTRANS!");
        });
    }

    if (periodePayBtn) {
        periodePayBtn.addEventListener('click', () => {
            alert("BAYAR CICILAN WOI PAKE MIDTRANS!");
        });
    }
});


