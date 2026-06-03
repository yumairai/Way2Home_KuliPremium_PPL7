<nav class="mandor-topbar">
    <div class="mandor-shell mandor-topbar-inner">
        <div class="mandor-brand-row">
            <span class="mandor-brand">Way2Home</span>
            <div class="mandor-toplinks">
                <a class="mandor-toplink {{ Route::is('mandor.dashboard') ? 'active' : '' }}"
                    href="{{ route('mandor.dashboard') }}">Dashboard</a>
                <a class="mandor-toplink {{ Route::is('mandor.tracking', 'mandor.proyek.tracking') ? 'active' : '' }}"
                    href="{{ route('mandor.tracking.redirect') }}">Tracking</a>
            </div>
        </div>
        <div class="mandor-account-row">
            <div class="mandor-user-meta">
                <div class="mandor-user-text">
                    <p class="mandor-user-name">{{ Auth::user()->name ?? 'Mandor' }}</p>
                    <p class="mandor-user-role">Mandor Lapangan</p>
                </div>
                <img alt="Mandor Profile Avatar" class="mandor-user-avatar"
                    data-alt="portrait of a professional construction foreman wearing a safety helmet and smiling at a construction site"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2YbiexjhD6A1G_CdWNSH8MfiYM3z66GOZBZ4k89eWgNFjqgXv8fxOtOe7v_N02yidOmPTZ8H5jB1g0iy8j0XzJ5gRwz8jTAI-SXqIl3nnlImxn8eugvyftdatv6Ip2p0_z27zgeNOy3u35CMDlOHPxi9gjTJcMknavleVc6CUeKisP0fifk7PX59imuiDUqifrgV4Lq4c2uB9bxn7hCfUGjsD0It3Dul2l_iJ8mDLrsP02uqdE_ASqH_nN35vBQN9IEpfQckgNK0" />
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
@push('scripts')
    <script src="{{ asset('js/logout.js') }}"></script>
@endpush
