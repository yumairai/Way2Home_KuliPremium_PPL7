@extends('admin.admin_page')
@section('title')
    Admin - Material Management
@endsection
@section('header')
    <h2>Kelola Material</h2>
    <p> Kelola stok dan informasi material proyek.</p>
@endsection
@section('stats')
    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">group</span>
            </div>
        </div>
        <p class="stat-title">Total Material</p>
        <h3 class="stat-value">{{ number_format($totalMaterial) }}</h3>
    </article>

    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">task_alt</span>
            </div>
        </div>
        <p class="stat-title">Material Terjual</p>
        <h3 class="stat-value">{{ number_format($totalTerjual) }}</h3>
    </article>

    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">home_repair_service</span>
            </div>
        </div>
        <p class="stat-title">Material Stok Habis</p>
        <h3 class="stat-value">{{ number_format($stokHabis) }}</h3>
    </article>

    <article class="stat-card">
        <div class="stat-head">
            <div class="stat-icon" style="background: rgba(180, 205, 254, 0.2); color: var(--color-secondary);">
                <span class="material-symbols-outlined">add_business</span>
            </div>
        </div>
        <p class="stat-title">Material Baru</p>
        <h3 class="stat-value">{{ number_format($materialBaru) }}</h3>
    </article>
@endsection
@section('content')
    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/admin/kelola_material.css') }}">
    @endpush

    {{-- Toolbar --}}
    <div class="mat-toolbar">
        <div class="mat-search">
            <span class="material-symbols-outlined">search</span>
            <input type="text" id="matSearch" placeholder="Cari nama material...">
        </div>
        <button class="mat-btn tambah" onclick="openModal()">
            <span class="material-symbols-outlined" style="font-size:18px;">add</span>
            Tambah Material
        </button>
    </div>

    {{-- Tabel --}}
    <div class="mat-table-wrap">
        <table class="mat-table" id="matTable">
            <thead>
                <tr>
                    <th>Foto</th>
                    <th>Nama Material</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Status</th>
                    <th style="padding-left: 4.5rem;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($materials as $mat)
                    <tr>
                        <td>
                            <img class="mat-foto" src="{{ $mat->path_foto_material }}" alt="{{ $mat->nama_material }}">
                        </td>
                        <td style="font-weight:600;">{{ $mat->nama_material }}</td>
                        <td>{{ $mat->kategori }}</td>
                        <td>Rp {{ number_format($mat->harga, 0, ',', '.') }}</td>
                        <td>{{ number_format($mat->stok) }}</td>
                        <td>{{ $mat->satuan }}</td>
                        <td>
                            @if ($mat->stok > 0)
                                <span class="mat-badge ready">Tersedia</span>
                            @else
                                <span class="mat-badge habis">Habis</span>
                            @endif
                        </td>
                        <td>
                            <div class="mat-actions">
                                <button class="mat-btn edit"
                                    onclick="openEditModal({{ $mat->id }}, '{{ addslashes($mat->nama_material) }}', '{{ addslashes($mat->kategori) }}', {{ $mat->harga }}, {{ $mat->stok }}, '{{ addslashes($mat->satuan) }}', '{{ addslashes($mat->deskripsi ?? '') }}')">
                                    <span class="material-symbols-outlined" style="font-size:15px;">edit</span>
                                    Edit
                                </button>
                                <form method="POST" action="{{ route('admin.material.destroy', $mat->id) }}"
                                    class="hapus-form">
                                    @csrf @method('DELETE')
                                    <button type="button" class="mat-btn hapus" onclick="konfirmasiHapus(this)">
                                        <span class="material-symbols-outlined" style="font-size:15px;">delete</span>
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:40px; color:var(--text-muted);">
                            Belum ada data material.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination --}}
        @if ($materials->hasPages())
            <div class="mat-pagination">
                {{ $materials->links() }}
            </div>
        @endif
    </div>

    {{-- Modal Tambah --}}
    <div class="mat-modal-backdrop" id="modalTambah">
        <div class="mat-modal">
            <h3>Tambah Material</h3>
            <form method="POST" action="{{ route('admin.material.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="mat-form-group">
                    <label>Nama Material</label>
                    <input type="text" name="nama_material" required placeholder="cth. Semen Portland 50kg">
                </div>
                <div class="mat-form-grid">
                    <div class="mat-form-group">
                        <label>Kategori</label>
                        <select name="kategori" required>
                            <option value="">-- Pilih --</option>
                            <option>Semen & Beton</option>
                            <option>Bata & Batu</option>
                            <option>Pasir & Kerikil</option>
                            <option>Besi & Baja</option>
                            <option>Kayu & Papan</option>
                            <option>Cat & Finishing</option>
                            <option>Keramik & Lantai</option>
                            <option>Pipa & Sanitasi</option>
                            <option>Atap & Genteng</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="mat-form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" required placeholder="cth. sak, m², pcs">
                    </div>
                </div>
                <div class="mat-form-grid">
                    <div class="mat-form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" required min="0" placeholder="75000">
                    </div>
                    <div class="mat-form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" required min="0" placeholder="100">
                    </div>
                </div>
                <div class="mat-form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" rows="3" placeholder="Deskripsi singkat material..."></textarea>
                </div>
                <div class="mat-form-group">
                    <label>Foto Material</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
                <div class="mat-modal-footer">
                    <button type="button" class="mat-btn cancel" onclick="closeModal()">Batal</button>
                    <button type="submit" class="mat-btn save">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div class="mat-modal-backdrop" id="modalEdit">
        <div class="mat-modal">
            <h3>Edit Material</h3>
            <form method="POST" id="editForm" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="mat-form-group">
                    <label>Nama Material</label>
                    <input type="text" name="nama_material" id="edit_nama" required>
                </div>
                <div class="mat-form-grid">
                    <div class="mat-form-group">
                        <label>Kategori</label>
                        <select name="kategori" id="edit_kategori" required>
                            <option>Semen & Beton</option>
                            <option>Bata & Batu</option>
                            <option>Pasir & Kerikil</option>
                            <option>Besi & Baja</option>
                            <option>Kayu & Papan</option>
                            <option>Cat & Finishing</option>
                            <option>Keramik & Lantai</option>
                            <option>Pipa & Sanitasi</option>
                            <option>Atap & Genteng</option>
                            <option>Lainnya</option>
                        </select>
                    </div>
                    <div class="mat-form-group">
                        <label>Satuan</label>
                        <input type="text" name="satuan" id="edit_satuan" required>
                    </div>
                </div>
                <div class="mat-form-grid">
                    <div class="mat-form-group">
                        <label>Harga (Rp)</label>
                        <input type="number" name="harga" id="edit_harga" required min="0">
                    </div>
                    <div class="mat-form-group">
                        <label>Stok</label>
                        <input type="number" name="stok" id="edit_stok" required min="0">
                    </div>
                </div>
                <div class="mat-form-group">
                    <label>Deskripsi</label>
                    <textarea name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                </div>
                <div class="mat-form-group">
                    <label>Ganti Foto (opsional)</label>
                    <input type="file" name="foto" accept="image/*">
                </div>
                <div class="mat-modal-footer">
                    <button type="button" class="mat-btn cancel" onclick="closeEditModal()">Batal</button>
                    <button type="submit" class="mat-btn save">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // ── Modal Tambah ──
        function openModal() {
            document.getElementById('modalTambah').classList.add('open');
        }

        function closeModal() {
            document.getElementById('modalTambah').classList.remove('open');
        }

        // ── Modal Edit ──
        function openEditModal(id, nama, kategori, harga, stok, satuan, deskripsi) {
            const form = document.getElementById('editForm');
            form.action = `/admin/kelola-material/${id}`;
            document.getElementById('edit_nama').value = nama;
            document.getElementById('edit_harga').value = harga;
            document.getElementById('edit_stok').value = stok;
            document.getElementById('edit_satuan').value = satuan;
            document.getElementById('edit_deskripsi').value = deskripsi;

            const sel = document.getElementById('edit_kategori');
            for (let opt of sel.options) {
                if (opt.value === kategori) {
                    opt.selected = true;
                    break;
                }
            }

            document.getElementById('modalEdit').classList.add('open');
        }

        function closeEditModal() {
            document.getElementById('modalEdit').classList.remove('open');
        }

        // Tutup modal klik backdrop
        ['modalTambah', 'modalEdit'].forEach(id => {
            document.getElementById(id).addEventListener('click', function(e) {
                if (e.target === this) this.classList.remove('open');
            });
        });

        // ── Search filter ──
        document.getElementById('matSearch').addEventListener('input', function() {
            const q = this.value.toLowerCase();
            document.querySelectorAll('#matTable tbody tr').forEach(row => {
                const nama = row.cells[1]?.textContent.toLowerCase() ?? '';
                row.style.display = nama.includes(q) ? '' : 'none';
            });
        });

        // ── Blokir scroll di input number ──
        document.addEventListener('wheel', function(e) {
            if (e.target.type === 'number') {
                e.preventDefault();
            }
        }, {
            passive: false
        });

        // ── Konfirmasi hapus ──
        async function konfirmasiHapus(btn) {
            const confirmed = await W2HDialog.confirm('Hapus material ini? Tindakan ini tidak bisa dibatalkan.');
            if (confirmed) {
                btn.closest('.hapus-form').submit();
            }
        }
    </script>
@endpush
