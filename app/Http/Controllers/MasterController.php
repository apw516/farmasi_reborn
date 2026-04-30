<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MasterBarangBPJS;
use App\Models\model_master_barang_x_master_bpjs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class MasterController extends Controller
{
    public function indexmappingbarang()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmappingbarang';
        return view('Master.indexmappingbarang', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function indexmasterbarang()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmasterbarang';
        $barang = MasterBarang::paginate(15);
        $tipebarang = DB::table('mt_tipe_barang')->get();        // Kirim view parsial
        $satuan = DB::table('mt_satuan')->get();        // Kirim view parsial
        $sediaan = DB::table('mt_sediaan')->get();        // Kirim view parsial
        $pabrikan = DB::table('mt_manufaktur')->get();        // Kirim view parsial
        $kelompokbarang = DB::table('mt_kelompok_barang')->get();        // Kirim view parsial
        $kelompoktarif = DB::table('mt_tarif_kelompok')->get();        // Kirim view parsial
        return view('Master.indexmasterbarang', compact([
            'menu',
            'date_start',
            'date_end',
            'barang',
            'tipebarang',
            'satuan',
            'sediaan',
            'pabrikan',
            'kelompokbarang',
            'kelompoktarif'
        ]));
    }
    public function indexmasterdpho()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmasterdpho';
        return view('Master.indexmasterdpho', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function indexmasterobatbpjs()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmasterobatbpjs';
        $barang = MasterBarangBPJS::paginate(15);
        return view('Master.indexmasterbarangbpjs', compact([
            'menu',
            'date_start',
            'date_end',
            'barang'
        ]));
    }
    public function indexmastersupplier()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexmastersupplier';
        return view('Master.indexmastersupplier', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function ambilbarangdpho(Request $request)
    {
        // if ($request->ajax()) {
        //     $data = DB::table('apt_online_ref_dpho as bpjs')
        //         ->left('master_barang_x_master_obat_bpjs as map', 'bpjs.kodeobat', '=', 'map.kode_obat_bpjs')
        //         ->left('mt_barang as simrs', 'map.kode_barang', '=', 'simrs.kode_barang')
        //         ->left('mt_set_barang_supplier as sett', 'map.kode_barang', '=', 'sett.kode_barang')
        //         ->left('mt_supplier', 'sett.kode_supplier', '=', 'mt_supplier.kode_supplier')
        //         ->select([
        //             'simrs.kode_barang',
        //             'bpjs.kodeobat',
        //             'bpjs.namaobat',
        //             'bpjs.restriksi',
        //             'bpjs.generik',
        //             'mt_supplier.nama_supplier',
        //             'mt_supplier.alamat_supplier',
        //             'map.kode_barang as kode_simrs', // Ambil kode mapping jika ada
        //             'simrs.nama_barang as nama_simrs' // Ambil nama dari tabel SIMRS
        //         ])
        //         ->orderBy('bpjs.id', 'desc');
        //     return DataTables::of($data)
        //         ->addIndexColumn()
        //         // Kita tambahkan kolom status secara logic di Server Side
        //         ->addColumn('status_mapping', function ($row) {
        //             if (empty($row->kode_simrs)) {
        //                 return '<span class="badge badge-danger">Belum Dimapping</span>';
        //             }
        //             return '<span class="badge badge-success">Termapping: ' . $row->nama_simrs . '</span>';
        //         })
        //         ->addColumn('aksi', function ($row) {
        //             return '<button class="btn btn-sm btn-primary btn-pilih-bpjs" data-kode="' . $row->kodeobat . '">Pilih</button>';
        //         })
        //         ->rawColumns(['status_mapping', 'aksi']) // Agar HTML dirender oleh browser
        //         ->make(true);
        // }

        if ($request->ajax()) {
            $data = DB::table('apt_online_ref_dpho as bpjs')
                ->Join('master_barang_x_master_obat_bpjs as map', 'bpjs.kodeobat', '=', 'map.kode_obat_bpjs')
                ->Join('mt_barang as simrs', 'map.kode_barang', '=', 'simrs.kode_barang')
                ->select([
                    'bpjs.id',
                    'bpjs.kodeobat',
                    'bpjs.namaobat',
                    'bpjs.restriksi',
                    'bpjs.generik',
                    'map.kode_barang as kode_simrs', // Mengecek apakah sudah dimapping
                    'simrs.nama_barang as nama_simrs', // Mengambil nama barang dari SIMRS
                    'simrs.kode_barang' // Kode barang asli dari tabel SIMRS
                ])
                ->orderBy('bpjs.id', 'desc');

            return DataTables::of($data)
                ->addIndexColumn()
                // Logika status mapping
                ->addColumn('status_mapping', function ($row) {
                    if (empty($row->kode_simrs)) {
                        return '<span class="badge bg-danger">Belum Dimapping</span>';
                    }
                    return '<span class="badge bg-success">Termapping: ' . $row->nama_simrs . '</span>';
                })
                // Tombol aksi
                ->addColumn('aksi', function ($row) {
                    return '<button class="btn btn-sm btn-primary btn-pilih-bpjs" 
                        data-kode="' . $row->kodeobat . '" 
                        data-nama="' . $row->namaobat . '">
                        <i class="fas fa-check"></i> Pilih
                    </button>';
                })
                ->rawColumns(['status_mapping', 'aksi'])
                ->make(true);
        }
    }
    public function ambilbarangbpjs(Request $request)
    {
        if ($request->ajax()) {
            // $data = MasterBarang::query();
            $data = MasterBarangBPJS::orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn() // Untuk nomor urut otomatis
                ->make(true);
        }
    }
    public function ambilbarang(Request $request)
    {
        if ($request->ajax()) {
            // $data = MasterBarang::query();
            // $data = MasterBarang::orderBy('id', 'desc');\
            $data = DB::table('mt_barang')
                ->leftJoin(
                    'apt_online_ref_dpho',
                    'mt_barang.kode_obat_bpjs',
                    '=',
                    'apt_online_ref_dpho.kodeobat'
                )
                ->select([
                    'mt_barang.kode_barang', // Gunakan prefix tabel
                    'mt_barang.nama_barang',
                    'mt_barang.satuan_besar',
                    'mt_barang.sediaan',
                    'mt_barang.dosis',
                    'mt_barang.aturan_pakai',
                    'apt_online_ref_dpho.kodeobat', // Contoh kolom dari tabel BPJS
                    'apt_online_ref_dpho.namaobat', // Contoh kolom dari tabel BPJS
                    'apt_online_ref_dpho.generik', // Contoh kolom dari tabel BPJS
                ])
                ->where('mt_barang.act', 1)
                ->orderBy('mt_barang.kode_barang', 'desc');
            return DataTables::of($data)
                ->addColumn('action', function ($row) {
                    // Tambahkan button edit dengan data-id untuk memicu modal/form
                    $btn = '<button class="editbarang btn btn-warning btn-sm" data-id="' . $row->kode_barang . '" data-bs-toggle="modal" data-bs-target="#modaleditbarang">
                           <i class="bi bi-pencil-square"></i>
                        </button>';

                    // Opsional: Tombol Hapus
                    $btn .= ' <button class="deletebarang btn btn-danger btn-sm" data-id="' . $row->kode_barang . '">
                            <i class="bi bi-trash3"></i>
                        </button>';

                    return $btn;
                })
                ->filterColumn('kodeobat', function ($query, $keyword) {
                    // Gunakan tabel apt_online_ref_dpho, bukan mt_barang
                    $query->where('apt_online_ref_dpho.kodeobat', 'LIKE', "%$keyword%");
                })
                ->make(true);
        }
    }
    public function hapusobat(Request $request)
    {
        try {
            $barang = MasterBarang::where('kode_barang', $request->id)->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data barang berhasil dihapus ...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function ambilbarangedit($id)
    {
        $barang = MasterBarang::where('kode_barang', $id)->first();
        $tipebarang = DB::table('mt_tipe_barang')->get();        // Kirim view parsial
        $satuan = DB::table('mt_satuan')->get();        // Kirim view parsial
        $sediaan = DB::table('mt_sediaan')->get();        // Kirim view parsial
        $pabrikan = DB::table('mt_manufaktur')->get();        // Kirim view parsial
        return view('Master.form_edit', compact('barang', 'tipebarang', 'satuan', 'sediaan', 'pabrikan'));
    }
    public function simpanbarang(Request $request)
    {
        try {
            $databarang = [
                'kode_barang' => $this->Get_kode_barang(),
                'kode_tipe' => $request->kode_tipe_barang,
                'klp_barang' => 'OBAT',
                'nama_generik' => $request->nama_generik,
                'nama_barang' => $request->nama_barang,
                'satuan_besar' => $request->satuan_besar,
                'satuan' => $request->satuan,
                'isi' => $request->isi,
                'act' => 1,
                'sediaan' => $request->sediaan,
                'id_pabrik' => $request->id_pabrik,
                'dosis' => $request->dosis,
                'kelompok_tarif_id' => $request->kelompok_tarif,
                'harga_beli' => $request->harga_beli,
                'harga_jual' => $request->harga_jual_baru,
                'hna' => $request->hna,
                'aturan_pakai' => $request->aturan_pakai
            ];
            MasterBarang::create($databarang);
            return response()->json([
                'status' => 'success',
                'message' => 'Data barang berhasil disimpan ...'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function updatebarang(Request $request)
    {
        // Validasi data
        $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
        ]);

        try {
            // Update menggunakan Query Builder (sesuai gaya query Anda sebelumnya)
            DB::table('mt_barang')
                ->where('kode_barang', $request->kode_barang)
                ->update([
                    'kode_tipe'    => $request->kode_tipe_barang,
                    'nama_barang'  => $request->nama_barang,
                    'harga_jual'  => $request->harga_jual,
                    'satuan_besar' => $request->satuan_besar,
                    'satuan'       => $request->satuan,
                    'isi'          => $request->isi,
                    'sediaan'      => $request->sediaan,
                    'id_pabrik'    => $request->id_pabrik,
                    'dosis'        => $request->dosis,
                ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Data barang berhasil diupdate'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function ambilbarangbelummapping(Request $request)
    {
        if ($request->ajax()) {
            // $data = MasterBarang::query();
            // $data = MasterBarang::orderBy('id', 'desc');\
            $data = DB::table('mt_barang')
                ->select([
                    'mt_barang.kode_barang', // Gunakan prefix tabel
                    'mt_barang.nama_barang',
                    'mt_barang.satuan_besar',
                    'mt_barang.sediaan',
                    'mt_barang.dosis',
                    'mt_barang.aturan_pakai',
                ])
                ->where('mt_barang.act', 1)
                ->where('mt_barang.kode_obat_bpjs', '=', 0)
                ->orderBy('mt_barang.kode_barang', 'desc');
            return DataTables::of($data)
                ->addIndexColumn() // Untuk nomor urut otomatis
                ->make(true);
        }
    }
    public function ambilsupplier(Request $request)
    {
        if ($request->ajax()) {
            // $data = MasterBarang::query();
            // $data = MasterBarang::orderBy('id', 'desc');
            $data = DB::table('mt_supplier')
                ->select([
                    'kode_supplier', // Gunakan prefix tabel
                    'kategori_supplier',
                    'nama_supplier',
                    'alamat_supplier',
                    'cp',
                    'tlp',
                    'termin' // Contoh kolom dari tabel BPJS
                ])
                ->orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn() // Untuk nomor urut otomatis
                ->make(true);
        }
    }
    public function simpanmappingobat(Request $request)
    {
        try {
            $data_simrs = json_decode($_POST['data_simrs'], true);
            $data_bpjs = json_decode($_POST['data_bpjs'], true);
            foreach ($data_bpjs as $nama3) {
                $index3 = $nama3['name'];
                $value3 = $nama3['value'];
                $arraybpjs[$index3] = $value3;
            }
            // dd($arraybpjs);
            foreach ($data_simrs as $nama2) {
                $index2 = $nama2['name'];
                $value2 = $nama2['value'];
                $dataSet2[$index2] = $value2;
                if ($index2 == 'dosis') {
                    $arraysimrs[] = $dataSet2;
                }
            }
            foreach ($arraysimrs as $d) {
                $datamapping = [
                    'kode_barang' => $d['kodebarang'],
                    'kode_obat_bpjs' => $arraybpjs['kodeobatbpjs'],
                    'tgl_entry' => $this->get_now(),
                    'pic' => auth()->user()->id . ' | ' . auth()->user()->nama
                ];
                // dd($datamapping);
                $cek = db::select('select id from master_barang_x_master_obat_bpjs where kode_barang = ?', [$d['kodebarang']]);
                if (count($cek) > 0) {
                    model_master_barang_x_master_bpjs::where('id', $cek[0]->kode_barang)->delete();
                }
                model_master_barang_x_master_bpjs::create($datamapping);
            }
            $data = [
                'kode' => 200,
                'message' => 'Sukses, data berhasil disimpan ...'
            ];
            echo json_encode($data);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function simpanmappingbaru(Request $request)
    {
        try {
            $dataobat = json_decode($_POST['dataobat'], true);
            // dd($arraybpjs);
            foreach ($dataobat as $nama2) {
                $index2 = $nama2['name'];
                $value2 = $nama2['value'];
                $dataSet2[$index2] = $value2;
                if ($index2 == 'ref_dpho') {
                    $arraydata[] = $dataSet2;
                }
            }
            foreach ($arraydata as $s) {
                MasterBarang::where('kode_barang', $s['kode_barang'])->update(['kode_obat_bpjs' => $s['ref_dpho']]);
            }
            $data = [
                'kode' => 200,
                'message' => 'Sukses, data berhasil disimpan ...'
            ];
            echo json_encode($data);
            die;
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => 'Ops error ! ...( ' . $err . ' )'
            ];
            echo json_encode($data);
            die;
        }
    }
    public function get_now()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $time = $dt->toTimeString();
        $now = $date . ' ' . $time;
        return $now;
    }
    public function get_date()
    {
        $dt = Carbon::now()->timezone('Asia/Jakarta');
        $date = $dt->toDateString();
        $now = $date;
        return $now;
    }
    public function get_kode_barang()
    {
        $q = DB::select('SELECT id,kode_barang,RIGHT(kode_barang,6) AS kd_max  FROM mt_barang
        ORDER BY id DESC
        LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%06s", $tmp);
            }
        } else {
            $kd = "000001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'B' . $kd;
    }
    public function caridpho(Request $request)
    {
        $cari = $request->q;

        $data = DB::table('apt_online_ref_dpho')
            ->where('namaobat', 'LIKE', "%$cari%")
            ->limit(20)
            ->get();

        $response = [];
        foreach ($data as $d) {
            $response[] = [
                'id'    => $d->kodeobat, // Sesuaikan primary key
                'text'  => $d->namaobat . ' - ' . $d->generik // Tampilan di dropdown
            ];
        }

        return response()->json($response);
    }
}
