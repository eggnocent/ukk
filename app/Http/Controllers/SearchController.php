<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;
use App\Models\Barang;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Pencarian untuk barangmasuk dan barangkeluar
        $barangMasukResults = BarangMasuk::with('barang')
            ->whereHas('barang', function($q) use ($query) {
                $q->where('nama_barang', 'like', '%' . $query . '%');
            })
            ->orWhere('tgl_masuk', 'like', '%' . $query . '%')
            ->get();

        $barangKeluarResults = BarangKeluar::with('barang')
            ->whereHas('barang', function($q) use ($query) {
                $q->where('nama_barang', 'like', '%' . $query . '%');
            })
            ->orWhere('tgl_keluar', 'like', '%' . $query . '%')
            ->get();

        // Gabungkan hasil pencarian
        $results = [
            'barangmasuk' => $barangMasukResults,
            'barangkeluar' => $barangKeluarResults
        ];

        return view('search.results', ['results' => $results, 'query' => $query]);
    }
}
