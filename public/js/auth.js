const AuthApp = {
    // --- HELPERS ---
    showError: (id, msg) => {
        const el = document.getElementById(`error-${id}`);
        if (el) el.innerText = msg;
    },

    clearErrors: () => {
        document.querySelectorAll('.error-text').forEach(el => el.innerText = '');
    },

    clearForm: () => {
        const form = document.getElementById('registerForm');
        if (form) form.reset();
    },

    getCsrfToken: () => document.querySelector('meta[name="csrf-token"]').content,

    // --- ACTIONS ---
    login: async (formData) => {
        try {
            const response = await fetch('/login', { // Sesuaikan dengan route web.php
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': AuthApp.getCsrfToken()
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            if (response.ok) {
                // Handle "Remember Me"
                if (formData.remember) {
                    localStorage.setItem('remembered_email', formData.email);
                } else {
                    localStorage.removeItem('remembered_email');
                }
                window.location.href = data.redirect;
            } else {
                AuthApp.showError('login', data.message || 'Kredensial salah.');
            }
        } catch (error) {
            AuthApp.showError('login', 'Terjadi kesalahan koneksi.');
        }
    },

    register: async (formData) => {
        try {
            const response = await fetch('/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': AuthApp.getCsrfToken()
                },
                body: JSON.stringify(formData)
            });

            const data = await response.json();

            // 🔥 pakai status, bukan response.ok doang
            if (response.status >= 200 && response.status < 300) {
                AuthApp.clearForm();
                window.location.href = data.redirect;
                return;
            }

            if (response.status === 422) {
                for (const key in data.errors) {
                    AuthApp.showError(key, data.errors[key][0]);
                }
                return;
            }

            // fallback
            alert(data.message || 'Registrasi Gagal');

        } catch (error) {
            console.error(error);
            alert('Kesalahan koneksi server.');
        }
    },

    logout: async () => {
        const response = await fetch('/logout', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            const data = await response.json();
            window.location.href = data.redirect;
        }
    }
};