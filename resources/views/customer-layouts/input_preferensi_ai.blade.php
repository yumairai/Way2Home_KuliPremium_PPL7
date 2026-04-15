@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/input_preferensi_ai.css') }}">
@endpush
@section('content')
    <div class="bg-ellipse"></div>
    <div class="container">
        <h1>PREFERENSI</h1>
        <div class="form-card">
            <!-- ROW 1 -->
            <div class="row">
                <div class="input-group">
                    <label>PREFERENSI LOKASI</label>
                    <select id="lokasi">
                        <option>Bandung Barat</option>
                        <option>Bandung Timur</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>GAYA ARSITEKTUR</label>
                    <select id="gaya_arsitektur">
                        <option>Minimalist</option>
                        <option>Modern</option>
                        <option>Mewah</option>
                    </select>
                </div>
            </div>

            <!-- ROW 2 -->
            <div class="row">
                <div class="input-group">
                    <label>ESTIMASI AREA RUMAH (<span id="areaValue">30</span> m²)</label>

                    <input type="range" id="areaRange" min="30" max="350" value="30">

                    <div class="range-info">
                        <span>30 m²</span>
                        <span>350 m²</span>
                    </div>
                </div>

                <div class="input-group">
                    <label>JUMLAH KAMAR</label>
                    <input type="number" id="jumlah_kamar" min="1" max="10" placeholder="1 - 10">
                </div>
            </div>

            <!-- ROW 3 -->
            <div class="input-group full">
                <label>ESTIMASI BUDGET (Rp <span id="budgetValue">100000000</span>)</label>

                <input type="range" id="budgetRange" min="100000000" max="2000000000" value="100000000" step="25000000">

                <div class="range-info">
                    <span>Rp 100 jt</span>
                    <span>Rp 2 M</span>
                </div>
            </div>

            <!-- PRIORITAS -->
            <div class="input-group full">
                <label>PRIORITAS PREFERENSI</label>
                <div class="priority-box">
                    <div class="box" data-value="biaya">Efisiensi Biaya</div>
                    <div class="box" data-value="estetik">Desain Estetik</div>
                    <div class="box" data-value="cepat">Konstruksi Cepat</div>
                </div>
            </div>

            <!-- BUTTON -->
            <button class="btn-submit" id="submitBtn">Buat Rekomendasi</button>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/customer/input_script.js') }}"></script>
@endpush
