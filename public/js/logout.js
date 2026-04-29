window.W2HLogout = window.W2HLogout || {
    submit: function (formId, message = 'Apakah Anda yakin ingin keluar?') {
        if (!confirm(message)) {
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