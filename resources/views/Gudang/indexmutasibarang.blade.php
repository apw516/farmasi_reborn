@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Mutasi Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Mutasi Barang</li>
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
                                            <i class="bi bi-plus-circle me-1"></i> Buat Mutasi Baru (Header & Detail)
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
                                    <div class="text-muted mt-2">Mengambil data Mutasi...</div>
                                </div>
                                <div class="table-responsive p-3" id="wrapperDataTable">
                                    <table id="tabelMutasi" class="table table-custom table-hover align-middle w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th>Dokumen</th>
                                                <th>Unit Asal</th>
                                                <th>Unit Tujuan</th>
                                                <th>Rincian Barang</th>
                                                {{-- <th class="text-center">Aksi</th> --}}
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div hidden class="v_2">
                <button class="btn btn-danger" onclick="kembali()">Kembali</button>
                {{-- <div class="v_form_header mt-2"> --}}
                {{-- <div class="card"> --}}
                {{-- <div class="card-header">Form Mutasi Header</div> --}}
                <form action="" id="form_mutasi_utama">
                    @csrf
                    <div class="v_form_header mt-2">
                        <div class="card mb-3">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="bi bi-arrow-left-right me-2"></i>Form Mutasi Header</h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Unit Asal (Gudang Pengirim)</label>
                                        <select class="form-select form-select-sm select2" name="unit_asal" id="unit_asal">
                                            <option value="">- Pilih Unit Asal -</option>
                                            @foreach ($mt_unit as $u)
                                                <option value="{{ $u->kode_unit }}">{{ $u->nama_unit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small fw-bold">Unit Tujuan (Penerima)</label>
                                        <select class="form-select form-select-sm select2" name="unit_tujuan"
                                            id="unit_tujuan">
                                            <option value="">- Pilih Unit Tujuan -</option>
                                            @foreach ($mt_unit as $u)
                                                <option value="{{ $u->kode_unit }}">{{ $u->nama_unit }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div hidden class="col-md-3">
                                        <label class="form-label small fw-bold">Keterangan Mutasi</label>
                                        <input type="text" class="form-control form-control-sm" name="keterangan"
                                            placeholder="Contoh: Stok Mingguan">
                                    </div>
                                    <div class="col-md-3">
                                        <button style="margin-top:30px" type="button" class="btn btn-success caridataobat"
                                            data-bs-toggle="modal" data-bs-target="#modalcariobat" onclick="cariobat()">
                                            <i class="bi bi-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-light fw-bold text-secondary">
                                Detail Barang Mutasi
                            </div>
                            <div class="card-body">

                                {{-- <form id="form_input_detail" class="mb-4 p-2 border rounded shadow-sm bg-white">
                                <div class="row g-2 align-items-end">
                                    <input type="hidden" id="kode_barang" name="kode_barang">
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold mb-1">Nama Barang</label>
                                        <div class="input-group input-group-sm">
                                            <input readonly type="text" class="form-control"
                                                placeholder="Pilih barang..." id="nama_barang">
                                            <button type="button" class="btn btn-success caridataobat"
                                                data-bs-toggle="modal" data-bs-target="#modalcariobat"
                                                onclick="cariobat()">
                                                <i class="bi bi-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form> --}}
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered table-hover" id="tabel_mutasi_detail">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="5%" class="text-center">No</th>
                                                <th width="15%">Kode</th>
                                                <th>Nama Barang</th>
                                                <th>Stok tersedia</th>
                                                <th width="15%" class="text-center">Satuan</th>
                                                <th width="15%" class="text-center">Jumlah</th>
                                                <th width="10%" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody id="isi_tabel_mutasi">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer text-end">
                                <button type="button" class="btn btn-danger btn-sm me-2">Batal</button>
                                <button type="button" class="btn btn-success btn-sm px-4"
                                    onclick="simpanmutasi()">Simpan
                                    Mutasi</button>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- </div> --}}
                {{-- </div> --}}
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalcariobat" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
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
    <style>
        /* Styling Tabel Modern */
        .table-custom {
            border-collapse: separate;
            border-spacing: 0 8px;
            /* Memberi jarak antar baris */
            font-size: 0.85rem;
        }

        .table-custom thead th {
            background-color: #f8f9fa;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            border-top: none;
            border-bottom: 2px solid #dee2e6;
            padding: 12px;
        }

        .table-custom tbody tr {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: all 0.2s;
        }

        .table-custom tbody tr:hover {
            background-color: #f1f5f9 !important;
            transform: translateY(-1px);
        }

        .table-custom td {
            padding: 12px;
            border-top: 1px solid #edf2f7;
        }

        /* Badge untuk detail barang */
        .item-detail {
            display: inline-block;
            background: #eef2ff;
            color: #4338ca;
            padding: 2px 8px;
            border-radius: 4px;
            margin-bottom: 4px;
            font-size: 0.75rem;
            border: 1px solid #e0e7ff;
        }

        .unit-label {
            font-weight: 600;
            color: #2d3748;
        }
    </style>
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
        $(document).ready(function() {
            // --- 1. Konfigurasi DataTable Server-side KynovaPharma ---
            var table = $('#tabelMutasi').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('gudang.data_mutasi') }}",
                    data: function(d) {
                        // Kirim data tambahan ke server
                        d.tanggalawal = $('#tanggalawal').val();
                        d.tanggalakhir = $('#tanggalakhir').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center text-muted',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'kode_mutasi_header',
                        render: function(data, type, row) {
                            return `<div>
                            <span class="fw-bold text-primary" style="font-size:16px">${data}</span><br>
                            <small class="text-bold" style="font-size:16px"><i class="bi bi-calendar3"></i> ${row.tgl_mutasi}</small>
                        </div>`;
                        }
                    },
                    {
                        data: 'unit_asal_barang',
                        render: (data) =>
                            `<span class="unit-label">${data}</span>`
                    },
                    {
                        data: 'unit_tujuan_barang',
                        render: (data) =>
                            `<span class="unit-label">${data}</span>`
                    },
                    {
                        data: 'keterangan', // Ini yang tadi berisi detail barang
                        render: function(data) {
                            if (!data) return '-';
                            // Ubah pemisah <br> menjadi format badge jika perlu
                            let items = data.split('<br>');
                            return items.map(item =>
                                `<div class="item-detail text-dark text-bold" style="font-size:16px;">${item}</div>`
                            ).join(
                                ' ');
                        }
                    }
                ],
                // Mengatur layout DataTables (Search & Length) agar lebih rapi
                dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
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

        function cariobat() {
            unit_asal = $('#unit_asal').val()
            $.ajax({
                type: 'POST',
                url: '{{ route('pencariansediaanbarang') }}', // Buat route & controller ini
                data: {
                    _token: "{{ csrf_token() }}",
                    unit_asal
                },
                error: function() {
                    Swal.fire('Error', 'Gagal mengambil detail Purchase Order.', 'error');
                },
                success: function(response) {
                    if (response.code == 'error') {
                        Swal.fire('Error', 'Pilih unit pengirim ...', 'error');
                    } else {
                        $('.v_t_b').html(response);
                    }
                }
            });
        }
    </script>
    <script>
        $(document).on('click', '.btn-pilih-stok', function() {
            const d = $(this).data(); // Mengambil semua data-attribute (kode, nama, batch, ed, satuan, stok)
            // 1. Cek apakah barang dengan batch yang sama sudah ada di tabel agar tidak duplikat
            let exists = false;
            $('.row-id_sediaan').each(function() {
                if ($(this).val() == d.id_sediaan && $(this).closest('tr').find('.row-id_sediaan').val() ==
                    d.id_sediaan) {
                    exists = true;
                }
            });
            if (exists) {
                Swal.fire('Info', 'Barang dengan stok batch ini sudah ada di daftar mutasi.', 'info');
                return;
            }
            // 2. Hitung nomor urut
            const no = $('#isi_tabel_mutasi tr').length + 1;
            // 3. Template baris baru
            const row = `
                    <tr>
                        <td class="text-center current-no">${no}</td>
                        <td>
                            <input type="hidden" name="id_sediaan[]" class="row-id_sediaan" value="${d.id_sediaan}">
                            <input type="hidden" name="kode_barang[]" class="row-kode" value="${d.kode}">
                            <input type="hidden" name="no_batch[]" class="row-batch" value="${d.batch}">
                            <input type="hidden" name="ed[]" value="${d.ed}">
                            <small class="fw-bold">${d.batch}</small><br>
                            <code class="text-primary"> Nomor Faktur : ${d.nomor_faktur}</code>
                        </td>
                        <td>
                            <span class="fw-bold">${d.nama}</span><br>
                            <small class="text-muted small">ED: ${d.ed}</small>
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="stok_tersedia[]" value="${d.stok_tersedia}">
                            <span class="badge bg-light text-dark border">${d.stok_tersedia}</span>
                        </td>
                        <td class="text-center">
                            <input type="hidden" name="satuan[]" value="${d.satuan}">
                            <span class="badge bg-light text-dark border">${d.satuan}</span>
                        </td>
                        <td>
                            <input type="number" name="qty_mutasi[]" class="form-control form-control-sm text-center" 
                                value="1" min="1" max="${d.stok}">
                            <div class="text-center"><small class="text-muted" style="font-size:10px">Maks: ${d.stok}</small></div>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-circle btn-hapus-row">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
            // 4. Tambahkan ke tabel
            $('#isi_tabel_mutasi').append(row);
            // 5. Tutup modal
            // $('#modalcariobat').modal('hide');
        });

        // Fungsi untuk menghapus baris
        $(document).on('click', '.btn-hapus-row', function() {
            $(this).closest('tr').remove();
            // Reset ulang nomor urut agar tetap berurutan
            $('#isi_tabel_mutasi tr').each(function(index) {
                $(this).find('.current-no').text(index + 1);
            });
        });

        function simpanmutasi() {
            // 1. Validasi Header (Unit Asal & Tujuan)
            let unit_asal = $('#unit_asal').val();
            let unit_tujuan = $('#unit_tujuan').val();
            let jumlah_item = $('#isi_tabel_mutasi tr').length;
            if (!unit_asal || !unit_tujuan) {
                Swal.fire('Peringatan', 'Unit Asal dan Tujuan harus diisi!', 'warning');
                return;
            }
            if (unit_asal === unit_tujuan) {
                Swal.fire('Peringatan', 'Unit Asal dan Tujuan tidak boleh sama!', 'warning');
                return;
            }
            if (jumlah_item === 0) {
                Swal.fire('Peringatan', 'Belum ada barang yang ditambahkan ke daftar mutasi!', 'warning');
                return;
            }
            // 2. Konfirmasi Simpan
            Swal.fire({
                title: 'Konfirmasi Simpan',
                text: "Apakah Anda yakin data mutasi sudah benar?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Simpan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {

                    // 3. Eksekusi AJAX
                    $.ajax({
                        url: "{{ route('mutasibarang.simpan') }}", // Ganti dengan route Anda
                        type: "POST",
                        data: $('#form_mutasi_utama')
                            .serialize(), // Pastikan semua input (header & detail) ada di dalam satu ID form ini
                        beforeSend: function() {
                            Swal.fire({
                                title: 'Memproses...',
                                text: 'Sedang menyimpan data mutasi',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function(response) {
                            if (response.code === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    // Reload halaman atau arahkan ke cetak nota
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Gagal', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let errorMsg = xhr.responseJSON ? xhr.responseJSON.message :
                                'Terjadi kesalahan sistem.';
                            Swal.fire('Error ' + xhr.status, errorMsg, 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
