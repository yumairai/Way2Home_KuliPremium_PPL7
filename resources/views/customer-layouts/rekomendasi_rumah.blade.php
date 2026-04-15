@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/rekomendasi_rumah.css') }}">
@endpush
@section('content')
    <div class="container">
        <h1>REKOMENDASI RUMAH</h1>
        <h3>Ai Generated</h3>
        <div class="card-container">
            <div class="card">
                <p>Desain 1</p>
                <img src="{{ asset('images/rekomendasi/rekom1.jpg') }}" alt="Rumah 1">
                <div class="details">
                    <h2>Modern Minimalist</h2>
                    <p>Estimasi Biaya: Rp 400.000.000</p>
                    <p>Area: 50 m²</p>
                    <p>Estimasi Waktu: 6 Bulan</p>
                </div>
            </div>

            <div class="card">
                <p>Desain 2</p>
                <img src="{{ asset('images/rekomendasi/rekom2.jpg') }}" alt="Rumah 2">
                <div class="details">
                    <h2>Modern Minimalist</h2>
                    <p>Estimasi Biaya: Rp 500.000.000</p>
                    <p>Area: 80 m²</p>
                    <p>Estimasi Waktu: 8 Bulan</p>
                </div>
            </div>

            <div class="card">
                <p>Desain 3</p>
                <img src="{{ asset('images/rekomendasi/rekom3.jpg') }}" alt="Rumah 3">
                <div class="details">
                    <h2>Modern Minimalist</h2>
                    <p>Estimasi Biaya: Rp 600.000.000</p>
                    <p>Area: 150 m²</p>
                    <p>Estimasi Waktu: 12 Bulan</p>
                </div>
            </div>
        </div>
        <!-- TOMBOL PILIH DESAIN -->
        <button>Pilih Desain</button>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/customer/recom_script.js') }}"></script>
@endpush
