<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;

class BarangKeluarController extends Controller
{
    use ValidatesRequests;

    public function index()
    {
        $barangkeluars = BarangKeluar::with('barang')->paginate(10);
        return view('barangkeluar.index', compact('barangkeluars'));
    }

    public function create()
    {
        $barangs = Barang::all();
        return view('barangkeluar.create', compact('barangs'));
    }

    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'tgl_keluar' => 'required|date',
        'qty_keluar' => 'required|integer|min:1',
        'barang_id' => 'required|exists:barang,id',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $barang = Barang::findOrFail($request->barang_id);

    // Periksa tanggal barang keluar dan barang masuk terakhir
    $barangMasukTerakhir = $barang->barangmasuk()->latest('tgl_masuk')->first();
    if ($barangMasukTerakhir && $request->tgl_keluar < $barangMasukTerakhir->tgl_masuk) {
        return redirect()->back()->withErrors(['tgl_keluar' => 'Tanggal barang keluar tidak boleh mendahului tanggal barang masuk terakhir.'])->withInput();
    }

    // Periksa ketersediaan stok
    if ($request->qty_keluar > $barang->stok) {
        return redirect()->back()->withErrors(['qty_keluar' => 'Jumlah keluar melebihi stok yang tersedia'])->withInput();
    }

    // Simpan data pengeluaran barang jika validasi berhasil
    $barangKeluar = new BarangKeluar();
    $barangKeluar->fill($request->all());
    $barangKeluar->save();

    // Kurangi stok barang yang keluar dari stok yang tersedia
    $barang->stok -= $request->qty_keluar;
    $barang->save();

    return redirect()->route('barangkeluar.index')->with(['success' => 'Data Barang Keluar Berhasil Disimpan!']);
}

    public function show($id)
    {
        $barangkeluar = BarangKeluar::findOrFail($id);
        return view('barangkeluar.show', compact('barangkeluar'));
    }

    public function edit($id)
    {
        $barangkeluar = BarangKeluar::findOrFail($id);
        $barangs = Barang::all();
        return view('barangkeluar.edit', compact('barangkeluar', 'barangs'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'tgl_keluar' => 'required|date',
            'qty_keluar' => 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $barangKeluar = BarangKeluar::findOrFail($id);
        $barang = Barang::findOrFail($request->barang_id);
    
        // Periksa tanggal barang keluar dan barang masuk terakhir
        $barangMasukTerakhir = $barang->barangmasuk()->latest('tgl_masuk')->first();
        if ($barangMasukTerakhir && $request->tgl_keluar < $barangMasukTerakhir->tgl_masuk) {
            return redirect()->back()->withErrors(['tgl_keluar' => 'Tanggal barang keluar tidak boleh mendahului tanggal barang masuk terakhir.'])->withInput();
        }
    
        // Periksa apakah jumlah keluar melebihi stok yang tersedia
        $jumlahBarangKeluarSebelumnya = $barangKeluar->qty_keluar;
        if ($request->qty_keluar > $barang->stok + $jumlahBarangKeluarSebelumnya) {
            return redirect()->back()->withErrors(['qty_keluar' => 'Jumlah keluar melebihi stok yang tersedia'])->withInput();
        }
    
        // Perbarui data pengeluaran barang
        $barangKeluar->update($request->all());
    
        // Perbarui stok barang
        $barang->stok += $jumlahBarangKeluarSebelumnya; // Kembalikan stok yang sebelumnya dikurangkan
        $barang->stok -= $request->qty_keluar; // Kurangi stok dengan jumlah baru yang keluar
        $barang->save();
    
        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Barang Keluar Berhasil Diperbarui!']);
    }

    public function destroy($id)
    {
        $barangkeluar = BarangKeluar::findOrFail($id);
        $barangkeluar->delete();

        return redirect()->route('barangkeluar.index')->with(['success' => 'Data Barang Keluar Berhasil Dihapus!']);
    }

    public function updateAPIBarangKeluar(Request $request, $barang_keluar_id)
    {
        $barangKeluar = BarangKeluar::find($barang_keluar_id);

        if (null == $barangKeluar) {
            return response()->json(['status' => "BarangKeluar tidak ditemukan"]);
        }

        $barangKeluar->tgl_keluar = $request->tanggal_keluar;
        $barangKeluar->qty_keluar = $request->jumlah_keluar;
        $barangKeluar->barang_id = $request->barang_id;
        $barangKeluar->save();

        return response()->json(["status" => "BarangKeluar berhasil diubah"]);
    }

    public function showAPIBarangKeluar(Request $request)
    {
        $barangKeluar = BarangKeluar::all();
        return response()->json($barangKeluar);
    }

    public function createAPIBarangKeluar(Request $request)
    {
        $this->validate($request, [
            'tgl_keluar' => 'required|date',
            'qty_keluar' => 'required|integer|min:1',
            'barang_id' => 'required|exists:barang,id',
        ]);

        $barangKeluar = BarangKeluar::create([
            'tgl_keluar' => $request->tanggal_keluar,
            'qty_keluar' => $request->jumlah_keluar,
            'barang_id' => $request->barang_id,
        ]);

        return response()->json(["status" => "data berhasil dibuat"]);
    }

    public function deleteAPIBarangKeluar($barang_keluar_id)
    {
        $del_barangKeluar = BarangKeluar::findOrFail($barang_keluar_id);
        $del_barangKeluar->delete();

        return response()->json(["status" => "data berhasil dihapus"]);
    }
}
