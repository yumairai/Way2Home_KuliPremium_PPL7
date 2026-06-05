<nav class="mandor-topbar">
    <div class="mandor-shell mandor-topbar-inner">
        <div class="mandor-brand-row">
            <a href="{{ route('mandor.dashboard') }}" class="mandor-brand-link">
                <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home" class="mandor-brand-logo">
                <span class="mandor-brand">Way2Home</span>
            </a>
            <div class="mandor-toplinks">
                <a class="mandor-toplink {{ Route::is('mandor.dashboard') ? 'active' : '' }}"
                    href="{{ route('mandor.dashboard') }}">Dashboard</a>
                <a class="mandor-toplink {{ Route::is('mandor.tracking', 'mandor.proyek.tracking') ? 'active' : '' }}"
                    href="{{ route('mandor.tracking.redirect') }}">Tracking</a>
            </div>
        </div>
        <div class="mandor-account-row">
            <button class="mandor-menu-toggle" type="button" aria-label="Buka navigasi mandor"
                aria-controls="mandor-nav-drawer" aria-expanded="false" data-mandor-drawer-toggle>
                <span class="material-symbols-outlined">menu</span>
            </button>
            <img alt="Mandor Profile Avatar" class="mandor-user-avatar"
                src="{{ Auth::user()?->avatar ?? asset('images/aset/avatar.jpg') }}" />
            <div class="mandor-user-text">
                <p class="mandor-user-name">{{ Auth::user()->name ?? 'Mandor' }}</p>
                <p class="mandor-user-role">Mandor Lapangan</p>
            </div>
            <form id="mandor-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button class="mandor-logout-btn" type="button"
                onclick="window.W2HLogout.submit('mandor-logout-form', 'Yakin ingin keluar?')">
                <span class="material-symbols-outlined">logout</span>
                <span>Logout</span>
            </button>
        </div>
    </div>
</nav>

<div class="mandor-drawer-backdrop" data-mandor-drawer-backdrop></div>
<aside class="mandor-drawer" id="mandor-nav-drawer" aria-hidden="true" aria-label="Navigasi mandor" data-mandor-drawer>
    <div class="mandor-drawer-header">
        <a href="{{ route('mandor.dashboard') }}" class="mandor-brand-link">
            <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home" class="mandor-brand-logo">
            <span class="mandor-brand">Way2Home</span>
        </a>
        <button class="mandor-drawer-close" type="button" aria-label="Tutup navigasi" data-mandor-drawer-close>
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    <div class="mandor-drawer-body">
        <div class="mandor-user-meta mandor-user-meta-drawer">
            <img alt="Mandor Profile Avatar" class="mandor-user-avatar"
                src="{{ Auth::user()?->avatar ?? asset('images/aset/avatar.jpg') }}" />
            <div class="mandor-user-text mandor-user-text-drawer">
                <p class="mandor-user-name">{{ Auth::user()->name ?? 'Mandor' }}</p>
                <p class="mandor-user-role">Mandor Lapangan</p>
            </div>
        </div>

        <p class="mandor-drawer-title">Navigasi</p>
        <div class="mandor-toplinks mandor-toplinks-drawer">
            <a class="mandor-toplink {{ Route::is('mandor.dashboard') ? 'active' : '' }}"
                href="{{ route('mandor.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </a>
            <a class="mandor-toplink {{ Route::is('mandor.tracking', 'mandor.proyek.tracking') ? 'active' : '' }}"
                href="{{ route('mandor.tracking.redirect') }}">
                <span class="material-symbols-outlined">map</span>
                <span>Tracking</span>
            </a>
        </div>

        <form id="mandor-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
        <button class="mandor-logout-btn mandor-logout-drawer" type="button"
            onclick="window.W2HLogout.submit('mandor-logout-form', 'Yakin ingin keluar?')">
            <span class="material-symbols-outlined">logout</span>
            <span>Logout</span>
        </button>
    </div>
</aside>

@push('scripts')
    <script src="{{ asset('js/logout.js') }}"></script>
    <script>
        (function() {
            const toggle = document.querySelector('[data-mandor-drawer-toggle]');
            const drawer = document.querySelector('[data-mandor-drawer]');
            const backdrop = document.querySelector('[data-mandor-drawer-backdrop]');
            const closeButton = document.querySelector('[data-mandor-drawer-close]');

            if (!toggle || !drawer || !backdrop || !closeButton) {
                return;
            }

            const setOpenState = (isOpen) => {
                drawer.classList.toggle('is-open', isOpen);
                backdrop.classList.toggle('is-open', isOpen);
                document.body.classList.toggle('mandor-drawer-open', isOpen);
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

            const desktopQuery = window.matchMedia('(min-width: 1024px)');
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
        })();
    </script>
@endpush
