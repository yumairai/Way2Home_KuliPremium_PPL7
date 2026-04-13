document.addEventListener('DOMContentLoaded', () => {
    const profileTrigger = document.querySelector('.nav-actions');
    const dropdown = document.querySelector('.profile-dropdown');

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
});