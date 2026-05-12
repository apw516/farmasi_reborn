@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Retur Ke Supplier</h3>
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
                                            <i class="bi bi-plus-circle me-1"></i> Retur Faktur Pembelian (Header & Detail)
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
                                    <table id="tbreturheader"
                                        class="table table-sm table-bordered table-hover align-middle w-100 fs-7">
                                        <thead class="table-light text-center text-uppercase fs-8 text-muted fw-bold">
                                            <tr>
                                                <th width="3%">No</th>
                                                <th width="12%">Tgl. Retur</th>
                                                <th width="10%">Kode Ret. PO</th>
                                                <th width="10%">No. PO</th>
                                                <th width="10%">No. Faktur</th>
                                                <th>Supplier</th>
                                                <th>Alasan Retur</th>
                                                <th width="8%">Tanggal PO</th>
                                                <th width="12%">Total Retur</th>
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
                        <div class="card-header">Silahkan Cari Nomor Faktur Yang ingin diretur ...</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="exampleInputPassword1" class="form-label">Tanggal Awal</label>
                                        <input type="date" class="form-control" value="{{ $date_start }}"
                                            id="tanggalawalpo">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="exampleInputPassword1" class="form-label">Tanggal Awal</label>
                                        <input type="date" class="form-control" value="{{ $date_end }}"
                                            id="tanggalakhirpo">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <button class="btn btn-success" style="margin-top:32px" id="btnProsesFilter2"><i
                                                class="bi bi-search"></i> Cari</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <table id="tablepoheader"
                                        class="table table-sm table-bordered table-hover align-middle w-100 fs-7">
                                        <thead class="table-light text-center text-uppercase fs-8 text-muted fw-bold">
                                            <tr>
                                                <th>NO</th>
                                                <th>Tanggal PO</th>
                                                <th>Nomor PO</th>
                                                <th>Nomor Faktur</th>
                                                <th>Nama Supplier</th>
                                                <th>Status Pembayaran</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="card-header mt-3">Detail PO</div>
                        <div class="card-body">
                            <table id="tablepodetail"
                                class="table table-sm table-bordered table-hover align-middle w-100 fs-7">
                                <thead class="table-light text-center text-uppercase fs-8 text-muted fw-bold">
                                    <tr>
                                        <th>NO</th>
                                        <th>Nomor PO</th>
                                        <th>Nama Barang</th>
                                        <th>Qty kecil</th>
                                        <th>Qty</th>
                                        <th>Satuan</th>
                                        <th>isi</th>
                                        <th>Nomor Batch</th>
                                        <th>ED</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-header mt-2">List Barang yang akan diretur</div>
                        <div class="card-body">
                            <div class="card-header">Data Header</div>
                            <div class="card-body mb-4">
                                <div class="row">
                                    <div class="col-md-3">
                                        <label class="small fw-bold">Kode PO</label>
                                        <input type="text" name="kode_po_header" id="kode_po_header"
                                            class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold">Tanggal Retur</label>
                                        <input type="date" name="tgl_retur_header" id="tgl_retur_header"
                                            class="form-control form-control-sm" value="">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="small fw-bold">Alasan Retur</label>
                                        <select class="form-select" aria-label="Default select example" id="alasan_retur">
                                            <option value="RETUR">Retur</option>
                                            <option value="EXPIRED">Expired</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body-retur">

                            </div>
                        </div>
                        <div class="card-footer">
                            <button id="btnSimpanRetur" class="btn btn-success"><i class="bi bi-floppy"></i> Simpan Data
                                Retur</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
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
            $("#unit_search").autocomplete({
                source: "{{ route('unit.search') }}",
                minLength: 2, // Mulai mencari setelah 2 karakter
                select: function(event, ui) {
                    // Set ID supplier ke hidden input saat dipilih
                    $("#kode_unit").val(ui.item.id);
                }
            });
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
        $(document).ready(function() {
            // --- 1. Konfigurasi DataTable Server-side KynovaPharma ---
            var table = $('#tbreturheader').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('dataretursupplier') }}",
                    data: function(d) {
                        // Kirim data tambahan ke server
                        d.tanggalawal = $('#tanggalawal').val();
                        d.tanggalakhir = $('#tanggalakhir').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false, // Matikan pencarian
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_retur',
                        name: 'retur.tgl_retur', // Pastikan menggunakan prefix tabel.kolom
                        searchable: true, // Aktifkan pencarian
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: 'kode_retur_po',
                        name: 'retur.kode_retur_po',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'kode_po',
                        name: 'retur.kode_po',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'no_faktur',
                        name: 'po.no_faktur',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'nama_supplier',
                        name: 'nama_supplier', // Ini alias dari DB::raw
                        searchable: true // Aktifkan pencarian
                    },
                    {
                        data: 'alasan_retur',
                        name: 'retur.alasan_retur',
                        searchable: false // Matikan pencarian
                    },
                    {
                        data: 'tgl_input',
                        name: 'po.tgl_input',
                        searchable: false // Matikan pencarian
                    },
                    {
                        data: 'total_retur',
                        name: 'retur.total_retur',
                        searchable: false // Matikan pencarian
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false, // Matikan pencarian
                        className: 'text-center bg-light'
                    }
                ]
            });
            // Trigger reload tabel saat tombol filter diklik
            $('#btn-filter').on('click', function() {
                table.draw();
            });
            // --- 2. Logic Tombol Proses Filter Tanggal ---
            $('#btnProsesFilter').on('click', function() {
                // Cek validasi tanggal
                let tglAwal = $('#tanggalawal').val();
                let tglAkhir = $('#tanggalakhir').val();
                let keterangan = $('#keterangan').val();
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
        $(document).ready(function() {
            // --- 1. Konfigurasi DataTable Server-side KynovaPharma ---
            var table2 = $('#tablepoheader').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 5, // <--- Set default tampilan menjadi 5 data
                lengthMenu: [5, 10, 25, 50],
                ajax: {
                    url: "{{ route('datapoheaderretur') }}",
                    data: function(d) {
                        // Kirim data tambahan ke server
                        d.tanggalawal = $('#tanggalawalpo').val();
                        d.tanggalakhir = $('#tanggalakhirpo').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false, // Matikan pencarian
                        className: 'text-center'
                    },
                    {
                        data: 'tgl_input',
                        name: 'tgl_input', // Pastikan menggunakan prefix tabel.kolom
                        searchable: true, // Aktifkan pencarian
                        className: 'text-center text-nowrap'
                    },
                    {
                        data: 'kode_po',
                        name: 'kode_po',
                        searchable: true, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'no_faktur',
                        name: 'no_faktur',
                        searchable: true, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'nama_supplier',
                        name: 'nama_supplier',
                        searchable: true, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'status_pembayaran',
                        name: 'status_pembayaran',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false, // Matikan pencarian
                        className: 'text-center bg-light'
                    }
                ]
            });
            // Trigger reload tabel saat tombol filter diklik
            $('#btn-filter2').on('click', function() {
                table2.draw();
            });
            // --- 2. Logic Tombol Proses Filter Tanggal ---
            $('#btnProsesFilter2').on('click', function() {
                // Cek validasi tanggal
                let tglAwal = $('#tanggalawalpo').val();
                let tglAkhir = $('#tanggalakhirpo').val();
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
                // Reload DataTable2 dengan parameter tanggal yang baru
                table2.ajax.reload(); // ajax.reload() akan memanggil ulang fungsi data() di atas
            });
        });

        function pilihpoheader(id) {
            if ($.fn.DataTable.isDataTable('#tablepodetail')) {
                $('#tablepodetail').DataTable().destroy();
            }
            var table = $('#tablepodetail').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('datadetailporetur') }}",
                    data: function(d) {
                        // Kirim data tambahan ke server
                        d.id = id
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false, // Matikan pencarian
                        className: 'text-center'
                    },
                    {
                        data: 'kode_po',
                        name: 'kode_po',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'qty_kecil',
                        name: 'qty_kecil',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'qty',
                        name: 'qty',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'isi',
                        name: 'isi',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'batch',
                        name: 'batch',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'ed',
                        name: 'ed',
                        searchable: false, // Matikan pencarian
                        className: 'text-center fw-bold text-primary'
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false, // Matikan pencarian
                        className: 'text-center bg-light'
                    }
                ]
            });
            $('#kode_po_header').val(id)
        }

        $(document).on('click', '.btn-pilih', function() {
            let kodeBarang = $(this).data('id');
            let batch = $(this).data('batch');
            let kodePO = $(this).data('nomor_po');
            let ed = $(this).data('ed');
            let id_detail = $(this).data('id_detail');
            $.ajax({
                url: "{{ route('get.form.retur') }}",
                type: "GET",
                data: {
                    kode_barang: kodeBarang,
                    kode_po: kodePO,
                    batch: batch,
                    ed: ed,
                    id_detail:id_detail
                },
                success: function(response) {
                    // Append hasil view dari controller ke dalam card-body
                    $('.card-body-retur').append(response);

                    // Opsional: Berikan notifikasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Barang ditambahkan ke list',
                        timer: 1000,
                        showConfirmButton: false
                    });
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil data stok sediaan', 'error');
                }
            });
        });
        // Fungsi untuk menghapus baris form dinamis
        $(document).on('click', '.btn-remove-item', function() {
            $(this).closest('.row-retur-item').remove();
        });

        $('#btnSimpanRetur').on('click', function(e) {
            e.preventDefault();
            // 1. Validasi sederhana: Cek apakah ada barang yang dipilih
            if ($('.row-retur-item').length === 0) {
                Swal.fire('Peringatan', 'Belum ada barang yang dipilih untuk diretur.', 'warning');
                return;
            }

            // 2. Kumpulkan data (Gunakan selector yang membungkus semua input)
            // Asumsikan semua input berada di dalam satu container besar atau form
            let formData = {
                _token: "{{ csrf_token() }}",
                kode_supplier: $('#kode_supplier_header').val(), // Ambil dari input header Anda
                kode_po: $('#kode_po_header').val(),
                tgl_retur: $('#tgl_retur_header').val(),
                alasan_retur: $('#alasan_retur').val(),
                // Ambil data array dari baris dinamis
                items: []
            };

            $('.row-retur-item').each(function() {
                formData.items.push({
                    id_detail: $(this).find('input[name="id_detail[]"]').val(),
                    kode_barang: $(this).find('input[name="kode_barang[]"]').val(),
                    batch: $(this).find('input[name="batch[]"]').val(),
                    qty_retur: $(this).find('input[name="qty_retur[]"]').val(),
                    harga: $(this).find('input[name="harga[]"]')
                        .val() // Jika ada hidden input harga
                });
            });

            // 3. Kirim via AJAX
            $.ajax({
                url: "{{ route('simpanretursupplier') }}",
                type: "POST",
                data: formData,
                beforeSend: function() {
                    $('#btnSimpanRetur').prop('disabled', true).html(
                        '<span class="spinner-border spinner-border-sm"></span> Menyimpan...');
                },
                success: function(res) {
                    Swal.fire('Berhasil!', 'Data retur berhasil disimpan.', 'success').then(() => {
                        location.reload(); // Atau arahkan ke halaman index
                    });
                },
                error: function(xhr) {
                    $('#btnSimpanRetur').prop('disabled', false).html(
                        '<i class="bi bi-floppy"></i> Simpan Data Retur');
                    let err = xhr.responseJSON;
                    Swal.fire('Gagal', err.message || 'Terjadi kesalahan sistem', 'error');
                }
            });
        });
    </script>
@endsection
