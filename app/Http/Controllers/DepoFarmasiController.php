<?php

namespace App\Http\Controllers;

use App\Models\MODEL_APOTEK_ONLINE;
use App\Models\model_resep_obat;
use App\Models\model_tabel_obat_racikan;
use App\Models\model_tabel_obat_reguler;
use App\Models\model_tabel_resep_kirim;
use App\Models\model_template_racikan;
use App\Models\model_template_racikan_detail;
use App\Models\model_ti_kartu_stok;
use App\Models\model_ts_layanan_detail;
use App\Models\model_mt_racikan;
use App\Models\model_mt_racikan_detail;
use App\Models\model_ts_layanan_header;
use App\Models\model_ts_retur_detail;
use App\Models\model_ts_retur_header;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;

class DepoFarmasiController extends Controller
{
    public function indexpelayananresep()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexpelayananresep';
        return view('Depofarmasi.indexpelayananresep', compact([
            'menu',
            'date_start',
            'date_end',
        ]));
    }
    public function ambildatakunjungan(Request $request)
    {
        $tanggalawal = $request->tanggalawal;
        $tanggalakhir = $request->tanggalakhir;
        $data = db::select('SELECT DATE(tgl_masuk) AS tgl_masuk,no_rm,kode_kunjungan
        ,fc_nama_px(no_rm) AS nama_pasien 
        ,fc_nama_unit1(kode_unit) AS nama_unit
        ,fc_alamat(no_rm) AS alamat 
        ,fc_nama_paramedis1(kode_paramedis) AS dokter
        ,fc_NAMA_PENJAMIN2(kode_penjamin) AS nama_penjamin
        ,no_sep
        FROM ts_kunjungan WHERE DATE(tgl_masuk) BETWEEN ? AND ?', [$tanggalawal, $tanggalakhir]);
        return view('Depofarmasi.tabel_kunjungan_pasien', compact([
            'data'
        ]));
    }
    public function ambildatastokdepo(Request $Request)
    {
        $keyword = $Request->input('keyword');
        $kodeUnitFilter = auth()->user()->unit; // Sesuaikan logika unit Anda

        $subQuery = DB::table('ti_kartu_stok')
            ->select('kode_barang', 'kode_unit', DB::raw('MAX(NO) as max_id'))
            ->where('kode_unit', $kodeUnitFilter)
            ->where('stok_current', '<>', 0)
            ->groupBy('kode_barang', 'kode_unit');

        $query = DB::table('ti_kartu_stok as t1')
            ->joinSub($subQuery, 't2', function ($join) {
                $join->on('t1.no', '=', 't2.max_id')
                    ->on('t1.kode_unit', '=', 't2.kode_unit');
            })
            ->join('mt_barang as mb', 't1.kode_barang', '=', 'mb.kode_barang')
            ->join('mt_unit as mu', 't1.kode_unit', '=', 'mu.kode_unit')
            ->where('t1.kode_unit', $kodeUnitFilter)
            ->where('mb.act', 1);

        // --- TAMBAHKAN FILTERING BERDASARKAN PARAMETER ---
        if (!empty($keyword)) {
            $query->where('mb.nama_barang', 'LIKE', "%{$keyword}%");
        }
        // -------------------------------------------------

        $query->select([
            'mb.nama_barang',
            'mu.nama_unit as unit',
            't1.stok_last',
            't1.stok_current',
            't1.tgl_stok',
            't1.kode_barang',
            't1.no',
            'mb.satuan',
            'mb.satuan_besar'
        ]);

        return DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }
    public function ambil_form_pelayanan_obat(Request $request)
    {
        $kode_kunjungan = $request->kode_kunjungan;
        $data_kunjungan = db::select('select * 
        ,fc_nama_px(no_rm) AS nama_pasien 
        ,fc_nama_unit1(kode_unit) AS nama_unit
        ,kode_unit
        ,fc_alamat(no_rm) AS alamat 
        ,fc_nama_paramedis1(kode_paramedis) AS dokter
        ,fc_NAMA_PENJAMIN2(kode_penjamin) AS nama_penjamin
        from ts_kunjungan where kode_kunjungan = ?', [$kode_kunjungan]);
        $data_obat = db::select('select * from master_barang_x_master_obat_bpjs');
        $mt_pasien = db::select('select * from mt_pasien where no_rm = ?', [$data_kunjungan[0]->no_rm]);
        $now = Carbon::now()->startOfMonth();
        $date_start = $now->format('Y-m-d');
        return view('Depofarmasi.form_pelayanan', compact([
            'data_kunjungan',
            'data_obat',
            'mt_pasien',
            'date_start'
        ]));
    }
    public function simpanresep_3(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $kode_kunjungan = $request->kode_kunjungan;
        $data = json_decode($_POST['data'], true);
        $arrayobat = [];
        foreach ($data as $nama3) {
            $index3 = $nama3['name'];
            $value3 = $nama3['value'];
            $dataSet3[$index3] = $value3;
            if ($index3 == 'aturan_pakai') {
                $arrayobat[] = $dataSet3;
            }
        }
        $collection = collect($arrayobat);
        $dataTerpisah = $collection->groupBy('jenis_obat');
        $obatreguler = $dataTerpisah->get('Reguler', []);
        $obatkronis = $dataTerpisah->get('Kronis', []);
        $obatkemo = $dataTerpisah->get('Kemo', []);
        $obatprb = $dataTerpisah->get('PRB', []);
        if (count($obatkronis) > 0) {
            foreach ($obatkronis as $a) {
                if ($a['tipe'] != 'RACIKAN') {
                    $get_barang = db::select('select kode_obat_bpjs from master_barang_x_master_obat_bpjs where kode_barang = ?', [$a['kode_barang']]);
                    if (count($get_barang) == 0) {
                        return response()->json([
                            'kode' => 500,
                            'message' => 'Obat Kronis ' . $a['namabarang'] . ' Belum mempunyai kode barang BPJS, silahkan lakukan mapping master barang  ...'
                        ], 200);
                    }
                } else {
                    $racikandetail = db::select('select * from template_racikan_detail where id_header = ?', [$a['kode_barang']]);
                    foreach ($racikandetail as $bb) {
                        $get_barang = db::select('select kode_obat_bpjs from master_barang_x_master_obat_bpjs where kode_barang = ?', [$bb->kode_barang]);
                        $mt_barang = db::select('select nama_barang from mt_barang where kode_barang = ?', [$bb->kode_barang]);
                        if (count($get_barang) == 0) {
                            return response()->json([
                                'kode' => 500,
                                'message' => 'Komponen racikan, Obat Kronis ' . $mt_barang[0]->nama_barang . ' Belum mempunyai kode barang BPJS, silahkan lakukan mapping master barang  ...'
                            ], 200);
                        }
                    }
                }
            }
        }
        if (count($obatkemo) > 0) {
            foreach ($obatkemo as $a) {
                $get_barang = db::select('select kode_obat_bpjs from master_barang_x_master_obat_bpjs where kode_barang = ?', [$a['kode_barang']]);
                if (count($get_barang) == 0) {
                    return response()->json([
                        'kode' => 500,
                        'message' => 'Obat Kemo ' . $a['namabarang'] . ' Belum mempunyai kode barang BPJS, silahkan lakukan mapping master barang  ...'
                    ], 200);
                }
            }
        }
        if (count($obatprb) > 0) {
            foreach ($obatprb as $a) {
                $get_barang = db::select('select kode_obat_bpjs from master_barang_x_master_obat_bpjs where kode_barang = ?', [$a['kode_barang']]);
                if (count($get_barang) == 0) {
                    return response()->json([
                        'kode' => 500,
                        'message' => 'Obat PRB ' . $a['namabarang'] . ' Belum mempunyai kode barang BPJS, silahkan lakukan mapping master barang  ...'
                    ], 200);
                }
            }
        }
        $collection = collect($arrayobat);
        $dataTerpisah = $collection->groupBy('jenis_obat');

        $resepKronisTerbentuk = false;
        $dataResepBPJS = null;

        $resepKemoTerbentuk = false;
        $dataResepBPJSKemo = null;

        $resepPRBTerbentuk = false;
        $dataResepBPJSPRB = null;
        try {
            DB::beginTransaction();
            $data_kunjungan = db::select('select *,fc_nama_px(no_rm) as nama_pasien,fc_alamat(no_rm) as alamat_pasien,fc_nama_unit1(kode_unit) as nama_unit from ts_kunjungan where kode_kunjungan = ?', [$kode_kunjungan]);
            if (empty($data_kunjungan)) {
                throw new \Exception("Data kunjungan tidak ditemukan.");
            }
            $mt_pasien = db::select('select * from mt_pasien where no_rm = ?', [$data_kunjungan[0]->no_rm]);
            if (empty($arrayobat)) {
                throw new \Exception("Daftar obat tidak boleh kosong.");
            }
            $kodeunit = auth()->user()->unit;
            $unit = db::select('select * from mt_unit where kode_unit =?', [$kodeunit]);
            $unit_kunjungan = db::select('select * from mt_unit where kode_unit =?', [$data_kunjungan[0]->kode_unit]);
            $data_paramedis = db::select('select * from mt_paramedis where kode_paramedis =?', [$data_kunjungan[0]->kode_paramedis]);
            if (count($dataTerpisah->get('PRB', [])) > 0) {
                $dataResepBPJSPRB = $this->prosesResepPRB($dataTerpisah->get('PRB'), $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan);
                $resepPRBTerbentuk = true;
            }
            if (count($dataTerpisah->get('Kemo', [])) > 0) {
                $dataResepBPJSKemo = $this->prosesResepKemo($dataTerpisah->get('Kemo'), $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan);
                $resepKemoTerbentuk = true;
            }
            if (count($dataTerpisah->get('Kronis', [])) > 0) {
                $dataResepBPJS = $this->prosesResepKronis($dataTerpisah->get('Kronis'), $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan);
                $resepKronisTerbentuk = true;
            }
            // --- PROSES REGULER ---
            if (count($dataTerpisah->get('Reguler', [])) > 0) {
                // Panggil fungsi terpisah untuk memproses resep reguler
                $this->prosesResepReguler($dataTerpisah->get('Reguler'), $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan);
            }

            DB::commit();
            return response()->json(['kode' => 200, 'message' => 'Resep berhasil disimpan'], 200);
        } catch (\Exception $e) {
            // ROLLBACK SEMUA PERUBAHAN DATABASE LOKAL
            DB::rollback();
            if ($resepKronisTerbentuk && $dataResepBPJS) {
                try {
                    $v->hapus_resep([
                        "nosjp" => $dataResepBPJS['noApotik'],
                        "refasalsjp" => $dataResepBPJS['noSep_Kunjungan'],
                        "noresep" => $dataResepBPJS['noResep']
                    ]);
                    Log::info("Resep BPJS berhasil dibatalkan: " . $dataResepBPJS['noResep']);
                } catch (\Exception $bpjsEx) {
                    Log::error("Gagal membatalkan resep BPJS: " . $bpjsEx->getMessage());
                }
            }
            if ($resepKemoTerbentuk && $dataResepBPJSKemo) {
                try {
                    $v->hapus_resep([
                        "nosjp" => $dataResepBPJSKemo['noApotik_kemo'],
                        "refasalsjp" => $dataResepBPJSKemo['noSep_Kunjungan_kemo'],
                        "noresep" => $dataResepBPJSKemo['noResep_kemo']
                    ]);
                    Log::info("Resep BPJS berhasil dibatalkan: " . $dataResepBPJSKemo['noResep_kemo']);
                } catch (\Exception $bpjsEx) {
                    Log::error("Gagal membatalkan resep BPJS: " . $bpjsEx->getMessage());
                }
            }
            if ($resepPRBTerbentuk && $dataResepBPJSPRB) {
                try {
                    $v->hapus_resep([
                        "nosjp" => $dataResepBPJSPRB['noApotik_PRB'],
                        "refasalsjp" => $dataResepBPJSPRB['noSep_Kunjungan_PRB'],
                        "noresep" => $dataResepBPJSPRB['noResep_PRB']
                    ]);
                    Log::info("Resep BPJS berhasil dibatalkan: " . $dataResepBPJSPRB['noResep_PRB']);
                } catch (\Exception $bpjsEx) {
                    Log::error("Gagal membatalkan resep BPJS: " . $bpjsEx->getMessage());
                }
            }
            $errorDetails = json_decode($e->getMessage(), true);
            if (is_array($errorDetails) && isset($errorDetails['noApotik'])) {
                Log::error("Error Stok: " . $errorDetails['message']);
                // 3. Batalkan ke BPJS menggunakan data yang dikirim melalui exception
                try {
                    $v->hapus_resep([
                        "nosjp" => $errorDetails['noApotik'],
                        "refasalsjp" => $errorDetails['noSep_Kunjungan'],
                        "noresep" => $errorDetails['noResep']
                    ]);
                    Log::info("Data BPJS berhasil dihapus karena stok kurang: " . $errorDetails['noResep']);
                } catch (\Exception $bpjsEx) {
                    Log::error("Gagal menghapus data BPJS: " . $bpjsEx->getMessage());
                }

                $pesanUser = $errorDetails['message'];
            } else {
                // Jika error sistem lain (bukan error stok terstruktur)
                $pesanUser = $e->getMessage();
                Log::error("Error Sistem: " . $pesanUser);
            }
            $errorDetails = json_decode($e->getMessage(), true);
            if (is_array($errorDetails) && isset($errorDetails['noApotik_kemo'])) {
                Log::error("Error Stok: " . $errorDetails['message_kemo']);
                // 3. Batalkan ke BPJS menggunakan data yang dikirim melalui exception
                try {
                    $v->hapus_resep([
                        "nosjp" => $errorDetails['noApotik_kemo'],
                        "refasalsjp" => $errorDetails['noSep_Kunjungan_kemo'],
                        "noresep" => $errorDetails['noResep_kemo']
                    ]);
                    Log::info("Data BPJS berhasil dihapus karena stok kurang: " . $errorDetails['noResep_kemo']);
                } catch (\Exception $bpjsEx) {
                    Log::error("Gagal menghapus data BPJS: " . $bpjsEx->getMessage());
                }

                $pesanUser = $errorDetails['message_kemo'];
            } else {
                // Jika error sistem lain (bukan error stok terstruktur)
                $pesanUser = $e->getMessage();
                Log::error("Error Sistem: " . $pesanUser);
            }
            $errorDetails = json_decode($e->getMessage(), true);
            if (is_array($errorDetails) && isset($errorDetails['noApotik_PRB'])) {
                Log::error("Error Stok: " . $errorDetails['message_PRB']);
                // 3. Batalkan ke BPJS menggunakan data yang dikirim melalui exception
                try {
                    $v->hapus_resep([
                        "nosjp" => $errorDetails['noApotik_PRB'],
                        "refasalsjp" => $errorDetails['noSep_Kunjungan_PRB'],
                        "noresep" => $errorDetails['noResep_PRB']
                    ]);
                    Log::info("Data BPJS berhasil dihapus karena stok kurang: " . $errorDetails['noResep_PRB']);
                } catch (\Exception $bpjsEx) {
                    Log::error("Gagal menghapus data BPJS: " . $bpjsEx->getMessage());
                }

                $pesanUser = $errorDetails['message_PRB'];
            } else {
                // Jika error sistem lain (bukan error stok terstruktur)
                $pesanUser = $e->getMessage();
                Log::error("Error Sistem: " . $pesanUser);
            }

            // 4. Kembalikan respons JSON
            return response()->json([
                'kode' => 500,
                'message' => 'Transaksi dibatalkan. ' . $pesanUser
            ], 200);
        }
    }
    public function prosesResepKronis($dataobat, $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan)
    {
        $r = DB::select("CALL GET_NOMOR_LAYANAN_HEADER('$kodeunit')");
        $PENJAMIN = $data_kunjungan[0]->kode_penjamin;
        if ($PENJAMIN == 'P01') {
            $kat_resep = 'Resep Tunai';
            $tipe_tx = '1';
        } else {
            $kat_resep = 'Resep Kredit';
            $tipe_tx = '2';
        }
        $kode_layanan_header = $r[0]->no_trx_layanan;
        if ($kode_layanan_header == "") {
            $year = date('y');
            $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
            DB::select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d H:i:s'), $kode_layanan_header, $kodeunit]);
        }
        $cek_resep_ke = db::select('select id from ts_layanan_header where kode_kunjungan = ? and kode_unit = ? and status_layanan != 3', [$kode_kunjungan, $kodeunit]);
        if (count($cek_resep_ke) == 0) {
            $urutan = 1;
        } else {
            $s =  count($cek_resep_ke);
            $urutan = $s + 1;
        }
        $data_layanan_header = [
            'kode_layanan_header' => $kode_layanan_header,
            'tgl_entry' => $this->get_now(),
            'kode_kunjungan' => $kode_kunjungan,
            'kode_unit' => auth()->user()->unit,
            'kode_tipe_transaksi' => $tipe_tx,
            'pic' => auth()->user()->id,
            'status_layanan' => '3',
            'keterangan' => 'Resep Ke : ' . $urutan . ' Kronis ',
            'total_layanan' => '0',
            'status_retur' => '0',
            'kode_penjaminx' => $data_kunjungan[0]->kode_penjamin,
            'tagihan_pribadi' => 0,
            'tagihan_penjamin' => 0,
            'status_pembayaran' => 'OPN',
            'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
            'unit_pengirim' => $data_kunjungan[0]->kode_unit . ' | ' . $data_kunjungan[0]->nama_unit,
            'diagnosa' => $data_kunjungan[0]->diagx,
        ];
        $lyheader = model_ts_layanan_header::create($data_layanan_header);
        $nomor_resep = $this->create_nomor_resep();
        $hasilAkhir = 0; // Mulai dengan 0
        foreach ($dataobat as $a) {
            // Cek apakah iterasi_obat adalah 1
            if ($a['iterasi_obat'] == 1) {
                $hasilAkhir = 1;
                break; // Hentikan loop segera setelah menemukan angka 1
            }
        }
        $data_resep = [
            "TGLSJP" => $this->get_now(),
            "REFASALSJP" => $data_kunjungan[0]->no_sep,
            "POLIRSP" => $unit_kunjungan[0]->KDPOLI,
            "KDJNSOBAT" => 2,
            "NORESEP" => $nomor_resep,
            "IDUSERSJP" => 'USR-1',
            "TGLRSP" => $this->get_now(),
            "TGLPELRSP" => $this->get_now(),
            "KdDokter" => $data_paramedis[0]->kode_dokter_jkn,
            "iterasi" => $hasilAkhir //iterasi harus diisi
        ];
        $data_resep_kirim = model_tabel_resep_kirim::create($data_resep);
        $id_resep_kirim = $data_resep_kirim->id;
        $response_data = $v->simpan_resep($data_resep);
        if ($response_data->metaData->code == 200) {
            $data_save = [
                'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                'noKartu' => $response_data->response->noKartu,
                'nama' => $response_data->response->nama,
                'faskesAsal' => $response_data->response->faskesAsal,
                'noApotik' => $response_data->response->noApotik,
                'noResep' => $response_data->response->noResep,
                'tglResep' => $response_data->response->tglResep,
                'kdJnsObat' => $response_data->response->kdJnsObat,
                'tglEntry' => $response_data->response->tglEntry,
                'pic' => auth()->user()->id,
                'status' => $response_data->metaData->code,
                'message' => $response_data->metaData->message,
                'id_resep_kirim' => $id_resep_kirim
            ];
            $IDRESEPJADI = model_resep_obat::create($data_save);
            model_tabel_resep_kirim::where('id', $id_resep_kirim)->update(['status_terkirim' => 'TERKIRIM', 'id_layanan_header' => $lyheader->id]);
            $now = $this->get_now();
            $jsf = DB::select('select * from mt_jasa_farmasi');
            $totalheader = 0;
            foreach ($dataobat as $a) {
                //proses obat NON RACIKAN
                if ($a['tipe'] != 'RACIKAN') {
                    $get_barang = db::select('select kode_obat_bpjs,nama_generik from master_barang_x_master_obat_bpjs where kode_barang = ?', [$a['kode_barang']]);
                    $data_obat_reguler = [
                        "NOSJP" => $response_data->response->noApotik,
                        "NORESEP" => $nomor_resep,
                        "KDOBT" => $get_barang[0]->kode_obat_bpjs,
                        "NMOBAT" => $get_barang[0]->nama_generik,
                        "SIGNA1OBT" => $a['signa1'],
                        "SIGNA2OBT" => $a['signa2'],
                        "JMLOBT" => $a['qtybeli'],
                        "JHO" => $a['qtybeli'],
                        "CatKhsObt" => $nomor_resep,
                    ];
                    // Simpan lokal dulu
                    $DATA_OBAT_LOCAL = model_tabel_obat_reguler::create($data_obat_reguler);
                    // Kirim ke BPJS
                    $response_data_obat = $v->save_non_racik($data_obat_reguler);
                    if ($response_data_obat->metaData->code == 200) {
                        //INSERT TS_LAYANAN_DETAIL
                        try {
                            $kode_detail_obat = $this->createLayanandetail();
                            $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang']]);
                            $total = $mt_barang[0]->harga_jual * $a['qtybeli'];
                            $diskon = 0;
                            $hitung = $diskon / 100 * $total;
                            $grandtotal = $total - $hitung + 1200 + 500;
                            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                                $tagihan_pribadi = 0;
                                $tagihan_penjamin = $grandtotal;
                            } else {
                                $tagihan_pribadi = $grandtotal;
                                $tagihan_penjamin = 0;
                            }
                            $ts_layanan_detail = [
                                'id_layanan_detail' => $kode_detail_obat,
                                'kode_layanan_header' => $kode_layanan_header,
                                'kode_tarif_detail' => '0',
                                'total_tarif' => $mt_barang[0]->harga_jual,
                                'jumlah_layanan' => $a['qtybeli'],
                                'total_layanan' => $total,
                                'diskon_layanan' => '0',
                                'grantotal_layanan' => $grandtotal,
                                'status_layanan_detail' => 'OPN',
                                'tgl_layanan_detail' => $now,
                                'kode_barang' => $a['kode_barang'],
                                'aturan_pakai' => $a['aturan_pakai'],
                                'kategori_resep' => $kat_resep,
                                'satuan_barang' => $mt_barang[0]->satuan,
                                'tipe_anestesi' => 81,
                                'tagihan_pribadi' => $tagihan_pribadi,
                                'tagihan_penjamin' =>  $tagihan_penjamin,
                                'tgl_layanan_detail_2' => $now,
                                'row_id_header' => $lyheader->id,
                            ];
                            $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                                $tagihan_pribadi_js = 0;
                                $tagihan_penjamin_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                            } else {
                                $tagihan_pribadi_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                                $tagihan_penjamin_js = 0;
                            }
                            $ts_layanan_detail_2 = [
                                'id_layanan_detail' => $this->createLayanandetail(),
                                'kode_layanan_header' => $kode_layanan_header,
                                'kode_tarif_detail' => 'TX23513',
                                'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                                'jumlah_layanan' => 1,
                                'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                                'diskon_layanan' => '0',
                                'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                                'status_layanan_detail' => 'OPN',
                                'tgl_layanan_detail' => $now,
                                'kategori_resep' => $kat_resep,
                                'satuan_barang' => '-',
                                'tagihan_pribadi' => $tagihan_pribadi_js,
                                'tagihan_penjamin' => $tagihan_penjamin_js,
                                'tipe_anestesi' => 81,
                                'tgl_layanan_detail_2' => $now,
                                'row_id_header' => $lyheader->id,
                            ];
                            $detail_2 = model_ts_layanan_detail::create($ts_layanan_detail_2);
                            model_tabel_obat_reguler::where('id', $DATA_OBAT_LOCAL->id)->update([
                                'status' => 'TERKIRIM',
                                'pic' => auth()->user()->id,
                                'id_resep_header' => $IDRESEPJADI->id,
                                'tgl_resep' => $this->get_now(),
                                'id_layanan_detail' => $detail->id
                            ]);
                            $totalheader = $totalheader + $grandtotal;
                            $stokTerakhir = DB::table('ti_kartu_stok')
                                ->where('kode_barang', $a['kode_barang'])
                                ->where('kode_unit', $kodeunit) // Unit Apotek
                                ->orderBy('no', 'desc')
                                ->first();
                            $saldoStok = $stokTerakhir ? $stokTerakhir->stok_current : 0;
                            if ($saldoStok < $a['qtybeli']) {
                                // Siapkan data untuk rollback BPJS
                                $dataError = [
                                    'message' => "Stok barang " . $a['namabarang'] . " tidak cukup. Sisa stok: " . $saldoStok,
                                    'noApotik' => $response_data->response->noApotik,
                                    'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                    'noResep' => $response_data->response->noResep
                                ];
                                throw new \Exception(json_encode($dataError));
                            } else {
                                try {
                                    $stok_current = $stokTerakhir->stok_current - $a['qtybeli'];
                                    $data_ti_kartu_stok = [
                                        'no_dokumen' => $kode_layanan_header,
                                        'no_dokumen_detail' => $kode_detail_obat,
                                        'tgl_stok' => $this->get_now(),
                                        'kode_unit' => auth()->user()->unit,
                                        'kode_barang' => $a['kode_barang'],
                                        'stok_last' => $stokTerakhir->stok_current,
                                        'stok_out' => $a['qtybeli'],
                                        'stok_current' => $stok_current,
                                        'harga_beli' => $mt_barang[0]->hna,
                                        'act' => '1',
                                        'act_ed' => '1',
                                        // 'input_by' => auth()->user()->id,
                                        'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                                    ];
                                    $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
                                } catch (\Exception $e) {
                                    $dataError = [
                                        'message' => "ERROR SYSTEM :" . $e->getMessage(),
                                        'noApotik' => $response_data->response->noApotik,
                                        'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                        'noResep' => $response_data->response->noResep
                                    ];
                                    throw new \Exception(json_encode($dataError));
                                }
                            }
                        } catch (\Exception $e) {
                            $dataError = [
                                'message' => "ERROR SYSTEM :" . $e->getMessage(),
                                'noApotik' => $response_data->response->noApotik,
                                'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                'noResep' => $response_data->response->noResep
                            ];
                            throw new \Exception(json_encode($dataError));
                        }
                    } else {
                        $dataError = [
                            'message' => "Gagal kirim obat Kronis {$a['namabarang']}: " . $response_data_obat->metaData->message,
                            'noApotik' => $response_data->response->noApotik,
                            'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                            'noResep' => $response_data->response->noResep
                        ];
                        // Throw exception dengan JSON string dari array tersebut
                        throw new \Exception(json_encode($dataError));
                    }
                } else {
                    //PROSES OBAT RACIKAN
                    $racikandetail = db::select('select * from template_racikan_detail where id_header = ?', [$a['kode_barang']]);
                    $racikan = db::select('select * from template_racikan_header where id =?', [$a['kode_barang']]);
                    //proses mt_racikan dulu
                    if ($racikan[0]->sediaan == 1) {
                        $kemasan = 'KAPSUL';
                        $tiperacik = 'NS';
                        $harga = '700';
                    } elseif ($racikan[0]->sediaan == 2) {
                        $kemasan = 'KERTAS';
                        $tiperacik = 'NS';
                        $harga = '700';
                    } else {
                        $kemasan = 'POT SALEP';
                        $tiperacik = 'S';
                        $harga = 10000;
                    }
                    $kode_racik = $this->get_kode_racik();
                    $data_mt_racikan_header = [
                        'kode_racik' => $kode_racik,
                        'tgl_racik' => $this->get_now(),
                        'nama_racik' => $racikan[0]->namaracikan,
                        'total_racik' => 0,
                        'tipe_racik' => $tiperacik,
                        'qty_racik' => $racikan[0]->qtyracikan,
                        'kemasan' => $kemasan,
                        'hrg_kemasan' => $harga,
                    ];
                    $mt_racikan_header = model_mt_racikan::create($data_mt_racikan_header);
                    $total_racik = 0;
                    foreach ($racikandetail as $dd) {
                        $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dd->kode_barang]);
                        $totalbarang = $mt_barang[0]->harga_jual + $dd->qty_barang;
                        $tt = $totalbarang + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                        $mt_racikan_detail_1 = [
                            'kode_racik' => $kode_racik,
                            'kode_barang' => $dd->kode_barang,
                            'qty_barang' => $dd->qty_barang,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'harga_satuan_barang' => $mt_barang[0]->harga_jual,
                            'subtotal_barang' => $totalbarang,
                            'grantotal_barang' => $totalbarang + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'harga_brg_embalase' => $totalbarang + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'qty_order' => $dd->qty_barang,
                        ];
                        $save_mt_racikan_detail_1 = model_mt_racikan_detail::create($mt_racikan_detail_1);
                        $mt_racikan_detail_2 = [
                            'kode_racik' => $kode_racik,
                            'kode_barang' => 'TX23513',
                            'qty_barang' => 1,
                            'satuan_barang' => '-',
                            'harga_satuan_barang' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'subtotal_barang' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'grantotal_barang' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'harga_brg_embalase' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'qty_order' => 1,
                        ];
                        $save_mt_racikan_detail_2 = model_mt_racikan_detail::create($mt_racikan_detail_2);
                        $total_racik = $total_racik + $tt;
                        $get_barang = db::select('select kode_obat_bpjs,nama_generik from master_barang_x_master_obat_bpjs where kode_barang = ?', [$dd->kode_barang]);
                        $data_obat_racik = [
                            "NOSJP" => $response_data->response->noApotik,
                            "NORESEP" => $nomor_resep,
                            "JNSROBT" => "R.01",
                            "KDOBT" => $get_barang[0]->kode_obat_bpjs,
                            "NMOBAT" => $get_barang[0]->nama_generik,
                            "SIGNA1OBT" => $a['signa1'],
                            "SIGNA2OBT" => $a['signa2'],
                            "PERMINTAAN" => $dd->dosis_racik,
                            "JMLOBT" => $dd->qty_barang,
                            "JHO" => $racikan[0]->qtyracikan,
                            "CatKhsObt" => $racikan[0]->namaracikan,
                        ];
                        $DATA_OBAT_LOCAL = model_tabel_obat_racikan::create($data_obat_racik);
                        // Kirim ke BPJS
                        $response_data_obat = $v->save_racikan($data_obat_racik);
                        if ($response_data_obat->metaData->code == 200) {
                            try {
                                model_tabel_obat_racikan::where('id', $DATA_OBAT_LOCAL->id)->update([
                                    'status' => 'TERKIRIM',
                                    'pic' => auth()->user()->id,
                                    'id_resep_header' => $IDRESEPJADI->id,
                                    'tgl_resep' => $this->get_now(),
                                    'id_layanan_detail' => $save_mt_racikan_detail_1->id
                                ]);
                                $stokTerakhir = DB::table('ti_kartu_stok')
                                    ->where('kode_barang', $dd->kode_barang)
                                    ->where('kode_unit', $kodeunit) // Unit Apotek
                                    ->orderBy('no', 'desc')
                                    ->first();
                                $saldoStok = $stokTerakhir ? $stokTerakhir->stok_current : 0;
                                if ($saldoStok <  $dd->qty_barang) {
                                    // Siapkan data untuk rollback BPJS
                                    $dataError = [
                                        'message' => "Stok barang " . $mt_barang[0]->nama_barang . " tidak cukup. Sisa stok: " . $saldoStok,
                                        'noApotik' => $response_data->response->noApotik,
                                        'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                        'noResep' => $response_data->response->noResep
                                    ];
                                    throw new \Exception(json_encode($dataError));
                                } else {
                                    try {
                                        $stok_current = $stokTerakhir->stok_current - $dd->qty_barang;
                                        $data_ti_kartu_stok = [
                                            'no_dokumen' => $kode_layanan_header,
                                            'no_dokumen_detail' => $kode_racik,
                                            'tgl_stok' => $this->get_now(),
                                            'kode_unit' => auth()->user()->unit,
                                            'kode_barang' => $dd->kode_barang,
                                            'stok_last' => $stokTerakhir->stok_current,
                                            'stok_out' => $dd->qty_barang,
                                            'stok_current' => $stok_current,
                                            'harga_beli' => $mt_barang[0]->hna,
                                            'act' => '1',
                                            'act_ed' => '1',
                                            // 'input_by' => auth()->user()->id,
                                            'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                                        ];
                                        $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
                                    } catch (\Exception $e) {
                                        $dataError = [
                                            'message' => "ERROR SYSTEM :" . $e->getMessage(),
                                            'noApotik' => $response_data->response->noApotik,
                                            'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                            'noResep' => $response_data->response->noResep
                                        ];
                                        throw new \Exception(json_encode($dataError));
                                    }
                                }
                            } catch (\Exception $e) {
                                $dataError = [
                                    'message' => "ERROR SYSTEM :" . $e->getMessage(),
                                    'noApotik' => $response_data->response->noApotik,
                                    'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                    'noResep' => $response_data->response->noResep
                                ];
                                throw new \Exception(json_encode($dataError));
                            }
                        } else {
                            $dataError = [
                                'message' => "Gagal kirim obat Kronis {$a['namabarang']}: " . $response_data_obat->metaData->message,
                                'noApotik' => $response_data->response->noApotik,
                                'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                                'noResep' => $response_data->response->noResep
                            ];
                            // Throw exception dengan JSON string dari array tersebut
                            throw new \Exception(json_encode($dataError));
                        }
                    }
                    model_mt_racikan::where('id', $mt_racikan_header->id)->update(['total_racik' => $total_racik]);
                    $kode_detail_obat = $this->createLayanandetail();
                    if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                        $tagihan_pribadi = 0;
                        $tagihan_penjamin = $total_racik;
                    } else {
                        $tagihan_pribadi = $total_racik;
                        $tagihan_penjamin = 0;
                    }
                    $grandtotal = $total_racik;
                    $ts_layanan_detail = [
                        'id_layanan_detail' => $kode_detail_obat,
                        'kode_layanan_header' => $kode_layanan_header,
                        'kode_tarif_detail' => '0',
                        'total_tarif' => $total_racik,
                        'jumlah_layanan' =>  $a['qtybeli'],
                        'total_layanan' => $total_racik,
                        'diskon_layanan' => '0',
                        'grantotal_layanan' => $total_racik,
                        'status_layanan_detail' => 'OPN',
                        'tgl_layanan_detail' => $now,
                        'kode_barang' => $kode_racik,
                        'aturan_pakai' => $a['aturan_pakai'],
                        'kategori_resep' => $kat_resep,
                        'satuan_barang' => '-',
                        'tipe_anestesi' => 81,
                        'tagihan_pribadi' => $tagihan_pribadi,
                        'tagihan_penjamin' =>  $tagihan_penjamin,
                        'tgl_layanan_detail_2' => $now,
                        'row_id_header' => $lyheader->id,
                    ];
                    $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                    if ($tiperacik == 'NS') {
                        $HARGA = $jsf[0]->jasa_racikan_powder;
                        $jumlahl = $a['qtybeli'] * $HARGA;
                        $jumlah = $a['qtybeli'];
                    } else {
                        $HARGA = $jsf[0]->jasa_racikan_salep;
                        $jumlah = 1;
                        $jumlahl = $HARGA;
                    }
                    if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                        $tagihan_pribadi_js = 0;
                        $tagihan_penjamin_js = $jumlahl;
                    } else {
                        $tagihan_pribadi_js = $jumlahl;
                        $tagihan_penjamin_js = 0;
                    }
                    $ts_layanan_detail_2 = [
                        'id_layanan_detail' => $this->createLayanandetail(),
                        'kode_layanan_header' => $kode_layanan_header,
                        'kode_tarif_detail' => 'TX23513',
                        'total_tarif' => $HARGA,
                        'jumlah_layanan' => $jumlah,
                        'total_layanan' => $jumlahl,
                        'diskon_layanan' => '0',
                        'grantotal_layanan' => $jumlahl,
                        'status_layanan_detail' => 'OPN',
                        'tgl_layanan_detail' => $now,
                        'kategori_resep' => $kat_resep,
                        'satuan_barang' => '-',
                        'tagihan_pribadi' => $tagihan_pribadi_js,
                        'tagihan_penjamin' => $tagihan_penjamin_js,
                        'tipe_anestesi' => 81,
                        'tgl_layanan_detail_2' => $now,
                        'row_id_header' => $lyheader->id,
                    ];
                    $detail_2 = model_ts_layanan_detail::create($ts_layanan_detail_2);
                    $totalheader = $totalheader + $grandtotal;
                }
            }
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = $jsf[0]->jasa_baca;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = $jsf[0]->jasa_baca;
            }
            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $jsf[0]->jasa_baca,
                'diskon_layanan' => '0',
                'jumlah_layanan' => 1,
                'total_layanan' => $jsf[0]->jasa_baca,
                'grantotal_layanan' => $jsf[0]->jasa_baca,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'kategori_resep' => $kat_resep,
                'satuan_barang' => '-',
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tipe_anestesi' => 81,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $lyheader->id,
            ];
            $detail3 = model_ts_layanan_detail::create($ts_layanan_detail3);
            $totalheader = $totalheader + $jsf[0]->jasa_baca;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
                $status_layanan = 2;
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
                $status_layanan = 1;
            }
            foreach ($dataobat as $a) {
                try {
                } catch (\Exception $e) {
                    return $dataError = [
                        'message' => $e->getMessage(),
                        'noApotik' => $response_data->response->noApotik,
                        'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                        'noResep' => $response_data->response->noResep
                    ];
                    throw new \Exception("error sistem : " . json_encode($dataError));
                }
            }
            model_ts_layanan_header::where('id', $lyheader->id)
                ->update(['status_layanan' => $status_layanan, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            return $dataError = [
                'message' => 'SUKSES',
                'noApotik' => $response_data->response->noApotik,
                'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                'noResep' => $response_data->response->noResep
            ];
        } else {
            throw new \Exception("Gagal kirim header resep Kronis ke BPJS: " . $response_data->metaData->message);
        }
    }
    public function prosesResepKemo($dataobat, $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan)
    {
        $r = DB::select("CALL GET_NOMOR_LAYANAN_HEADER('$kodeunit')");
        $PENJAMIN = $data_kunjungan[0]->kode_penjamin;
        if ($PENJAMIN == 'P01') {
            $kat_resep = 'Resep Tunai';
            $tipe_tx = '1';
        } else {
            $kat_resep = 'Resep Kredit';
            $tipe_tx = '2';
        }
        $kode_layanan_header = $r[0]->no_trx_layanan;
        if ($kode_layanan_header == "") {
            $year = date('y');
            $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
            DB::select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d H:i:s'), $kode_layanan_header, $kodeunit]);
        }
        $cek_resep_ke = db::select('select id from ts_layanan_header where kode_kunjungan = ? and kode_unit = ? and status_layanan != 3', [$kode_kunjungan, $kodeunit]);
        if (count($cek_resep_ke) == 0) {
            $urutan = 1;
        } else {
            $s =  count($cek_resep_ke);
            $urutan = $s + 1;
        }
        $data_layanan_header = [
            'kode_layanan_header' => $kode_layanan_header,
            'tgl_entry' => $this->get_now(),
            'kode_kunjungan' => $kode_kunjungan,
            'kode_unit' => auth()->user()->unit,
            'kode_tipe_transaksi' => $tipe_tx,
            'pic' => auth()->user()->id,
            'status_layanan' => '3',
            'keterangan' => 'Resep Ke : ' . $urutan . ' Kemo ',
            'total_layanan' => '0',
            'status_retur' => '0',
            'kode_penjaminx' => $data_kunjungan[0]->kode_penjamin,
            'tagihan_pribadi' => 0,
            'tagihan_penjamin' => 0,
            'status_pembayaran' => 'OPN',
            'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
            'unit_pengirim' => $data_kunjungan[0]->kode_unit . ' | ' . $data_kunjungan[0]->nama_unit,
            'diagnosa' => $data_kunjungan[0]->diagx,
        ];
        $lyheader = model_ts_layanan_header::create($data_layanan_header);
        $nomor_resep = $this->create_nomor_resep();
        $hasilAkhir = 0; // Mulai dengan 0

        foreach ($dataobat as $a) {
            // Cek apakah iterasi_obat adalah 1
            if ($a['iterasi_obat'] == 1) {
                $hasilAkhir = 1;
                break; // Hentikan loop segera setelah menemukan angka 1
            }
        }
        $data_resep = [
            "TGLSJP" => $this->get_now(),
            "REFASALSJP" => $data_kunjungan[0]->no_sep,
            "POLIRSP" => $unit_kunjungan[0]->KDPOLI,
            "KDJNSOBAT" => 2,
            "NORESEP" => $nomor_resep,
            "IDUSERSJP" => 'USR-1',
            "TGLRSP" => $this->get_now(),
            "TGLPELRSP" => $this->get_now(),
            "KdDokter" => $data_paramedis[0]->kode_dokter_jkn,
            "iterasi" => $hasilAkhir //iterasi harus diisi
        ];
        $data_resep_kirim = model_tabel_resep_kirim::create($data_resep);
        $id_resep_kirim = $data_resep_kirim->id;
        $response_data = $v->simpan_resep($data_resep);
        if ($response_data->metaData->code == 200) {
            $data_save = [
                'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                'noKartu' => $response_data->response->noKartu,
                'nama' => $response_data->response->nama,
                'faskesAsal' => $response_data->response->faskesAsal,
                'noApotik' => $response_data->response->noApotik,
                'noResep' => $response_data->response->noResep,
                'tglResep' => $response_data->response->tglResep,
                'kdJnsObat' => $response_data->response->kdJnsObat,
                'tglEntry' => $response_data->response->tglEntry,
                'pic' => auth()->user()->id,
                'status' => $response_data->metaData->code,
                'message' => $response_data->metaData->message,
                'id_resep_kirim' => $id_resep_kirim
            ];
            $IDRESEPJADI = model_resep_obat::create($data_save);
            model_tabel_resep_kirim::where('id', $id_resep_kirim)->update(['status_terkirim' => 'TERKIRIM', 'id_layanan_header' => $lyheader->id]);
            $now = $this->get_now();
            $jsf = DB::select('select * from mt_jasa_farmasi');
            $totalheader = 0;
            foreach ($dataobat as $a) {
                $get_barang = db::select('select kode_obat_bpjs,nama_generik from master_barang_x_master_obat_bpjs where kode_barang = ?', [$a['kode_barang']]);
                $data_obat_reguler = [
                    "NOSJP" => $response_data->response->noApotik,
                    "NORESEP" => $nomor_resep,
                    "KDOBT" => $get_barang[0]->kode_obat_bpjs,
                    "NMOBAT" => $get_barang[0]->nama_generik,
                    "SIGNA1OBT" => $a['signa1'],
                    "SIGNA2OBT" => $a['signa2'],
                    "JMLOBT" => $a['qtybeli'],
                    "JHO" => $a['qtybeli'],
                    "CatKhsObt" => "TEST",
                ];
                // Simpan lokal dulu
                $DATA_OBAT_LOCAL = model_tabel_obat_reguler::create($data_obat_reguler);
                // Kirim ke BPJS
                $response_data_obat = $v->save_non_racik($data_obat_reguler);
                if ($response_data_obat->metaData->code == 200) {
                    //INSERT TS_LAYANAN_DETAIL
                    try {
                        $kode_detail_obat = $this->createLayanandetail();
                        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang']]);
                        $total = $mt_barang[0]->harga_jual * $a['qtybeli'];
                        $diskon = 0;
                        $hitung = $diskon / 100 * $total;
                        $grandtotal = $total - $hitung + 1200 + 500;
                        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                            $tagihan_pribadi = 0;
                            $tagihan_penjamin = $grandtotal;
                        } else {
                            $tagihan_pribadi = $grandtotal;
                            $tagihan_penjamin = 0;
                        }
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '0',
                            'total_tarif' => $mt_barang[0]->harga_jual,
                            'jumlah_layanan' => $a['qtybeli'],
                            'total_layanan' => $total,
                            'diskon_layanan' => '0',
                            'grantotal_layanan' => $grandtotal,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $a['kode_barang'],
                            'aturan_pakai' => $a['aturan_pakai'],
                            'kategori_resep' => $kat_resep,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => 82,
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' =>  $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $lyheader->id,
                        ];
                        $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                            $tagihan_pribadi_js = 0;
                            $tagihan_penjamin_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                        } else {
                            $tagihan_pribadi_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                            $tagihan_penjamin_js = 0;
                        }
                        $ts_layanan_detail_2 = [
                            'id_layanan_detail' => $this->createLayanandetail(),
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => 'TX23513',
                            'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'jumlah_layanan' => 1,
                            'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'diskon_layanan' => '0',
                            'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kategori_resep' => $kat_resep,
                            'satuan_barang' => '-',
                            'tagihan_pribadi' => $tagihan_pribadi_js,
                            'tagihan_penjamin' => $tagihan_penjamin_js,
                            'tipe_anestesi' => 81,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $lyheader->id,
                        ];
                        $detail_2 = model_ts_layanan_detail::create($ts_layanan_detail_2);
                        model_tabel_obat_reguler::where('id', $DATA_OBAT_LOCAL->id)->update([
                            'status' => 'TERKIRIM',
                            'pic' => auth()->user()->id,
                            'id_resep_header' => $IDRESEPJADI->id,
                            'tgl_resep' => $this->get_now(),
                            'id_layanan_detail' => $detail->id
                        ]);
                        $totalheader = $totalheader + $grandtotal;
                        $stokTerakhir = DB::table('ti_kartu_stok')
                            ->where('kode_barang', $a['kode_barang'])
                            ->where('kode_unit', $kodeunit) // Unit Apotek
                            ->orderBy('no', 'desc')
                            ->first();
                        $saldoStok = $stokTerakhir ? $stokTerakhir->stok_current : 0;
                        if ($saldoStok < $a['qtybeli']) {
                            // Siapkan data untuk rollback BPJS
                            $errorDetails = [
                                'message_kemo' => "Stok barang " . $a['namabarang'] . " tidak cukup. Sisa stok: " . $saldoStok,
                                'noApotik_kemo' => $response_data->response->noApotik,
                                'noSep_Kunjungan_kemo' => $response_data->response->noSep_Kunjungan,
                                'noResep_kemo' => $response_data->response->noResep
                            ];
                            throw new \Exception(json_encode($errorDetails));
                        } else {
                            try {

                                $stok_current = $stokTerakhir->stok_current - $a['qtybeli'];
                                $data_ti_kartu_stok = [
                                    'no_dokumen' => $kode_layanan_header,
                                    'no_dokumen_detail' => $kode_detail_obat,
                                    'tgl_stok' => $this->get_now(),
                                    'kode_unit' => auth()->user()->unit,
                                    'kode_barang' => $a['kode_barang'],
                                    'stok_last' => $stokTerakhir->stok_current,
                                    'stok_out' => $a['qtybeli'],
                                    'stok_current' => $stok_current,
                                    'harga_beli' => $mt_barang[0]->hna,
                                    'act' => '1',
                                    'act_ed' => '1',
                                    // 'input_by' => auth()->user()->id,
                                    'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                                ];
                                $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
                            } catch (\Exception $e) {
                                $errorDetails = [
                                    'message_kemo' => "ERROR SYSTEM :" . $e->getMessage(),
                                    'noApotik_kemo' => $response_data->response->noApotik,
                                    'noSep_Kunjungan_kemo' => $response_data->response->noSep_Kunjungan,
                                    'noResep_kemo' => $response_data->response->noResep
                                ];
                                throw new \Exception(json_encode($errorDetails));
                            }
                        }
                    } catch (\Exception $e) {
                        $errorDetails = [
                            'message_kemo' => "ERROR SYSTEM :" . $e->getMessage(),
                            'noApotik_kemo' => $response_data->response->noApotik,
                            'noSep_Kunjungan_kemo' => $response_data->response->noSep_Kunjungan,
                            'noResep_kemo' => $response_data->response->noResep
                        ];
                        throw new \Exception(json_encode($errorDetails));
                    }
                } else {
                    $errorDetails = [
                        'message_kemo' => "Gagal kirim obat Kemo {$a['namabarang']}: " . $response_data_obat->metaData->message,
                        'noApotik_kemo' => $response_data->response->noApotik,
                        'noSep_Kunjungan_kemo' => $response_data->response->noSep_Kunjungan,
                        'noResep_kemo' => $response_data->response->noResep
                    ];

                    // Throw exception dengan JSON string dari array tersebut
                    throw new \Exception(json_encode($errorDetails));
                }
            }
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = $jsf[0]->jasa_baca;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = $jsf[0]->jasa_baca;
            }
            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $jsf[0]->jasa_baca,
                'diskon_layanan' => '0',
                'jumlah_layanan' => 1,
                'total_layanan' => $jsf[0]->jasa_baca,
                'grantotal_layanan' => $jsf[0]->jasa_baca,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'kategori_resep' => $kat_resep,
                'satuan_barang' => '-',
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tipe_anestesi' => 81,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $lyheader->id,
            ];
            $detail3 = model_ts_layanan_detail::create($ts_layanan_detail3);
            $totalheader = $totalheader + $jsf[0]->jasa_baca;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
                $status_layanan = 2;
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
                $status_layanan = 1;
            }
            foreach ($dataobat as $a) {
                try {
                } catch (\Exception $e) {
                    return $errorDetails = [
                        'message_kemo' => $e->getMessage(),
                        'noApotik_kemo' => $response_data->response->noApotik,
                        'noSep_Kunjungan_kemo' => $response_data->response->noSep_Kunjungan,
                        'noResep_kemo' => $response_data->response->noResep
                    ];
                    throw new \Exception("error sistem : " . json_encode($errorDetails));
                }
            }
            model_ts_layanan_header::where('id', $lyheader->id)
                ->update(['status_layanan' => $status_layanan, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            return $errorDetails = [
                'message_kemo' => 'SUKSES',
                'noApotik_kemo' => $response_data->response->noApotik,
                'noSep_Kunjungan_kemo' => $response_data->response->noSep_Kunjungan,
                'noResep_kemo' => $response_data->response->noResep
            ];
        } else {
            throw new \Exception("Gagal kirim header resep Kemo ke BPJS: " . $response_data->metaData->message);
        }
    }
    public function prosesResepPRB($dataobat, $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan)
    {
        $r = DB::select("CALL GET_NOMOR_LAYANAN_HEADER('$kodeunit')");
        $PENJAMIN = $data_kunjungan[0]->kode_penjamin;
        if ($PENJAMIN == 'P01') {
            $kat_resep = 'Resep Tunai';
            $tipe_tx = '1';
        } else {
            $kat_resep = 'Resep Kredit';
            $tipe_tx = '2';
        }
        $kode_layanan_header = $r[0]->no_trx_layanan;
        if ($kode_layanan_header == "") {
            $year = date('y');
            $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
            DB::select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d H:i:s'), $kode_layanan_header, $kodeunit]);
        }
        $cek_resep_ke = db::select('select id from ts_layanan_header where kode_kunjungan = ? and kode_unit = ? and status_layanan != 3', [$kode_kunjungan, $kodeunit]);
        if (count($cek_resep_ke) == 0) {
            $urutan = 1;
        } else {
            $s =  count($cek_resep_ke);
            $urutan = $s + 1;
        }
        $data_layanan_header = [
            'kode_layanan_header' => $kode_layanan_header,
            'tgl_entry' => $this->get_now(),
            'kode_kunjungan' => $kode_kunjungan,
            'kode_unit' => auth()->user()->unit,
            'kode_tipe_transaksi' => $tipe_tx,
            'pic' => auth()->user()->id,
            'status_layanan' => '3',
            'keterangan' => 'Resep Ke : ' . $urutan . ' PRB ',
            'total_layanan' => '0',
            'status_retur' => '0',
            'kode_penjaminx' => $data_kunjungan[0]->kode_penjamin,
            'tagihan_pribadi' => 0,
            'tagihan_penjamin' => 0,
            'status_pembayaran' => 'OPN',
            'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
            'unit_pengirim' => $data_kunjungan[0]->kode_unit . ' | ' . $data_kunjungan[0]->nama_unit,
            'diagnosa' => $data_kunjungan[0]->diagx,
        ];
        $lyheader = model_ts_layanan_header::create($data_layanan_header);
        $nomor_resep = $this->create_nomor_resep();
        $hasilAkhir = 0; // Mulai dengan 0

        foreach ($dataobat as $a) {
            // Cek apakah iterasi_obat adalah 1
            if ($a['iterasi_obat'] == 1) {
                $hasilAkhir = 1;
                break; // Hentikan loop segera setelah menemukan angka 1
            }
        }
        $data_resep = [
            "TGLSJP" => $this->get_now(),
            "REFASALSJP" => $data_kunjungan[0]->no_sep,
            "POLIRSP" => $unit_kunjungan[0]->KDPOLI,
            "KDJNSOBAT" => 2,
            "NORESEP" => $nomor_resep,
            "IDUSERSJP" => 'USR-1',
            "TGLRSP" => $this->get_now(),
            "TGLPELRSP" => $this->get_now(),
            "KdDokter" => $data_paramedis[0]->kode_dokter_jkn,
            "iterasi" => $hasilAkhir //iterasi harus diisi
        ];
        $data_resep_kirim = model_tabel_resep_kirim::create($data_resep);
        $id_resep_kirim = $data_resep_kirim->id;
        $response_data = $v->simpan_resep($data_resep);
        if ($response_data->metaData->code == 200) {
            $data_save = [
                'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                'noKartu' => $response_data->response->noKartu,
                'nama' => $response_data->response->nama,
                'faskesAsal' => $response_data->response->faskesAsal,
                'noApotik' => $response_data->response->noApotik,
                'noResep' => $response_data->response->noResep,
                'tglResep' => $response_data->response->tglResep,
                'kdJnsObat' => $response_data->response->kdJnsObat,
                'tglEntry' => $response_data->response->tglEntry,
                'pic' => auth()->user()->id,
                'status' => $response_data->metaData->code,
                'message' => $response_data->metaData->message,
                'id_resep_kirim' => $id_resep_kirim
            ];
            $IDRESEPJADI = model_resep_obat::create($data_save);
            model_tabel_resep_kirim::where('id', $id_resep_kirim)->update(['status_terkirim' => 'TERKIRIM', 'id_layanan_header' => $lyheader->id]);
            $now = $this->get_now();
            $jsf = DB::select('select * from mt_jasa_farmasi');
            $totalheader = 0;
            foreach ($dataobat as $a) {
                $get_barang = db::select('select kode_obat_bpjs,nama_generik from master_barang_x_master_obat_bpjs where kode_barang = ?', [$a['kode_barang']]);
                $data_obat_reguler = [
                    "NOSJP" => $response_data->response->noApotik,
                    "NORESEP" => $nomor_resep,
                    "KDOBT" => $get_barang[0]->kode_obat_bpjs,
                    "NMOBAT" => $get_barang[0]->nama_generik,
                    "SIGNA1OBT" => $a['signa1'],
                    "SIGNA2OBT" => $a['signa2'],
                    "JMLOBT" => $a['qtybeli'],
                    "JHO" => $a['qtybeli'],
                    "CatKhsObt" => "TEST",
                ];
                // Simpan lokal dulu
                $DATA_OBAT_LOCAL = model_tabel_obat_reguler::create($data_obat_reguler);
                // Kirim ke BPJS
                $response_data_obat = $v->save_non_racik($data_obat_reguler);
                if ($response_data_obat->metaData->code == 200) {
                    //INSERT TS_LAYANAN_DETAIL
                    try {
                        $kode_detail_obat = $this->createLayanandetail();
                        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang']]);
                        $total = $mt_barang[0]->harga_jual * $a['qtybeli'];
                        $diskon = 0;
                        $hitung = $diskon / 100 * $total;
                        $grandtotal = $total - $hitung + 1200 + 500;
                        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                            $tagihan_pribadi = 0;
                            $tagihan_penjamin = $grandtotal;
                        } else {
                            $tagihan_pribadi = $grandtotal;
                            $tagihan_penjamin = 0;
                        }
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '0',
                            'total_tarif' => $mt_barang[0]->harga_jual,
                            'jumlah_layanan' => $a['qtybeli'],
                            'total_layanan' => $total,
                            'diskon_layanan' => '0',
                            'grantotal_layanan' => $grandtotal,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $a['kode_barang'],
                            'aturan_pakai' => $a['aturan_pakai'],
                            'kategori_resep' => $kat_resep,
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => 81,
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' =>  $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $lyheader->id,
                        ];
                        $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                            $tagihan_pribadi_js = 0;
                            $tagihan_penjamin_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                        } else {
                            $tagihan_pribadi_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                            $tagihan_penjamin_js = 0;
                        }
                        $ts_layanan_detail_2 = [
                            'id_layanan_detail' => $this->createLayanandetail(),
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => 'TX23513',
                            'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'jumlah_layanan' => 1,
                            'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'diskon_layanan' => '0',
                            'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kategori_resep' => $kat_resep,
                            'satuan_barang' => '-',
                            'tagihan_pribadi' => $tagihan_pribadi_js,
                            'tagihan_penjamin' => $tagihan_penjamin_js,
                            'tipe_anestesi' => '84',
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $lyheader->id,
                        ];
                        $detail_2 = model_ts_layanan_detail::create($ts_layanan_detail_2);
                        model_tabel_obat_reguler::where('id', $DATA_OBAT_LOCAL->id)->update([
                            'status' => 'TERKIRIM',
                            'pic' => auth()->user()->id,
                            'id_resep_header' => $IDRESEPJADI->id,
                            'tgl_resep' => $this->get_now(),
                            'id_layanan_detail' => $detail->id
                        ]);
                        $totalheader = $totalheader + $grandtotal;
                        $stokTerakhir = DB::table('ti_kartu_stok')
                            ->where('kode_barang', $a['kode_barang'])
                            ->where('kode_unit', $kodeunit) // Unit Apotek
                            ->orderBy('no', 'desc')
                            ->first();
                        $saldoStok = $stokTerakhir ? $stokTerakhir->stok_current : 0;
                        if ($saldoStok < $a['qtybeli']) {
                            // Siapkan data untuk rollback BPJS
                            $dataError = [
                                'message_PRB' => "Stok barang " . $a['namabarang'] . " tidak cukup. Sisa stok: " . $saldoStok,
                                'noApotik_PRB' => $response_data->response->noApotik,
                                'noSep_Kunjungan_PRB' => $response_data->response->noSep_Kunjungan,
                                'noResep_PRB' => $response_data->response->noResep
                            ];
                            throw new \Exception(json_encode($dataError));
                        } else {
                            try {
                                $stok_current = $stokTerakhir->stok_current - $a['qtybeli'];
                                $data_ti_kartu_stok = [
                                    'no_dokumen' => $kode_layanan_header,
                                    'no_dokumen_detail' => $kode_detail_obat,
                                    'tgl_stok' => $this->get_now(),
                                    'kode_unit' => auth()->user()->unit,
                                    'kode_barang' => $a['kode_barang'],
                                    'stok_last' => $stokTerakhir->stok_current,
                                    'stok_out' => $a['qtybeli'],
                                    'stok_current' => $stok_current,
                                    'harga_beli' => $mt_barang[0]->hna,
                                    'act' => '1',
                                    'act_ed' => '1',
                                    // 'input_by' => auth()->user()->id,
                                    'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                                ];
                                $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
                            } catch (\Exception $e) {
                                $dataError = [
                                    'message_PRB' => "ERROR SYSTEM :" . $e->getMessage(),
                                    'noApotik_PRB' => $response_data->response->noApotik,
                                    'noSep_Kunjungan_PRB' => $response_data->response->noSep_Kunjungan,
                                    'noResep_PRB' => $response_data->response->noResep
                                ];
                                throw new \Exception(json_encode($dataError));
                            }
                        }
                    } catch (\Exception $e) {
                        $dataError = [
                            'message_PRB' => "ERROR SYSTEM :" . $e->getMessage(),
                            'noApotik_PRB' => $response_data->response->noApotik,
                            'noSep_Kunjungan_PRB' => $response_data->response->noSep_Kunjungan,
                            'noResep_PRB' => $response_data->response->noResep
                        ];
                        throw new \Exception(json_encode($dataError));
                    }
                } else {
                    $dataError = [
                        'message_PRB' => "Gagal kirim obat {$a['namabarang']}: " . $response_data_obat->metaData->message,
                        'noApotik_PRB' => $response_data->response->noApotik,
                        'noSep_Kunjungan_PRB' => $response_data->response->noSep_Kunjungan,
                        'noResep_PRB' => $response_data->response->noResep
                    ];

                    // Throw exception dengan JSON string dari array tersebut
                    throw new \Exception(json_encode($dataError));
                }
            }
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagian_penjamin_head = $jsf[0]->jasa_baca;
                $tagian_pribadi_head = 0;
            } else {
                $tagian_penjamin_head = 0;
                $tagian_pribadi_head = $jsf[0]->jasa_baca;
            }
            $ts_layanan_detail3 = [
                'id_layanan_detail' => $this->createLayanandetail(),
                'kode_layanan_header' => $kode_layanan_header,
                'kode_tarif_detail' => 'TX23523',
                'total_tarif' => $jsf[0]->jasa_baca,
                'diskon_layanan' => '0',
                'jumlah_layanan' => 1,
                'total_layanan' => $jsf[0]->jasa_baca,
                'grantotal_layanan' => $jsf[0]->jasa_baca,
                'status_layanan_detail' => 'OPN',
                'tgl_layanan_detail' => $now,
                'kategori_resep' => $kat_resep,
                'satuan_barang' => '-',
                'tagihan_pribadi' => $tagian_pribadi_head,
                'tagihan_penjamin' => $tagian_penjamin_head,
                'tipe_anestesi' => 81,
                'tgl_layanan_detail_2' => $now,
                'row_id_header' => $lyheader->id,
            ];
            $detail3 = model_ts_layanan_detail::create($ts_layanan_detail3);
            $totalheader = $totalheader + $jsf[0]->jasa_baca;
            if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                $tagihan_penjamin_header = $totalheader;
                $tagihan_pribadi_header = '0';
                $status_layanan = 2;
            } else {
                $tagihan_penjamin_header = '0';
                $tagihan_pribadi_header = $totalheader;
                $status_layanan = 1;
            }
            foreach ($dataobat as $a) {
                try {
                } catch (\Exception $e) {
                    return $dataError = [
                        'message_PRB' => $e->getMessage(),
                        'noApotik_PRB' => $response_data->response->noApotik,
                        'noSep_Kunjungan_PRB' => $response_data->response->noSep_Kunjungan,
                        'noResep_PRB' => $response_data->response->noResep
                    ];
                    throw new \Exception("error sistem : " . json_encode($dataError));
                }
            }
            model_ts_layanan_header::where('id', $lyheader->id)
                ->update(['status_layanan' => $status_layanan, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
            return $dataError = [
                'message_PRB' => 'SUKSES',
                'noApotik_PRB' => $response_data->response->noApotik,
                'noSep_Kunjungan_PRB' => $response_data->response->noSep_Kunjungan,
                'noResep_PRB' => $response_data->response->noResep
            ];
        } else {
            throw new \Exception("Gagal kirim header resep ke BPJS: " . $response_data->metaData->message);
        }
    }
    public function prosesResepReguler($dataobat, $data_kunjungan, $v, $kodeunit, $unit, $unit_kunjungan, $data_paramedis, $kode_kunjungan)
    {
        $r = DB::select("CALL GET_NOMOR_LAYANAN_HEADER('$kodeunit')");
        $PENJAMIN = $data_kunjungan[0]->kode_penjamin;
        $jsf = DB::select('select * from mt_jasa_farmasi');
        if ($PENJAMIN == 'P01') {
            $kat_resep = 'Resep Tunai';
            $tipe_tx = '1';
        } else {
            $kat_resep = 'Resep Kredit';
            $tipe_tx = '2';
        }
        $kode_layanan_header = $r[0]->no_trx_layanan;
        if ($kode_layanan_header == "") {
            $year = date('y');
            $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
            DB::select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d H:i:s'), $kode_layanan_header, $kodeunit]);
        }
        $cek_resep_ke = db::select('select id from ts_layanan_header where kode_kunjungan = ? and kode_unit = ? and status_layanan != 3', [$kode_kunjungan, $kodeunit]);
        if (count($cek_resep_ke) == 0) {
            $urutan = 1;
        } else {
            $s =  count($cek_resep_ke);
            $urutan = $s + 1;
        }
        $data_layanan_header = [
            'kode_layanan_header' => $kode_layanan_header,
            'tgl_entry' => $this->get_now(),
            'kode_kunjungan' => $kode_kunjungan,
            'kode_unit' => auth()->user()->unit,
            'kode_tipe_transaksi' => $tipe_tx,
            'pic' => auth()->user()->id,
            'status_layanan' => '3',
            'keterangan' => 'Resep Ke :' . $urutan,
            'total_layanan' => '0',
            'status_retur' => '0',
            'kode_penjaminx' => $data_kunjungan[0]->kode_penjamin,
            'tagihan_pribadi' => 0,
            'tagihan_penjamin' => 0,
            'status_pembayaran' => 'OPN',
            'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
            'unit_pengirim' => $data_kunjungan[0]->kode_unit . ' | ' . $data_kunjungan[0]->nama_unit,
            'diagnosa' => $data_kunjungan[0]->diagx,
        ];
        $lyheader = model_ts_layanan_header::create($data_layanan_header);
        $now = $this->get_now();
        $totalheader = 0;
        foreach ($dataobat as $a) {
            $kode_detail_obat = $this->createLayanandetail();
            if ($a['tipe'] != 'RACIKAN') {
                $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang']]);
                $total = $mt_barang[0]->harga_jual * $a['qtybeli'];
                $diskon = 0;
                $hitung = $diskon / 100 * $total;
                $grandtotal = $total - $hitung + 1200 + 500;
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = $grandtotal;
                } else {
                    $tagihan_pribadi = $grandtotal;
                    $tagihan_penjamin = 0;
                }
                $ts_layanan_detail = [
                    'id_layanan_detail' => $kode_detail_obat,
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => '0',
                    'total_tarif' => $mt_barang[0]->harga_jual,
                    'jumlah_layanan' => $a['qtybeli'],
                    'total_layanan' => $total,
                    'diskon_layanan' => '0',
                    'grantotal_layanan' => $grandtotal,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'kode_barang' => $a['kode_barang'],
                    'aturan_pakai' => $a['aturan_pakai'],
                    'kategori_resep' => $kat_resep,
                    'satuan_barang' => $mt_barang[0]->satuan,
                    'tipe_anestesi' => 80,
                    'tagihan_pribadi' => $tagihan_pribadi,
                    'tagihan_penjamin' =>  $tagihan_penjamin,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $lyheader->id,
                ];
                $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi_js = 0;
                    $tagihan_penjamin_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                } else {
                    $tagihan_pribadi_js = $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                    $tagihan_penjamin_js = 0;
                }
                $ts_layanan_detail_2 = [
                    'id_layanan_detail' => $this->createLayanandetail(),
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => 'TX23513',
                    'total_tarif' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'jumlah_layanan' => 1,
                    'total_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'diskon_layanan' => '0',
                    'grantotal_layanan' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'kategori_resep' => $kat_resep,
                    'satuan_barang' => '-',
                    'tagihan_pribadi' => $tagihan_pribadi_js,
                    'tagihan_penjamin' => $tagihan_penjamin_js,
                    'tipe_anestesi' => 80,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $lyheader->id,
                ];
                $detail_2 = model_ts_layanan_detail::create($ts_layanan_detail_2);
                $totalheader = $totalheader + $grandtotal;
                $stokTerakhir = DB::table('ti_kartu_stok')
                    ->where('kode_barang', $a['kode_barang'])
                    ->where('kode_unit', $kodeunit) // Unit Apotek
                    ->orderBy('no', 'desc')
                    ->first();

                $saldoStok = $stokTerakhir ? $stokTerakhir->stok_current : 0;
                if ($saldoStok < $a['qtybeli']) {
                    throw new \Exception("Stok barang " . $a['namabarang'] . " tidak cukup. Sisa stok: " . $saldoStok);
                } else {
                    $stok_current = $stokTerakhir->stok_current - $a['qtybeli'];
                    $data_ti_kartu_stok = [
                        'no_dokumen' => $kode_layanan_header,
                        'no_dokumen_detail' => $kode_detail_obat,
                        'tgl_stok' => $this->get_now(),
                        'kode_unit' => auth()->user()->unit,
                        'kode_barang' => $a['kode_barang'],
                        'stok_last' => $stokTerakhir->stok_current,
                        'stok_out' => $a['qtybeli'],
                        'stok_current' => $stok_current,
                        'harga_beli' => $mt_barang[0]->hna,
                        'act' => '1',
                        'act_ed' => '1',
                        // 'input_by' => auth()->user()->id,
                        'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                    ];
                    $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
                }
            } else {
                //PROSES OBAT RACIKAN
                $racikandetail = db::select('select * from template_racikan_detail where id_header = ?', [$a['kode_barang']]);
                $racikan = db::select('select * from template_racikan_header where id =?', [$a['kode_barang']]);
                //proses mt_racikan dulu
                if ($racikan[0]->sediaan == 1) {
                    $kemasan = 'KAPSUL';
                    $tiperacik = 'NS';
                    $harga = '700';
                } elseif ($racikan[0]->sediaan == 2) {
                    $kemasan = 'KERTAS';
                    $tiperacik = 'NS';
                    $harga = '700';
                } else {
                    $kemasan = 'POT SALEP';
                    $tiperacik = 'S';
                    $harga = 10000;
                }
                $kode_racik = $this->get_kode_racik();
                $data_mt_racikan_header = [
                    'kode_racik' => $kode_racik,
                    'tgl_racik' => $this->get_now(),
                    'nama_racik' => $racikan[0]->namaracikan,
                    'total_racik' => 0,
                    'tipe_racik' => $tiperacik,
                    'qty_racik' => $racikan[0]->qtyracikan,
                    'kemasan' => $kemasan,
                    'hrg_kemasan' => $harga,
                ];
                $mt_racikan_header = model_mt_racikan::create($data_mt_racikan_header);
                $total_racik = 0;
                foreach ($racikandetail as $dd) {
                    $mt_barang = db::select('select * from mt_barang where kode_barang = ?', [$dd->kode_barang]);
                    $totalbarang = $mt_barang[0]->harga_jual + $dd->qty_barang;
                    $tt = $totalbarang + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase;
                    $mt_racikan_detail_1 = [
                        'kode_racik' => $kode_racik,
                        'kode_barang' => $dd->kode_barang,
                        'qty_barang' => $dd->qty_barang,
                        'satuan_barang' => $mt_barang[0]->satuan,
                        'harga_satuan_barang' => $mt_barang[0]->harga_jual,
                        'subtotal_barang' => $totalbarang,
                        'grantotal_barang' => $totalbarang + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                        'harga_brg_embalase' => $totalbarang + $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                        'qty_order' => $dd->qty_barang,
                    ];
                    $save_mt_racikan_detail_1 = model_mt_racikan_detail::create($mt_racikan_detail_1);
                    $mt_racikan_detail_2 = [
                        'kode_racik' => $kode_racik,
                        'kode_barang' => 'TX23513',
                        'qty_barang' => 1,
                        'satuan_barang' => '-',
                        'harga_satuan_barang' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                        'subtotal_barang' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                        'grantotal_barang' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                        'harga_brg_embalase' => $jsf[0]->jasa_resep + $jsf[0]->jasa_embalase,
                        'qty_order' => 1,
                    ];
                    $save_mt_racikan_detail_2 = model_mt_racikan_detail::create($mt_racikan_detail_2);
                    $total_racik = $total_racik + $tt;
                    $get_barang = db::select('select kode_obat_bpjs,nama_generik from master_barang_x_master_obat_bpjs where kode_barang = ?', [$dd->kode_barang]);
                    // $data_obat_racik = [
                    //     "NOSJP" => $response_data->response->noApotik,
                    //     "NORESEP" => $nomor_resep,
                    //     "JNSROBT" => "R.01",
                    //     "KDOBT" => $get_barang[0]->kode_obat_bpjs,
                    //     "NMOBAT" => $get_barang[0]->nama_generik,
                    //     "SIGNA1OBT" => $a['signa1'],
                    //     "SIGNA2OBT" => $a['signa2'],
                    //     "PERMINTAAN" => $dd->dosis_racik,
                    //     "JMLOBT" => $dd->qty_barang,
                    //     "JHO" => $racikan[0]->qtyracikan,
                    //     "CatKhsObt" => $racikan[0]->namaracikan,
                    // ];
                    // $DATA_OBAT_LOCAL = model_tabel_obat_racikan::create($data_obat_racik);
                    // Kirim ke BPJS
                    // $response_data_obat = $v->save_racikan($data_obat_racik);
                    // if ($response_data_obat->metaData->code == 200) {
                    try {
                        // model_tabel_obat_racikan::where('id', $DATA_OBAT_LOCAL->id)->update([
                        //     'status' => 'TERKIRIM',
                        //     'pic' => auth()->user()->id,
                        //     'id_resep_header' => $IDRESEPJADI->id,
                        //     'tgl_resep' => $this->get_now(),
                        //     'id_layanan_detail' => $save_mt_racikan_detail_1->id
                        // ]);
                        $stokTerakhir = DB::table('ti_kartu_stok')
                            ->where('kode_barang', $dd->kode_barang)
                            ->where('kode_unit', $kodeunit) // Unit Apotek
                            ->orderBy('no', 'desc')
                            ->first();
                        $saldoStok = $stokTerakhir ? $stokTerakhir->stok_current : 0;
                        if ($saldoStok <  $dd->qty_barang) {
                            // Siapkan data untuk rollback BPJS
                            $dataError = [
                                'message' => "Stok barang " . $mt_barang[0]->nama_barang . " tidak cukup. Sisa stok: " . $saldoStok,
                                'noApotik' => '',
                                'noSep_Kunjungan' => '',
                                'noResep' => ''
                            ];
                            throw new \Exception(json_encode($dataError));
                        } else {
                            try {
                                $stok_current = $stokTerakhir->stok_current - $dd->qty_barang;
                                $data_ti_kartu_stok = [
                                    'no_dokumen' => $kode_layanan_header,
                                    'no_dokumen_detail' => $kode_racik,
                                    'tgl_stok' => $this->get_now(),
                                    'kode_unit' => auth()->user()->unit,
                                    'kode_barang' => $dd->kode_barang,
                                    'stok_last' => $stokTerakhir->stok_current,
                                    'stok_out' => $dd->qty_barang,
                                    'stok_current' => $stok_current,
                                    'harga_beli' => $mt_barang[0]->hna,
                                    'act' => '1',
                                    'act_ed' => '1',
                                    // 'input_by' => auth()->user()->id,
                                    'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
                                ];
                                $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
                            } catch (\Exception $e) {
                                $dataError = [
                                    'message' => "ERROR SYSTEM :" . $e->getMessage(),
                                    'noApotik' => '',
                                    'noSep_Kunjungan' => '',
                                    'noResep' => ''
                                ];
                                throw new \Exception(json_encode($dataError));
                            }
                        }
                    } catch (\Exception $e) {
                        // $dataError = [
                        //     'message' => "ERROR SYSTEM :" . $e->getMessage(),
                        //     'noApotik' => $response_data->response->noApotik,
                        //     'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                        //     'noResep' => $response_data->response->noResep
                        // ];
                        // throw new \Exception(json_encode($dataError));
                    }
                    // } else {
                    //     $dataError = [
                    //         'message' => "Gagal kirim obat Kronis {$a['namabarang']}: " . $response_data_obat->metaData->message,
                    //         'noApotik' => $response_data->response->noApotik,
                    //         'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                    //         'noResep' => $response_data->response->noResep
                    //     ];
                    //     // Throw exception dengan JSON string dari array tersebut
                    //     throw new \Exception(json_encode($dataError));
                    // }
                }
                model_mt_racikan::where('id', $mt_racikan_header->id)->update(['total_racik' => $total_racik]);
                $kode_detail_obat = $this->createLayanandetail();
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi = 0;
                    $tagihan_penjamin = $total_racik;
                } else {
                    $tagihan_pribadi = $total_racik;
                    $tagihan_penjamin = 0;
                }
                $grandtotal = $total_racik;
                $ts_layanan_detail = [
                    'id_layanan_detail' => $kode_detail_obat,
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => '0',
                    'total_tarif' => $total_racik,
                    'jumlah_layanan' =>  $a['qtybeli'],
                    'total_layanan' => $total_racik,
                    'diskon_layanan' => '0',
                    'grantotal_layanan' => $total_racik,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'kode_barang' => $kode_racik,
                    'aturan_pakai' => $a['aturan_pakai'],
                    'kategori_resep' => $kat_resep,
                    'satuan_barang' => '-',
                    'tipe_anestesi' => 80,
                    'tagihan_pribadi' => $tagihan_pribadi,
                    'tagihan_penjamin' =>  $tagihan_penjamin,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $lyheader->id,
                ];
                $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                if ($tiperacik == 'NS') {
                    $HARGA = $jsf[0]->jasa_racikan_powder;
                    $jumlahl = $a['qtybeli'] * $HARGA;
                    $jumlah = $a['qtybeli'];
                } else {
                    $HARGA = $jsf[0]->jasa_racikan_salep;
                    $jumlah = 1;
                    $jumlahl = $HARGA;
                }
                if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                    $tagihan_pribadi_js = 0;
                    $tagihan_penjamin_js = $jumlahl;
                } else {
                    $tagihan_pribadi_js = $jumlahl;
                    $tagihan_penjamin_js = 0;
                }
                $ts_layanan_detail_2 = [
                    'id_layanan_detail' => $this->createLayanandetail(),
                    'kode_layanan_header' => $kode_layanan_header,
                    'kode_tarif_detail' => 'TX23513',
                    'total_tarif' => $HARGA,
                    'jumlah_layanan' => $jumlah,
                    'total_layanan' => $jumlahl,
                    'diskon_layanan' => '0',
                    'grantotal_layanan' => $jumlahl,
                    'status_layanan_detail' => 'OPN',
                    'tgl_layanan_detail' => $now,
                    'kategori_resep' => $kat_resep,
                    'satuan_barang' => '-',
                    'tagihan_pribadi' => $tagihan_pribadi_js,
                    'tagihan_penjamin' => $tagihan_penjamin_js,
                    'tipe_anestesi' => 80,
                    'tgl_layanan_detail_2' => $now,
                    'row_id_header' => $lyheader->id,
                ];
                $detail_2 = model_ts_layanan_detail::create($ts_layanan_detail_2);
                $totalheader = $totalheader + $grandtotal;
            }
        }
        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
            $tagian_penjamin_head = $jsf[0]->jasa_baca;
            $tagian_pribadi_head = 0;
        } else {
            $tagian_penjamin_head = 0;
            $tagian_pribadi_head = $jsf[0]->jasa_baca;
        }
        $ts_layanan_detail3 = [
            'id_layanan_detail' => $this->createLayanandetail(),
            'kode_layanan_header' => $kode_layanan_header,
            'kode_tarif_detail' => 'TX23523',
            'total_tarif' => $jsf[0]->jasa_baca,
            'diskon_layanan' => '0',
            'jumlah_layanan' => 1,
            'total_layanan' => $jsf[0]->jasa_baca,
            'grantotal_layanan' => $jsf[0]->jasa_baca,
            'status_layanan_detail' => 'OPN',
            'tgl_layanan_detail' => $now,
            'kategori_resep' => $kat_resep,
            'satuan_barang' => '-',
            'tagihan_pribadi' => $tagian_pribadi_head,
            'tagihan_penjamin' => $tagian_penjamin_head,
            'tipe_anestesi' => 80,
            'tgl_layanan_detail_2' => $now,
            'row_id_header' => $lyheader->id,
        ];
        $detail3 = model_ts_layanan_detail::create($ts_layanan_detail3);
        $totalheader = $totalheader + $jsf[0]->jasa_baca;
        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
            $tagihan_penjamin_header = $totalheader;
            $tagihan_pribadi_header = '0';
            $status_layanan = 2;
        } else {
            $tagihan_penjamin_header = '0';
            $tagihan_pribadi_header = $totalheader;
            $status_layanan = 1;
        }
        model_ts_layanan_header::where('id', $lyheader->id)
            ->update(['status_layanan' => $status_layanan, 'total_layanan' => $totalheader, 'tagihan_penjamin' => $tagihan_penjamin_header, 'tagihan_pribadi' => $tagihan_pribadi_header]);
    }
    public function proseskomponenracik(Request $request)
    {
        $dataheader = json_decode($_POST['dataheader'], true);
        $datakomponen = json_decode($_POST['datakomponen'], true);
        $kode_kunjungan = $request->kode_kunjungan;
        $data_kunjungan = db::select('select * from ts_kunjungan where kode_kunjungan = ?', [$kode_kunjungan]);
        $kode_penjamin = DB::table('ts_kunjungan')
            ->where('kode_kunjungan', $kode_kunjungan)
            ->value('kode_penjamin');
        foreach ($dataheader as $nama) {
            $index_header = $nama['name'];
            $value = $nama['value'];
            $dataHeader[$index_header] = $value;
        }
        foreach ($datakomponen as $nama_komponen) {
            $index_komponen = $nama_komponen['name'];
            $value_komponen = $nama_komponen['value'];
            $dataKomponen[$index_komponen] = $value_komponen;
        }
        // dd($dataHeader);
        if ($dataHeader['namaracikan'] == '') {
            $response = [
                'status' => 'error',
                'message' => 'Nama racikan wajib diisi ...'
            ];
            return response()->json($response);
            die;
        }
        if ($dataHeader['sediaan'] == '0') {
            $response = [
                'status' => 'error',
                'message' => 'Sediaan belum dipilih ...'
            ];
            return response()->json($response);
            die;
        }
        if ($dataHeader['qtyracikan'] == '0' || $dataHeader['qtyracikan'] == '') {
            $response = [
                'status' => 'error',
                'message' => 'QTY racikan tidak boleh kosong ...'
            ];
            return response()->json($response);
            die;
        }
        if ($dataKomponen['komponen_kodebarang'] == '') {
            $response = [
                'status' => 'error',
                'message' => 'Tidak ada obat yang dipilih ...'
            ];
            return response()->json($response);
            die;
        }
        $cleanJumlah = filter_var(str_replace(',', '.', $dataKomponen['komponen_dosisracik']), FILTER_VALIDATE_FLOAT);
        if ($cleanJumlah === false) {
            $response = [
                'status' => 'error',
                'message' => 'Input yang dimasukkan bukan angka valid.'
            ];
            return response()->json($response);
            die;
        }
        if ($dataKomponen['komponen_dosisracik'] == '' || $dataKomponen['komponen_dosisracik'] == '0') {
            $response = [
                'status' => 'error',
                'message' => 'Isi dosis racik yang dibutuhkan ...'
            ];
            return response()->json($response);
            die;
        }
        if ($dataKomponen['komponen_dosis'] == '' || $dataKomponen['komponen_dosis'] == '0') {
            $response = [
                'status' => 'error',
                'message' => 'Isi dosis awal obat ...'
            ];
            return response()->json($response);
            die;
        }
        if ($kode_penjamin != 'P01') {
            $get_barang = db::select('select kode_obat_bpjs from master_barang_x_master_obat_bpjs where kode_barang = ?', [$dataKomponen['komponen_kodebarang']]);
            if (count($get_barang) == 0) {
                // $response = [
                //     'status' => 'error',
                //     'message' => 'PASIEN BPJS , Obat ' . $dataKomponen['komponen_namabarang'] . ' Belum mempunyai kode barang BPJS untuk keperluan briding apotek online, silahkan lakukan mapping master barang  ...',
                //     'data' => [
                //         'nama_barang' => $dataKomponen['komponen_namabarang'] ?? 'Tanpa Nama',
                //         'kode_barang' => $dataKomponen['komponen_kodebarang'] ?? 'Tanpa Nama',
                //         'satuan_barang' => $dataKomponen['komponen_satuanbarang'] ?? 'Tanpa Nama',
                //         'dosis_awal' => $dataKomponen['komponen_dosis'] ?? 'Tanpa Nama',
                //         'dosis_racik' => $dataKomponen['komponen_dosisracik'] ?? 'Tanpa Nama',
                //         'jumlah' => $kebutuhan ?? 0,
                //         'stok_current' => 0,
                //         // Tambahkan field lain yang ingin ditampilkan di view
                //     ]
                // ];
                // return response()->json($response);
                // die;
            }
        }
        $stok = DB::table('ti_kartu_stok')
            ->where('kode_barang', $dataKomponen['komponen_kodebarang'])
            ->where('kode_unit', auth()->user()->unit)
            ->orderBy('no', 'desc')
            ->value('stok_current');
        $dosis_diminta = $dataKomponen['komponen_dosisracik'];
        $jumlah_racikan = $dataHeader['qtyracikan'];
        $stok_mg = $dataKomponen['komponen_dosis'];
        $ss = ($dosis_diminta * $jumlah_racikan) / $stok_mg;
        $kebutuhan = round($ss * 2) / 2;
        $sisa_stok = $stok - $kebutuhan;
        if (!$sisa_stok || $sisa_stok < 0) {
            $response = [
                'status' => 'error',
                'message' => 'Stok tidak cukup !',
                'data' => [
                    'nama_barang' => $dataKomponen['komponen_namabarang'] ?? 'Tanpa Nama',
                    'kode_barang' => $dataKomponen['komponen_kodebarang'] ?? 'Tanpa Nama',
                    'satuan_barang' => $dataKomponen['komponen_satuanbarang'] ?? 'Tanpa Nama',
                    'dosis_awal' => $dataKomponen['komponen_dosis'] ?? 'Tanpa Nama',
                    'dosis_racik' => $dataKomponen['komponen_dosisracik'] ?? 'Tanpa Nama',
                    'jumlah' => $kebutuhan ?? 0,
                    'stok_current' => 0,
                    // Tambahkan field lain yang ingin ditampilkan di view
                ]
            ];
        } else {
            $response = [
                'status' => 'success',
                'message' => 'Obat berhasil ditambahkan ke daftar!',
                'data' => [
                    'nama_barang' => $dataKomponen['komponen_namabarang'] ?? 'Tanpa Nama',
                    'kode_barang' => $dataKomponen['komponen_kodebarang'] ?? 'Tanpa Nama',
                    'satuan_barang' => $dataKomponen['komponen_satuanbarang'] ?? 'Tanpa Nama',
                    'dosis_awal' => $dataKomponen['komponen_dosis'] ?? 'Tanpa Nama',
                    'dosis_racik' => $dataKomponen['komponen_dosisracik'] ?? 'Tanpa Nama',
                    'jumlah' => $kebutuhan ?? 0,
                    'stok_current' => $stok ?? 0,
                    // Tambahkan field lain yang ingin ditampilkan di view
                ]
            ];
        }
        return response()->json($response);
    }
    public function simpanobatracikan(Request $request)
    {
        $dataheader = json_decode($_POST['dataheader'], true);
        $datakomponen = json_decode($_POST['datakomponen'], true);
        $kode_kunjungan = $request->kode_kunjungan;
        $data_kunjungan = db::select('select * from ts_kunjungan where kode_kunjungan = ?', [$kode_kunjungan]);
        $kode_penjamin = DB::table('ts_kunjungan')
            ->where('kode_kunjungan', $kode_kunjungan)
            ->value('kode_penjamin');
        foreach ($dataheader as $nama) {
            $index_header = $nama['name'];
            $value = $nama['value'];
            $dataHeader[$index_header] = $value;
        }
        foreach ($datakomponen as $nama_komponen) {
            $index_komponen = $nama_komponen['name'];
            $value_komponen = $nama_komponen['value'];
            $dataKomponen[$index_komponen] = $value_komponen;
            if ($index_komponen == 'list_dosis_racik_barang') {
                $arrayKomponenObat[] = $dataKomponen;
            }
        }
        $header = [
            'namaracikan' => $dataHeader['namaracikan'],
            'tiperacikan' => $dataHeader['tiperacikan'],
            'sediaan' => $dataHeader['sediaan'],
            'qtyracikan' => $dataHeader['qtyracikan'],
            'aturanpakai' => $dataHeader['aturanpakai'],
            'unit_layanan' => auth()->user()->unit,
            'unit_kirim' => $data_kunjungan[0]->kode_unit,
            'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
            'pic' => auth()->user()->id
        ];
        $h = model_template_racikan::create($header);
        foreach ($arrayKomponenObat as $d) {
            $detail = [
                'id_header' => $h->id,
                'kode_barang' => $d['list_kode_barang'],
                'qty_barang' => $d['list_qty_barang'],
                'dosis_awal' => $d['list_dosis_barang'],
                'dosis_racik' => $d['list_dosis_racik_barang'],
            ];
            model_template_racikan_detail::create($detail);
        }
        return response()->json([
            'kode' => 200,
            'message' => 'Obat racikan berhasil disimpan ...'
        ], 200);
    }
    public function simpanresep(Request $request)
    {

        // ... dalam fungsi Anda ...

        $jenisobat = $request->jenisobat;
        $iterasi = $request->iterasi;
        $kode_kunjungan = $request->kode_kunjungan;
        if ($jenisobat == 0) {
            return response()->json(['kode' => 500, 'message' => 'jenis obat belum dipilih ...'], 200);
            die;
        }
        if ($iterasi == '-') {
            return response()->json(['kode' => 500, 'message' => 'iterasi obat belum dipilih ...'], 200);
            die;
        }
        // Gunakan Transaksi Database agar jika satu gagal, semua dibatalkan
        DB::beginTransaction();

        try {
            $data_kunjungan = db::select('select *,fc_nama_unit1(kode_unit) as nama_unit from ts_kunjungan where kode_kunjungan = ?', [$kode_kunjungan]);
            if (empty($data_kunjungan)) {
                throw new \Exception("Data kunjungan tidak ditemukan.");
            }

            $mt_pasien = db::select('select * from mt_pasien where no_rm = ?', [$data_kunjungan[0]->no_rm]);
            $data = json_decode($_POST['data'], true);

            $arrayobat = [];
            foreach ($data as $nama3) {
                $index3 = $nama3['name'];
                $value3 = $nama3['value'];
                $dataSet3[$index3] = $value3;
                if ($index3 == 'aturan_pakai') {
                    $arrayobat[] = $dataSet3;
                }
            }

            if (empty($arrayobat)) {
                throw new \Exception("Daftar obat tidak boleh kosong.");
            }
            //create kode_layanan_header
            $kodeunit = auth()->user()->unit;
            $unit = db::select('select * from mt_unit where kode_unit =?', [$kodeunit]);
            $unit_kunjungan = db::select('select * from mt_unit where kode_unit =?', [$data_kunjungan[0]->kode_unit]);
            $data_paramedis = db::select('select * from mt_paramedis where kode_paramedis =?', [$data_kunjungan[0]->kode_paramedis]);

            $r = DB::select("CALL GET_NOMOR_LAYANAN_HEADER('$kodeunit')");
            $kode_layanan_header = $r[0]->no_trx_layanan;
            if ($kode_layanan_header == "") {
                $year = date('y');
                $kode_layanan_header = $unit[0]->prefix_unit . $year . date('m') . date('d') . '000001';
                DB::select('insert into mt_nomor_trx (tgl,no_trx_layanan,unit) values (?,?,?)', [date('Y-m-d H:i:s'), $kode_layanan_header, $kodeunit]);
            }

            //insert data layanan header simrs
            $data_layanan_header = [
                'kode_layanan_header' => $kode_layanan_header,
                'tgl_entry' => $this->get_now(),
                'kode_kunjungan' => $kode_kunjungan,
                'kode_unit' => auth()->user()->unit,
                'kode_tipe_transaksi' => '2',
                'pic' => auth()->user()->id,
                'status_layanan' => '3',
                'keterangan' => 'PENDING',
                'total_layanan' => '0',
                'status_retur' => '0',
                'kode_penjaminx' => $data_kunjungan[0]->kode_penjamin,
                'tagihan_pribadi' => 0,
                'tagihan_penjamin' => 0,
                'status_pembayaran' => 'OPN',
                'dok_kirim' => $data_kunjungan[0]->kode_paramedis,
                'unit_pengirim' => $data_kunjungan[0]->kode_unit . ' | ' . $data_kunjungan[0]->nama_unit,
                'diagnosa' => $data_kunjungan[0]->diagx,
            ];
            $lyheader = model_ts_layanan_header::create($data_layanan_header);
            $nomor_resep = $this->create_nomor_resep();



            $data_resep = [
                "TGLSJP" => $this->get_now(),
                "REFASALSJP" => $data_kunjungan[0]->no_sep,
                "POLIRSP" => $unit_kunjungan[0]->KDPOLI,
                "KDJNSOBAT" => $jenisobat,
                "NORESEP" => $nomor_resep,
                "IDUSERSJP" => 'USR-1',
                "TGLRSP" => $this->get_now(),
                "TGLPELRSP" => $this->get_now(),
                "KdDokter" => $data_paramedis[0]->kode_dokter_jkn,
                "iterasi" => $iterasi
            ];
            $data_resep_kirim = model_tabel_resep_kirim::create($data_resep);
            $id_resep_kirim = $data_resep_kirim->id;
            $v = new MODEL_APOTEK_ONLINE();
            // KIRIM HEADER RESEP KE BPJS
            $response_data = $v->simpan_resep($data_resep);
            if ($response_data->metaData->code == 200) {
                $data_save = [
                    'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                    'noKartu' => $response_data->response->noKartu,
                    'nama' => $response_data->response->nama,
                    'faskesAsal' => $response_data->response->faskesAsal,
                    'noApotik' => $response_data->response->noApotik,
                    'noResep' => $response_data->response->noResep,
                    'tglResep' => $response_data->response->tglResep,
                    'kdJnsObat' => $response_data->response->kdJnsObat,
                    'tglEntry' => $response_data->response->tglEntry,
                    'pic' => auth()->user()->id,
                    'status' => $response_data->metaData->code,
                    'message' => $response_data->metaData->message,
                    'id_resep_kirim' => $id_resep_kirim
                ];
                $IDRESEPJADI = model_resep_obat::create($data_save);
                // Tandai resep lokal sudah terkirim header-nya
                model_tabel_resep_kirim::where('id', $id_resep_kirim)->update(['status_terkirim' => 'TERKIRIM', 'id_layanan_header' => $lyheader->id]);
                // KIRIM DETAIL OBAT SATU PER SATU
                $now = $this->get_now();
                foreach ($arrayobat as $a) {
                    $data_obat_reguler = [
                        "NOSJP" => $response_data->response->noApotik,
                        "NORESEP" => $nomor_resep,
                        "KDOBT" => $a['kodebpjs'],
                        "NMOBAT" => $a['nama_generik'],
                        "SIGNA1OBT" => $a['signa1'],
                        "SIGNA2OBT" => $a['signa2'],
                        "JMLOBT" => $a['qtybeli'],
                        "JHO" => $a['qtybeli'],
                        "CatKhsObt" => "TEST",
                    ];
                    // Simpan lokal dulu
                    $DATA_OBAT_LOCAL = model_tabel_obat_reguler::create($data_obat_reguler);
                    // Kirim ke BPJS
                    $response_data_obat = $v->save_non_racik($data_obat_reguler);
                    if ($response_data_obat->metaData->code == 200) {
                        //INSERT TS_LAYANAN_DETAIL
                        $kode_detail_obat = $this->createLayanandetail();
                        $mt_barang = DB::select('select * from mt_barang where kode_barang = ?', [$a['kode_barang']]);
                        $total = $mt_barang[0]->harga_jual * $a['qtybeli'];
                        $diskon = 0;
                        $hitung = $diskon / 100 * $total;
                        $grandtotal = $total - $hitung + 1200 + 500;
                        if ($data_kunjungan[0]->kode_penjamin != 'P01') {
                            $tagihan_pribadi = 0;
                            $tagihan_penjamin = $grandtotal;
                        } else {
                            $tagihan_pribadi = $grandtotal;
                            $tagihan_penjamin = 0;
                        }
                        $ts_layanan_detail = [
                            'id_layanan_detail' => $kode_detail_obat,
                            'kode_layanan_header' => $kode_layanan_header,
                            'kode_tarif_detail' => '0',
                            'total_tarif' => $mt_barang[0]->harga_jual,
                            'jumlah_layanan' => $a['qtybeli'],
                            'total_layanan' => $total,
                            'diskon_layanan' => '0',
                            'grantotal_layanan' => $grandtotal,
                            'status_layanan_detail' => 'OPN',
                            'tgl_layanan_detail' => $now,
                            'kode_barang' => $a['kode_barang'],
                            'aturan_pakai' => $a['aturan_pakai'],
                            'kategori_resep' => '0',
                            'satuan_barang' => $mt_barang[0]->satuan,
                            'tipe_anestesi' => '0',
                            'tagihan_pribadi' => $tagihan_pribadi,
                            'tagihan_penjamin' =>  $tagihan_penjamin,
                            'tgl_layanan_detail_2' => $now,
                            'row_id_header' => $lyheader->id,
                        ];
                        $detail = model_ts_layanan_detail::create($ts_layanan_detail);
                        model_tabel_obat_reguler::where('id', $DATA_OBAT_LOCAL->id)->update([
                            'status' => 'TERKIRIM',
                            'pic' => auth()->user()->id,
                            'id_resep_header' => $IDRESEPJADI->id,
                            'tgl_resep' => $this->get_now(),
                            'id_layanan_detail' => $detail->id
                        ]);
                    } else {
                        // JIKA OBAT GAGAL TERKIRIM, LEMPAR EXCEPTION UNTUK ROLLBACK
                        throw new \Exception("Gagal kirim obat {$a['nama_generik']}: " . $response_data_obat->metaData->message);
                    }
                }

                // Jika semua berhasil
                DB::commit();
                return response()->json(['kode' => 200, 'message' => 'Resep berhasil dikirim'], 200);
            } else {
                // JIKA HEADER RESEP GAGAL DIKIRIM KE BPJS
                throw new \Exception("Gagal kirim header resep ke BPJS: " . $response_data->metaData->message);
            }
        } catch (\Exception $e) {
            // ROLLBACK SEMUA PERUBAHAN DATABASE JIKA ADA ERROR
            DB::rollback();
            Log::error("Error Kirim Resep: " . $e->getMessage());
            // HAPUS DI BPJS JIKA HEADER SUDAH TERLANJUR TERKIRIM
            if (isset($response_data) && $response_data->metaData->code == 200) {
                $v->hapus_resep([
                    "nosjp" => $response_data->response->noApotik,
                    "refasalsjp" => $response_data->response->noSep_Kunjungan,
                    "noresep" => $response_data->response->noResep
                ]);
            }
            return response()->json([
                'kode' => 500,
                'message' => $e->getMessage()
            ], 200);
        }
    }
    public function indexcarisep()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexcarisep';
        // $data_resep = db::select('select * from apt_online_resep_obat');
        $data_resep = db::select('SELECT 
        c.`id`
        ,a.id AS id_resep_kirim
        ,a.`TGLSJP`
        ,a.`REFASALSJP` AS noSep_Kunjungan
        ,c.`noApotik`
        ,a.`status_terkirim` AS status_bridging
        -- ,b.`kode_kunjungan`
        ,c.`noKartu`
        ,c.`nama`
        ,c.`status` AS status_resep
        -- ,b.`kode_layanan_header`
        -- ,b.tgl_entry as tglEntry 
        ,c.tglResep 
        ,c.noResep  
        -- ,fc_nama_unit1(b.kode_unit) as nama_unit
        FROM apt_online_resep_kirim_obat a
        -- LEFT OUTER JOIN ts_layanan_header b ON a.`id_layanan_header` = b.id
        LEFT OUTER JOIN apt_online_resep_obat c ON a.`id` = c.`id_resep_kirim`');
        return view('Depofarmasi.indexcarisep', compact([
            'menu',
            'date_start',
            'date_end',
            'data_resep'
        ]));
    }
    public function indexriwayatretur()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $today = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexriwayatretur';
        $today = $this->get_date();
        return view('Depofarmasi.indexriwayatretur', compact([
            'menu',
            'today',
            'date_end'
        ]));
    }
    public function indexriwayatpelayanan()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexcarisep';
        $today = $this->get_date();
        return view('Depofarmasi.index_riwayat_pelayanan', compact([
            'menu',
            'date_start',
            'date_end',
            'today'
        ]));
    }
    public function indexriwayatkartustok()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexriwayatkartustok';
        $today = $this->get_date();
        $mt_unit = db::select('select * from mt_unit where kode_unit > 4000 and kode_unit < 4014');
        return view('Depofarmasi.index_riwayat_kartu_stok', compact([
            'menu',
            'date_start',
            'date_end',
            'today',
            'mt_unit'
        ]));
    }
    public function ambildatariwayatretur(Request $request)
    {
        $tglawal = $request->tglawal;
        $tglakhir = $request->tglakhir;
        $data = db::select('SELECT a.kode_kunjungan
        ,a.`no_rm`
        ,fc_nama_px(a.no_rm) AS nama_pasien
        ,b.`kode_retur_header`
        ,c.`kode_layanan_header`
        ,b.`tgl_retur`
        ,fc_nama_unit1(a.`kode_unit`) AS nama_unit_asal
        ,fc_nama_unit1(c.`kode_unit`) AS nama_unit
        ,b.`total_retur` 
        FROM ts_kunjungan a 
        INNER JOIN ts_retur_header b ON a.`kode_kunjungan` = b.`kode_kunjungan`
        INNER JOIN ts_layanan_header c ON b.`kode_layanan_header` = c.kode_layanan_header
        WHERE c.kode_unit = ? 
        AND DATE(b.tgl_retur) BETWEEN ? AND ? ORDER BY b.id DESC', [auth()->user()->unit, $tglawal, $tglakhir]);
        return view('Depofarmasi.tabel_riwayat_retur_pelayanan', compact([
            'data'
        ]));
    }
    public function ambildatariwayatpelayanan(Request $request)
    {
        // $data_resep = db::select('select * from apt_online_resep_obat');
        $data_resep = db::select('SELECT a.`kode_kunjungan`,a.`no_rm`
        ,a.tgl_masuk
        ,a.no_sep
        ,b.tgl_entry
        ,b.`kode_layanan_header`
        ,c.`REFASALSJP`
        ,d.`noApotik`
        ,d.`noResep`
        ,fc_nama_px(a.no_rm) AS nama_pasien
        ,fc_nama_unit1(a.kode_unit) AS nama_unit_pengirim
        ,fc_nama_unit1(b.kode_unit) AS nama_unit_penerima
        ,fc_alamat(no_rm) AS alamat_pasien
        ,fc_NAMA_PENJAMIN2(a.`kode_penjamin`) AS penjamin
        ,b.`keterangan`
        ,c.`status_terkirim`
        ,c.`iterasi`
        ,b.id as idheader
        FROM ts_kunjungan a 
        INNER JOIN ts_layanan_header b ON a.`kode_kunjungan` = b.`kode_kunjungan`
        LEFT JOIN apt_online_resep_kirim_obat c ON b.`id` = c.`id_layanan_header`
        LEFT JOIN apt_online_resep_obat d ON c.`id` = d.`id_resep_kirim`
        WHERE b.`kode_unit` IN(4002,4008)
        AND b.`status_layanan` != 3 AND DATE(b.`tgl_entry`) BETWEEN ? AND ? ORDER BY b.id DESC', [$request->tglawal, $request->tglakhir]);
        return view('Depofarmasi.tabel_riwayat_pelayanan', compact([
            'data_resep'
        ]));
    }
    public function ambildetailpelayananresep(Request $request)
    {
        $idheader = $request->idheader;
        $data_header = db::select('select *,fc_NAMA_PENJAMIN2(kode_penjaminx) as nama_penjamin,fc_NAMA_PARAMEDIS1(dok_kirim) as nama_dokter from ts_layanan_header where id = ?', [$idheader]);
        // $data_detail = db::select('select fc_nama_barang(kode_barang) as nama_barang,fc_nama_tarif(substr(kode_tarif_detail,1,6)) as nama_tarif,total_tarif,jumlah_layanan,total_layanan,grantotal_layanan,tgl_layanan_detail,kategori_resep,satuan_barang,tagihan_pribadi,tagihan_penjamin,tipe_anestesi,id_layanan_detail,id,kode_layanan_header from ts_layanan_detail where row_id_header = ?', [$idheader]);
        $row_id_header = $idheader; // Contoh nilai

        $data_detail = DB::table('ts_layanan_detail as t')
            ->leftJoin('mt_racikan as r', function ($join) {
                $join->on('t.kode_barang', '=', 'r.kode_racik')
                    ->where('t.kode_barang', 'like', '%R%');
            })
            ->select([
                DB::raw("IF(t.kode_barang LIKE '%R%', r.nama_racik, fc_nama_barang(t.kode_barang)) as nama_barang"),
                DB::raw("fc_nama_tarif(SUBSTR(t.kode_tarif_detail,1,6)) as nama_tarif"),
                't.total_tarif',
                't.jumlah_layanan',
                't.total_layanan',
                't.grantotal_layanan',
                't.tgl_layanan_detail',
                't.kategori_resep',
                't.satuan_barang',
                't.tipe_anestesi',
                't.tagihan_pribadi',
                't.tagihan_penjamin',
                't.tipe_anestesi',
                't.id_layanan_detail',
                't.id',
                't.kode_layanan_header'
            ])
            // PERBAIKAN DI SINI:
            // Gunakan where standar (otomatis array) atau whereRaw dengan array bindings
            ->where('t.row_id_header', $row_id_header)
            ->get();;
        $data_bridging = db::select('select * from apt_online_resep_kirim_obat a 
        LEFT JOIN apt_online_resep_obat b on a.id = b.id_resep_kirim
        where id_layanan_header = ?', [$idheader]);
        return view('Depofarmasi.data_detail_layanan', compact([
            'data_header',
            'data_detail',
            'data_bridging'
        ]));
    }
    public function returresep(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $id = $request->id;
        $resep_bridging = db::select('select * from apt_online_resep_kirim_obat where id_layanan_header = ?', [$id]);
        if (count($resep_bridging) > 0) {
            if ($resep_bridging[0]->status_terkirim == 'TERKIRIM') {
                $data_resep_jadi = db::select('select * from apt_online_resep_obat where id_resep_kirim = ?', [$resep_bridging[0]->id]);
                $data_resep = [
                    "nosjp" => $data_resep_jadi[0]->noApotik,
                    "refasalsjp" => $data_resep_jadi[0]->noSep_Kunjungan,
                    "noresep" => $data_resep_jadi[0]->noResep
                ];
                $response_data = $v->hapus_resep($data_resep);
                if ($response_data->metaData->code == 200) {
                    model_tabel_obat_reguler::where('id_resep_header', $data_resep_jadi[0]->id)->update(['status' => 'BATAL']);
                    model_tabel_resep_kirim::where('id', $data_resep_jadi[0]->id_resep_kirim)->update(['status_terkirim' => 'BATAL']);
                    model_resep_obat::where('id_resep_kirim', $id)->update(['message' => 'BATAL']);
                } else {
                    return response()->json([
                        'kode' => 500,
                        'message' => $response_data->metaData->message
                    ], 200);
                }
            }
        }
        //update data layanan simrs dan kartu stok
        model_ts_layanan_header::where('id', $id)->update(['status_layanan' => 3, 'status_retur' => 'CCL']);
        model_ts_layanan_detail::where('row_id_header', $id)->update(['status_layanan_detail' => 'CCL']);
        //GET DETAIL
        $ts_layanan_header = db::select('select * from ts_layanan_header where id = ?', [$id]);
        $kode_retur = $this->get_retur_header($ts_layanan_header[0]->kode_unit);
        $ts_retur_header = [
            'kode_kunjungan' => $ts_layanan_header[0]->kode_kunjungan,
            'kode_retur_header' => $kode_retur,
            'kode_layanan_header' => $ts_layanan_header[0]->kode_layanan_header,
            'tgl_retur' => $this->get_now(),
            'total_retur' => $ts_layanan_header[0]->total_layanan,
            'alasan_retur' => 'RETUR',
            'status_retur' => 'CLS',
            'pic' => auth()->user()->id,
            'status_pembayaran' => 'OPN',
        ];
        $hh = model_ts_retur_header::create($ts_retur_header);
        $detail = DB::table('ts_layanan_detail')
            ->where('row_id_header', $id)
            ->whereNotNull('kode_barang')
            ->get();
        $data_kunjungan = db::select('select *,fc_nama_px(no_rm) as nama_pasien,fc_alamat(no_rm) as alamat_pasien,fc_nama_unit1(kode_unit) as nama_unit from ts_kunjungan where kode_kunjungan = ?', [$ts_layanan_header[0]->kode_kunjungan]);
        foreach ($detail as $d) {
            $kode_retur_detail = $this->get_ret_det();
            $ts_retur_detail = [
                'kode_retur_detail' => $kode_retur_detail,
                'tgl_retur_detail' => $this->get_now(),
                'kode_retur_header' => $kode_retur,
                'id_layanan_detail' => $d->id_layanan_detail,
                'qty_retur' => $d->jumlah_layanan,
                'qty_sisa' => $d->jumlah_layanan,
                'tarif_layanan' => $d->total_tarif,
                'total_retur_detail' => $d->total_layanan,
                'status_retur_detail' => 'CLS',
                'row_id_header' => $hh->id,
            ];
            model_ts_retur_detail::create($ts_retur_detail);
            $kode_barang = $d->kode_barang;
            $stok = DB::table('ti_kartu_stok')
                ->where('kode_barang', $kode_barang)
                ->where('kode_unit', auth()->user()->unit)
                ->orderBy('no', 'desc') // Ambil ID terbesar
                ->first(); // Hanya ambil 1 baris teratas
            $stok_last = $stok->stok_current;
            $stok_in = $d->jumlah_layanan;
            $stok_out = 0;
            $stok_current = $stok_last + $stok_in;
            $mt_barang = db::select('select * from mt_barang where kode_barang =?', [$kode_barang]);
            $data_ti_kartu_stok = [
                'no_dokumen' => $kode_retur,
                'no_dokumen_detail' => $kode_retur_detail,
                'tgl_stok' => $this->get_now(),
                'kode_unit' => auth()->user()->unit,
                'kode_barang' => $kode_barang,
                'stok_last' => $stok_last,
                'stok_out' => $stok_out,
                'stok_in' => $stok_in,
                'stok_current' => $stok_current,
                'harga_beli' => $mt_barang[0]->hna,
                'act' => '1',
                'act_ed' => '1',
                // 'input_by' => auth()->user()->id,
                'keterangan' => $data_kunjungan[0]->no_rm . '|' . $data_kunjungan[0]->nama_pasien . '|' . $data_kunjungan[0]->alamat_pasien,
            ];
            $insert_ti_kartu_stok = model_ti_kartu_stok::create($data_ti_kartu_stok);
        }
        return response()->json([
            'kode' => 200,
            'message' => 'Data resep berhasil diretur ..!'
        ], 200);
    }
    public function hapusracikan(Request $request)
    {
        $id = $request->idtemplate;
        model_template_racikan::where('id', $id)->delete();
        model_template_racikan_detail::where('id_header', $id)->delete();
        return response()->json([
            'kode' => 200,
            'message' => 'Data resep berhasil dihapus ..!'
        ], 200);
    }
    public function hapusresep(Request $request)
    {
        $v = new MODEL_APOTEK_ONLINE();
        $id = $request->idresep;
        $data_resep_jadi = db::select('select * from apt_online_resep_obat where id = ?', [$id]);
        $data_resep = [
            "nosjp" => $request->noapotik,
            "refasalsjp" => $request->nosep,
            "noresep" => $request->noresep
        ];
        $response_data = $v->hapus_resep($data_resep);
        if ($response_data->metaData->code == 200) {
            model_tabel_obat_reguler::where('id_resep_header', $id)->delete();
            model_tabel_resep_kirim::where('id', $data_resep_jadi[0]->id_resep_kirim)->delete();
            model_resep_obat::where('id', $id)->delete();
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
    public function createresep(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama3) {
            $index3 = $nama3['name'];
            $value3 = $nama3['value'];
            $arraydata[$index3] = $value3;
        }
        $data_resep = [
            "TGLSJP" => $this->get_now(),
            "REFASALSJP" => "1018R0010226V000001",
            "POLIRSP" => "IGD",
            "KDJNSOBAT" => "3", //(1. Obat PRB, 2. Obat Kronis Blm Stabil, 3. Obat Kemoterapi)
            "NORESEP" => "12346",
            "IDUSERSJP" => "USR-01",
            "TGLRSP" => "2021-08-05 00:00:00",
            "TGLPELRSP" => "2021-08-05 00:00:00",
            "KdDokter" => "259916",
            "iterasi" => "0" //(0. Non Iterasi, 1. Iterasi)
        ];
        $v = new MODEL_APOTEK_ONLINE();
        try {
            $response_data = $v->simpan_resep($data_resep);
            if ($response_data->metaData->code == 200) {
                $data_save = [
                    'noSep_Kunjungan' => $response_data->response->noSep_Kunjungan,
                    'noKartu' => $response_data->response->noKartu,
                    'nama' => $response_data->response->nama,
                    'faskesAsal' => $response_data->response->faskesAsal,
                    'noApotik' => $response_data->response->noApotik,
                    'noResep' => $response_data->response->noResep,
                    'tglResep' => $response_data->response->tglResep,
                    'kdJnsObat' => $response_data->response->kdJnsObat,
                    'tglEntry' => $response_data->response->tglEntry,
                    'pic' => auth()->user()->id,
                    'status' => $response_data->metaData->code,
                    'message' => $response_data->metaData->message,
                ];
                model_resep_obat::create($data_save);
                return response()->json([
                    'kode' => 200,
                    'message' => 'Data Resep berhasil disimpan'
                ], 200);
                die;
            } else {
                return response()->json([
                    'kode' => 500,
                    'message' => 'Data Resep gagal disimpan'
                ], 200);
                die;
            }
        } catch (\Exception $e) {
            return response()->json([
                'kode' => 500,
                'message' => 'Data Resep gagal disimpan'
            ], 200);
            die;
        }
    }
    public function ambilformobatreguler(Request $request)
    {
        $id = $request->idresep;
        $data_resep = db::select('select * from apt_online_resep_obat where id = ?', [$id]);
        $mt_barang = db::select('select * from master_barang_x_master_obat_bpjs');
        return view('Depofarmasi.form_insert_obat_reguler', compact([
            'data_resep',
            'mt_barang'
        ]));
    }
    public function simpanobatreguler(Request $request)
    {
        $data = json_decode($_POST['data'], true);
        foreach ($data as $nama3) {
            $index3 = $nama3['name'];
            $value3 = $nama3['value'];
            $dataSet3[$index3] = $value3;
            if ($index3 == 'signa2') {
                $v[] = $dataSet3;
            }
        }
        foreach ($v as $vv) {
            $data_resep = [
                "NOSJP" => "0125A01602260000001",
                "NORESEP" => "12346",
                "KDOBT" => $vv['kodebpjs'],
                "NMOBAT" => $vv['namabarang'],
                "SIGNA1OBT" => $vv['signa1'],
                "SIGNA2OBT" => $vv['signa2'],
                "JMLOBT" => $vv['qtybeli'],
                "JHO" => $vv['qtybeli'],
                "CatKhsObt" => "TEST",
            ];
            $v = new MODEL_APOTEK_ONLINE();
            $response_data = $v->save_non_racik($data_resep);
        }
    }
    public function detailresep(Request $request)
    {
        $idresep = $request->idresep;
        echo $idresep;
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
    public function create_nomor_resep()
    {
        $q = DB::select('SELECT id,NORESEP,RIGHT(NORESEP,4) AS kd_max  FROM apt_online_resep_non_racikan
        WHERE DATE(tgl_resep) = CURDATE()
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
        return 'R' . $kd;
    }
    public function createLayanandetail()
    {
        $q = DB::select('SELECT id,id_layanan_detail,RIGHT(id_layanan_detail,6) AS kd_max  FROM ts_layanan_detail
        WHERE DATE(tgl_layanan_detail) = CURDATE()
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
        return 'DET' . date('ymd') . $kd;
    }
    public function get_ret_det()
    {
        $q = DB::select('SELECT id,kode_retur_detail,RIGHT(kode_retur_detail,6) AS kd_max  FROM ts_retur_detail
        WHERE DATE(tgl_retur_detail) = CURDATE()
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
        return 'RETDET' . date('ymd') . $kd;
    }
    public function get_retur_header($unit)
    {
        $mt_unit = db::select('select * from mt_unit where kode_unit = ?', [$unit]);
        $q = DB::select('SELECT id,kode_retur_header,RIGHT(kode_retur_header,6) AS kd_max  FROM ts_retur_header
        WHERE DATE(tgl_retur) = CURDATE()
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
        return 'RET' . $mt_unit[0]->prefix_unit . date('ymd') . $kd;
    }
    public function get_kode_racik()
    {
        $q = DB::select('SELECT id,kode_racik,RIGHT(kode_racik,3) AS kd_max  FROM mt_racikan
        WHERE DATE(tgl_racik) = CURDATE()
        ORDER BY id DESC
        LIMIT 1');
        $kd = "";
        if (count($q) > 0) {
            foreach ($q as $k) {
                $tmp = ((int) $k->kd_max) + 1;
                $kd = sprintf("%03s", $tmp);
            }
        } else {
            $kd = "001";
        }
        date_default_timezone_set('Asia/Jakarta');
        return 'R' . date('ymd') . $kd;
    }
    public function ambillistobatracikan()
    {
        $data = DB::table('template_racikan_header as a')
            ->join('template_racikan_detail as b', 'a.id', '=', 'b.id_header')
            ->select([
                'a.*',
                DB::raw("fc_NAMA_PARAMEDIS1(a.dok_kirim) as nama_dokter"),
                DB::raw("fc_nama_unit1(a.unit_kirim) as nama_unit_kirim"),
                DB::raw("GROUP_CONCAT(fc_nama_barang(b.kode_barang) SEPARATOR ', ') as keterangan_detail")
            ])
            ->groupBy('a.id') // Pastikan ID header unik
            ->orderBy('a.id', 'DESC') // Move ordering to its own method
            ->get();
        return view('Depofarmasi.tabel_obat_racik', compact([
            'data'
        ]));
    }
    public function ambilobatracik(Request $request)
    {
        $id = $request->idtemplate;
        $data = DB::table('template_racikan_header as a')
            ->select([
                'a.*'
            ])
            ->where('a.id', $id) // Filter berdasarkan ID Header
            ->get();

        if ($data->isEmpty()) {
            return response()->json(['html' => '<p class="text-danger">Data tidak ditemukan.</p>']);
        }

        // Render file blade menjadi string HTML
        $view = view('Depofarmasi.form_racikan', compact('data'))->render();
        return response()->json(['html' => $view]);
    }
    public function ambilkartustok(Request $request)
    {
        $kode_unit = $request->kode_unit; // Contoh: '4002'
        $subQuery = DB::table('ti_kartu_stok')
            ->select('kode_barang', DB::raw('MAX(no) as max_id'))
            ->where('kode_unit', $kode_unit)
            ->groupBy('kode_barang');
        $query = DB::table('ti_kartu_stok as t')
            ->joinSub($subQuery, 'latest', function ($join) {
                $join->on('t.no', '=', 'latest.max_id');
            })
            ->leftJoin('mt_barang as m', 't.kode_barang', '=', 'm.kode_barang')
            ->select([
                't.no_dokumen',
                't.kode_barang',
                't.no',
                'm.nama_barang',
                't.stok_current',
                't.stok_last',
                't.stok_in',
                't.stok_out',
                't.tgl_stok',
                't.keterangan'
            ])
            ->orderBy('t.no', 'desc');
        return DataTables::of($query)
            ->addIndexColumn()
            // Paksa pencarian nama_barang merujuk ke tabel m (mt_barang)
            ->filterColumn('nama_barang', function ($query, $keyword) {
                $query->where('m.nama_barang', 'like', "%{$keyword}%");
            })
            // Paksa pencarian kode_barang merujuk ke tabel t (ti_kartu_stok)
            ->filterColumn('kode_barang', function ($query, $keyword) {
                $query->where('t.kode_barang', 'like', "%{$keyword}%");
            })
            ->editColumn('stok_current', function ($row) {
                return number_format($row->stok_current, 0, ',', '.');
            })
            ->make(true);
        // return DataTables::of($query)
        //     ->addIndexColumn()
        //     ->editColumn('stok_current', function ($row) {
        //         return number_format($row->stok_current, 0, ',', '.');
        //     })
        //     ->editColumn('tgl_stok', function ($row) {
        //         return $row->tgl_stok ? \Carbon\Carbon::parse($row->tgl_stok)->format('d-m-Y') : '-';
        //     })
        //     ->make(true);
    }
}
