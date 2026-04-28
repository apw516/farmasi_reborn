<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class laporanController extends Controller
{
    public function indexperencanaan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexperencanaan';
        $tipe = db::select('select * from mt_tipe_barang');
        return view('Laporan.indexperencanaanpembelian', compact([
            'menu',
            'date_start',
            'date_end',
            'tipe'
        ]));
    }
    public function indexdatafastmoving()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexdatafastmoving';
        $tipe = db::select('select * from mt_tipe_barang');
        $unit = db::select('select * from mt_unit where kode_unit > 4000 and kode_unit < 4014');
        return view('Laporan.indexdatafastmoving', compact([
            'menu',
            'date_start',
            'date_end',
            'tipe',
            'unit'
        ]));
    }
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
        $tipe = db::select('select * from mt_tipe_barang');
        return view('Laporan.indexrencanapengadaanbarang', compact([
            'menu',
            'date_start',
            'date_end',
            'tipe'
        ]));
    }
    public function buatrencanapengadaan(Request $request)
    {
        $kode_tipe = $request->kode_tipe;
        // $endDate = Carbon::parse($tgl);
        // 1. Tentukan Parameter Tanggal (Misal: Hari ini atau dari Request)
        $tglParameter = $request->tanggalakhir ?? Carbon::now()->toDateString();
        $endDate = Carbon::parse($tglParameter);

        // 2. Tentukan Angka Bulan (1-12) untuk 3 Bulan Terakhir
        $m1 = $endDate->format('n');                         // Bulan ini
        $m2 = $endDate->copy()->subMonth(1)->format('n');    // 1 bulan lalu
        $m3 = $endDate->copy()->subMonth(2)->format('n');
        $m4 = $endDate->copy()->subMonth(3)->format('n');


        // 2 bulan lalu
        $m11 = $endDate->format('M');                         // Bulan ini
        $m22 = $endDate->copy()->subMonth(1)->format('M');    // 1 bulan lalu
        $m33 = $endDate->copy()->subMonth(2)->format('M');    // 2 bulan lalu
        $m44 = $endDate->copy()->subMonth(3)->format('M');    // 2 bulan lalu

        // 3. Tentukan Tahun terkait masing-masing bulan
        $y1 = $endDate->format('Y');
        $y2 = $endDate->copy()->subMonth(1)->format('Y');
        $y3 = $endDate->copy()->subMonth(2)->format('Y');
        $y4 = $endDate->copy()->subMonth(3)->format('Y');

        $stokKeluar = DB::connection('mysql2')->table('ti_kartu_stok as s')
            ->join('mt_barang as b', 's.kode_barang', '=', 'b.kode_barang')
            ->select(
                's.kode_barang',
                'b.satuan',
                'b.hna',
                'b.isi',
                DB::raw("fc_nama_barang(s.kode_barang) as nama_barang"),
                // Total Keluar Bulan ke-3 (Paling Lama)
                DB::raw("SUM(CASE WHEN MONTH(s.tgl_stok) = '$m4' AND YEAR(s.tgl_stok) = '$y4' 
                 THEN s.stok_out ELSE 0 END) as bulan_ke_3"),

                // Total Keluar Bulan ke-2
                DB::raw("SUM(CASE WHEN MONTH(s.tgl_stok) = '$m3' AND YEAR(s.tgl_stok) = '$y3' 
                 THEN s.stok_out ELSE 0 END) as bulan_ke_2"),

                // Total Keluar Bulan ke-1 (Bulan Parameter)
                DB::raw("SUM(CASE WHEN MONTH(s.tgl_stok) = '$m2' AND YEAR(s.tgl_stok) = '$y2' 
                 THEN s.stok_out ELSE 0 END) as bulan_ke_1"),
                DB::raw("(SELECT sub.stok_current 
                  FROM ti_kartu_stok sub 
                  WHERE sub.kode_barang = s.kode_barang 
                  AND sub.kode_unit = '4001'
                  AND sub.tgl_stok <= '" . $endDate->toDateString() . "'
                  ORDER BY sub.no DESC LIMIT 1) as sisa_stok_sekarang")
            )
            ->where('s.kode_unit', '4001') // Filter Unit
            ->where('b.kode_tipe', $kode_tipe) // Filter Unit
            ->whereBetween('s.tgl_stok', [
                $endDate->copy()->subMonth(3)->startOfMonth()->toDateString(), // Awal bulan ke-3
                $endDate->toDateString()                                      // Sampai tanggal parameter
            ])
            ->groupBy('s.kode_barang')
            ->orderBy('nama_barang', 'ASC')
            ->get();
        return view('Laporan.tabel_r', compact([
            'stokKeluar',
            'm22',
            'm33',
            'm44',
            'endDate'
        ]));
    }
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
    public function getChartData(Request $request)
    {
        $tglMulai = $request->tgl_mulai ?? now()->subMonths(3)->format('Y-m-d');
        $tglSelesai = $request->tgl_selesai ?? now()->format('Y-m-d');
        $limit = $request->limit ?? 10;
        $unit_data = $request->unit_data;
        $data = DB::select("
        SELECT 
            fc_nama_barang(kode_barang) AS label,
            SUM(stok_out) AS total
        FROM ti_kartu_stok
        WHERE 
            kode_unit = ? 
            AND stok_out > 0
            AND tgl_stok BETWEEN ? AND ?
        GROUP BY kode_barang
        ORDER BY total DESC
        LIMIT ?
    ", [$unit_data, $tglMulai, $tglSelesai, (int)$limit]);

        return response()->json([
            'labels' => collect($data)->pluck('label'),
            'values' => collect($data)->pluck('total'),
        ]);
    }
    public function getChartWeekly(Request $request)
    {
        $tglMulai = $request->tgl_mulai ?? '2026-01-01';
        $tglSelesai = $request->tgl_selesai ?? '2026-03-31';
        $limit = $request->limit ?? 10;
        $unit_data = $request->unit_data;

        $data = DB::select("
        SELECT 
            YEARWEEK(tgl_stok, 1) as periode,
            CONCAT('Minggu ', DATE_FORMAT(tgl_stok, '%v-%Y')) AS label, 
            SUM(stok_out) AS total
            FROM ti_kartu_stok
            WHERE 
                kode_unit = ?
                AND tgl_stok BETWEEN ? AND ?
            GROUP BY periode, label
            -- Urutkan berdasarkan nilai integer YEARWEEK agar kronologis
            ORDER BY periode ASC
            LIMIT ?
        ", [$unit_data, $tglMulai, $tglSelesai, (int)$limit]);
        return response()->json([
            'labels' => collect($data)->pluck('label'),
            'values' => collect($data)->pluck('total'),
        ]);
    }
    public function getChartValueContribution(Request $request)
    {
        $tglMulai = $request->tgl_mulai ?? '2026-01-01';
        $tglSelesai = $request->tgl_selesai ?? '2026-03-31';
        $limit = $request->limit ?? 10;
        $unit_data = $request->unit_data;
        // Query untuk mendapatkan nilai total per barang
        $rawData = DB::select("
        SELECT 
            fc_nama_barang(ti_kartu_stok.kode_barang) AS nama,
            SUM(stok_out * b.harga_jual) AS total_nilai,
            b.harga_jual
        FROM ti_kartu_stok
        INNER JOIN mt_barang b on ti_kartu_stok.kode_barang = b.kode_barang
        WHERE 
            kode_unit = ? 
            AND tgl_stok BETWEEN ? AND ?
            AND stok_out > 0
        GROUP BY ti_kartu_stok.kode_barang
        ORDER BY total_nilai DESC
    ", [$unit_data,$tglMulai, $tglSelesai]);
        $data = collect($rawData);

        // Ambil Top 5
        $top5 = $data->take($limit);
        // Hitung sisanya sebagai "Lainnya"
        $othersValue = $data->slice($limit)->sum('total_nilai');

        $labels = $top5->pluck('nama')->toArray();
        $values = $top5->pluck('total_nilai')->toArray();

        if ($othersValue > 0) {
            $labels[] = 'Lainnya';
            $values[] = $othersValue;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values,
        ]);
    }
}
