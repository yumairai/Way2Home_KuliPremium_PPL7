window.W2HLogout = window.W2HLogout || {
    submit: async function (formId, message = 'Apakah Anda yakin ingin keluar?') {
        const confirmed = window.W2HDialog && typeof window.W2HDialog.confirm === 'function'
            ? await window.W2HDialog.confirm(message, {
                title: 'Konfirmasi Keluar',
                confirmText: 'Ya, keluar',
                cancelText: 'Batal',
                variant: 'warning',
            })
            : window.confirm(message);

        if (!confirmed) {
            return;
        }

        const form = document.getElementById(formId);
        if (!form) {
            console.error('Form logout tidak ditemukan: ' + formId);
            return;
        }

        form.submit();
    }
};