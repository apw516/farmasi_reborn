<?php

use App\Http\Controllers\ApotekOnlineController;
use App\Http\Controllers\authController;
use App\Http\Controllers\dashboarController;
use App\Http\Controllers\DepoFarmasiController;
use App\Http\Controllers\GudangFarmasiControlle;
use App\Http\Controllers\GudangFarmasiController;
use App\Http\Controllers\laporanController;
use App\Http\Controllers\MasterController;
use App\Http\Controllers\VclaimController;
use Illuminate\Support\Facades\Route;

Route::get('/', [authController::class, 'Index'])->middleware('guest');
Route::get('/login', [authController::class, 'index'])->middleware('guest')->name('login');
Route::post('/login', [authController::class, 'authenticate']);
Route::get('/logout', [authController::class, 'logout'])->name('logout');
Route::post('/register', [authController::class, 'Store'])->middleware('guest')->name('register');
Route::get('/logout', [authController::class, 'logout'])->name('logout');



Route::get('/dashboard', [dashboarController::class, 'Index'])->name('indexdashboard');
Route::get('/home', [dashboarController::class, 'Index'])->middleware('guest')->name('home');


Route::get('/indexpelayananresep', [DepoFarmasiController::class, 'indexpelayananresep'])->middleware('auth')->name('indexpelayananresep');
Route::post('/ambildatakunjungan', [DepoFarmasiController::class, 'ambildatakunjungan'])->middleware('auth')->name('ambildatakunjungan');
Route::post('/ambil_form_pelayanan_obat', [DepoFarmasiController::class, 'ambil_form_pelayanan_obat'])->middleware('auth')->name('ambil_form_pelayanan_obat');
Route::post('/simpanresep', [DepoFarmasiController::class, 'simpanresep_3'])->middleware('auth')->name('simpanresep');
Route::post('/detailresep', [DepoFarmasiController::class, 'detailresep'])->middleware('auth')->name('detailresep');
Route::get('/ambilkartustok', [DepoFarmasiController::class, 'ambilkartustok'])->middleware('auth')->name('stok.data');



Route::get('/indexriwayatkartustok', [DepoFarmasiController::class, 'indexriwayatkartustok'])->middleware('auth')->name('indexriwayatkartustok');
Route::get('/indexriwayatretur', [DepoFarmasiController::class, 'indexriwayatretur'])->middleware('auth')->name('indexriwayatretur');
Route::get('/indexcarisep', [DepoFarmasiController::class, 'indexriwayatpelayanan'])->middleware('auth')->name('indexcarisep');
Route::post('/ambildatariwayatpelayanan', [DepoFarmasiController::class, 'ambildatariwayatpelayanan'])->middleware('auth')->name('ambildatariwayatpelayanan');
Route::post('/ambildatariwayatretur', [DepoFarmasiController::class, 'ambildatariwayatretur'])->middleware('auth')->name('ambildatariwayatretur');
Route::post('/createresep', [DepoFarmasiController::class, 'createresep'])->middleware('auth')->name('createresep');
Route::post('/ambilformobatreguler', [DepoFarmasiController::class, 'ambilformobatreguler'])->middleware('auth')->name('ambilformobatreguler');
Route::post('/simpanobatreguler', [DepoFarmasiController::class, 'simpanobatreguler'])->middleware('auth')->name('simpanobatreguler');
Route::get('/ambildatasepobat', [DepoFarmasiController::class, 'ambildatasepobat'])->name('ambildatasepobat');
Route::post('/hapusresep', [DepoFarmasiController::class, 'hapusresep'])->middleware('auth')->name('hapusresep');
Route::get('/ambildatastokdepo', [DepoFarmasiController::class, 'ambildatastokdepo'])->name('ambildatastokdepo');
Route::post('/ambildetailpelayananresep', [DepoFarmasiController::class, 'ambildetailpelayananresep'])->middleware('auth')->name('ambildetailpelayananresep');
Route::post('/returresep', [DepoFarmasiController::class, 'returresep'])->middleware('auth')->name('returresep');
Route::post('/proseskomponenracik', [DepoFarmasiController::class, 'proseskomponenracik'])->middleware('auth')->name('proseskomponenracik');
Route::post('/simpanobatracikan', [DepoFarmasiController::class, 'simpanobatracikan'])->middleware('auth')->name('simpanobatracikan');
Route::post('/ambillistobatracikan', [DepoFarmasiController::class, 'ambillistobatracikan'])->middleware('auth')->name('ambillistobatracikan');
Route::post('/ambilobatracik', [DepoFarmasiController::class, 'ambilobatracik'])->middleware('auth')->name('ambilobatracik');
Route::post('/hapusracikan', [DepoFarmasiController::class, 'hapusracikan'])->middleware('auth')->name('hapusracikan');








Route::get('/indexcreatesep', [VclaimController::class, 'indexcreatesep'])->middleware('auth')->name('indexcreatesep');
Route::post('/createsep', [VclaimController::class, 'createsep'])->middleware('auth')->name('createsep');




