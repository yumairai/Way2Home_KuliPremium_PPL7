(function () {
    function initNavbarDrawer() {
        const toggle = document.querySelector('[data-nav-drawer-toggle]');
        const drawer = document.querySelector('[data-nav-drawer]');
        const backdrop = document.querySelector('[data-nav-drawer-backdrop]');
        const closeButton = document.querySelector('[data-nav-drawer-close]');

        if (!toggle || !drawer || !backdrop || !closeButton) {
            return;
        }

        const setOpenState = (isOpen) => {
            drawer.classList.toggle('is-open', isOpen);
            backdrop.classList.toggle('is-open', isOpen);
            document.body.classList.toggle('nav-drawer-open', isOpen);
            toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            drawer.setAttribute('aria-hidden', isOpen ? 'false' : 'true');
        };

        const closeDrawer = () => setOpenState(false);

        toggle.addEventListener('click', () => {
            setOpenState(!drawer.classList.contains('is-open'));
        });

        closeButton.addEventListener('click', closeDrawer);
        backdrop.addEventListener('click', closeDrawer);

        drawer.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', closeDrawer);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeDrawer();
            }
        });

        const desktopQuery = window.matchMedia('(min-width: 768px)');
        const handleViewportChange = (event) => {
            if (event.matches) {
                closeDrawer();
            }
        };

        if (typeof desktopQuery.addEventListener === 'function') {
            desktopQuery.addEventListener('change', handleViewportChange);
        } else if (typeof desktopQuery.addListener === 'function') {
            desktopQuery.addListener(handleViewportChange);
        }
    }

    document.addEventListener('DOMContentLoaded', initNavbarDrawer);
})();