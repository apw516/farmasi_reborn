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


Route::post('/simpanmappingobatdaridepo', [DepoFarmasiController::class, 'simpanmappingbaru'])->middleware('auth')->name('simpanmappingobatdaridepo');

Route::get('/ambilbarangmappingdepo', [DepoFarmasiController::class, 'ambilbarangmappingdepo'])->middleware('auth')->name('ambilbarangmappingdepo');
Route::get('/indexmappingbarang2', [DepoFarmasiController::class, 'indexmappingbarang2'])->middleware('auth')->name('indexmappingbarang2');
Route::get('/indexbuatresep', [DepoFarmasiController::class, 'indexbuatresep'])->middleware('auth')->name('indexbuatresep');
Route::get('/indexpelayananresep', [DepoFarmasiController::class, 'indexpelayananresep'])->middleware('auth')->name('indexpelayananresep');
Route::get('/indexdatapelayanan', [DepoFarmasiController::class, 'indexdatapelayanan'])->middleware('auth')->name('indexdatapelayanan');
Route::post('/ambildatakunjungan', [DepoFarmasiController::class, 'ambildatakunjungan'])->middleware('auth')->name('ambildatakunjungan');
Route::post('/ambildatakunjungan_w_unit', [DepoFarmasiController::class, 'ambildatakunjungan_w_unit'])->middleware('auth')->name('ambildatakunjungan_w_unit');
Route::post('/ambil_form_pelayanan_obat', [DepoFarmasiController::class, 'ambil_form_pelayanan_obat'])->middleware('auth')->name('ambil_form_pelayanan_obat');
Route::post('/ambil_form_buat_resep', [DepoFarmasiController::class, 'ambil_form_buat_resep'])->middleware('auth')->name('ambil_form_buat_resep');
Route::post('/simpanresep', [DepoFarmasiController::class, 'simpanresep_3'])->middleware('auth')->name('simpanresep');
Route::post('/detailresep', [DepoFarmasiController::class, 'detailresep'])->middleware('auth')->name('detailresep');
Route::get('/ambilkartustok', [DepoFarmasiController::class, 'ambilkartustok'])->middleware('auth')->name('stok.data');
Route::get('/get.riwayat.pelayanan', [DepoFarmasiController::class, 'ambilriwayatpelayanan'])->middleware('auth')->name('get.riwayat.pelayanan');
Route::get('/get.detail.pelayanan', [DepoFarmasiController::class, 'ambildetailpelayanan'])->middleware('auth')->name('get.detail.pelayanan');
Route::post('/layanan.batal', [DepoFarmasiController::class, 'batallayananfarmasi'])->middleware('auth')->name('layanan.batal');



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
Route::post('/retursatuan', [DepoFarmasiController::class, 'retursatuan'])->middleware('auth')->name('retursatuan');
Route::post('/ambildetailretur', [DepoFarmasiController::class, 'ambildetailretur'])->middleware('auth')->name('ambildetailretur');








Route::get('/indexcreatesep', [VclaimController::class, 'indexcreatesep'])->middleware('auth')->name('indexcreatesep');
Route::post('/createsep', [VclaimController::class, 'createsep'])->middleware('auth')->name('createsep');


Route::get('/get.form.retur', [GudangFarmasiController::class, 'formretursupplier'])->middleware('auth')->name('get.form.retur');
Route::get('/indexretursupplier', [GudangFarmasiController::class, 'indexretursupplier'])->middleware('auth')->name('indexretursupplier');
Route::get('/gudang/detail-batch', [GudangFarmasiController::class, 'getDetailBatch'])->name('gudang.detailbatch');
Route::get('/gudang.stokdatabarang', [GudangFarmasiController::class, 'stokdatabarang'])->middleware('auth')->name('gudang.stokdatabarang');
Route::get('/indexstokbarang', [GudangFarmasiController::class, 'indexstokbarang'])->middleware('auth')->name('indexstokbarang');
Route::get('/indexmasterstok', [GudangFarmasiController::class, 'indexmasterstok'])->name('indexmasterstok');
Route::get('/ambildatastok', [GudangFarmasiController::class, 'ambildatastok'])->name('ambildatastok');
Route::get('/indexterimabarangpo', [GudangFarmasiController::class, 'indexterimabarangpo'])->name('indexterimabarangpo');
Route::get('/search-supplier', [GudangFarmasiController::class, 'searchsupplier'])->name('supplier.search');
Route::get('/search-unit', [GudangFarmasiController::class, 'searchunit'])->name('unit.search');
Route::post('/simpanpoheader', [GudangFarmasiController::class, 'simpanpoheader'])->name('simpanpoheader');
Route::post('/ambildatatgpoheader', [GudangFarmasiController::class, 'ambildatatgpoheader'])->name('ambildatatgpoheader');
Route::post('/ambilformdetailpo', [GudangFarmasiController::class, 'ambilformdetailpo'])->name('ambilformdetailpo');
Route::post('/prosesbarangpilihanPO', [GudangFarmasiController::class, 'prosesbarangpilihanPO'])->name('prosesbarangpilihanPO');
Route::post('/totalhitungpurchaseorder', [GudangFarmasiController::class, 'totalhitungpurchaseorder'])->name('totalhitungpurchaseorder');
Route::post('/simpanpoheaderfinal', [GudangFarmasiController::class, 'simpanpoheaderfinal'])->name('simpanpoheaderfinal');
Route::GET('/po_header.data', [GudangFarmasiController::class, 'ambildatatgpoheader'])->name('po_header.data');
Route::GET('/datapoheaderretur', [GudangFarmasiController::class, 'datapoheaderretur'])->name('datapoheaderretur');
Route::GET('/datadetailporetur', [GudangFarmasiController::class, 'datadetailporetur'])->name('datadetailporetur');
Route::get('/dataretursupplier', [GudangFarmasiController::class, 'dataretursupplier'])->name('dataretursupplier');
Route::get('/gudang.data_mutasi', [GudangFarmasiController::class, 'ambildatamutasi'])->name('gudang.data_mutasi');
Route::post('/gudang.purchase-order.ambil_detail_po', [GudangFarmasiController::class, 'detailpo'])->name('gudang.purchase-order.ambil_detail_po');
Route::get('/indexstoksediaanbarang', [GudangFarmasiController::class, 'indexstoksediaanbarang'])->name('indexstoksediaanbarang');
Route::get('/indexmutasibarang', [GudangFarmasiController::class, 'indexmutasibarang'])->name('indexmutasibarang');
Route::get('/indexbonruangan', [GudangFarmasiController::class, 'indexbonruangan'])->name('indexbonruangan');
Route::get('/ambildatasediaan', [GudangFarmasiController::class, 'ambildatasediaan'])->name('ambildatasediaan');
Route::post('/ambilformmutasi', [GudangFarmasiController::class, 'ambilformmutasi'])->name('ambilformmutasi');
Route::post('/simpanmutasi', [GudangFarmasiController::class, 'simpanmutasi'])->name('simpanmutasi');
Route::post('/pencarianbarang', [GudangFarmasiController::class, 'pencarianbarang'])->name('pencarianbarang');
Route::get('/ambilbaranguntukpo', [GudangFarmasiController::class, 'ambilbarang'])->name('ambilbaranguntukpo');
Route::post('/batalkanpo', [GudangFarmasiController::class, 'batalkanpo'])->name('batalkanpo');
Route::post('/pencariansediaanbarang', [GudangFarmasiController::class, 'pencariansediaanbarang'])->name('pencariansediaanbarang');
Route::post('/mutasibarang.simpan', [GudangFarmasiController::class, 'simpanmutasibanyak'])->name('mutasibarang.simpan');
Route::post('/simpanretursupplier', [GudangFarmasiController::class, 'simpanretursupplier'])->name('simpanretursupplier');
Route::get('/barang/edit/{id}', [MasterController::class, 'ambilbarangedit'])->name('barang/edit/{id}');



