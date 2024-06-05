<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Kategori;
use App\Models\Barang;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Validation\ValidatesRequests;


class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use ValidatesRequests;
    public function index()
    {
         //Memanggil store procedure : OK
        //  $rsetKategori = DB::select('CALL getKategoriAll');
        //  return view('experimen.index',compact('rsetKategori'));

        // $rsetKategori = Kategori::latest()->paginate(10);
        // $rsetKategori = Kategori::find(1)->barangs();

        // dd($rsetKategori);
        // return $rsetKategori->all();

        // $rsetKategori = DB::table('kategori')->paginate(2);

        // $rsetKategori = DB::table('kategori')
        //     ->select('id','kategori', 'jenis')
        //     ->paginate(2);


        // $rsetKategori = Kategori::select('id','deskripsi','kategori',
        //     \DB::raw('(CASE
        //         WHEN kategori = "M" THEN "Modal"
        //         WHEN kategori = "A" THEN "Alat"
        //         WHEN kategori = "BHP" THEN "Bahan Habis Pakai"
        //         ELSE "Bahan Tidak Habis Pakai"
        //         END) AS ketKategori'))
        //     ->paginate(2);
        // //  OK

        // $rsetKategori = DB::select('CALL getKategoriAll()','ketKategori("M")');
        // $rsetKategori = DB::raw("SELECT ketKategori("M") as someValue') ;

        // memanggil store function
        //----------------------------------------------------------------------------------
        // $rsetKategori = DB::table('kategori')
        //      ->select('id','deskripsi',DB::raw('ketKategori(kategori) as ketkategori'))
        //      ->get();
       // return $rsetKategori;
       //----------------------------------------------------------------------------------


       // memanggil store function ->pagination
       //----------------------------------------------------------------------------------
        // $rsetKategori = DB::table('kategori')
        //                ->select('id','deskripsi',DB::raw('ketKategori(kategori) as ketkategori'))->paginate(1);
        // return view('kategori.index',compact('rsetKategori'));
        //----------------------------------------------------------------------------------

        // Relasi one to Many Model
       //----------------------------------------------------------------------------------
        // $rsetKategori = Kategori::all();
        // return view('kategori.relasi', compact('rsetKategori'));
       //----------------------------------------------------------------------------------

        // cek data
        // return DB::table('kategori')->get();


        //Relasi one to many Kategori-barang
        //Migration: Query Builder
        //referensi https://laravel.com/docs/10.x/queries#joins
        // $rsetKategori = DB::table('kategori')
        //     ->join('barang', 'kategori.id', '=', 'barang.kategori_id')
        //     ->select('kategori.*', 'barang.merk', 'barang.seri')
        //     ->get();
        // return $rsetKategori;


        //mengakses method dari model Kategori - OK
        // ----------------------------------------------------------------
        $rsetKategori = Kategori::all();
        return view('kategori.index', compact('rsetKategori'));
    
        // ----------------------------------------------------------------
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $aKategori = array('blank'=>'Pilih Kategori',
                            'M'=>'Barang Modal',
                            'A'=>'Alat',
                            'BHP'=>'Bahan Habis Pakai',
                            'BTHP'=>'Bahan Tidak Habis Pakai'
                            );
        return view('kategori.create',compact('aKategori'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
    $this->validate($request, [
        'deskripsi' => 'required',
        'kategori' => 'required|in:M,A,BHP,BTHP',
    ]);

        //create post
        Kategori::create([
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori,
        ]);

        //redirect to index
        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // versi standar
        // ------------------------------------------------------------
        // $rsetKategori = Kategori::find($id);
        // return $rsetKategori;
        // ------------------------------------------------------------

        //memanfaatkan custom method di model
        // ------------------------------------------------------------
        // $rsetKategori = Kategori::showKategoriById($id);
        //  return $rsetKategori;
        // return view('kategori.show', compact('rsetKategori'));
        // ------------------------------------------------------------

        if (DB::table('barang')->where('kategori_id', $id)->exists()) {
            $rsetKategori = Kategori::find($id); // Jika ada barang yang terkait, ambil objek kategori dengan find().
        } else {
            $rsetKategori = Kategori::showKategoriById($id); // Jika tidak ada barang yang terkait, gunakan showKategoriById().
        }

        //return $rsetKategori;
        return view('kategori.show', compact('rsetKategori'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $aKategori = array('blank'=>'Pilih Kategori',
        'M'=>'Barang Modal',
        'A'=>'Alat',
        'BHP'=>'Bahan Habis Pakai',
        'BTHP'=>'Bahan Tidak Habis Pakai'
    );

        $rsetKategori = Kategori::find($id);
        //return $rsetBarang;
        return view('kategori.edit', compact('rsetKategori','aKategori'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'deskripsi'   => 'required',
            'kategori'    => 'required | in:M,A,BHP,BTHP',
        ]);

        $rsetKategori = Kategori::find($id);

        $rsetKategori->update([
            'deskripsi'  => $request->deskripsi,
            'kategori'   => $request->kategori
            ]);

            //redirect to index
        return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {


        if (DB::table('barang')->where('kategori_id', $id)->exists()){
            return redirect()->route('kategori.index')->with(['Gagal' => 'Data Gagal Dihapus!']);
        } else {
            $rsetKategori = Kategori::find($id);
            $rsetKategori->delete();
            return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);
        }




        // $rsetKategori = Kategori::find($id);

        // //delete kategori
        // $rsetKategori->delete();

        // //redirect to index
        // return redirect()->route('kategori.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }

    function getAPIKategori() {
        $rsetKategori = Kategori::all();
        $data = array("data"=>$rsetKategori);
        // $data = array("data" => $rsetKategori);
        return response()->json($data);
    }

    function getAPIKategorisatu($id) {
        $rsetKategori = Kategori::find($id);
        $data = array("data"=>$rsetKategori);
        // $data = array("data" => $rsetKategori);
        return response()->json($data);
    }
}