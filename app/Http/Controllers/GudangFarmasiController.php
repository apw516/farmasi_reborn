<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MasterBarangBPJS;
use App\Models\model_master_barang_x_master_bpjs;
use App\Models\model_master_supplier;
use App\Models\model_tg_po_header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class GudangFarmasiController extends MasterController
{
    public function indexmasterstok()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmasterstok';
        return view('Gudang.indexdatastok', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function indexterimabarangpo()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexterimabarangpo';
        $tipe = DB::table('mt_tipe_barang')->get();
        $satuan = DB::table('mt_satuan')->get();
        $today = $this->get_date();
        return view('Gudang.indexterimabarangpo', compact([
            'menu',
            'date_start',
            'date_end',
            'tipe',
            'today',
            'satuan'
        ]));
    }
    public function ambildatastok()
    {
        $subQuery = DB::table('ti_kartu_stok')
            ->select('kode_barang', 'kode_unit', DB::raw('MAX(NO) as max_id'))
            ->groupBy('kode_barang', 'kode_unit');
        $query = DB::table('ti_kartu_stok as t1')
            ->joinSub($subQuery, 't2', 't1.no', '=', 't2.max_id')
            // Join langsung ke tabel master daripada pakai fungsi
            ->join('mt_barang as mb', 't1.kode_barang', '=', 'mb.kode_barang')
            ->join('mt_unit as mu', 't1.kode_unit', '=', 'mu.kode_unit')
            ->select([
                'mb.nama_barang', // Ambil kolom langsung
                'mu.nama_unit as unit',
                't1.stok_last',
                't1.tgl_stok',
                't1.kode_barang',
                't1.no'
            ]);

        return DataTables::of($query)
            ->addIndexColumn()
            // Sekarang filterColumn akan sangat cepat karena mencari di kolom tabel asli
            ->filterColumn('nama_barang', function ($query, $keyword) {
                $query->where('mb.nama_barang', 'LIKE', "%{$keyword}%");
            })
            ->filterColumn('unit', function ($query, $keyword) {
                $query->where('mu.nama_unit', 'LIKE', "%{$keyword}%");
            })
            ->make(true);
    }
    public function searchsupplier(Request $request)
    {
        $term = $request->get('term');
        $suppliers = model_master_supplier::where('nama_supplier', 'LIKE', '%' . $term . '%')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->kode_supplier,
                    'alamat' => $item->alamat_supplier,
                    'telp' => $item->tlp,
                    'value' => $item->nama_supplier, // Ini yang akan muncul di input
                    'label' => $item->nama_supplier . ' | ' . $item->alamat_supplier  // Ini yang akan muncul di daftar dropdown
                ];
            });
        return response()->json($suppliers);
    }
    public function ambildatatgpoheader(Request $request)
    {
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $data_header = db::select('select *,fc_NAMA_SUPPLIER(kode_supplier) as nama_supplier from tg_po_header where tgl_input between ?  and ?', [$tanggalawal, $tanggalakhir]);
        return view('Gudang.tabel_po_header', compact([
            'data_header'
        ]));
    }
    public function ambilformdetailpo(Request $request)
    {
        $id = $request->id;
        $data_header = db::select('select *,fc_NAMA_SUPPLIER(kode_supplier) as nama_supplier from tg_po_header where id = ?', [$id]);
        return view('Gudang.form_add_detail_po', compact([
            'data_header'
        ]));
    }
    public function simpanpoheader(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama3) {
            $index3 = $nama3['name'];
            $value3 = $nama3['value'];
            $dataSet3[$index3] = $value3;
        }
        dd($dataSet3);
        $datasave = [
            'kode_po' => $this->get_kode_po(),
            'no_faktur' => $dataSet3['nomorfaktur'],
            'kode_supplier' => $dataSet3['supplier_id'],
            'total_po' => $dataSet3['totalpo_asli'],
            'ppn' => $dataSet3['ppn_asli'],
            'sub_gtotal_po' => $dataSet3['totalpo_asli'] + $dataSet3['ppn_asli'],
            'disk_rupiah' => 0,
            'disk_persen' => 0,
            'gtotal_po' => $dataSet3['totalpo_asli'] + $dataSet3['ppn_asli'],
            'total_utang' => $dataSet3['totalhutang_asli'],
            'tgl_beli' => $dataSet3['tanggalbeli'],
            'tgl_terima' => $dataSet3['tanggalterima'],
            'tipe_po' => 'D',
            'tipe_trx' => 'K',
            'status_po' => 'CLS',
            'tgl_input' => $this->get_now(),
            'input_by' => auth()->user()->id,
            'pph' => 0,
            'kode_unit' => '4001',
            'status_retur' => 'OPN',
            'status_tagihan' => 'OPN',
            'status_pembayaran' => 'OPN',
            'materai' => 0,
            'keterangan' => 'MELALUI WEB'
        ];
        model_tg_po_header::create($datasave);
        return response()->json([
            'kode' => 200,
            'message' => 'Data PO berhasil disimpan ...'
        ], 200);
        die;
    }
    public function prosesbarangpilihanPO(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama3) {
            $index3 = $nama3['name'];
            $value3 = $nama3['value'];
            $dataSet3[$index3] = $value3;
        }
        $kode_barang = $dataSet3['kodebarang'];
        $nama_barang = $dataSet3['namabarangpilihan'];
        $qty = $dataSet3['qty'];
        $satuan = $dataSet3['satuan'];
        $hrgasatuan = $dataSet3['hrgasatuan'];
        $hrgasatuanasli = $dataSet3['hrgasatuanasli'];
        $ed = $dataSet3['ed'];
        $nobatch = $dataSet3['nobatch'];
        if ($qty == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Isi jumlah barang ...',
                'html'   => ''
            ]);
        } elseif ($hrgasatuan == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Harga satuan wajib diisi ...',
                'html'   => ''
            ]);
        } elseif ($ed == '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Expired date wajib diisi ...',
                'html'   => ''
            ]);
        } elseif ($nobatch == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Batch wajib diisi ...',
                'html'   => ''
            ]);
        } else {
            $diskon = $dataSet3['diskon'];
            $satuana = DB::table('mt_satuan')->get();
            $subtotal = $hrgasatuanasli * $qty;
            $hitungdiskon = $subtotal * $diskon / 100;

            $subtotal_final = $subtotal - $hitungdiskon;
            $subtotal_format = number_format($subtotal_final, 0, ',', '.');
            $dataarray = [
                'kode_barang' => $kode_barang,
                'nama_barang' => $nama_barang,
                'qty' => $qty,
                'satuan' => $satuan,
                'hrgasatuan' => $hrgasatuan,
                'hrgasatuanasli' => $hrgasatuanasli,
                'diskon' => $diskon,
                'nobatch' => $nobatch,
                'ed' => $ed,
                'subtotal' => $subtotal_final,
                'subtotal_format' => $subtotal_format
            ];
            $html = view('Gudang.form_barang_po', compact(['dataarray', 'satuana']))->render();
            return response()->json([
                'status' => 'success',
                'message' => 'ok',
                'html'   => $html
            ]);
        }
    }
    public function totalhitungpurchaseorder(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama2) {
            $index2 = $nama2['name'];
            $value2 = $nama2['value'];
            $dataSet2[$index2] = $value2;
            if ($index2 == 'list_subtotal_asli') {
                $bb[] = $dataSet2;
            }
        }
        $total = 0;
        foreach ($bb as $b) {
            $total = $total + $b['list_subtotal_asli'];
        }
        $total_format = number_format($total, 0, ',', '.');
        $total_asli = $total;
        return response()->json([
            'status' => 'success',
            'message' => 'ok',
            'total'   => $total_asli,
            'total_format' => $total_format
        ]);
    }
    public function get_kode_po()
    {
        $q = DB::select('SELECT id,kode_po,RIGHT(kode_po,4) AS kd_max  FROM tg_po_header
        WHERE DATE(tgl_input) = CURDATE()
        ORDER BY id DESC
        LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%04s", $tmp);
            }
        } else {
            $kd = "0001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'PO' . date('ymd') . $kd;
    }
}
