<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\DetailOrder;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;

class ManageMaterialController extends Controller
{
    public function __construct(private SupabaseStorageService $supabase) {}

    public function index()
    {
        $totalMaterial = Material::count();
        $stokHabis     = Material::where('stok', '<=', 0)->count();
        $materialBaru  = Material::whereDate('created_at', '>=', now()->subDays(30))->count();
        $totalTerjual  = DetailOrder::sum('jumlah');
        $materials     = Material::latest()->paginate(10);

        return view('admin.kelola_material', compact(
            'totalMaterial', 'stokHabis', 'materialBaru', 'totalTerjual', 'materials'
        ));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_material' => 'required|string|max:255',
            'kategori'      => 'required|string|max:100',
            'harga'         => 'required|integer|min:0',
            'deskripsi'     => 'nullable|string',
            'stok'          => 'required|integer|min:0',
            'satuan'        => 'required|string|max:50',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Upload ke Supabase public-assets, simpan URL lengkap
            $data['path_foto_material'] = $this->supabase->uploadPublic(
                $request->file('foto'),
                'materials'
            );
        }
        unset($data['foto']);

        Material::create($data);

        return back()->with('success', 'Material berhasil ditambahkan.');
    }

    public function update(Request $request, Material $material)
    {
        $data = $request->validate([
            'nama_material' => 'required|string|max:255',
            'kategori'      => 'required|string|max:100',
            'harga'         => 'required|integer|min:0',
            'deskripsi'     => 'nullable|string',
            'stok'          => 'required|integer|min:0',
            'satuan'        => 'required|string|max:50',
            'foto'          => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama dari Supabase kalau ada
            if ($material->getRawOriginal('path_foto_material')) {
                $this->supabase->deletePublic($material->getRawOriginal('path_foto_material'));
            }
            // Upload foto baru
            $data['path_foto_material'] = $this->supabase->uploadPublic(
                $request->file('foto'),
                'materials'
            );
        }
        unset($data['foto']);

        $material->update($data);

        return back()->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        // Hapus foto dari Supabase
        if ($material->getRawOriginal('path_foto_material')) {
            $this->supabase->deletePublic($material->getRawOriginal('path_foto_material'));
        }
        $material->delete();

        return back()->with('success', 'Material berhasil dihapus.');
    }
}