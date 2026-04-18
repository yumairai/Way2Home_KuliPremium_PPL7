<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    private const PER_PAGE = 6;

    // Menampilkan halaman marketplace
    public function index(Request $request)
    {
        $materials = $this->buildQuery($request)->paginate(self::PER_PAGE)->appends($request->query());
        return view('customer-layouts.material_marketplace', compact('materials'));
    }

    // API: Memberikan data JSON
    public function getMaterials(Request $request)
    {
        $materials = $this->buildQuery($request)->paginate(self::PER_PAGE);
        return response()->json($materials);
    }

    // Private: Build query dengan filter yang sama untuk index dan getMaterials
    private function buildQuery(Request $request)
    {
        $query = Material::query();

        // Filter by kategori
        if ($request->has('kategori') && $request->kategori) {
            $query->whereIn('kategori', (array) $request->kategori);
        }

        // Filter by harga max
        if ($request->has('harga_max') && $request->harga_max > 0) {
            $query->where('harga', '<=', $request->harga_max);
        }

        // Filter by stok
        if ($request->has('stok') && $request->stok) {
            if ($request->stok === 'ready') {
                $query->where('stok', '>', 0);
            } elseif ($request->stok === 'preorder') {
                $query->where('stok', '<=', 0);
            }
        }

        // Search by nama
        if ($request->has('search') && $request->search) {
            $query->where('nama_material', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sort = $request->query('sort', 'terbaru');
        switch ($sort) {
            case 'harga_rendah':
                $query->orderBy('harga', 'asc');
                break;
            case 'harga_tinggi':
                $query->orderBy('harga', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query;
    }
}
