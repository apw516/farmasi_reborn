<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\MODEL_APOTEK_ONLINE;
use App\Models\model_apotek_ref_dpho;
use Carbon\Carbon;

class ApotekOnlineController extends dashboarController
{
    function downloadrefdpho(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        try {
            $DATA = $v->referensi_dpho();
            if ($DATA->metaData->code == 200 && $DATA->metaData->message == 'OK') {
                $cek = db::select('select id from apt_online_ref_dpho');
                if (count($cek) > 0) {
                    model_apotek_ref_dpho::truncate();
                    $cek = db::select('select id from apt_online_ref_dpho');
                }
                foreach ($DATA->response->list as $d) {
                    $data2 = [
                        'kodeobat' => $d->kodeobat,
                        'namaobat' => $d->namaobat,
                        'prb' => $d->prb,
                        'kronis' => $d->kronis,
                        'kemo' => $d->kemo,
                        'harga' => $d->harga,
                        'restriksi' => $d->restriksi,
                        'generik' => $d->generik,
                        'aktif' => $d->aktif,
                        'sedia' => $d->sedia,
                        'stok' => $d->stok,
                        'tgl_download' => $this->get_now(),
                    ];
                    model_apotek_ref_dpho::create($data2);
                }
                $data = [
                    'kode' => 200,
                    'message' => 'Data berhasil diperbaharui ...'
                ];
                echo json_encode($data);
                die;
            }
        } catch (\Exception $e) {
            $err = $e->getMessage();
            $data = [
                'kode' => 500,
                'message' => $err
            ];
            echo json_encode($data);
            die;
        }
    }
    function carisep_apotekonline(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $nosep = $request->nosep;
        try {
            $DATA = $v->daftar_pelayanan_obat($nosep);
            // dd($DATA);
            // $DATA = $v->carikunjungansep($nosep);
            // dd($DATA);
            if ($DATA->metaData->code == 200 && $DATA->metaData->message == 'OK') {
                return view('apotekonline.detailsepapotek', compact([
                    'DATA'
                ]));
            } else {
                return view('apotekonline.pesaneror');
            }
        } catch (\Exception $e) {
            return view('apotekonline.pesaneror');
        }
    }
    function indexcarisepaptonline()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexcarisep_apotek';
        return view('apotekonline.indexcarisep', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    function indexdaftarresep()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexdaftarresep';
        return view('apotekonline.indexdaftarrresep', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function caridaftarresep_apotekonline(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $awal = $request->tglawal;
        $akhir = $request->tglakhir;
        $jenistanggal = $request->jenistanggal;
        $data = [
            'kdppk' => '0125A016',
            'KdJnsObat' => '0',
            'JnsTgl' => $jenistanggal,
            'TglMulai' => $awal,
            'TglAkhir' => $akhir,
        ];
        try {
            $DATA = $v->daftar_resep($data);
            // dd($DATA);
            if ($DATA->metaData->code == 200 && $DATA->metaData->message == 'OK') {
                return view('apotekonline.tabel_daftar_resep', compact([
                    'DATA'
                ]));
            } else {
                return view('apotekonline.pesaneror');
            }
        } catch (\Exception $e) {
            return view('apotekonline.pesaneror');
        }
    }
    public function hapusresepapotekonline(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $nosep = $request->nosep;
        $noapotik = $request->noapotik;
        $idnoresep = $request->noresep;
        $data_resep = [
            "nosjp" => $noapotik,
            "refasalsjp" => $nosep,
            "noresep" => $idnoresep
        ];
        $response_data = $v->hapus_resep($data_resep);
        if ($response_data->metaData->code == 200) {
            return response()->json([
                'kode' => 200,
                'message' => 'Data resep berhasil dihapus ..!'
            ], 200);
        } else {
            return response()->json([
                'kode' => 500,
                'message' => 'Gagal hapus resep'
            ], 200);
        }
    }
    function indexriwayatpelayananonline()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $this->get_date();
        $date_end = $this->get_date();
        $menu = 'indexriwayatpelayananonline';
        return view('apotekonline.index_riwayat_pelayanan', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function ambilriwayat_pelayananpeserta(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $awal = $request->tglawal;
        $akhir = $request->tglakhir;
        $nomorkartu = $request->nomorkartu;
        try {
            $DATA = $v->riwayat_obat($awal, $akhir, $nomorkartu);
            if ($DATA->metaData->code == 200 && $DATA->metaData->message == 'OK') {
                return view('apotekonline.tabel_riwayat_pelayanan_peserta', compact([
                    'DATA'
                ]));
            } else {
                return view('apotekonline.pesaneror');
            }
        } catch (\Exception $e) {
            return view('apotekonline.pesaneror');
        }
    }
    public function ambilriwayat_pelayananpesertabyrs(Request $request)
    {
        $awal = $request->tglawal;
        $akhir = $request->tglakhir;
        $rm = $request->rm;
        $data1 = db::select("SELECT kode_kunjungan,tgl_masuk,fc_nama_unit1(kode_unit) AS unit_tujuan,fc_NAMA_PARAMEDIS1(kode_paramedis) AS nama_dokter
        FROM ts_kunjungan WHERE  no_rm = ? ORDER BY kode_kunjungan DESC",[$rm]);

        // dd($data1);
        $data = db::select("SELECT a.`kode_kunjungan`
        ,a.`tgl_masuk`
        ,fc_nama_unit1(a.`kode_unit`) AS unit_kunjungan
        ,fc_nama_unit1(b.kode_unit) AS unit_penerima
        ,fc_nama_barang(c.`kode_barang`) AS nama_barang
        ,c.`jumlah_layanan`
        ,c.`aturan_pakai`
        ,c.`tipe_anestesi`
        ,a.`status_kunjungan`
        ,b.`status_layanan`
        ,c.`status_layanan_detail`
        FROM ts_kunjungan a 
        INNER JOIN ts_layanan_header b ON a.`kode_kunjungan` = b.kode_kunjungan
        INNER JOIN ts_layanan_detail c ON b.`id` = c.`row_id_header`
        WHERE a.no_rm = ? 
        AND c.`kode_barang` IS NOT NULL
        ORDER BY kode_kunjungan DESC",[$rm]);
        return view('Depofarmasi.riwayatobatrs',compact([
            'data1','data'
        ]));
    }
    function indexdataklaim()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexdataklaim';
        return view('apotekonline.indexdataklaim', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function ambil_data_monitoring_klaim(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $jenisobat = $request->jenisobat;
        $status = $request->status;
        try {
            $DATA = $v->caridataklaim($bulan, $tahun, $jenisobat, $status);
            // dd($DATA);
            if ($DATA->metaData->code == 200 && $DATA->metaData->message == 'OK') {
                return view('apotekonline.tabel_monitoring_klaim', compact([
                    'DATA'
                ]));
            } else {
                return view('apotekonline.pesaneror');
            }
        } catch (\Exception $e) {
            return view('apotekonline.pesaneror');
        }
    }
    public function ambil_reakp_peserta_prb(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        try {
            $DATA = $v->rekap_peserta_prb($tahun, $bulan);
            dd($DATA);
            // dd($DATA);
            if ($DATA->metaData->code == 200 && $DATA->metaData->message == 'OK') {
                return view('apotekonline.tabel_monitoring_klaim', compact([
                    'DATA'
                ]));
            } else {
                return view('apotekonline.pesaneror');
            }
        } catch (\Exception $e) {
            return view('apotekonline.pesaneror');
        }
    }
    function indexrekapprb()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexrekapprb';
        return view('apotekonline.indexrekappesertaprb', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
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
