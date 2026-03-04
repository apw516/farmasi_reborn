<?php

namespace App\Http\Controllers;

use App\Models\MasterBarang;
use App\Models\MasterBarangBPJS;
use App\Models\model_master_barang_x_master_bpjs;
use App\Models\VclaimModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;

class VclaimController extends Controller
{
    public function indexcreatesep()
    {
        $now = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();
        $date_start = $now->format('Y-m-d');
        $date_end = $end->format('Y-m-d');
        $menu = 'indexcreatesep';
        return view('Vclaim.indexcreatesep', compact([
            'menu',
            'date_start',
            'date_end'
        ]));
    }
    public function createsep(Request $request)
    {
        // 0002083363874
        // 1018R0010226V000001
        //0000450059905
        $get_sep = [
            "request" => [
                "t_sep" => [
                    "noKartu" => "0002083363874",
                    "tglSep" => "2026-03-03",
                    "ppkPelayanan" => "1018R001",
                    "jnsPelayanan" => "2",
                    "klsRawat" => [
                        "klsRawatHak" => "1",
                        "klsRawatNaik" => "",
                        "pembiayaan" => "",
                        "penanggungJawab" => ""
                    ],
                    "noMR" => "01007086",
                    "rujukan" => [
                        "asalRujukan" => "2",
                        "tglRujukan" => "",
                        "noRujukan" => "",
                        "ppkRujukan" => "1018R001"
                    ],
                    "catatan" => "",
                    "diagAwal" => "E10",
                    "poli" => [
                        "tujuan" => "IGD",
                        "eksekutif" => "0"
                    ],
                    "cob" => [
                        "cob" => "0"
                    ],
                    "katarak" => [
                        "katarak" => "0"
                    ],
                    "jaminan" => [
                        "lakaLantas" => "0",
                        "noLP" => "0",
                        "penjamin" => [
                            "tglKejadian" => "",
                            "keterangan" => "",
                            "suplesi" => [
                                "suplesi" => "0",
                                "noSepSuplesi" => "",
                                "lokasiLaka" => [
                                    "kdPropinsi" => "",
                                    "kdKabupaten" => "",
                                    "kdKecamatan" => ""
                                ]
                            ]
                        ]
                    ],
                    "tujuanKunj" => "0",
                    "flagProcedure" => "",
                    "kdPenunjang" => "",
                    "assesmentPel" => "",
                    "skdp" => [
                        "noSurat" => "",
                        "kodeDPJP" => ""
                    ],
                    "dpjpLayan" => "259916",
                    "noTelp" => "082123332344",
                    "user" => "waled | " . auth()->user()->id_simrs
                ]
            ]
        ];
        $v = new VclaimModel();
        // sleep(150);
        $datasep = $v->insertsep2($get_sep);
        dd($datasep);
    }
}
