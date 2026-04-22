<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MasterBarangBPJS;
use App\Models\model_master_barang_x_master_bpjs;
use App\Models\model_master_supplier;
use App\Models\model_stok_persediaan;
use App\Models\model_tg_po_detail;
use App\Models\model_tg_po_header;
use App\Models\model_ti_kartu_stok;
use App\Models\mutasidetail;
use App\Models\mutasiheader;
use App\Models\tg_retur_po_detail;
use App\Models\tg_retur_po_header;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class GudangFarmasiController extends MasterController
{
    public function indexstokbarang()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexstokbarang';
        $today = $this->get_date();
        $mt_unit = db::select('select * from mt_unit where kode_unit > 4000 and kode_unit < 4014');
        $tipe = db::select('select * from mt_tipe_barang');
        return view('Gudang.index_data_stok_barang', compact([
            'menu',
            'date_start',
            'date_end',
            'today',
            'mt_unit',
            'tipe'
        ]));
    }
    public function stokdatabarang(Request $request)
    {
        try {
            $kode_unit = $request->kode_unit;
            $kode_tipe = $request->kode_tipe; // Parameter dari request

            $data = DB::table('ti_stok_pesediaan as s')
                ->join('mt_barang as b', 's.kode_barang', '=', 'b.kode_barang')
                ->select([
                    's.kode_barang',
                    'b.nama_barang',
                    'b.satuan',
                    DB::raw("SUM(s.stok_sekarang) AS total_stok_barang"),
                    DB::raw("MAX(s.tgl_entry) as tgl_entry_terakhir")
                ])
                // Filter Berdasarkan Tipe Barang (mt_barang)
                ->when($kode_tipe, function ($query, $kode_tipe) {
                    return $query->where('b.kode_tipe', $kode_tipe);
                })
                // Jika kode_unit TIDAK kosong, tambahkan filter dan kolom unit
                ->when($kode_unit, function ($query, $kode_unit) {
                    return $query->addSelect([
                        's.kode_unit',
                        DB::raw("fc_nama_unit1(s.kode_unit) as nama_unit")
                    ])
                        ->where('s.kode_unit', $kode_unit)
                        ->groupBy('s.kode_unit');
                }, function ($query) {
                    // Jika kode_unit KOSONG, berikan kolom bayangan agar DataTables tidak error
                    return $query->addSelect([
                        DB::raw("'' as kode_unit"),
                        DB::raw("'SEMUA UNIT' as nama_unit")
                    ]);
                })
                ->groupBy('s.kode_barang', 'b.nama_barang', 'b.satuan')
                ->having('total_stok_barang', '>', 0)
                ->get();

            return datatables()->of($data)
                ->addIndexColumn()
                ->addColumn('aksi', function ($row) {
                    return '<button type="button" class="btn btn-info btn-sm btn-detail-batch" 
                data-kode="' . $row->kode_barang . '" 
                data-unit="' . $row->kode_unit . '"
                data-nama="' . $row->nama_barang . '">
                <i class="bi bi-list-ul"></i> Detail Batch
            </button>';
                })
                ->rawColumns(['aksi'])
                ->make(true);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function indexmutasibarang()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmutasibarang';
        $tipe = DB::table('mt_tipe_barang')->get();
        $satuan = DB::table('mt_satuan')->get();
        $today = $this->get_date();
        $mt_unit = Unit::where('kode_unit', '>', '4000')->where('kode_unit', '<', '5000')->get();
        return view('Gudang.indexmutasibarang', compact([
            'menu',
            'date_start',
            'date_end',
            'tipe',
            'today',
            'satuan',
            'mt_unit'
        ]));
    }
    public function indexstoksediaanbarang()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexstoksediaanbarang';
        $mt_unit = db::select('select * from mt_unit where kode_unit > 4000 and kode_unit < 4014');
        $tipe = db::select('select * from mt_tipe_barang');

        return view('Gudang.indexstokpersediaan', compact([
            'menu',
            'date_start',
            'date_end',
            'mt_unit',
            'tipe'
        ]));
    }
    public function batalkanpo(Request $request)
    {
        $nomorpo = $request->noPO;
        DB::beginTransaction();
        try {
            $sediaan = model_stok_persediaan::where('nomor_dokumen', $nomorpo)->get();
            foreach ($sediaan as $ss) {
                $stok_awal = $ss->stok_awal;
                $stok_sekarang = $ss->stok_sekarang;
                if ($stok_sekarang != $stok_awal) {
                    return response()->json([
                        'code' => '500',
                        'message' => 'Retur PO Gagal, Data barang sudah dimutasi ke unit lain ...'
                    ]);
                    die;
                }
            }
            $header = model_tg_po_header::where('kode_po', $nomorpo)->get()->first();
            model_tg_po_header::where('kode_po', $nomorpo)->update(['status_po' => 'CCL', 'status_retur' => 'CCL', 'status_tagihan' => 'CCL', 'status_pembayaran' => 'CCL']);
            $detail = model_tg_po_detail::where('kode_po', $nomorpo)->get();
            model_tg_po_detail::where('kode_po', $nomorpo)->update(['status_po_detail' => 'CCL']);
            $kode_retur = $this->kode_retur_po();
            $data_retur_header = [
                'kode_retur_po' =>  $kode_retur,
                'kode_po' => $nomorpo,
                'tgl_retur' => $this->get_now(),
                'total_retur' => $header->gtotal_po,
                'alasan_retur' => 'RETUR',
                'pic' => auth()->user()->id,
            ];
            tg_retur_po_header::create($data_retur_header);
            $datenow = $this->get_now();
            foreach ($detail as $d) {
                $kode_retur_detail = $this->get_kode_retur_detail_po();
                $data_retur_detail = [
                    'kode_retur_po_detail' => $kode_retur_detail,
                    'tgl_retur_po_detail' => $datenow,
                    'kode_retur_po' => $kode_retur,
                    'kode_barang' => $d->kode_barang,
                    'id_po_detail' => $d->id,
                    'qty_Awal' => $d->qty_kecil,
                    'qty_retur' => $d->qty_kecil,
                    'qty_sisa' => 0,
                    'hrg_satuan' => $d->hrg_satuan / $d->qty_kecil,
                    'total_retur_po_detail' => $d->hrg_satuan,
                    'status_retur_po_detail' => 'CLS',
                    'satuan' => $d->satuan,
                ];
                tg_retur_po_detail::create($data_retur_detail);
            }
            foreach ($sediaan as $s) {
                $stok_sekarang = $s->stok_sekarang;
                $kode_unit = $s->kode_unit;
                $kode_barang = $s->kode_barang;
                $id_sediaan = $s->id;

                model_stok_persediaan::where('id', $s->id)->update(['stok_sekarang' => 0]);
                $stok_terakhir_2 = DB::table('ti_kartu_stok as a')
                    ->where('a.kode_barang', $kode_barang)
                    ->where('a.kode_unit', $kode_unit)
                    ->orderBy('a.NO', 'desc')
                    ->first();
                if ($stok_terakhir_2) {
                    $stok_current = $stok_terakhir_2->stok_current -  $stok_sekarang;
                    $stok_in = 0;
                    $stok_out =  $stok_sekarang;
                    $stok_last = $stok_terakhir_2->stok_current;
                    $hrgbeli_hist = $stok_terakhir_2->harga_beli_history;
                    $harga_beli = $stok_terakhir_2->harga_beli;
                } else {
                    $stok_current =  0;
                    $stok_in = 0;
                    $stok_out =  $stok_sekarang;
                    $stok_last = 0;
                    $hrgbeli_hist = 0;
                    $harga_beli = $s->hpp;
                }
                $data_ti_kartu_stok_2 = [
                    'no_dokumen' => $kode_retur,
                    'no_dokumen_detail' => '',
                    'no_faktur' => '',
                    'tgl_stok' => $this->get_now(),
                    'kode_unit' => $kode_unit,
                    'kode_barang' => $kode_barang,
                    'stok_last' => $stok_last,
                    'stok_in' => $stok_in,
                    'stok_out' => $stok_out,
                    // 'stok_ed' => '',
                    'stok_current' => $stok_current,
                    // 'qty_pending' => '',
                    // 'stok_global' => '',
                    'harga_beli' => $harga_beli,
                    'inputby' => auth()->user()->id,
                    'ed_obat' => $s->ED,
                    'keterangan' => 'BATAL PO',
                    'harga_beli_history' => $hrgbeli_hist,
                    'id_sediaan' => $id_sediaan,
                    // 'no_dokumen' => '',
                ];
                model_ti_kartu_stok::create($data_ti_kartu_stok_2);
            }
            DB::commit();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'ok',
                'html'   => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => '500',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function pencarianbarang(Request $request)
    {
        $kode_supplier = $request->kodesupplier;
        $kategori_barang = $request->kategori_barang;
        return view('Gudang.tabel_barang', compact([
            'kode_supplier',
            'kategori_barang'
        ]));
    }
    public function ambilbarang(Request $request)
    {

        if ($request->ajax()) {
            $kode_supplier = $request->kode_supplier;
            $kategori_barang = $request->kategori_barang;
            $data = DB::table('mt_set_barang_supplier as a')
                // Join ke mt_barang (b) untuk mendapatkan detail item & kategori
                ->join('mt_barang as b', 'a.kode_barang', '=', 'b.kode_barang')
                ->select([
                    'b.kode_barang',
                    'b.nama_barang',
                    'b.satuan_besar',
                    'b.satuan',
                    'b.sediaan',
                    'b.dosis',
                    'b.kode_tipe',
                    'b.isi',
                    'a.kode_supplier'
                ]);
            // ->Where('a.kode_supplier', $kode_supplier)
            // ->Where('b.kode_tipe', $kategori_barang);

            if (!empty($kode_supplier)) {
                $data->where('a.kode_supplier', $kode_supplier);
            }

            // Filter Opsional: Kategori Barang
            if (!empty($kategori_barang)) {
                $data->where('b.kode_tipe', $kategori_barang);
            }

            $data->orderBy('b.nama_barang', 'asc');

            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }
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
    public function getDetailBatch(Request $request)
    {
        $kode_barang = $request->kode_barang;
        $kode_unit = $request->kode_unit;

        $detail = DB::table('ti_stok_pesediaan as s')
            ->join('mt_barang as b', 's.kode_barang', '=', 'b.kode_barang')
            ->leftJoin('mt_supplier as sup', 's.kode_supplier', '=', 'sup.kode_supplier') // Gunakan leftJoin jika supplier opsional
            ->select([
                's.nomor_batch',
                's.ED',
                's.stok_sekarang',
                's.tgl_entry',
                'b.nama_barang',
                's.nomor_faktur',
                's.jenis_dokumen',
                'sup.nama_supplier',
                'b.satuan',
                'sup.nama_supplier' // Mengambil nama supplier
            ])
            ->where('s.kode_barang', $kode_barang)
            // Jika unit kosong (pencarian global), maka ambil dari semua unit
            ->when($kode_unit, function ($query, $kode_unit) {
                return $query->where('s.kode_unit', $kode_unit);
            })
            ->where('s.stok_sekarang', '>', 0)
            ->orderBy('s.ED', 'asc')
            ->get();
        return response()->json($detail);
    }
    public function ambildatasediaan(Request $request)
    {
        // dd('ok');
        $kode_unit = $request->kode_unit;
        $kode_tipe = $request->kode_tipe;

        if ($request->ajax()) {
            // Gunakan Eager Loading untuk efisiensi
            $data = model_stok_persediaan::with(['barang', 'mt_supplier', 'unit', 'po_header'])
                ->select('ti_stok_pesediaan.*')

                // Filter Berdasarkan Unit (Jika parameter kode_unit ada)
                ->when($kode_unit, function ($query, $kode_unit) {
                    return $query->where('ti_stok_pesediaan.kode_unit', $kode_unit);
                })

                // Filter Berdasarkan Tipe Barang (Mencari ke tabel mt_barang melalui relasi 'barang')
                ->when($kode_tipe, function ($query, $kode_tipe) {
                    return $query->whereHas('barang', function ($q) use ($kode_tipe) {
                        $q->where('kode_tipe', $kode_tipe);
                    });
                })

                ->orderBy('id', 'DESC');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_barang', function ($row) {
                    return $row->barang->nama_barang ?? '-';
                })
                ->addColumn('no_faktur', function ($row) {
                    return $row->po_header->no_faktur ?? '-';
                })
                ->addColumn('nama_supplier', function ($row) {
                    return $row->mt_supplier->nama_supplier ?? '-';
                })
                ->addColumn('nama_unit', function ($row) {
                    return $row->unit->nama_unit ?? '-';
                })
                ->addColumn('sediaan', function ($row) {
                    // Menampilkan stok + satuan (misal: 10 BOX)
                    return $row->stok_sekarang . ' ' . ($row->barang->satuan ?? '');
                })
                ->editColumn('tgl_entry', function ($row) {
                    return $row->tgl_entry ? \Carbon\Carbon::parse($row->tgl_entry)->format('d-m-Y') : '-';
                })
                ->editColumn('ED', function ($row) {
                    // Memberi warna merah jika sudah kadaluarsa (Opsional tapi berguna di farmasi)
                    $date = $row->ED ? \Carbon\Carbon::parse($row->ED)->format('d-m-Y') : '-';
                    if ($row->ED && \Carbon\Carbon::parse($row->ED)->isPast()) {
                        return '<span class="text-danger fw-bold">' . $date . ' (Expired)</span>';
                    }
                    return $date;
                })
                ->editColumn('hpp', function ($row) {
                    return 'Rp ' . number_format($row->hpp, 0, ',', '.');
                })
                ->addColumn('aksi', function ($row) {
                    $btn = '<div class="btn-group" role="group">';
                    $btn .= '<a idbarang="' . $row->id . '" class="btn btn-sm btn-info pilihbarangmutasi" title="Mutasi Stok" data-bs-toggle="modal" data-bs-target="#modalmutasibarang"><i class="fas fa-exchange-alt"></i> Mutasi</a>';
                    $btn .= '<a hidden href="/stok/retur/' . $row->id . '" class="btn btn-sm btn-warning" title="Retur Barang"><i class="fas fa-undo"></i> Retur</a>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['aksi', 'ED', 'sediaan'])
                ->make(true);
        }
        // return view('Gudang.tabel_stok_sediaan');
    }
    public function ambilformmutasi(Request $request)
    {
        $id = $request->idbarang;
        $data = model_stok_persediaan::where('id', $id)->get()->first();
        $kode_barang = $data->kode_barang;
        $barang = MasterBarang::where('kode_barang', $kode_barang)->get()->first();
        $unit = Unit::where('kode_unit', '>', '4000')->get();
        return view('Gudang.form_mutasi', compact([
            'data',
            'unit',
            'barang'
        ]));
    }
    public function simpanmutasi(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama3) {
            $index3 = $nama3['name'];
            $value3 = $nama3['value'];
            $dataa[$index3] = $value3;
        }
        DB::beginTransaction();
        try {
            $datasediaan = model_stok_persediaan::where('id', $dataa['idsediaan'])->get()->first();
            $KODE_mutasi = $this->GET_KODE_MUTASI();
            $barang = MasterBarang::where('kode_barang', $dataa['kode_barang'])->get()->first();
            $unit_1 = Unit::where('kode_unit', $datasediaan->kode_unit)->get()->first();
            $unit_2 = Unit::where('kode_unit', $dataa['unittujuan'])->get()->first();
            $keterangan = 'Mutasi dari ' . $unit_1->nama_unit . ' Ke ' . $unit_2->nama_unit;
            $data_mutasi_header = [
                'kode_mutasi_header' => $KODE_mutasi,
                'tgl_mutasi' => $this->get_now(),
                'unit_asal' => $datasediaan->kode_unit,
                'unit_tujuan' => $dataa['unittujuan'],
                'pic' => auth()->user()->id,
                'kode_request' => '',
                'kode_tipe' => '2',
                'pemberi' => '',
                'penerima' => '',
                'keterangan' => $keterangan,
            ];

            mutasiheader::create($data_mutasi_header);
            $data_mutasi_detail = [
                'kode_mutasi_header' => $KODE_mutasi,
                'kode_barang' => $dataa['kode_barang'],
                'qty' =>  $dataa['jumlahmutasi'],
                'satuan' => $barang->satuan,
                'qty_kecil' =>  $dataa['jumlahmutasi'],
                'ed_obat' => $dataa['ed'],
                'nomor_batch' => $dataa['nomor_batch'],
                'catatan' => $keterangan,
                'sisa_stok_depo_asal' => $datasediaan->stok_sekarang - $dataa['jumlahmutasi'],
                'nomor_faktur' => $datasediaan->nomor_faktur,
                'kode_supplier' => $datasediaan->kode_supplier
            ];
            mutasidetail::create($data_mutasi_detail);
            $data_sediaan = [
                'kode_barang' => $dataa['kode_barang'],
                'hpp' =>  $datasediaan->hpp,
                'kode_unit' => $dataa['unittujuan'],
                'ED' => $dataa['ed'],
                'nomor_batch' => $dataa['nomor_batch'],
                'stok_awal' => $dataa['jumlahmutasi'],
                'stok_sekarang' => $dataa['jumlahmutasi'],
                'nomor_dokumen' => $KODE_mutasi,
                'jenis_dokumen' => $keterangan,
                'tgl_entry' => $this->get_date(),
                'nomor_faktur' => $datasediaan->nomor_faktur,
                'kode_supplier' => $datasediaan->kode_supplier
            ];
            $data_sediaan = model_stok_persediaan::create($data_sediaan);
            model_stok_persediaan::where('id', $dataa['idsediaan'])->update([
                'stok_sekarang' => $datasediaan->stok_sekarang - $dataa['jumlahmutasi']
            ]);
            //penguranan stok di unit asal
            $stok_terakhir_2 = DB::table('ti_kartu_stok as a')
                ->where('a.kode_barang', $dataa['kode_barang'])
                ->where('a.kode_unit', $datasediaan->kode_unit)
                ->orderBy('a.NO', 'desc')
                ->first();
            if ($stok_terakhir_2) {
                $stok_current = $stok_terakhir_2->stok_current - $dataa['jumlahmutasi'];
                $stok_in = 0;
                $stok_out = $dataa['jumlahmutasi'];
                $stok_last = $stok_terakhir_2->stok_current;
                $hrgbeli_hist = $stok_terakhir_2->harga_beli_history;
                $harga_beli = $stok_terakhir_2->harga_beli;
            } else {
                $stok_current =  0;
                $stok_in = 0;
                $stok_out = $dataa['jumlahmutasi'];
                $stok_last = 0;
                $hrgbeli_hist = 0;
                $harga_beli = $datasediaan->hpp;
            }
            $data_ti_kartu_stok_2 = [
                'no_dokumen' => $KODE_mutasi,
                'no_dokumen_detail' => '',
                'no_faktur' => '',
                'tgl_stok' => $this->get_now(),
                'kode_unit' => $datasediaan->kode_unit,
                'kode_barang' => $dataa['kode_barang'],
                'stok_last' => $stok_last,
                'stok_in' => $stok_in,
                'stok_out' => $stok_out,
                // 'stok_ed' => '',
                'stok_current' => $stok_current,
                // 'qty_pending' => '',
                // 'stok_global' => '',
                'harga_beli' => $harga_beli,
                'inputby' => auth()->user()->id,
                'ed_obat' => $dataa['ed'],
                'keterangan' => $keterangan,
                'harga_beli_history' => $hrgbeli_hist,
                'id_sediaan' => $data_sediaan->id,
                // 'no_dokumen' => '',
            ];
            model_ti_kartu_stok::create($data_ti_kartu_stok_2);
            $stok_terakhir = DB::table('ti_kartu_stok as a')
                ->where('a.kode_barang', $dataa['kode_barang'])
                ->where('a.kode_unit', $dataa['unittujuan'])
                ->orderBy('a.NO', 'desc')
                ->first();
            if ($stok_terakhir) {
                $stok_current = $dataa['jumlahmutasi'] + $stok_terakhir->stok_current;
                $stok_in = $dataa['jumlahmutasi'];
                $stok_out = 0;
                $stok_last = $stok_terakhir->stok_current;
                $hrgbeli_hist = $stok_terakhir->harga_beli_history;
                $harga_beli = $stok_terakhir->harga_beli;
            } else {
                $stok_current =  $dataa['jumlahmutasi'];
                $stok_in =  $dataa['jumlahmutasi'];
                $stok_out = 0;
                $stok_last = 0;
                $hrgbeli_hist = 0;
                $harga_beli = $datasediaan->hpp;
            }
            $data_ti_kartu_stok = [
                'no_dokumen' => $KODE_mutasi,
                'no_dokumen_detail' => '',
                'no_faktur' => '',
                'tgl_stok' => $this->get_now(),
                'kode_unit' => $dataa['unittujuan'],
                'kode_barang' => $dataa['kode_barang'],
                'stok_last' => $stok_last,
                'stok_in' => $stok_in,
                'stok_out' => $stok_out,
                // 'stok_ed' => '',
                'stok_current' => $stok_current,
                // 'qty_pending' => '',
                // 'stok_global' => '',
                'harga_beli' => $harga_beli,
                'inputby' => auth()->user()->id,
                'ed_obat' => $dataa['ed'],
                'keterangan' => $keterangan,
                'harga_beli_history' => $hrgbeli_hist,
                'id_sediaan' => $data_sediaan->id,
                // 'no_dokumen' => '',
            ];
            model_ti_kartu_stok::create($data_ti_kartu_stok);
            DB::commit();
            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'ok',
                'html'   => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => '500',
                'message' => $e->getMessage()
            ]);
        }
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
        $master_pabrikan = db::table('mt_manufaktur')->get();
        return view('Gudang.indexterimabarangpo', compact([
            'menu',
            'date_start',
            'date_end',
            'tipe',
            'today',
            'satuan',
            'master_pabrikan'
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
    public function ambildatamutasi(Request $request)
    {
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;

        $query = DB::table('ti_mutasi_header')
            ->select([
                'ti_mutasi_header.*',
                DB::raw('fc_nama_unit1(unit_asal) as unit_asal_barang'),
                DB::raw('fc_nama_unit1(unit_tujuan) as unit_tujuan_barang'),
                // Subquery detail barang dengan format lengkap
                DB::raw("(SELECT GROUP_CONCAT(
                    CONCAT(b.nama_barang, ' ( qty : ', d.qty, ' ', d.satuan, ') - Exp: ', DATE_FORMAT(d.ed_obat, '%d/%m/%y'))
                    SEPARATOR '<br>') 
                  FROM ti_mutasi_detail d 
                  JOIN mt_barang b ON d.kode_barang = b.kode_barang 
                  WHERE d.kode_mutasi_header = ti_mutasi_header.kode_mutasi_header
                 ) as detail_barang_lengkap")
            ]);

        if (!empty($tanggalawal) && !empty($tanggalakhir)) {
            $query->whereBetween('tgl_mutasi', [
                $tanggalawal . ' 00:00:00',
                $tanggalakhir . ' 23:59:59'
            ]);
        }
        $query->orderBy('ti_mutasi_header.id', 'DESC');
        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('tgl_mutasi', function ($row) {
                return $row->tgl_mutasi ? date('d-m-Y', strtotime($row->tgl_mutasi)) : '-';
            })
            // Mengisi keterangan dengan detail barang yang sudah diformat
            ->editColumn('keterangan', function ($row) {
                if ($row->detail_barang_lengkap) {
                    return '<small>' . $row->detail_barang_lengkap . '</small>';
                }
                return $row->keterangan;
            })
            ->addColumn('aksi', function ($row) {
                return '
            <div class="text-center">
                <button type="button" class="btn btn-sm btn-info" 
                        onclick="viewDetailMutasi(\'' . $row->kode_mutasi_header . '\')">
                    <i class="bi bi-eye"></i></button>
            </div>';
            })
            // Tambahkan 'keterangan' ke rawColumns agar tag <br> dan <small> berfungsi
            ->rawColumns(['aksi', 'keterangan'])
            ->make(true);
    }
    public function ambildatatgpoheader(Request $request)
    {
        // Pastikan request datang via AJAX
        if ($request->ajax()) {

            $tanggalawal = $request->tanggalawal;
            $tanggalakhir = $request->tanggalakhir;

            // 1. Definisikan Query Utama menggunakan Query Builder (Paling Cepat)
            $query = DB::table('tg_po_header')
                ->select([
                    '*',
                    // Panggil Function MySQL fc_NAMA_SUPPLIER langsung di query
                    DB::raw('fc_NAMA_SUPPLIER(kode_supplier) as nama_supplier')
                ]);

            // 2. Terapkan Filter Tanggal 'between'
            // Gunakan conditional where agar query fleksibel
            if (!empty($tanggalawal) && !empty($tanggalakhir)) {
                $query->whereBetween('tgl_input', [$tanggalawal, $tanggalakhir]);
            }

            // 3. Masukkan Query ke Engine DataTables Yajra
            return DataTables::of($query)
                ->addIndexColumn() // Menambahkan kolom 'DT_RowIndex' untuk penomoran
                // --- OPTIMASI VISUAL DI SERVER-SIDE (Opsional tapi disarankan) ---
                // Format Tanggal Input
                ->filterColumn('nama_supplier', function ($query, $keyword) {
                    // Logika: Kita mencari berdasarkan HASIL DARI FUNGSI MYSQL
                    // sql = fc_NAMA_SUPPLIER(kode_supplier) LIKE ?
                    $sql = "fc_NAMA_SUPPLIER(kode_supplier) LIKE ?";
                    $query->whereRaw($sql, ["%{$keyword}%"]);
                })
                ->editColumn('tgl_input', function ($row) {
                    // Contoh: 10 Apr 2026 (WIB)
                    return Carbon::parse($row->tgl_input)->format('d M Y') . ' <small class="text-muted">(' . Carbon::parse($row->tgl_input)->format('H:i') . ' WIB)</small>';
                })
                // Format Nama Supplier (Beri highlight)
                ->editColumn('nama_supplier', function ($row) {
                    return '<strong>' . $row->nama_supplier . '</strong><br><small class="text-muted">' . $row->kode_supplier . '</small>';
                })
                // Format Grand Total (Mata Uang)
                ->editColumn('gtotal_po', function ($row) {
                    // Sesuaikan nama field 'grand_total' dengan field asli di tabel Anda
                    return '<div class="text-end fw-bold text-success">Rp ' . number_format($row->gtotal_po, 0, ',', '.') . '</div>';
                })
                // Menambahkan Kolom Aksi (Tombol Detail, Edit, Hapus)
                ->addColumn('aksi', function ($row) {
                    $disabled = ($row->status_po == 'CCL') ? 'disabled' : '';
                    return '
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary" title="Detail PO"
                            onclick="viewDetailPO(\'' . $row->kode_po . '\')">
                            <i class="bi bi-search"></i>
                        </button>                       
                        <button type="button" class="btn btn-outline-danger" title="Batal PO"
                            ' . $disabled . ' 
                            onclick="deletepo(\'' . $row->kode_po . '\')">
                            <i class="bi bi-trash"></i>
                        </button>                       
                    </div>';
                })
                // WAJIB mendaftarkan kolom yang berisi HTML agar tidak dianggap string biasa oleh DataTables
                ->rawColumns(['tgl_input', 'nama_supplier', 'gtotal_po', 'aksi'])
                // Terakhir, buat respon JSON
                ->make(true);
        }

        // Jika request bukan AJAX, tolak atau arahkan kembali
        abort(404);
    }
    public function detailpo(Request $request)
    {
        // Pastikan request datang via AJAX
        if ($request->ajax()) {

            $no_po = $request->no_po;
            // 1. Validasi input
            if (empty($no_po)) {
                return response()->json(['status' => 'error', 'message' => 'Nomor PO tidak valid.'], 400);
            }
            $header = DB::table('tg_po_header as h')
                ->join('mt_supplier as s', 'h.kode_supplier', '=', 's.kode_supplier')
                ->select([
                    'h.*',
                    's.nama_supplier',
                    's.alamat_supplier',
                    's.tlp as telepon_supplier',
                    's.cp as email_supplier',
                    // Panggil Function MySQL fc_NAMA_USER jika perlu mengetahui siapa yang input
                    DB::raw('fc_NAMA_USER(h.input_by) as nama_user_input')
                ])
                ->where('h.kode_po', $no_po)
                ->first(); // Ambil satu baris saja

            // Cek jika header tidak ditemukan
            if (!$header) {
                return response()->json(['status' => 'error', 'message' => 'Data Header PO tidak ditemukan.'], 404);
            }

            // 3. Ambil Data Detail PO (Banyak baris item barang)
            // Kita JOIN dengan master barang untuk mendapatkan nama barang yang akurat
            $details = DB::table('tg_po_detail as d')
                ->join('mt_barang as b', 'd.kode_barang', '=', 'b.kode_barang')
                ->select([
                    'd.*',
                    'b.nama_barang',
                    'b.satuan_besar as nama_satuan_besar',
                    'b.satuan  as nama_satuan_sedang',
                    'b.sediaan as nama_satuan_kecil',
                ])
                ->where('d.kode_po', $no_po)
                ->orderBy('d.id', 'asc') // Urutkan berdasarkan urutan input
                ->get(); // Ambil banyak baris

            // 4. Hitung Ringkasan (Opsional, jika tidak disimpan di Header)
            $total_item = $details->count();
            $total_qty = $details->sum('qty');

            // 5. Render View Blade khusus detail (fragmen HTML)
            // Kita parsing data header, details, dan ringkasan ke view
            return view('Gudang.detail_po', compact('header', 'details', 'total_item', 'total_qty'))->render();
        }

        // Jika request bukan AJAX, tolak
        abort(404);
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
        $qty = $dataSet3['qty'] * $dataSet3['rasio_kecil'];
        $satuan = $dataSet3['satuan'];
        // $rasio_sedang = $dataSet3['rasio_sedang'];
        $rasio_kecil = $dataSet3['rasio_kecil'];
        // $satuan_sedang = $dataSet3['satuan_sedang'];
        $satuan_kecil = $dataSet3['satuan_kecil'];
        $hrgasatuan = $dataSet3['hrgasatuan'];
        $hrgasatuanasli = $dataSet3['hrgasatuanasli'];
        $ed = $dataSet3['ed'];
        $nobatch = $dataSet3['nobatch'];
        $id_pabrik = $dataSet3['pabrikan'];
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
        } elseif ($nobatch == '') {
            return response()->json([
                'status' => 'error',
                'message' => 'Nomor Batch wajib diisi ...',
                'html'   => ''
            ]);
        } else {
            $diskon = $dataSet3['diskon'];
            if ($diskon == '') {
                $diskon = 0;
            }
            $satuana = DB::table('mt_satuan')->get();
            $pabrik = DB::table('mt_manufaktur')->where('id', $id_pabrik)->get()->first();
            $nama_pabrik = $pabrik->nama_pabrik;
            $subtotal = $hrgasatuanasli * $qty;
            $hitungdiskon = $subtotal * $diskon / 100;
            $subtotal_final = $subtotal - $hitungdiskon;
            $subtotal_format = number_format($subtotal_final, 0, ',', '.');
            $dataarray = [
                'kode_barang' => $kode_barang,
                'nama_barang' => $nama_barang,
                'qty' => $qty,
                'satuan' => $satuan,
                'rasio_sedang' => '0',
                'rasio_kecil' => $rasio_kecil,
                'satuan_sedang' => '0',
                'satuan_kecil' => $satuan_kecil,
                'hrgasatuan' => $hrgasatuan,
                'hrgasatuanasli' => $hrgasatuanasli,
                'diskon' => $diskon,
                'nobatch' => $nobatch,
                'ed' => $ed,
                'nama_pabrik' => $nama_pabrik,
                'id_pabrik' => $id_pabrik,
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
    public function simpanpoheaderfinal(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        $data2 = json_decode($_POST['data2'], true);
        DB::beginTransaction();
        try {
            foreach ($data2 as $nama3) {
                $index22 = $nama3['name'];
                $value22 = $nama3['value'];
                $dataheader[$index22] = $value22;
            }
            foreach ($data as $nama2) {
                $index2 = $nama2['name'];
                $value2 = $nama2['value'];
                $dataSet2[$index2] = $value2;
                if ($index2 == 'list_subtotal_asli') {
                    $bb[] = $dataSet2;
                }
            }
            // dd($bb);
            $kode_po = $this->get_kode_po();
            $data_save_header = [
                'kode_po' => $kode_po,
                'no_faktur' => $dataheader['nomorfaktur'],
                'kode_supplier' => $dataheader['supplier_id'],
                'total_po' => $dataheader['totalpembelianasli'],
                'ppn' => $dataheader['nominalppn'],
                'sub_gtotal_po' => $dataheader['subgrandtotalasli'],
                'disk_rupiah' => $dataheader['potongantunai'],
                'disk_persen' => $dataheader['potonganpersen'],
                'gtotal_po' => $dataheader['grandtotalasli'],
                'total_utang' => $dataheader['totalutangasli'],
                'tgl_beli' => $dataheader['tanggalbeli'],
                'tgl_terima' => $dataheader['tanggalterima'],
                'tipe_po' => 'D',
                'tipe_trx' => $dataheader['tipepembelian'],
                'status_po' => 'CLS',
                'tgl_input' => $this->get_now(),
                'input_by' => auth()->user()->id,
                'pph' => 0,
                'kode_unit' => '4001',
                'pic2' => '',
                'materai' => $dataheader['nominalmateraiasli'],
                'keterangan' => 'INPUT DARI APLIKASI WEB',
                'tgl_jatuh_tempo' => $dataheader['jatuh_tempo'],
            ];
            model_tg_po_header::create($data_save_header);
            foreach ($bb as $b) {
                $isi_kecil = $b['list_rasio_kecil'];
                $isi_sedang = $b['list_rasio_sedang'];
                $qty = $b['list_qty'] / $b['list_rasio_kecil'];
                $isi = $qty  * $isi_kecil;
                $isi_qty = $qty * $isi_sedang;
                // dd($qty);
                $hrg_satuan_kecil = $b['list_hrgasatuanasli'];
                $data_save_detail = [
                    'kode_po' => $kode_po,
                    'kode_barang' => $b['list_kodebarang'],
                    'qty' => $qty,
                    'satuan' => $b['list_satuan'],
                    'isi' => $b['list_rasio_kecil'],
                    'qty_kecil' => $isi,
                    'diskon' => $b['list_diskon'],
                    'sub_total' => $b['list_subtotal_asli'],
                    'batch' => $b['list_nobatch'],
                    'ed' => $b['list_ed'],
                    'hrg_satuan' => $b['list_hrgasatuanasli'] * $b['list_rasio_kecil'],
                    'hrg_jual' => '0',
                    'cek_mutasi' => 0,
                    'hrg_satuan_kecil' => $hrg_satuan_kecil
                ];
                model_tg_po_detail::create($data_save_detail);
                $kode_unit = '4001';
                $data_sediaan = [
                    'kode_barang' => $b['list_kodebarang'],
                    'hpp' =>  $hrg_satuan_kecil,
                    'kode_unit' => 4001,
                    'ED' => $b['list_ed'],
                    'nomor_batch' => $b['list_nobatch'],
                    'stok_awal' => $isi,
                    'stok_sekarang' => $isi,
                    'nomor_dokumen' => $kode_po,
                    'jenis_dokumen' => 'STOK_PO',
                    'nomor_faktur' => $dataheader['nomorfaktur'],
                    'kode_supplier' => $dataheader['supplier_id'],
                    'tgl_entry' => $this->get_date(),
                ];
                $data_sediaan = model_stok_persediaan::create($data_sediaan);
                $stok_terakhir = DB::table('ti_kartu_stok as a')
                    ->where('a.kode_barang', $b['list_kodebarang'])
                    ->where('a.kode_unit', '4001')
                    ->orderBy('a.NO', 'desc')
                    ->first();
                if ($stok_terakhir) {
                    $stok_current = $isi + $stok_terakhir->stok_current;
                    $stok_in = $isi;
                    $stok_out = 0;
                    $stok_last = $stok_terakhir->stok_current;
                    $hrgbeli_hist = $stok_terakhir->harga_beli_history;
                } else {
                    $stok_current = $isi;
                    $stok_in = $isi;
                    $stok_out = 0;
                    $stok_last = 0;
                    $hrgbeli_hist = 0;
                }
                $data_ti_kartu_stok = [
                    'no_dokumen' => $kode_po,
                    'no_dokumen_detail' => '',
                    'no_faktur' => $dataheader['nomorfaktur'],
                    'tgl_stok' => $this->get_now(),
                    'kode_unit' => $kode_unit,
                    'kode_barang' => $b['list_kodebarang'],
                    'stok_last' => $stok_last,
                    'stok_in' => $stok_in,
                    'stok_out' => $stok_out,
                    // 'stok_ed' => '',
                    'stok_current' => $stok_current,
                    // 'qty_pending' => '',
                    // 'stok_global' => '',
                    'harga_beli' => $hrg_satuan_kecil,
                    'inputby' => auth()->user()->id,
                    'ed_obat' => $b['list_ed'],
                    'keterangan' => 'STOK MASUK DARI PO',
                    'harga_beli_history' => $hrgbeli_hist,
                    'id_sediaan' => $data_sediaan->id,
                    // 'no_dokumen' => '',
                ];
                model_ti_kartu_stok::create($data_ti_kartu_stok);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'ok',
                'html'   => ''
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'code' => 'error',
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
    public function totalhitungpurchaseorder(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        $data2 = json_decode($_POST['data2'], true);
        foreach ($data2 as $nama3) {
            $index22 = $nama3['name'];
            $value22 = $nama3['value'];
            $dataheader[$index22] = $value22;
        }
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
        if ($dataheader['potonganpersen'] == 0 || $dataheader['potonganpersen'] == '') {
            $potongan = $dataheader['potongantunai'];
        } else {
            $hitungpotongan = $total * $dataheader['potonganpersen'];
            $potongan = $hitungpotongan / 100;
        }
        $subgrantotal = $total - $potongan;
        $format_subgrantotal =  number_format($subgrantotal, 0, ',', '.');

        if ($dataheader['nominalppn'] == 0 || $dataheader['nominalppn'] == '') {
            $ppn = 0;
        } else {
            $ppn = 11;
        }
        $materai = $dataheader['nominalmateraiasli'];
        $hitungpajak = $total * $ppn;
        $total_ppn = $hitungpajak / 100;

        $grandtotal = $subgrantotal + $total_ppn + $materai;
        $format_grantotal =  number_format($grandtotal, 0, ',', '.');


        if ($dataheader['tipepembelian'] == 'K') {
            $hutang = $grandtotal;
            $format_hutang =  number_format($hutang, 0, ',', '.');
        } else {
            $hutang = 0;
            $format_hutang =  number_format($hutang, 0, ',', '.');
        }
        return response()->json([
            'status' => 'success',
            'message' => 'ok',
            'total'   => $total_asli,
            'total_format' => $total_format,
            'subgrantotal' => $subgrantotal,
            'format_subgrantotal' => $format_subgrantotal,
            'grandtotal' => $grandtotal,
            'format_grantotal' => $format_grantotal,
            'hutang' => $hutang,
            'format_hutang' => $format_hutang,
        ]);
    }
    public function get_kode_retur_detail_po()
    {
        $q = DB::select('SELECT id,kode_retur_po_detail,RIGHT(kode_retur_po_detail,4) AS kd_max  FROM tg_retur_po_detail
        WHERE DATE(tgl_retur_po_detail) = CURDATE()
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
        return 'RETDET' . date('ymd') . $kd;
    }
    public function kode_retur_po()
    {
        $q = DB::select('SELECT id,kode_retur_po,RIGHT(kode_retur_po,4) AS kd_max  FROM tg_retur_po_header
        WHERE DATE(tgl_retur) = CURDATE()
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
        return 'RETPO' . date('ymd') . $kd;
    }
    public function GET_KODE_MUTASI()
    {
        $q = DB::select('SELECT id,kode_mutasi_header,RIGHT(kode_mutasi_header,4) AS kd_max  FROM ti_mutasi_header
        WHERE DATE(tgl_mutasi) = CURDATE()
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
        return 'MT' . date('ymd') . $kd;
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
    public function pencariansediaanbarang(Request $request)
    {
        $kode_unit = $request->unit_asal;
        if (strlen($kode_unit) == 0) {
            return response()->json([
                'code' => 'error',
                'status' => 'error',
                'message' => 'Silahkan Pilih Unit ...'
            ]);
        } else {
            $sediaan = DB::table('ti_stok_pesediaan as a')
                ->join('mt_barang as b', 'a.kode_barang', '=', 'b.kode_barang')
                ->join('mt_tipe_barang as c', 'b.kode_tipe', '=', 'c.kode_tipe')
                ->select([
                    'a.*',
                    'a.id as id_sediaan',
                    'b.nama_barang',
                    'b.satuan_besar',
                    'b.satuan',
                    'a.nomor_faktur',
                    'b.sediaan',
                    'b.dosis',
                    'c.nama_tipe'
                ])
                ->where('a.kode_unit', $kode_unit)
                ->where('a.stok_sekarang', '>', 0)
                ->get();
            return view('Gudang.tabel_stok_sediaan_unit', compact([
                'sediaan'
            ]));
        }
    }
    public function simpanmutasibanyak(Request $request)
    {
        DB::beginTransaction();
        try {
            $KODE_mutasi = $this->GET_KODE_MUTASI();
            $unit_1 = Unit::where('kode_unit', $request->unit_asal)->get()->first();
            $unit_2 = Unit::where('kode_unit', $request->unit_tujuan)->get()->first();
            $keterangan = 'Mutasi dari ' . $unit_1->nama_unit . ' Ke ' . $unit_2->nama_unit;
            $data_mutasi_header = [
                'kode_mutasi_header' => $KODE_mutasi,
                'tgl_mutasi' => $this->get_now(),
                'unit_asal' => $request->unit_asal,
                'unit_tujuan' => $request->unit_tujuan,
                'pic' => auth()->user()->id,
                'kode_request' => '',
                'kode_tipe' => '2',
                'pemberi' => '',
                'penerima' => '',
                'keterangan' => $keterangan,
            ];
            mutasiheader::create($data_mutasi_header);
            foreach ($request->kode_barang as $key => $kode) {
                $id_sediaan = $request->id_sediaan[$key];
                $datasediaan = model_stok_persediaan::where('id', $id_sediaan)->get()->first();
                $barang = MasterBarang::where('kode_barang', $kode)->get()->first();
                $data_mutasi_detail = [
                    'kode_mutasi_header' => $KODE_mutasi,
                    'kode_barang' => $kode,
                    'qty' =>  $request->qty_mutasi[$key],
                    'satuan' => $request->satuan[$key],
                    'qty_kecil' =>  $request->qty_mutasi[$key],
                    'ed_obat' => $request->ed[$key],
                    'nomor_batch' => $request->no_batch[$key],
                    'catatan' => $keterangan,
                    'sisa_stok_depo_asal' => $datasediaan->stok_sekarang - $request->qty_mutasi[$key],
                    'nomor_faktur' => $datasediaan->nomor_faktur,
                    'kode_supplier' => $datasediaan->kode_supplier
                ];
                mutasidetail::create($data_mutasi_detail);
                $data_sediaan = [
                    'kode_barang' => $kode,
                    'hpp' =>  $datasediaan->hpp,
                    'kode_unit' => $request->unit_tujuan,
                    'ED' => $request->ed[$key],
                    'nomor_batch' => $request->no_batch[$key],
                    'stok_awal' => $request->qty_mutasi[$key],
                    'stok_sekarang' => $request->qty_mutasi[$key],
                    'nomor_dokumen' => $KODE_mutasi,
                    'jenis_dokumen' => $keterangan,
                    'tgl_entry' => $this->get_date(),
                    'nomor_faktur' => $datasediaan->nomor_faktur,
                    'kode_supplier' => $datasediaan->kode_supplier
                ];
                $data_sediaan = model_stok_persediaan::create($data_sediaan);
                model_stok_persediaan::where('id', $id_sediaan)->update([
                    'stok_sekarang' => $datasediaan->stok_sekarang - $request->qty_mutasi[$key]
                ]);


                $stok_terakhir_2 = DB::table('ti_kartu_stok as a')
                    ->where('a.kode_barang', $kode)
                    ->where('a.kode_unit', $request->unit_asal)
                    ->orderBy('a.NO', 'desc')
                    ->first();
                if ($stok_terakhir_2) {
                    $stok_current = $stok_terakhir_2->stok_current - $request->qty_mutasi[$key];
                    $stok_in = 0;
                    $stok_out = $request->qty_mutasi[$key];
                    $stok_last = $stok_terakhir_2->stok_current;
                    $hrgbeli_hist = $stok_terakhir_2->harga_beli_history;
                    $harga_beli = $stok_terakhir_2->harga_beli;
                } else {
                    $stok_current =  0;
                    $stok_in = 0;
                    $stok_out = $request->qty_mutasi[$key];
                    $stok_last = 0;
                    $hrgbeli_hist = 0;
                    $harga_beli = $datasediaan->hpp;
                }
                $data_ti_kartu_stok_2 = [
                    'no_dokumen' => $KODE_mutasi,
                    'no_dokumen_detail' => '',
                    'no_faktur' => '',
                    'tgl_stok' => $this->get_now(),
                    'kode_unit' => $request->unit_asal,
                    'kode_barang' => $kode,
                    'stok_last' => $stok_last,
                    'stok_in' => $stok_in,
                    'stok_out' => $stok_out,
                    // 'stok_ed' => '',
                    'stok_current' => $stok_current,
                    // 'qty_pending' => '',
                    // 'stok_global' => '',
                    'harga_beli' => $harga_beli,
                    'inputby' => auth()->user()->id,
                    'ed_obat' => $request->ed[$key],
                    'keterangan' => $keterangan,
                    'harga_beli_history' => $hrgbeli_hist,
                    'id_sediaan' => $datasediaan->id,
                    // 'no_dokumen' => '',
                ];

                model_ti_kartu_stok::create($data_ti_kartu_stok_2);
                $stok_terakhir = DB::table('ti_kartu_stok as a')
                    ->where('a.kode_barang', $kode)
                    ->where('a.kode_unit', $request->unit_tujuan)
                    ->orderBy('a.NO', 'desc')
                    ->first();
                if ($stok_terakhir) {
                    $stok_current = $request->qty_mutasi[$key] + $stok_terakhir->stok_current;
                    $stok_in = $request->qty_mutasi[$key];
                    $stok_out = 0;
                    $stok_last = $stok_terakhir->stok_current;
                    $hrgbeli_hist = $stok_terakhir->harga_beli_history;
                    $harga_beli = $stok_terakhir->harga_beli;
                } else {
                    $stok_current =  $request->qty_mutasi[$key];
                    $stok_in =  $request->qty_mutasi[$key];
                    $stok_out = 0;
                    $stok_last = 0;
                    $hrgbeli_hist = 0;
                    $harga_beli = $datasediaan->hpp;
                }
                $data_ti_kartu_stok = [
                    'no_dokumen' => $KODE_mutasi,
                    'no_dokumen_detail' => '',
                    'no_faktur' => '',
                    'tgl_stok' => $this->get_now(),
                    'kode_unit' => $request->unit_tujuan,
                    'kode_barang' => $kode,
                    'stok_last' => $stok_last,
                    'stok_in' => $stok_in,
                    'stok_out' => $stok_out,
                    // 'stok_ed' => '',
                    'stok_current' => $stok_current,
                    // 'qty_pending' => '',
                    // 'stok_global' => '',
                    'harga_beli' => $harga_beli,
                    'inputby' => auth()->user()->id,
                    'ed_obat' => $request->ed[$key],
                    'keterangan' => $keterangan,
                    'harga_beli_history' => $hrgbeli_hist,
                    'id_sediaan' => $data_sediaan->id,
                    // 'no_dokumen' => '',
                ];
                model_ti_kartu_stok::create($data_ti_kartu_stok);
            }
            DB::commit();
            return response()->json(['code' => 'success', 'message' => 'Mutasi berhasil disimpan!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['code' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
