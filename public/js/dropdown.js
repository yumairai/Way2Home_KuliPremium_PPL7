// Gunakan var atau cek window agar tidak error deklarasi ganda
if (typeof window.AuthApp === 'undefined') {
    window.AuthApp = {
        logout: function() {
            if (confirm('Apakah Anda yakin ingin keluar?')) {
                const form = document.getElementById('logout-form');
                if (form) {
                    form.submit();
                } else {
                    console.error("Form logout tidak ditemukan!");
                }
            }
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const profileTrigger = document.getElementById('profileDropdown');
    const dropdown = document.querySelector('.profile-dropdown');

    // Cek apakah elemennya ada di halaman ini
    if (profileTrigger && dropdown) {
        profileTrigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !profileTrigger.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                dropdown.classList.remove('active');
            }
        });
    }
});