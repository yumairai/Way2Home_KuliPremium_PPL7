@extends('customer-layouts.main')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/cart.css') }}">
@endpush

@section('content')
    <section class="cart-section">
        <main class="cart-main">
            <div class="cart-header">
                <h1 class="cart-title">Shopping Cart</h1>
                <p class="cart-subtitle">Periksa dan konfirmasikan pesanan material Anda.</p>
            </div>

            <div class="cart-layout">
                <div class="cart-left">
                    <div class="delivery-address-form">
                        <div class="delivery-address-header">
                            <img src="{{ asset('images/icon/location.png') }}" alt="Location Icon">
                            <h2 class="delivery-address-title">Data Pengiriman</h2>
                        </div>
                        <div class="delivery-address-grid">
                            <div class="delivery-address-field">
                                <label class="delivery-address-label" for="nama_lengkap">Nama Lengkap</label>
                                <input class="delivery-address-input" id="nama_lengkap" placeholder="Contoh: Budi Santoso" type="text" value="{{ $user->name ?? '' }}" />
                            </div>
                            <div class="delivery-address-field">
                                <label class="delivery-address-label" for="nomor_telepon">Nomor Telepon</label>
                                <input class="delivery-address-input" id="nomor_telepon" placeholder="Contoh: 08123456789" type="tel" value="{{ $customer->no_hp ?? $user->phone_number ?? '' }}" />
                            </div>
                            <div class="delivery-address-field full-width">
                                <label class="delivery-address-label" for="alamat_lengkap">Alamat Lengkap</label>
                                <textarea class="delivery-address-textarea" id="alamat_lengkap" placeholder="Tuliskan alamat detail...">{{ $user->address ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="delivery-address-checkbox-container">
                            <input class="delivery-address-checkbox" id="save_primary" type="checkbox" />
                            <label class="delivery-address-checkbox-label" for="save_primary">Simpan sebagai data pengiriman utama</label>
                        </div>
                    </div>

                    <div class="cart-items" id="cartContainer">
                        </div>
                </div>

                <aside class="cart-right">
                    <div class="cart-summary">
                        <h2 class="cart-summary-title">Ringkasan Belanja</h2>
                        
                        <div class="cart-summary-items" id="summaryContainer">
                            </div>

                        <div class="cart-summary-totals">
                            <div class="cart-summary-total-row">
                                <span class="cart-summary-total-label">Subtotal</span>
                                <span class="cart-summary-total-value" id="subtotalValue">Rp 0</span>
                            </div>
                            <div class="cart-summary-total-row">
                                <span class="cart-summary-total-label">Biaya Layanan</span>
                                <span class="cart-summary-total-value" id="serviceFeeValue">Rp 0</span>
                            </div>
                            <div class="cart-summary-total-row">
                                <span class="cart-summary-total-label">Ongkos Kirim</span>
                                <span class="cart-summary-total-value free">Gratis</span>
                            </div>
                        </div>
                        <div class="cart-summary-grand-total">
                            <span class="cart-summary-grand-label">Total Harga</span>
                            <span class="cart-summary-grand-value" id="grandTotalValue">Rp 0</span>
                        </div>
                        
                        <button class="cart-checkout-btn" id="checkoutBtn">
                            Konfirmasi &amp; Bayar
                            <img src="{{ asset('images/icon/chevron-right.png') }}" alt="Next Icon">
                        </button>

                        <div class="cart-secure-note">
                            <img src="{{ asset('images/icon/lock.png') }}" alt="Secure Icon" class="cart-secure-icon">
                            Secure Checkout Guarantee
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </section>
@endsection

@push('scripts')
    <script type="text/javascript"
        src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}">
    </script>

    <script src="{{ asset('js/customer/cart.js') }}"></script>
@endpush