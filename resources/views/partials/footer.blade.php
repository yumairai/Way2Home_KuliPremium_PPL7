<!-- Footer Shell -->
<footer>
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-brand-info">
                <img src="{{ asset('images/aset/logo-w2h.png') }}" alt="Logo Way2Home">
                <span class="footer-brand-name">Way2Home</span>
            </div>
            <p class="footer-brand-text">Platform konstruksi digital untuk pembangunan rumah, renovasi, dan material.</p>
        </div>
        <div class="footer-links">
            <a href="{{ route('home') }}">Beranda</a>
            <a href="{{ url('/recommendation') }}">Desain</a>
            <a href="{{ url('/material') }}">Material</a>
            <a href="{{ url('/renovation') }}">Renovasi</a>
            <a href="https://wa.me/6281384310179" target="_blank" rel="noopener noreferrer">Kontak</a>
        </div>
        <div class="footer-actions">
            <a class="footer-icon-btn" href="https://wa.me/6281384310179" target="_blank" rel="noopener noreferrer"
                aria-label="Hubungi Way2Home via WhatsApp">
                <img src="{{ asset('images/icon/whatsapp.png') }}" alt="WhatsApp">
            </a>
        </div>
    </div>
</footer>