Route::post('/hapusobat', [MasterController::class, 'hapusobat'])->name('hapusobat');
Route::post('/updatemasterbarang', [MasterController::class, 'updatebarang'])->name('updatemasterbarang');
Route::post('/simpanmasterbarang', [MasterController::class, 'simpanbarang'])->name('simpanmasterbarang');




Route::get('/indexmappingbarang', [MasterController::class, 'indexmappingbarang'])->name('indexmappingbarang');
Route::get('/indexmastersupplier', [MasterController::class, 'indexmastersupplier'])->name('indexmastersupplier');
Route::get('/indexmasterdpho', [MasterController::class, 'indexmasterdpho'])->name('indexmasterdpho');
Route::get('/indexmasterbarang', [MasterController::class, 'indexmasterbarang'])->name('indexmasterbarang');
Route::get('/indexmasterobatbpjs', [MasterController::class, 'indexmasterobatbpjs'])->name('indexmasterobatbpjs');
Route::get('/ambilbarangdpho', [MasterController::class, 'ambilbarangdpho'])->name('ambilbarangdpho');
Route::get('/ambilbarang', [MasterController::class, 'ambilbarang'])->name('ambilbarang');
Route::get('/ambilbarangbelummapping', [MasterController::class, 'ambilbarangbelummapping'])->name('ambilbarangbelummapping');
Route::get('/ambilsupplier', [MasterController::class, 'ambilsupplier'])->name('ambilsupplier');
Route::get('/ambilbarangbpjs', [MasterController::class, 'ambilbarangbpjs'])->name('ambilbarangbpjs');
Route::post('/simpanmappingobat', [MasterController::class, 'simpanmappingobat'])->name('simpanmappingobat');
Route::post('/simpanmappingbaru', [MasterController::class, 'simpanmappingbaru'])->name('simpanmappingbaru');
Route::get('/cari.dpho', [MasterController::class, 'caridpho'])->name('cari.dpho');


Route::post('/chart.fastmoving', [laporanController::class, 'getChartData'])->name('chart.fastmoving');
Route::get('/chart.weekly', [laporanController::class, 'getChartWeekly'])->name('chart.weekly');
Route::get('/chart.value_contribution', [laporanController::class, 'getChartValueContribution'])->name('chart.value_contribution');
Route::get('/indexdatafastmoving', [laporanController::class, 'indexdatafastmoving'])->name('indexdatafastmoving');
Route::get('/indexperencanaan', [laporanController::class, 'indexperencanaan'])->name('indexperencanaan');
Route::get('/indexlaporanmasterpengadaan', [laporanController::class, 'indexlaporanmasterpengadaan'])->name('indexlaporanmasterpengadaan');
Route::get('/indexrencanapengadaanbarang', [laporanController::class, 'indexrencanapengadaanbarang'])->name('indexrencanapengadaanbarang');
Route::post('/ambildatalaporanpembelianbarang', [laporanController::class, 'ambildatalaporanpembelianbarang'])->name('ambildatalaporanpembelianbarang');
Route::post('/ambildatarencanapengadaan', [laporanController::class, 'getLaporanAnalisisStok'])->name('ambildatarencanapengadaan');
Route::post('/buatrencanapengadaan', [laporanController::class, 'buatrencanapengadaan'])->name('buatrencanapengadaan');



Route::post('/downloadrefdpho', [ApotekOnlineController::class, 'downloadrefdpho'])->name('downloadrefdpho');
Route::get('/indexreferensidpho', [ApotekOnlineController::class, 'indexreferensidpho'])->name('indexreferensidpho');
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
