@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Faktur Pembelian</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Faktur Pembelian</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="v_1">
                <div class="card mt-2">
                    <div class="card-header bg-light"></div>
                    <div class="card-body">
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-header bg-white border-0 pt-3 pb-0">
                                <h6 class="card-title text-uppercase text-muted fw-bold mb-0">Filter Periode & Aksi</h6>
                            </div>
                            <div class="card-body">
                                <form id="formFilterPO" class="row g-3 align-items-end">
                                    <div class="col-md-3 col-lg-2">
                                        <label for="tanggalawal" class="form-label small fw-bold text-muted">Tanggal Input
                                            (Awal)</label>
                                        <input type="date" class="form-control form-control-sm" id="tanggalawal"
                                            name="tanggalawal" value="{{ $date_start }}">
                                    </div>
                                    <div class="col-md-3 col-lg-2">
                                        <label for="tanggalakhir" class="form-label small fw-bold text-muted">Tanggal Input
                                            (Akhir)</label>
                                        <input type="date" class="form-control form-control-sm" id="tanggalakhir"
                                            name="tanggalakhir" value="{{ $date_end }}">
                                    </div>
                                    <div class="col-md-2 col-lg-1">
                                        <div class="d-grid">
                                            <button type="button" class="btn btn-primary btn-sm px-3" id="btnProsesFilter">
                                                <i class="bi bi-funnel me-1"></i> Proses
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 col-lg-7 text-end">
                                        <a class="btn btn-success btn-sm px-3" onclick="ambilformheader()">
                                            <i class="bi bi-plus-circle me-1"></i> Buat Faktur Pembelian (Header & Detail)
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card shadow-sm border-0 mb-4">
                            <div class="card-body p-0">
                                <div class="text-center py-5" id="loaderDataTable" style="display:none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <div class="text-muted mt-2">Mengambil data PO...</div>
                                </div>

                                <div class="table-responsive p-3" id="wrapperDataTable">
                                    <table id="tbHeaderPO"
                                        class="table table-sm table-bordered table-hover align-middle w-100 fs-7">
                                        <thead class="table-light text-center text-uppercase fs-8 text-muted fw-bold">
                                            <tr>
                                                <th width="3%">No</th>
                                                <th width="12%">Tgl. Input</th>
                                                <th width="10%">No. PO</th>
                                                <th width="10%">No. Faktur</th>
                                                <th>Supplier</th>
                                                <th width="8%">Jatuh Tempo</th>
                                                <th width="8%">Status</th>
                                                <th width="12%">Grand Total</th>
                                                <th width="10%">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div hidden class="v_2">
                <button class="btn btn-danger" onclick="kembali()">Kembali</button>
                <div class="v_form_header mt-2">
                    <div class="card">
                        <div class="card-header">Form PO Header</div>
                        <div class="card-body p-2">
                            <form action="" class="formpoheader">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <div class="row g-1">
                                            <div class="col-md-7">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Pilih Supplier</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        id="supplier_search" name="supplier_search">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Supplier ID</label>
                                                    <input readonly type="text" class="form-control form-control-sm"
                                                        id="supplier_id" name="supplier_id">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Alamat</label>
                                                    <textarea rows="3" readonly class="form-control form-control-sm" id="alamat_supplier" name="alamat_supplier"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Telp</label>
                                                    <input readonly type="text" class="form-control form-control-sm"
                                                        id="telp_supplier" name="telp_supplier">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="row g-1">
                                            <div class="col-md-8">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Tgl Pembelian</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        name="tanggalbeli" value="{{ $today }}">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Jatuh tempo</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        value="{{ $today }}" name="jatuh_tempo">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Tgl Penerimaan</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        name="tanggalterima" value="{{ $today }}">
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Tipe Pembelian</label>
                                                    <select class="form-select form-select-sm" id="tipepembelian"
                                                        name="tipepembelian">
                                                        <option value="K">Kredit</option>
                                                        <option value="T">Tunai</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Kategori Barang</label>
                                                    <select class="form-select form-select-sm" name="kategoribarang"
                                                        id="kategoribarang">
                                                        @foreach ($tipe as $t)
                                                            <option value="{{ $t->kode_tipe }}">{{ $t->kode_tipe }} |
                                                                {{ $t->nama_tipe }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Nomor Faktur</label>
                                                    <input type="text" class="form-control form-control-sm"
                                                        name="nomorfaktur" id="nomorfaktur">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-5">
                                        <div class="row g-1">
                                            <div class="col-6 d-flex align-items-center justify-content-between mb-1">
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="checkbox" id="materai"
                                                        name="materai">
                                                    <label class="form-check-label small" for="materai">Materai</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="checkbox" id="ppn"
                                                        name="ppn">
                                                    <label class="form-check-label small" for="ppn">PPN</label>
                                                </div>
                                                <div class="form-check form-check-inline mb-0">
                                                    <input class="form-check-input" type="checkbox" id="pph"
                                                        name="pph">
                                                    <label class="form-check-label small" for="ppn">PPH</label>
                                                </div>
                                            </div>

                                            <div class="col-12">
                                                <div class="mb-1 row g-0 align-items-center">
                                                    <label class="col-sm-2 form-label small mb-0">Total Pembelian</label>
                                                    <div class="col-sm-8">
                                                        <input readonly type="text"
                                                            class="form-control form-control-sm text-end"
                                                            name="totalpembelian" id="totalpembelian" value="0">
                                                        <input hidden readonly type="text"
                                                            class="form-control form-control-sm text-end"
                                                            name="totalpembelianasli" id="totalpembelianasli"
                                                            value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Potongan (%)</label>
                                                    <div class="input-group input-group-sm">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-end"
                                                            name="potonganpersen" value="0">
                                                        <span class="input-group-text p-1">%</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="mb-1">
                                                    <label class="form-label small mb-0">Potongan (Rp)</label>
                                                    <input type="number" class="form-control form-control-sm text-end"
                                                        name="potongantunai" id="potongantunai" value="0">
                                                    <input hidden type="number"
                                                        class="form-control form-control-sm text-end"
                                                        name="potongantunaiasli" id="potongantunaiasli" value="0">
                                                    <label hidden class="form-label small mb-0" id="labelasli2">Potongan
                                                        (Rp)</label>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row g-0 align-items-center">
                                                    <label class="col-sm-2 form-label small mb-0">Sub Grand Total</label>
                                                    <div class="col-sm-8">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-end fw-bold"
                                                            name="subgrandtotal" id="subgrandtotal" readonly
                                                            value="0">
                                                        <input hidden type="number"
                                                            class="form-control form-control-sm text-end fw-bold"
                                                            name="subgrandtotalasli" id="subgrandtotalasli" readonly
                                                            value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row g-0 align-items-center">
                                                    <label class="col-sm-2 form-label small mb-0">PPN</label>
                                                    <div class="col-sm-8">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-end fw-bold"
                                                            name="nominalppn" id='nominalppn' readonly value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row g-0 align-items-center">
                                                    <label class="col-sm-2 form-label small mb-0">Materai</label>
                                                    <div class="col-sm-8">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-end fw-bold"
                                                            name="nominalmaterai" id="nominalmaterai" value="0">
                                                        <input hidden type="number"
                                                            class="form-control form-control-sm text-end fw-bold"
                                                            name="nominalmateraiasli" id="nominalmateraiasli"
                                                            value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row g-0 align-items-center">
                                                    <label class="col-sm-2 form-label small mb-0 text-dark fw-bold">Grand
                                                        Total</label>
                                                    <div class="col-sm-8">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-end border-primary"
                                                            readonly name="grandtotal" id="grandtotal" value="0">
                                                        <input hidden type="number"
                                                            class="form-control form-control-sm text-end border-primary"
                                                            readonly name="grandtotalasli" id="grandtotalasli"
                                                            value="0">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="mb-1 row g-0 align-items-center">
                                                    <label class="col-sm-2 form-label small mb-0 text-dark fw-bold">TOTAL
                                                        UTANG</label>
                                                    <div class="col-sm-8">
                                                        <input type="number"
                                                            class="form-control form-control-sm text-end border-primary"
                                                            readonly name="totalutang" id="totalutang" value="0">
                                                        <input hidden type="number"
                                                            class="form-control form-control-sm text-end border-primary"
                                                            readonly name="totalutangasli" id="totalutangasli"
                                                            value="0">
                                                        <div class="btn-group float-end mt-2" role="group"
                                                            aria-label="Basic mixed styles example">
                                                            <button type="button" class="btn btn-warning"
                                                                onclick="resetform()"><i class="bi bi-x-octagon"></i>
                                                                Reset</button>
                                                            <button type="button" class="btn btn-danger"
                                                                onclick="batalpo()"><i class="bi bi-x-octagon"></i>
                                                                Batal</button>
                                                            <button disabled type="button"
                                                                class="btn btn-primary btnsimpan" onclick="simpanpo()"><i
                                                                    class="bi bi-floppy"></i> Simpan</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">

                                                    </div>
                                                    <div class="col-md-6">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="card-header mt-5">
                                <form action="" class="formbarangpilihan">
                                    <div class="row align-items-end">
                                        <div hidden class="col-md-1">
                                            <div class="mb-2">
                                                <label class="form-label small">Kode Barang</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="kodebarang" name="kodebarang">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">Nama Barang</label>
                                                <div class="input-group input-group-sm">
                                                    <input readonly type="text" class="form-control"
                                                        placeholder="Cari barang..." id="namabarangpilihan"
                                                        name="namabarangpilihan">
                                                    <button type="button" class="btn btn-success" data-bs-toggle="modal"
                                                        data-bs-target="#modalcariobat" onclick="cariobat()">
                                                        <i class="bi bi-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <div class="mb-2">
                                                <label class="form-label small fw-bold">QTY (Utama)</label>
                                                <input type="number" class="form-control form-control-sm text-center"
                                                    id="qty" name="qty" value="1" min="1">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="row g-1">
                                                <div class="col-1">
                                                    <div class="mb-2">
                                                        <label class="form-label small text-muted">Satuan</label>
                                                        <select class="form-select form-select-sm" id="satuan"
                                                            name="satuan">
                                                            {{-- <option value="0">- Pilih -</option> --}}
                                                            {{-- @foreach ($satuan as $s)
                                                                <option value="{{ $s->kode_satuan }}">
                                                                    {{ $s->nama_satuan }}
                                                                </option>
                                                            @endforeach --}}
                                                        </select>
                                                    </div>
                                                </div>
                                                <div hidden class="col-2">
                                                    <div class="mb-2">
                                                        <label class="form-label small text-muted">Sat. Kecil</label>
                                                        <select class="form-select form-select-sm" id="satuan_kecil"
                                                            name="satuan_kecil">
                                                            <option value="0">- Pilih -</option>
                                                            @foreach ($satuan as $s)
                                                                <option value="{{ $s->kode_satuan }}">
                                                                    {{ $s->nama_satuan }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="mb-2">
                                                        <label class="form-label small text-danger">Isi (Rasio)</label>
                                                        <input type="number"
                                                            class="form-control form-control-sm text-center"
                                                            id="rasio_kecil" name="rasio_kecil" placeholder="0"
                                                            min="1">
                                                    </div>
                                                </div>
                                                <div class="col-1">
                                                    <div class="mb-2">
                                                        <label class="form-label small text-muted">Hrg Sat.</label>
                                                        <input type="text"
                                                            class="form-control form-control-sm input-mask-uang"
                                                            id="hrgasatuan" name="hrgasatuan" value="0">
                                                        <input hidden type="text" id="hrgasatuanasli"
                                                            name="hrgasatuanasli">
                                                        <label hidden for="exampleFormControlInput1" id="labelasli"
                                                            class="form-label">Diskon</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Disc</label>
                                                        <input type="text" class="form-control form-control-sm input-mask-uang"
                                                            id="diskon" name="diskon" value="0">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="row g-1">
                                                        <div class="col-6">
                                                            <div class="mb-2">
                                                                <label class="form-label small text-muted">Batch</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    id="nobatch" name="nobatch" placeholder="No...">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="mb-2">
                                                                <label class="form-label small text-muted">ED</label>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    id="ed" name="ed">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-bold">Pilih Pabrikan</label>
                                                        <select class="form-select form-select-sm" name="pabrikan"
                                                            id="pabrikan">
                                                            @foreach ($master_pabrikan as $t)
                                                                <option value="{{ $t->id }}">{{ $t->nama_pabrik }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-center">
                                            <div class="mb-2">
                                                <div class="d-grid">
                                                    <button type="button" class="btn btn-success btn-sm addbarang"
                                                        onclick="prosesbarang()">
                                                        <i class="bi bi-arrow-down-left-square fs-6"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="card-body">
                                <label for="exampleFormControlInput1" class="form-label">List Barang ....</label>
                                <div class="v_list_b mt-2">
                                    <form action="" method="post" class="v_list_barang  mt-2 mt-2">
                                        <div class="draft_barang">
                                            <div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-secondary prosesbarang" onclick="hitungtotal()"><i
                                        class="bi bi-cloud-arrow-up" style="margin-right:4px"></i> Proses Barang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetailPO" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-vla text-white">
                    <h5 class="modal-title fs-6"><i class="bi bi-search me-2"></i>Detail Purchase Order (PO)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="v_detail_po"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalcariobat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Silahkan Pilih Obat</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="v_t_b">

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
  
    <script>
        $(document).ready(function() {
            $('#ppn').change(function() {
                if ($(this).is(':checked')) {
                    // Jika diklik (Centang), isi form
                    $('#nominalppn').val('11'); // Contoh nilai standar
                } else {
                    // Jika centang dilepas, kosongkan kembali
                    $('#nominalppn').val('0');
                }
            });
            $('#materai').change(function() {
                if ($(this).is(':checked')) {
                    // Jika diklik (Centang), isi form
                    $('#nominalmateraiasli').val('10000'); // Contoh nilai standar
                    $('#nominalmaterai').val('10.000'); // Contoh nilai standar
                } else {
                    // Jika centang dilepas, kosongkan kembali
                    $('#nominalmaterai').val('0');
                    $('#nominalmateraiasli').val('0');
                }
            });
        });

        function kembali() {
            $('.v_1').removeAttr('hidden', true)
            $('.v_2').attr('hidden', true)
        }

        function caridataerimabarang() {
            tanggalawal = $('#tanggalawal').val()
            tanggalakhir = $('#tanggalakhir').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggalawal,
                    tanggalakhir
                },
                url: '<?= route('ambildatatgpoheader') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_data_po_header').html(response);
                }
            });
        }
        $(document).ready(function() {
            $("#supplier_search").autocomplete({
                source: "{{ route('supplier.search') }}",
                minLength: 2, // Mulai mencari setelah 2 karakter
                select: function(event, ui) {
                    // Set ID supplier ke hidden input saat dipilih
                    $("#supplier_id").val(ui.item.id);
                    $("#alamat_supplier").val(ui.item.alamat);
                    $("#telp_supplier").val(ui.item.telp);
                }
            });
        });

        function ambilformheader() {
            $('.v_2').removeAttr('hidden', true)
            $('.v_1').attr('hidden', true)
        }

        function batal() {
            $('.v_form_header').attr('hidden', true)
        }

        function simpanpoheader() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "Pastikan data sudah terisi dengan benar",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "OK"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "apakah data sudah benar ?",
                        showDenyButton: false,
                        showCancelButton: true,
                        confirmButtonText: "Ya, Simpan ",
                        denyButtonText: `Don't save`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            simpanpoheaderfinal()
                        } else if (result.isDenied) {

                        }
                    });
                }
            });
        }

        function simpanpoheaderfinal() {
            var data = $('.formpoheader').serializeArray();
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpanpoheader') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    if (response.kode == '500') {
                        // Kondisi jika validasi gagal atau ada error sistem
                        Swal.fire({
                            icon: 'error',
                            title: 'Ups!',
                            text: response.message,
                        });
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'OK!',
                            text: response.message,
                        });
                        location.reload()
                    }
                }
            });
        }
        $('body').off('click', '.pilihobat').on('click', '.pilihobat', function(event) {
            event.preventDefault();
            var kode_barang = $(this).data('kode_barang');
            var isi = $(this).attr('isi');
            var nama_barang = $(this).data('nama_barang');
            var satuan_barang = $(this).data('nama_satuan');
            var nama_satuan_kecil = $(this).attr('nama_satuan_kecil');
            var id_pabrik = $(this).attr('id_pabrik');

            var kode_satuan_besar = "satuan besar"; // Pastikan atribut ini ada di tombol
            var nama_satuan_besar = $(this).data('nama_satuan'); // Pastikan atribut ini ada di tombol


            var kode_satuan_kecil = "satuan kecil"; // Ambil dari atribut
            var nama_satuan_kecil = $(this).attr('nama_satuan_kecil'); // Ambil dari atribut
            // $('#satuan').val(satuan_barang).trigger('change');
            // $('#satuan_kecil').val(nama_satuan_kecil).trigger('change');
            var selectSatuan = $('#satuan');
            selectSatuan.empty();
            // Tambahkan pilihan Satuan Besar
            selectSatuan.append(new Option(nama_satuan_besar, kode_satuan_besar));

            // Tambahkan pilihan Satuan Kecil (jika berbeda dengan satuan besar)
            if (kode_satuan_kecil && kode_satuan_kecil !== kode_satuan_besar) {
                selectSatuan.append(new Option(nama_satuan_kecil, kode_satuan_kecil));
            }

            // 3. Set value dan trigger change
            selectSatuan.val(kode_satuan_besar).trigger('change');
            $('#pabrikan').val(id_pabrik).trigger('change');
            $('#namabarangpilihan').val(nama_barang)
            $('#kodebarang').val(kode_barang)
            $('#rasio_kecil').val(isi)
            Swal.fire({
                title: nama_barang + " Berhasil dipilih",
                icon: "success",
                timer: 1500,
                showConfirmButton: false
            });
        });

        function prosesbarang() {
            var data = $('.formbarangpilihan').serializeArray();
            nama = $('#namabarangpilihan').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('prosesbarangpilihanPO') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    if (response.status == 'success') {
                        $('.draft_barang').append(response.html);
                        Swal.fire({
                            title: nama + " Berhasil ditambahkan dilist ...",
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            title: response.message,
                            icon: "error",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }

        function resetform() {
            $('.remove_field').removeAttr('disabled', true)
            $('.addbarang').removeAttr('disabled', true)
            $('.prosesbarang').removeAttr('disabled', true)
            $('.btnsimpan').attr('disabled', true)
            $('#materai, #ppn, #pph').removeAttr('disabled', true);
        }

        function simpanpo() {
            var data = $('.v_list_barang').serializeArray();
            var data2 = $('.formpoheader').serializeArray();
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data),
                    data2: JSON.stringify(data2),
                },
                url: '<?= route('simpanpoheaderfinal') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    if (response.status == 'success') {
                        Swal.fire({
                            title: response.message,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });
                        location.reload()
                    } else {
                        Swal.fire({
                            title: response.message,
                            icon: "error",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }

        function hitungtotal() {
            var data = $('.v_list_barang').serializeArray();
            var data2 = $('.formpoheader').serializeArray();
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data),
                    data2: JSON.stringify(data2),
                },
                url: '<?= route('totalhitungpurchaseorder') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    if (response.status == 'success') {
                        let container = $('.v_form_header');
                        let container2 = $('.draft_barang');
                        $('#totalpembelian').val(response.total_format)
                        $('#totalpembelianasli').val(response.total)
                        $('#subgrandtotalasli').val(response.subgrantotal)
                        $('#subgrandtotal').val(response.format_subgrantotal)
                        $('#grandtotal').val(response.format_grantotal)
                        $('#grandtotalasli').val(response.grandtotal)
                        $('#totalutangasli').val(response.hutang)
                        $('#totalutang').val(response.format_hutang)
                        $('#totalpembelian').focus();
                        $('.remove_field').attr('disabled', true)
                        $('.addbarang').attr('disabled', true)
                        $('.prosesbarang').attr('disabled', true)
                        $('.btnsimpan').removeAttr('disabled', true)
                        $('#materai, #ppn, #pph').prop('disabled', true);
                    } else {
                        Swal.fire({
                            title: response.message,
                            icon: "error",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                }
            });
        }
        $('.draft_barang').on("click", ".remove_field", function(e) {
            e.preventDefault();
            $(this).closest('.row').remove();
        });
        const inputMask = document.getElementById('hrgasatuan');
        const inputAsli = document.getElementById('hrgasatuanasli');
        const labelAsli = document.getElementById('labelasli');
        const inputMask2 = document.getElementById('potongantunai');
        const inputAsli2 = document.getElementById('potongantunaiasli');
        const labelAsli2 = document.getElementById('labelasli2');
        // const inputMask2 = document.getElementById('ppn_mask');
        // const inputAsli2 = document.getElementById('ppn_asli');
        // const labelAsli2 = document.getElementById('label_asli_ppn');
        // const inputMask3 = document.getElementById('totalhutang_mask');
        // const inputAsli3 = document.getElementById('totalhutang_asli');
        // const labelAsli3 = document.getElementById('label_asli_totalhutang');
        inputMask.addEventListener('keyup', function(e) {
            // 1. Ambil angka saja dari input
            let nominal = this.value.replace(/[^,\d]/g, '').toString();
            // 2. Masukkan angka bersih ke input hidden & label
            inputAsli.value = nominal;
            labelAsli.innerText = nominal;
            // 3. Ubah tampilan input menjadi format ribuan
            this.value = formatRupiah(nominal);
        });
        inputMask2.addEventListener('keyup', function(e) {
            // 1. Ambil angka saja dari input
            let nominal = this.value.replace(/[^,\d]/g, '').toString();
            // 2. Masukkan angka bersih ke input hidden & label
            inputAsli2.value = nominal;
            labelAsli2.innerText = nominal;
            // 3. Ubah tampilan input menjadi format ribuan
            this.value = formatRupiah(nominal);
        });
        // inputMask3.addEventListener('keyup', function(e) {
        //     // 1. Ambil angka saja dari input
        //     let nominal = this.value.replace(/[^,\d]/g, '').toString();

        //     // 2. Masukkan angka bersih ke input hidden & label
        //     inputAsli3.value = nominal;
        //     labelAsli3.innerText = nominal;

        //     // 3. Ubah tampilan input menjadi format ribuan
        //     this.value = formatRupiah(nominal);
        // });
        /* Fungsi Format Ribuan */
        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }

        function sedangprosespo() {
            $('.btnsimpan').removeAttr('disabled', true)
            $('#ppn').css('pointer-events', 'none').attr('tabindex', '-1');
            $('#materai').css('pointer-events', 'none').attr('tabindex', '-1');
            $('#pph').css('pointer-events', 'none').attr('tabindex', '-1');
            let container = $('.formpoheader');
            container.find('input[type=text],input[type=number]').attr('readonly', false);

        }

        function batalpo() {
            let container = $('.v_form_header');
            // Kosongkan Input Teks, Password, Number, Email, dan Textarea
            container.find('input[type=number]').val('0');
            container.find('input[type=text]').val('');
            // Reset Select (kembali ke opsi pertama / silakan pilih)
            container.find('select').prop('selectedIndex', 0);
            // Hilangkan Centang pada Checkbox dan Radio
            container.find('input[type=checkbox], input[type=radio]').prop('checked', false);
            location.reload()
        }
        $(document).ready(function() {
            // --- 1. Konfigurasi DataTable Server-side KynovaPharma ---
            var table = $('#tbHeaderPO').DataTable({
                processing: true, // Menampilkan spinner loader bawaan
                serverSide: true, // Mengaktifkan mode Server-side
                responsive: true, // Responsif di layar HP/Tablet
                autoWidth: false,
                pageLength: 25, // Default jumlah baris per halaman
                order: [
                    [2, 'desc']
                ], // Urutan default berdasarkan No. PO (Kolom 2) secara Descending

                // Konfigurasi request AJAX Serverside
                ajax: {
                    url: "{{ route('po_header.data') }}", // Route JSON Server-side di Controller
                    type: 'POST',
                    data: function(d) {
                        // Kirim parameter filter tanggal ke Controller
                        d._token = "{{ csrf_token() }}";
                        d.tanggalawal = $('#tanggalawal').val();
                        d.tanggalakhir = $('#tanggalakhir').val();
                    },
                    beforeSend: function() {
                        $('#wrapperDataTable').hide(); // Sembunyikan tabel dulu
                        $('#loaderDataTable').show(); // Tampilkan loader custom
                    },
                    complete: function() {
                        $('#loaderDataTable').hide(); // Sembunyikan loader
                        $('#wrapperDataTable').show(); // Tampilkan tabel kembali
                    },
                    error: function(xhr, error, thrown) {
                        $('#loaderDataTable').hide();
                        Swal.fire('Error DataTable', 'Gagal mengambil data PO Header. ' + thrown,
                            'error');
                    }
                },

                // Definisi Kolom sesuai Response JSON dari Controller (Yajra)
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_input',
                        name: 'tgl_input',
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: 'kode_po',
                        name: 'kode_po',
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'no_faktur',
                        name: 'no_faktur',
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'nama_supplier',
                        name: 'nama_supplier'
                    }, // Menampilkan Nama & Kode Supplier (Formatting via Controller)
                    {
                        data: 'tgl_jatuh_tempo',
                        name: 'tgl_jatuh_tempo',
                        className: 'text-center'
                    },
                    {
                        data: 'status_po',
                        name: 'status_po',
                        className: 'text-center',
                        render: function(data, type, row) {
                            // Formatting visual status_po (Jika di Controller belum di-format)
                            if (data == 'CLS')
                                return '<span class="badge rounded-pill bg-success small">Aktif</span>';
                            if (data == 2)
                                return '<span class="badge rounded-pill bg-secondary small">Selesai</span>';
                            return '<span class="badge rounded-pill bg-danger small">Batal</span>';
                        }
                    },
                    {
                        data: 'gtotal_po',
                        name: 'gtotal_po'
                    }, // Menampilkan Rp Grand Total (Formatting via Controller)
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center bg-light'
                    }
                ],

                // Konfigurasi Bahasa/UI DataTables
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data PO Header yang ditemukan pada periode ini.",
                    info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    search: "Cari berdasarkan No. PO/Supplier/User:",
                    paginate: {
                        first: "Awal",
                        last: "Akhir",
                        next: "Lanjut",
                        previous: "Mundur"
                    }
                }
            });

            // --- 2. Logic Tombol Proses Filter Tanggal ---
            $('#btnProsesFilter').on('click', function() {
                // Cek validasi tanggal
                let tglAwal = $('#tanggalawal').val();
                let tglAkhir = $('#tanggalakhir').val();

                if (!tglAwal || !tglAkhir) {
                    Swal.fire('Filter Gagal',
                        'Silahkan pilih Tanggal Input (Awal & Akhir) terlebih dahulu.', 'warning');
                    return;
                }

                if (tglAwal > tglAkhir) {
                    Swal.fire('Filter Gagal',
                        'Tanggal Input (Awal) tidak boleh lebih besar dari Tanggal Input (Akhir).',
                        'warning');
                    return;
                }

                // Reload DataTable dengan parameter tanggal yang baru
                table.ajax.reload(); // ajax.reload() akan memanggil ulang fungsi data() di atas
            });
        });
        // --- 3. Contoh Fungsi JavaScript Aksi (View Detail PO) ---
        function viewDetailPO(noPO) {
            // Tampilkan Modal
            $('#modalDetailPO').modal('show');
            $('#v_detail_po').html(''); // Kosongkan konten lama
            // Loader AJAX
            let loader =
                '<div class="text-center py-5"><div class="spinner-border text-vla" role="status"></div><div class="text-muted mt-2 small">Mengambil detail PO...</div></div>';
            $('#v_detail_po').html(loader);
            // Ambil data detail via AJAX
            $.ajax({
                type: 'POST',
                url: '{{ route('gudang.purchase-order.ambil_detail_po') }}', // Buat route & controller ini
                data: {
                    _token: "{{ csrf_token() }}",
                    no_po: noPO
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail Purchase Order.', 'error');
                    $('#modalDetailPO').modal('hide');
                },
                success: function(response) {
                    // Tampilkan respon HTML (tabel detail, form header, dll) ke dalam modal
                    $('#v_detail_po').html(response);
                }
            });
        }

        function deletepo(noPO) {
            // Tampilkan Modal
            Swal.fire({
                title: "Anda yakin ?",
                text: "Data PO dengan NOMOR " + noPO + " Akan dibatalkan ...",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Batal ..."
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            noPO
                        },
                        url: '<?= route('batalkanpo') ?>',
                        error: function(response) {
                            spinner_off()
                            alert('error')
                        },
                        success: function(response) {
                            spinner_off()
                            if (response.status == 'success') {
                                Swal.fire({
                                    title: response.message,
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                location.reload()
                            } else {
                                Swal.fire({
                                    title: response.message,
                                    icon: "error",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            }
                        }
                    });
                }
            });
        }

        function cariobat() {
            kodesupplier = $('#supplier_id').val()
            kategori_barang = $('#kategoribarang').val()
            $.ajax({
                type: 'POST',
                url: '{{ route('pencarianbarang') }}', // Buat route & controller ini
                data: {
                    _token: "{{ csrf_token() }}",
                    kodesupplier,
                    kategori_barang
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail Purchase Order.', 'error');
                },
                success: function(response) {
                    // Tampilkan respon HTML (tabel detail, form header, dll) ke dalam modal
                    $('.v_t_b').html(response);
                }
            });
        }
    </script>
@endsection
