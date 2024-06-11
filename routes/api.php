<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/showkategori', [KategoriController::class, 'showAPIKategori']);
Route::post('editkategori/{kategori_id}', [KategoriController::class, 'updateAPIKategori']);
Route::post('createkategori',[KategoriController::class, 'createAPIKategori']);
Route::delete('deletekategori/{kategori_id}',[KategoriController::class, 'deleteAPIKategori']);

Route::get('/showbarang', [BarangController::class, 'showAPIBarang']);
Route::post('editbarang/{barang_id}', [BarangController::class, 'updateAPIBarang']);
Route::post('createbarang',[BarangController::class, 'createAPIBarang']);
Route::delete('deletebarang/{barang_id}',[BarangController::class, 'deleteAPIBarang']);

Route::get('/showbarangmasuk', [BarangMasukController::class, 'showAPIBarangMasuk']);
Route::post('editbarangmasuk/{barangmasuk_id}', [BarangMasukController::class, 'updateAPIBarangMasuk']);
Route::post('createbarangmasuk',[BarangMasukController::class, 'createAPIBarangMasuk']);
Route::delete('deletebarangmasuk/{barangmasuk_id}',[BarangMasukController::class, 'deleteAPIBarangMasuk']);

Route::get('/showbarangkeluar', [BarangKeluarController::class, 'showAPIBarangKeluar']);
Route::post('editbarangkeluar/{barangkeluar_id}', [BarangKeluarController::class, 'updateAPIBarangKeluar']);
Route::post('createbarangkeluar',[BarangKeluarController::class, 'createAPIBarangKeluar']);
Route::delete('deletebarangkeluar/{barangkeluar_id}',[BarangKeluarController::class, 'deleteAPIBarangKeluar']);