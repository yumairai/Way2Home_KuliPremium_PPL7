{{-- CONTOH VIEW UNTUK DISPLAY MATERIAL DENGAN GAMBAR DARI SUPABASE --}}
@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Daftar Material</h1>

    @if ($materials->count() > 0)
        <div class="row">
            @foreach ($materials as $material)
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        {{-- GAMBAR MATERIAL --}}
                        <div class="card-img-top" style="height: 250px; overflow: hidden; background-color: #f5f5f5;">
                            <img 
                                src="{{ $material->path_foto_material }}" 
                                alt="{{ $material->nama_material }}"
                                style="width: 100%; height: 100%; object-fit: cover;"
                                onerror="this.src='{{ asset('images/aset/placeholder-material.png') }}'"
                            >
                        </div>

                        {{-- CARD BODY --}}
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title">{{ $material->nama_material }}</h5>
                            
                            <p class="text-muted small mb-2">
                                <strong>Kategori:</strong> {{ $material->kategori }}
                            </p>

                            <p class="card-text text-truncate" title="{{ $material->deskripsi }}">
                                {{ Str::limit($material->deskripsi, 80) }}
                            </p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 mb-0 text-success">
                                        Rp {{ number_format($material->harga, 0, ',', '.') }}
                                    </span>
                                </div>

                                <small class="text-muted d-block mb-2">
                                    Stok: <strong>{{ $material->stok }} {{ $material->satuan }}</strong>
                                </small>

                                <button class="btn btn-primary btn-sm w-100">
                                    <i class="fas fa-shopping-cart"></i> Pesan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Belum ada material tersedia.
        </div>
    @endif
</div>

<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .card-img-top img {
        transition: transform 0.3s ease;
    }

    .card:hover .card-img-top img {
        transform: scale(1.05);
    }
</style>
@endsection
