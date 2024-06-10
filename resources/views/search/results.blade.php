@extends('layout.dashboard')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Hasil Pencarian</h1>

    <!-- Tabel Hasil Pencarian -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Pencarian</h6>
        </div>
        <div class="card-body">
            @if($results['kategori']->isEmpty() && $results['barang']->isEmpty())
                <p>Tidak ada hasil ditemukan.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Kategori</th>
                                <th>Spesifikasi</th>
                                <th>Stok</th>
                                <!-- Tambahkan kolom lain yang diperlukan -->
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach($results['kategori'] as $kategori)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $kategori->deskripsi }}</td>
                                    <td>{{ $kategori->kategori }}</td>
                                    <td>{{ $kategori->spesifikasi }}</td>
                                    <td>-</td>
                                    <!-- Tambahkan kolom lain yang diperlukan -->
                                </tr>
                            @endforeach
                            @foreach($results['barang'] as $barang)
                                <tr>
                                    <td>{{ $no++ }}</td>
                                    <td>{{ $barang->merk }} {{ $barang->seri }}</td>
                                    <td>{{ $barang->kategori->kategori }}</td>
                                    <td>{{ $barang->spesifikasi }}</td>
                                    <td>{{ $barang->stok }}</td>
                                    <!-- Tambahkan kolom lain yang diperlukan -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
