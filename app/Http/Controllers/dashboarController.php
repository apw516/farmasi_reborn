<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class dashboarController extends Controller
{
    public function Index()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'Dashboard';
       
        return view('Dashboard.index', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function grafikStok_labels()
    {
        // ... kode query Anda sebelumnya ...
        $subquery = DB::table('ti_kartu_stok')
            ->select('kode_barang', DB::raw('MAX(no) as max_id'))
            ->groupBy('kode_barang');
        $stokTerakhir = DB::table('ti_kartu_stok as log')
            ->joinSub($subquery, 'ti_kartu_stok', function ($join) {
                $join->on('log.no', '=', 'ti_kartu_stok.max_id');
            })
            ->select(
                'log.kode_barang',
                DB::raw("fc_nama_barang(log.kode_barang) as nama_barang"),
                'log.stok_last',
                'log.tgl_stok'
            )
            ->orderBy('log.stok_last', 'desc') // Urutkan stok terbanyak agar grafik bagus
            ->limit(15) // Ambil 15 besar saja agar grafik tidak terlalu padat
            ->get();

        // Pisahkan untuk keperluan grafik
        $labels = $stokTerakhir->pluck('nama_barang');
        $values = $stokTerakhir->pluck('stok_last');
        return $labels;
    }
    public function grafikStok_values()
    {
        // ... kode query Anda sebelumnya ...
        $subquery = DB::table('ti_kartu_stok')
            ->select('kode_barang', DB::raw('MAX(no) as max_id'))
            ->groupBy('kode_barang');
        $stokTerakhir = DB::table('ti_kartu_stok as log')
            ->joinSub($subquery, 'ti_kartu_stok', function ($join) {
                $join->on('log.no', '=', 'ti_kartu_stok.max_id');
            })
            ->select(
                'log.kode_barang',
                DB::raw("fc_nama_barang(log.kode_barang) as nama_barang"),
                'log.stok_last',
                'log.tgl_stok'
            )
            ->orderBy('log.stok_last', 'desc') // Urutkan stok terbanyak agar grafik bagus
            ->limit(15) // Ambil 15 besar saja agar grafik tidak terlalu padat
            ->get();

        // Pisahkan untuk keperluan grafik
        $labels = $stokTerakhir->pluck('nama_barang')->toArray();
        $values = $stokTerakhir->pluck('stok_last')->toArray();
        return $values;
    }
    public function getStokTerakhir()
    {
        // 1. Ambil ID terakhir untuk setiap barang (Subquery)
        $subquery = DB::table('ti_kartu_stok')
            ->select('kode_barang', DB::raw('MAX(no) as max_id'))
            ->groupBy('kode_barang');
        // dd($subquery);
        // 2. Join ke tabel log utama menggunakan ID tersebut
        $stokTerakhir = DB::table('ti_kartu_stok as log')
            ->joinSub($subquery, 'ti_kartu_stok', function ($join) {
                $join->on('log.no', '=', 'ti_kartu_stok.max_id');
            })
            ->select(
                'log.kode_barang',
                DB::raw("fc_nama_barang(log.kode_barang) as nama_barang"),
                'log.stok_last',
                'log.tgl_stok'
            )
            ->orderBy('log.kode_barang', 'asc')
            ->get();

        return $stokTerakhir;
    }
}