Route::get('/indexmasterstok', [GudangFarmasiController::class, 'indexmasterstok'])->name('indexmasterstok');
Route::get('/ambildatastok', [GudangFarmasiController::class, 'ambildatastok'])->name('ambildatastok');
Route::get('/indexterimabarangpo', [GudangFarmasiController::class, 'indexterimabarangpo'])->name('indexterimabarangpo');
Route::get('/search-supplier', [GudangFarmasiController::class, 'searchsupplier'])->name('supplier.search');
Route::post('/simpanpoheader', [GudangFarmasiController::class, 'simpanpoheader'])->name('simpanpoheader');
Route::post('/ambildatatgpoheader', [GudangFarmasiController::class, 'ambildatatgpoheader'])->name('ambildatatgpoheader');
Route::post('/ambilformdetailpo', [GudangFarmasiController::class, 'ambilformdetailpo'])->name('ambilformdetailpo');


Route::get('/indexmappingbarang', [MasterController::class, 'indexmappingbarang'])->name('indexmappingbarang');
Route::get('/indexmastersupplier', [MasterController::class, 'indexmastersupplier'])->name('indexmastersupplier');
Route::get('/indexmasterdpho', [MasterController::class, 'indexmasterdpho'])->name('indexmasterdpho');
Route::get('/indexmasterbarang', [MasterController::class, 'indexmasterbarang'])->name('indexmasterbarang');
Route::get('/indexmasterobatbpjs', [MasterController::class, 'indexmasterobatbpjs'])->name('indexmasterobatbpjs');
Route::get('/ambilbarangdpho', [MasterController::class, 'ambilbarangdpho'])->name('ambilbarangdpho');
Route::get('/ambilbarang', [MasterController::class, 'ambilbarang'])->name('ambilbarang');
Route::get('/ambilsupplier', [MasterController::class, 'ambilsupplier'])->name('ambilsupplier');
Route::get('/ambilbarangbpjs', [MasterController::class, 'ambilbarangbpjs'])->name('ambilbarangbpjs');
Route::post('/simpanmappingobat', [MasterController::class, 'simpanmappingobat'])->name('simpanmappingobat');


Route::get('/indexlaporanmasterpengadaan', [laporanController::class, 'indexlaporanmasterpengadaan'])->name('indexlaporanmasterpengadaan');
Route::get('/indexrencanapengadaanbarang', [laporanController::class, 'indexrencanapengadaanbarang'])->name('indexrencanapengadaanbarang');
Route::post('/ambildatalaporanpembelianbarang', [laporanController::class, 'ambildatalaporanpembelianbarang'])->name('ambildatalaporanpembelianbarang');
Route::post('/ambildatarencanapengadaan', [laporanController::class, 'getLaporanAnalisisStok'])->name('ambildatarencanapengadaan');



Route::post('/downloadrefdpho', [ApotekOnlineController::class, 'downloadrefdpho'])->name('downloadrefdpho');
Route::post('/hapusresepapotekonline', [ApotekOnlineController::class, 'hapusresepapotekonline'])->name('hapusresepapotekonline');
Route::get('/indexcarisepaptonline', [ApotekOnlineController::class, 'indexcarisepaptonline'])->middleware('auth')->name('indexcarisepaptonline');
Route::post('/carisep_apotekonline', [ApotekOnlineController::class, 'carisep_apotekonline'])->middleware('auth')->name('carisep_apotekonline');
Route::get('/indexdaftarresep', [ApotekOnlineController::class, 'indexdaftarresep'])->middleware('auth')->name('indexdaftarresep');
Route::post('/caridaftarresep_apotekonline', [ApotekOnlineController::class, 'caridaftarresep_apotekonline'])->middleware('auth')->name('caridaftarresep_apotekonline');
Route::get('/indexriwayatpelayananonline', [ApotekOnlineController::class, 'indexriwayatpelayananonline'])->middleware('auth')->name('indexriwayatpelayananonline');
Route::post('/ambilriwayat_pelayananpeserta', [ApotekOnlineController::class, 'ambilriwayat_pelayananpeserta'])->middleware('auth')->name('ambilriwayat_pelayananpeserta');
Route::post('/ambilriwayat_pelayananpesertabyrs', [ApotekOnlineController::class, 'ambilriwayat_pelayananpesertabyrs'])->middleware('auth')->name('ambilriwayat_pelayananpesertabyrs');
Route::get('/indexdataklaim', [ApotekOnlineController::class, 'indexdataklaim'])->middleware('auth')->name('indexdataklaim');
Route::post('/ambil_data_monitoring_klaim', [ApotekOnlineController::class, 'ambil_data_monitoring_klaim'])->middleware('auth')->name('ambil_data_monitoring_klaim');
Route::get('/indexrekapprb', [ApotekOnlineController::class, 'indexrekapprb'])->middleware('auth')->name('indexrekapprb');
Route::post('/ambil_reakp_peserta_prb', [ApotekOnlineController::class, 'ambil_reakp_peserta_prb'])->middleware('auth')->name('ambil_reakp_peserta_prb');
Route::get('/indexdataklaim', [ApotekOnlineController::class, 'indexdataklaim'])->middleware('auth')->name('indexdataklaim');
