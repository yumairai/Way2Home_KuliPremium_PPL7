@extends('customer-layouts.main')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/input_preferensi_ai.css') }}">
@endpush
@section('content')
    <div class="bg-ellipse"></div>
    <div class="container">
        <div class="page-hero">
            <div class="page-hero-copy">
                <p class="hero-eyebrow">Perencanaan rumah yang lebih presisi</p>
                <h1>PREFERENSI</h1>
                <p class="hero-description">Sesuaikan lokasi, gaya, luas, dan budget Anda agar AI kami dapat merekomendasikan
                    rumah yang paling sesuai dengan kebutuhan Anda.</p>
            </div>
            <div class="page-hero-visual" aria-hidden="true">
                <img src="{{ asset('images/aset/construction.jpg') }}" alt="Ilustrasi rumah dan konstruksi">
                <div class="page-hero-visual-overlay"></div>
                <div class="hero-floating-card">
                    <span>Jawa Barat</span>
                    <strong>Siap Membangun</strong>
                </div>
            </div>
        </div>
        <div class="form-card">
            <!-- ROW 1 -->
            <div class="row">
                <div class="input-group">
                    <label>PREFERENSI LOKASI</label>
                    <select id="location">
                        <option>Kota Bandung</option>
                        <option>Kabupaten Bandung</option>
                        <option>Kabupaten Bandung Barat</option>
                        <option>Kota Cimahi</option>
                        <option>Kabupaten Sumedang</option>
                        <option>Kabupaten Garut</option>
                        <option>Kota Tasikmalaya</option>
                        <option>Kabupaten Tasikmalaya</option>
                        <option>Kabupaten Cianjur</option>
                        <option>Kota Sukabumi</option>
                        <option>Kabupaten Sukabumi</option>
                        <option>Kota Bogor</option>
                        <option>Kabupaten Bogor</option>
                        <option>Kota Depok</option>
                        <option>Kota Bekasi</option>
                        <option>Kabupaten Bekasi</option>
                        <option>Kabupaten Karawang</option>
                        <option>Kabupaten Purwakarta</option>
                        <option>Kabupaten Subang</option>
                        <option>Kabupaten Indramayu</option>
                        <option>Kota Cirebon</option>
                        <option>Kabupaten Cirebon</option>
                        <option>Kabupaten Kuningan</option>
                        <option>Kabupaten Majalengka</option>
                        <option>Kabupaten Ciamis</option>
                        <option>Kota Banjar</option>
                        <option>Kabupaten Pangandaran</option>
                        <option>Bandung Barat</option>
                        <option>Bandung Timur</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>GAYA ARSITEKTUR</label>
                    <select id="style">
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

                    <input type="range" id="areaRange" min="25" max="350" value="30">

                    <div class="range-info">
                        <span>25 m²</span>
                        <span>350 m²</span>
                    </div>
                </div>

                <div class="input-group">
                    <label>JUMLAH KAMAR TIDUR</label>
                    <input type="number" id="bedrooms" min="1" max="10" placeholder="1 - 10" value="1">
                </div>
            </div>

            <!-- ROW 3 -->
            <div class="row">
                <div class="input-group">
                    <label>JUMLAH KAMAR MANDI</label>
                    <input type="number" id="bathrooms" min="1" max="5" placeholder="1 - 5" value="1">
                </div>

                <div class="input-group">
                    <label>JUMLAH GARASI</label>
                    <input type="number" id="garage" min="0" max="5" placeholder="0 - 5" value="0">
                </div>
            </div>

            <!-- ROW 4 -->
            <div class="row">
                <div class="input-group">
                    <label>KUALITAS DESAIN (1-10)</label>
                    <input type="number" id="quality" min="1" max="10" placeholder="1 - 10" value="5">
                </div>

                <div class="input-group">
                    <label>FLEKSIBILITAS BUDGET</label>
                    <input type="number" id="flexibility" min="0" max="50" placeholder="0 - 50%" value="10" step="5">
                    <small>% dari budget</small>
                </div>
            </div>

            <!-- ROW 5 -->
            <div class="input-group full">
                <label>ESTIMASI BUDGET (Rp <span id="budgetValue">100000000</span>)</label>

                <input type="range" id="budgetRange" min="100000000" max="2000000000" value="100000000" step="25000000">

                <div class="range-info">
                    <span>Rp 100 jt</span>
                    <span>Rp 2 M</span>
                </div>
            </div>

            <!-- AC REQUIRED -->
            <div class="input-group full" style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" id="ac_required" style="width: 20px; height: 20px; cursor: pointer;">
                <label for="ac_required" style="margin: 0; cursor: pointer;">AC/Pendingin Ruangan Diperlukan</label>
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
