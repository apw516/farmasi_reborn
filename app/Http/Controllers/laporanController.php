<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class laporanController extends Controller
{
    public function indexlaporanmasterpengadaan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'laporanpengadaanbarang';
        return view('Laporan.indexlaporanmasterpengadaan', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function indexrencanapengadaanbarang()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexrencanapengadaanbarang';
        return view('Laporan.indexrencanapengadaanbarang', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    // public function ambildatarencanapengadaan(Request $request)
    // {
    //     // Ambil 3 bulan terakhir untuk label header
    //     $months = collect(range(2, 0))->map(function ($i) {
    //         return Carbon::now()->subMonths($i);
    //     });
    //     $dataPO = DB::table('tg_po_detail as a')
    //         ->join('tg_po_header as b', 'a.kode_po', '=', 'b.kode_po')
    //         ->select([
    //             'a.kode_barang',
    //             DB::raw("fc_nama_barang(a.kode_barang) AS nama_barang"),
    //             // Pivot data berdasarkan bulan (1-12)
    //             DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[0]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_1"),
    //             DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[1]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_2"),
    //             DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[2]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_3"),
    //             DB::raw('SUM(a.qty_kecil) as total_po')
    //         ])
    //         ->where('b.tgl_beli', '>=', $months[0]->copy()->startOfMonth())
    //         ->groupBy('a.kode_barang')
    //         ->orderBy('total_po', 'desc')
    //         // ->limit(10)
    //         ->get();
    //     return view('Laporan.tabel_rencana_pembelian', compact([
    //         'dataPO',
    //         'months'
    //     ]));
    // }
    // public function getdatarencanapengadaan_lengkap()
    // {
    //     // 1. Ambil 3 bulan terakhir (Carbon)
    //     $months = collect(range(2, 0))->map(fn($i) => Carbon::now()->subMonths($i));

    //     // 2. Subquery Sisa Stok Terakhir
    //     $subStok = DB::table('ti_kartu_stok')
    //         ->select('kode_barang', 'stok_last')
    //         ->whereIn('no', function ($query) {
    //             $query->select(DB::raw('MAX(no)'))
    //                 ->from('ti_kartu_stok')
    //                 ->groupBy('kode_barang');
    //         });

    //     // 3. Main Query (PO + Sisa Stok)
    //     $dataPO = DB::table('tg_po_detail as a')
    //         ->join('tg_po_header as b', 'a.kode_po', '=', 'b.kode_po')
    //         ->leftJoinSub($subStok, 'stok_last', function ($join) {
    //             $join->on('a.kode_barang', '=', 'stok_last.kode_barang');
    //         })
    //         ->select([
    //             'a.kode_barang',
    //             DB::raw("fc_nama_barang(a.kode_barang) AS nama_barang"),
    //             // Pivot Bulanan
    //             DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[0]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_1"),
    //             DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[1]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_2"),
    //             DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[2]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_3"),
    //             DB::raw('SUM(a.qty_kecil) as total_po'),
    //             'stok_last.stok_last as sisa_gudang' // Kolom sisa stok
    //         ])
    //         ->where('b.tgl_beli', '>=', $months[0]->copy()->startOfMonth())
    //         ->groupBy('a.kode_barang', 'stok_last.stok_last')
    //         ->orderBy('total_po', 'desc')
    //         ->limit(10)
    //         ->get();
    //     dd($dataPO);
    // }
    public function getLaporanAnalisisStok(Request $request)
    {
        // 1. Ambil input tanggal dari user (default ke hari ini jika kosong)
        $tglInput = $request->input('tanggalakhir', Carbon::now()->toDateString());
        $endDate = Carbon::parse($tglInput);
        // 2. Buat urutan 3 bulan ke belakang dari tanggal yang dipilih
        $months = collect(range(2, 0))->map(function ($i) use ($endDate) {
            return $endDate->copy()->subMonths($i);
        });
        // // 1. Ambil 3 bulan terakhir
        // $months = collect(range(2, 0))->map(function ($i) {
        //     return Carbon::now()->subMonths($i);
        // });

        // 2. Subquery untuk mendapatkan ID terakhir per barang di log_stok
        $subStok = DB::table('ti_kartu_stok')
            ->select('kode_barang', 'stok_last')
            ->whereIn('no', function ($query) {
                $query->select(DB::raw('MAX(no)'))
                    ->from('ti_kartu_stok')
                    ->groupBy('kode_barang');
            });

        // 3. Main Query (PO Join Subquery Stok)
        $dataPO = DB::table('tg_po_detail as a')
            ->join('tg_po_header as b', 'a.kode_po', '=', 'b.kode_po')
            ->leftJoinSub($subStok, 'stok_terakhir', function ($join) {
                $join->on('a.kode_barang', '=', 'stok_terakhir.kode_barang');
            })
            ->select([
                'a.kode_barang',
                DB::raw("fc_nama_barang(a.kode_barang) AS nama_barang"),
                // Pivot data 3 bulan
                DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[0]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_1"),
                DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[1]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_2"),
                DB::raw("SUM(CASE WHEN MONTH(b.tgl_beli) = " . $months[2]->month . " THEN a.qty_kecil ELSE 0 END) AS bulan_3"),
                DB::raw('SUM(a.qty_kecil) as total_po'),
                // Ambil kolom sisa stok dari join subquery
                DB::raw('IFNULL(stok_terakhir.stok_last, 0) as sisa_stok')
            ])
            ->where('b.tgl_beli', '>=', $months[0]->copy()->startOfMonth())
            ->groupBy('a.kode_barang', 'stok_terakhir.stok_last')
            ->orderBy('total_po', 'desc')
            ->get();

        $dataPO = $dataPO->map(function ($item) {
            // Hitung rata-rata pemakaian per bulan (Total PO 3 bulan terakhir / 3)
            $avgKebutuhan = $item->total_po / 3;

            // Logika Skala Prioritas
            if ($item->sisa_stok <= ($avgKebutuhan * 0.5)) {
                // Jika stok kurang dari atau sama dengan kebutuhan untuk 2 minggu (0.5 bulan)
                $item->status = 'URGENT';
                $item->warna = 'danger';
                $item->pesan = 'Stok Sangat Kritis!';
            } elseif ($item->sisa_stok < $avgKebutuhan) {
                // Jika stok di bawah kebutuhan 1 bulan
                $item->status = 'SAFETY';
                $item->warna = 'warning';
                $item->pesan = 'Segera Re-order';
            } else {
                // Stok masih cukup untuk 1 bulan ke depan
                $item->status = 'AMAN';
                $item->warna = 'success';
                $item->pesan = 'Stok Mencukupi';
            }

            // Rekomendasi jumlah beli (Kebutuhan ideal - sisa stok)
            $item->rekomendasi_beli = max(0, round($avgKebutuhan - $item->sisa_stok));

            return $item;
        });
        return view('Laporan.tabel_rencana_pembelian', compact('dataPO', 'months', 'endDate'));
        // return view('Laporan.tabel_rencana_pembelian', compact('dataPO', 'months'));
    }
    public function getRencanaPembelian(Request $request)
    {
        $tglAwal  = $request->input('tgl_awal', Carbon::now()->subMonths(3)->toDateString());
        $tglAkhir = $request->input('tgl_akhir', Carbon::now()->toDateString());
        // 1. Subquery untuk Sisa Stok Terakhir
        $subStok = DB::table('ti_kartu_stok')
            ->select([
                DB::raw("fc_nama_barang(kode_barang) AS nama_barang"),
                DB::raw("kode_barang"),
                DB::raw("stok_last as sisa_stok_terakhir"),
            ])
            ->whereIn('no', function ($query) {
                $query->select(DB::raw('MAX(no)'))
                    ->from('ti_kartu_stok')
                    ->groupBy('kode_barang');
            });

        // 2. Main Query dengan Filter Range Tanggal
        $rencanaBeli = DB::table('tg_po_detail as p')
            ->leftJoinSub($subStok, 's', function ($join) {
                $join->on('p.kode_barang', '=', 's.kode_barang');
            })
            ->join('tg_po_header as a', 'p.kode_po', '=', 'a.kode_po') // Sesuaikan foreign key b.id_header
            ->select([
                'p.kode_barang',
                // Dinamis berdasarkan bulan dari tglAwal dan tglAkhir
                DB::raw("SUM(CASE WHEN MONTH(a.tgl_beli) = MONTH(DATE_SUB('$tglAkhir', INTERVAL 2 MONTH)) THEN p.qty_kecil ELSE 0 END) AS Bulan_1"),
                DB::raw("SUM(CASE WHEN MONTH(a.tgl_beli) = MONTH(DATE_SUB('$tglAkhir', INTERVAL 1 MONTH)) THEN p.qty_kecil ELSE 0 END) AS Bulan_2"),
                DB::raw("SUM(CASE WHEN MONTH(a.tgl_beli) = MONTH('$tglAkhir') THEN p.qty_kecil ELSE 0 END) AS Bulan_3"),
                's.sisa_stok_terakhir as Sisa',
                DB::raw("GREATEST(0, ROUND((SUM(p.qty_kecil) / 3) - IFNULL(s.sisa_stok_terakhir, 0))) AS Beli_Lagi")
            ])
            ->whereBetween('a.tgl_beli', [$tglAwal, $tglAkhir]) // Filter Range Tanggal
            ->groupBy('p.kode_barang', 's.sisa_stok_terakhir')
            ->get();
    }
    public function ambildatalaporanpembelianbarang(Request $request)
    {
        $tglAwal = $request->tanggalawal;
        $tglAkhir = $request->tanggalakhir;
        // dd($tglAkhir);
        DB::statement("SET lc_time_names = 'id_ID'");
        $laporan = DB::table('tg_po_header as a')
            ->join('tg_po_detail as b', 'a.kode_po', '=', 'b.kode_po') // Sesuaikan foreign key b.id_header
            ->select([
                DB::raw("fc_nama_barang(b.kode_barang) AS nama_barang"),
                DB::raw("YEAR(a.tgl_beli) AS tahun"),
                DB::raw("MONTHNAME(a.tgl_beli) AS bulan"),
                DB::raw("COUNT(b.id) AS jumlah_po"),
                DB::raw("SUM(b.qty_kecil) AS total_barang_dipesan"),
                DB::raw("satuan"),
                DB::raw("isi"),
            ])
            // Filter berdasarkan range tanggal
            ->whereBetween('a.tgl_beli', [$tglAwal, $tglAkhir])
            ->groupBy('b.kode_barang', 'tahun', 'bulan', 'satuan', 'isi')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->orderBy('b.kode_barang', 'asc')
            ->get();

        $penjualan = DB::table('ts_layanan_detail as detail')
            ->join('ts_layanan_header as header', 'header.id', '=', 'detail.row_id_header')
            ->join('mt_barang as barang', 'detail.kode_barang', '=', 'barang.kode_barang')
            ->select([
                'detail.kode_barang',
                'barang.isi',
                'barang.satuan_besar',
                DB::raw("fc_nama_barang(detail.kode_barang) as nama_barang"),
                DB::raw("YEAR(header.tgl_entry) as tahun"),
                DB::raw("MONTH(header.tgl_entry) as bulan_angka"), // Tambahkan ini untuk sorting
                DB::raw("MONTHNAME(header.tgl_entry) as bulan"),
                DB::raw("SUM(detail.jumlah_layanan) as jumlah_layanan"),
                DB::raw("SUM(detail.total_layanan) as total_pendapatan")
            ])
            ->whereBetween('header.tgl_entry', [$tglAwal, $tglAkhir])
            ->whereAnd('kode_barang!=', '')
            ->groupBy('detail.kode_barang', 'tahun', 'bulan_angka', 'bulan') // Masukkan bulan_angka ke sini
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan_angka', 'desc') // Gunakan alias bulan_angka
            ->orderBy('detail.kode_barang', 'asc')
            ->get();
        return view('Laporan.tabel_laporan_pengadaan', compact([
            'laporan',
            'penjualan'
        ]));
    }
}
