<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// UTILITY ROUTES FOR SHARED HOSTING (Delete after use)
Route::get('/clear-cache', function() {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    return "All cache cleared!";
});

Route::get('/storage-link', function() {
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return "Storage linked!";
});

Route::get('/migrate', function() {
    \Illuminate\Support\Facades\Artisan::call('migrate --force');
    return "Migrations run!";
});

// ROUTE UNTUK CEK DATA BARANG MASUK
Route::get('/test-data-barang-masuk', function() {
    try {
        $data = \App\Models\BarangMasuk::with('barang')->latest()->limit(10)->get();
        
        if ($data->isEmpty()) {
            return "Databasenya konek, tapi belum ada data barang masuk.";
        }

        $output = "<h1>10 Data Barang Masuk Terakhir</h1><table border='1' cellpadding='10'>";
        $output .= "<tr><th>No Transaksi</th><th>Nama Barang</th><th>Jumlah</th><th>Tanggal</th><th>Status</th></tr>";
        
        foreach ($data as $item) {
            $namaBarang = $item->barang ? $item->barang->nama_barang : 'Barang Terhapus';
            $output .= "<tr>
                <td>{$item->no_transaksi}</td>
                <td>{$namaBarang}</td>
                <td>{$item->quantity}</td>
                <td>" . ($item->tanggal ? $item->tanggal->format('d-m-Y') : '-') . "</td>
                <td>{$item->status}</td>
            </tr>";
        }
        $output .= "</table>";
        
        return $output;
    } catch (\Exception $e) {
        return "Gagal ambil data: " . $e->getMessage();
    }
});
