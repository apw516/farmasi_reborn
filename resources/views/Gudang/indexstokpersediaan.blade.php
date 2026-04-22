@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Stok Persediaan</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Stok Persediaan</li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Pilih Unit</label>
                        <select class="form-select form-select-lg mb-3" aria-label="Large select example" id="filter_unit">
                            <option value="">
                                SEDIAAN SEMUA UNIT</option>
                            @foreach ($mt_unit as $u)
                                <option value="{{ $u->kode_unit }}" @if ($u->kode_unit == auth()->user()->unit) selected @endif>
                                    {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Pilih Tipe Barang</label>
                        <select class="form-select form-select-lg mb-3" aria-label="Large select example" id="filtertipe">
                            <option value="">
                                SEMUA TIPE</option>
                            @foreach ($tipe as $u)
                                <option value="{{ $u->kode_tipe }}">
                                    {{ $u->nama_tipe }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card mt-3">
                {{-- <div class="card-header">Data Stok Persediaan</div> --}}
                <div class="card-body">
                    <div class="v_data_barang">
                        <style>
                            /* Membuat header tabel lebih tegas */
                            #tabelStok thead th {
                                background-color: #f8f9fa;
                                color: #334155;
                                text-transform: uppercase;
                                font-size: 0.75rem;
                                letter-spacing: 0.025em;
                                vertical-align: middle;
                                border-bottom: 2px solid #e2e8f0;
                            }

                            /* Memperhalus baris tabel */
                            #tabelStok tbody td {
                                vertical-align: middle;
                                font-size: 0.875rem;
                                color: #475569;
                            }

                            /* Badge kustom untuk sediaan dan ED */
                            .badge-sediaan {
                                background-color: #e0f2fe;
                                color: #0369a1;
                                font-weight: 600;
                                padding: 0.4em 0.8em;
                                border-radius: 6px;
                            }

                            .text-expired {
                                color: #dc2626;
                                font-weight: bold;
                            }
                        </style>
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3">
                                <h6 class="m-0 font-weight-bold text-primary">Tabel Persediaan Barang</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="tabelStok" class="table table-hover table-sm w-100">
                                        <thead>
                                            <tr>
                                                <th class="text-center">No</th>
                                                <th>Nomor Faktur</th>
                                                <th>Batch</th>
                                                {{-- <th>Kode</th> --}}
                                                <th>Nama Barang</th>
                                                <th class="text-center">Expired</th>
                                                <th class="text-right">Stok</th>
                                                {{-- <th>Stok</th> --}}
                                                <th>Unit</th>
                                                <th class="text-right">HPP</th>
                                                <th class="text-center">Entry</th>
                                                <th class="text-center">Aksi</th>
                                            </tr>
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
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modalmutasibarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Mutasi Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="v_form_mutasi">

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="simpanmutasi()">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .badge-warning {
            background-color: #ffc107;
            color: #000;
            padding: 0.4em 0.6em;
            border-radius: 4px;
            font-weight: bold;
        }

        .badge-danger {
            background-color: #dc3545;
            color: #fff;
            padding: 0.4em 0.6em;
            border-radius: 4px;
            font-weight: bold;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }

        .btn-group .btn i {
            margin-right: 3px;
        }
    </style>
    <script>
        $(document).ready(function() {
            var table = $('#tabelStok').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, 'desc']
                ],
                ajax: {
                    url: "{{ route('ambildatasediaan') }}",
                    data: function(d) {
                        d.kode_unit = $('#filter_unit').val(); // Ambil dari dropdown unit
                        d.kode_tipe = $('#filtertipe').val(); // Ambil dari dropdown unit

                    }
                },
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari barang...",
                    lengthMenu: "_MENU_",
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nomor_faktur',
                        name: 'nomor_faktur',
                        render: function(data, type, row) {
                            // Menggabungkan Nomor Batch dengan No Dokumen dan Jenis Dokumen di bawahnya
                            var html = '<div>' + (data ? data : '-') + '</div>';

                            // Baris kedua berisi Jenis Dokumen & Nomor Dokumen
                            var detail =
                                '<small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.1;">Supplier : ' +
                                (row.nama_supplier ? row.nama_supplier : '') + '</small>';

                            return html + detail;
                        }
                    },
                    {
                        data: 'nomor_batch',
                        name: 'nomor_batch',
                        render: function(data, type, row) {
                            // Menggabungkan Nomor Batch dengan No Dokumen dan Jenis Dokumen di bawahnya
                            var html = '<div>' + (data ? data : '-') + '</div>';

                            // Baris kedua berisi Jenis Dokumen & Nomor Dokumen
                            // var detail =
                            //     '<small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.1;">' +
                            //     (row.jenis_dokumen ? row.jenis_dokumen : '') + ': ' +
                            //     (row.nomor_dokumen ? row.nomor_dokumen : '-') +
                            //     '</small>';

                            return html;
                        }
                    }, // Asumsi field db: no_batch
                    // {
                    //     data: 'kode_barang',
                    //     name: 'barang.kode_barang'
                    // },
                    {
                        data: 'nama_barang',
                        name: 'barang.nama_barang'
                    },
                    {
                        data: 'ED',
                        name: 'ED',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (!data || data === '-') return '-';

                            // Hitung selisih hari menggunakan Moment.js
                            var expiredDate = moment(data, "DD-MM-YYYY");
                            var today = moment().startOf('day');
                            var daysDiff = expiredDate.diff(today, 'days');

                            // Logika Warning: stok tidak nol DAN ED < 90 hari
                            // row.stok_sekarang mengambil nilai mentah dari data stok
                            if (row.stok_sekarang > 0 && daysDiff <= 90) {
                                // Jika sudah lewat (negatif), kasih warna merah (Danger)
                                if (daysDiff < 0) {
                                    return '<span class="badge badge-danger" title="Sudah Kedaluwarsa">' +
                                        data + '</span>';
                                }
                                // Jika mendekati 90 hari, kasih warna kuning (Warning)
                                return '<span class="badge badge-warning" title="Segera Kedaluwarsa (' +
                                    daysDiff + ' hari lagi)">' + data + '</span>';
                            }

                            return '<span class="text-secondary">' + data + '</span>';
                        }
                    },
                    {
                        data: 'sediaan',
                        name: 'sediaan',
                        render: function(data) {
                            return '<span class="badge-sediaan">' + data + '</span>';
                        }
                    },
                    {
                        data: 'nama_unit',
                        name: 'unit.nama_unit'
                    },
                    {
                        data: 'hpp',
                        name: 'hpp',
                        className: 'text-right'
                    },
                    {
                        data: 'tgl_entry',
                        name: 'created_at',
                        className: 'text-center',
                        searchable: false
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });

            // Refresh tabel saat unit diganti
            $('#filter_unit').on('change', function() {
                table.ajax.reload();
            });
            $('#filtertipe').on('change', function() {
                table.ajax.reload();
            });
        });
        // $(document).ready(function() {
        //     $('#tabelStok').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: "{{ route('ambildatasediaan') }}",
        //         order: [
        //             [1, 'desc']
        //         ], // Default urutkan berdasarkan Tgl Entry
        //         language: {
        //             search: "_INPUT_",
        //             searchPlaceholder: "Cari barang...",
        //             lengthMenu: "_MENU_",
        //         },
        //         columns: [{
        //                 data: 'DT_RowIndex',
        //                 name: 'DT_RowIndex',
        //                 orderable: false,
        //                 searchable: false,
        //                 className: 'text-center'
        //             },
        //             {
        //                 data: 'no_faktur',
        //                 name: 'po_header.no_faktur'
        //             },
        //             {
        //                 data: 'nomor_batch',
        //                 name: 'nomor_batch',
        //                 render: function(data, type, row) {
        //                     // Menggabungkan Nomor Batch dengan No Dokumen dan Jenis Dokumen di bawahnya
        //                     var html = '<div>' + (data ? data : '-') + '</div>';

        //                     // Baris kedua berisi Jenis Dokumen & Nomor Dokumen
        //                     var detail =
        //                         '<small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1.1;">' +
        //                         (row.jenis_dokumen ? row.jenis_dokumen : '') + ': ' +
        //                         (row.nomor_dokumen ? row.nomor_dokumen : '-') +
        //                         '</small>';

        //                     return html + detail;
        //                 }
        //             }, // Asumsi field db: no_batch
        //             {
        //                 data: 'kode_barang',
        //                 name: 'barang.kode_barang'
        //             },
        //             {
        //                 data: 'ED',
        //                 name: 'ED',
        //                 className: 'text-center',
        //                 render: function(data, type, row) {
        //                     if (!data || data === '-') return '-';

        //                     // Hitung selisih hari menggunakan Moment.js
        //                     var expiredDate = moment(data, "DD-MM-YYYY");
        //                     var today = moment().startOf('day');
        //                     var daysDiff = expiredDate.diff(today, 'days');

        //                     // Logika Warning: stok tidak nol DAN ED < 90 hari
        //                     // row.stok_sekarang mengambil nilai mentah dari data stok
        //                     if (row.stok_sekarang > 0 && daysDiff <= 90) {
        //                         // Jika sudah lewat (negatif), kasih warna merah (Danger)
        //                         if (daysDiff < 0) {
        //                             return '<span class="badge badge-danger" title="Sudah Kedaluwarsa">' +
        //                                 data + '</span>';
        //                         }
        //                         // Jika mendekati 90 hari, kasih warna kuning (Warning)
        //                         return '<span class="badge badge-warning" title="Segera Kedaluwarsa (' +
        //                             daysDiff + ' hari lagi)">' + data + '</span>';
        //                     }

        //                     return '<span class="text-secondary">' + data + '</span>';
        //                 }
        //             },
        //             {
        //                 data: 'nama_barang',
        //                 name: 'barang.nama_barang'
        //             },
        //             {
        //                 data: 'sediaan',
        //                 name: 'sediaan',
        //                 render: function(data) {
        //                     return '<span class="badge-sediaan">' + data + '</span>';
        //                 }
        //             },
        //             {
        //                 data: 'nama_unit',
        //                 name: 'unit.nama_unit'
        //             },
        //             {
        //                 data: 'hpp',
        //                 name: 'hpp',
        //                 className: 'text-right'
        //             },
        //             {
        //                 data: 'tgl_entry',
        //                 name: 'created_at',
        //                 className: 'text-center',
        //                 searchable: false
        //             },
        //             {
        //                 data: 'aksi',
        //                 name: 'aksi',
        //                 orderable: false,
        //                 searchable: false,
        //                 className: 'text-center'
        //             }
        //         ]
        //     });
        // });
        $(document).on('click', '.pilihbarangmutasi', function(event) {
            event.preventDefault(); // Mencegah reload jika berupa link
            var idbarang = $(this).attr('idbarang');
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    idbarang,
                },
                url: '<?= route('ambilformmutasi') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_form_mutasi').html(response);
                }
            });
        });

        function simpanmutasi() {
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
                            simpanmutasifinal()
                        } else if (result.isDenied) {

                        }
                    });
                }
            });
        }

        function simpanmutasifinal() {
            var data = $('.form_isi_mutasi').serializeArray();
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpanmutasi') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    if (response.code == '500') {
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
    </script>
@endsection
