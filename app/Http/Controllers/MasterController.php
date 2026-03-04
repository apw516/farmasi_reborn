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
        return view('Master.indexmasterbarang', compact([
            'menu',
            'date_start',
            'date_end',
            'barang'
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
            // $data = MasterBarang::orderBy('id', 'desc');
            $data = DB::table('mt_barang')
                ->leftJoin(
                    'master_barang_x_master_obat_bpjs',
                    'mt_barang.kode_barang',
                    '=',
                    'master_barang_x_master_obat_bpjs.kode_barang'
                )
                ->select([
                    'mt_barang.kode_barang', // Gunakan prefix tabel
                    'mt_barang.nama_barang',
                    'mt_barang.satuan_besar',
                    'mt_barang.sediaan',
                    'mt_barang.dosis',
                    'mt_barang.aturan_pakai',
                    'master_barang_x_master_obat_bpjs.kode_obat_bpjs' // Contoh kolom dari tabel BPJS
                ])
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
}
