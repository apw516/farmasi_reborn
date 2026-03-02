@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Mapping Master Barang</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Mapping Master Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Pilih Master Barang ( SIMRS )</div>
                        <div class="card-body">
                            <div class="v_data_barang">
                                <table id="tabel_barang_simrs" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kode Barang</th>
                                            <th>Nama Barang</th>
                                            <th>Satuan</th>
                                            <th>sediaan</th>
                                            <th>Dosis</th>
                                            <th>Status</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Pilih Nama Obat Generik ( BPJS )</div>
                        <div class="card-body">
                            <div class="v_data_barang">
                                <table id="tabel_barang_bpjs" class="table table-bordered table-hover"
                                    style="font-size:14px">
                                    <thead>
                                        <tr>
                                            <th>Kode Obat</th>
                                            <th>Nama Obat</th>
                                            <th>Generik</th>
                                            <th>Restriksi</th>
                                            <th>Dosis</th>
                                            <th></th>
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
            <div class="row">
                <div class="card">
                    <div class="card-header">Form Mapping data Obat BPJS dan SIMRS</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">List Obat SIMRS</div>
                                    <div class="card-body">
                                        <form action="" method="post" class="v_list_barang  mt-2 mt-2">
                                            <div class="draft_barang">
                                                <div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">Nama Obat BPJS</div>
                                    <div class="card-body">
                                        <form class="form_obat_bpjs" id="form_obat_bpjs">
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Nama Obat</label>
                                                <input readonly type="text" class="form-control" id="namaobatbpjs"
                                                    name="namaobatbpjs" aria-describedby="emailHelp">
                                                <input hidden readonly type="text" class="form-control" id="kodeobatbpjs"
                                                    name="kodeobatbpjs" aria-describedby="emailHelp">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Generik</label>
                                                <input readonly type="text" class="form-control" id="generik"
                                                    name="generik" aria-describedby="emailHelp">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleInputEmail1" class="form-label">Restriksi</label>
                                                <input readonly type="text" class="form-control" id="restriksi"
                                                    name="restriksi" aria-describedby="emailHelp">
                                            </div>
                                            <div class="mb-3">
                                                <label for="exampleInputPassword1" class="form-label">Dosis</label>
                                                <input readonly type="text" class="form-control" id="dosis"
                                                    name="dosis">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success float-end" onclick="alertsimpandatamapping()">Simpan Data</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var tableBPJS = $('#tabel_barang_bpjs').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 3,
                ajax: "{{ route('ambilbarangbpjs') }}",
                columns: [{
                        data: 'kodeobat',
                        name: 'kodeobat'
                    },
                    {
                        data: 'namaobat',
                        name: 'namaobat'
                    },
                    {
                        data: 'generik',
                        name: 'generik'
                    },
                    {
                        data: 'restriksi',
                        name: 'restriksi'
                    },
                    {
                        data: 'sedia',
                        name: 'sedia'
                    },
                    {
                        // Tambahkan kolom aksi di sini
                        data: null,
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                    <button class="btn btn-sm btn-success btn-pilih-bpjs" 
                            data-kode="${row.kodeobat}" 
                            data-nama="${row.namaobat}"
                            data-restriksi="${row.restriksi}"
                            data-generik="${row.generik}"
                            data-sedia="${row.sedia}">
                        <i class="bi bi-check2-square"></i>
                    </button>
                `;
                        }
                    }
                ]
            });
        });
        $(document).ready(function() {
            $('#tabel_barang_simrs').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 5,
                ajax: "{{ route('ambilbarang') }}",
                columns: [{
                        data: 'kode_barang',
                        name: 'mt_barang.kode_barang'
                    },
                    {
                        data: 'nama_barang',
                        name: 'mt_barang.nama_barang'
                    },
                    {
                        data: 'satuan_besar',
                        name: 'mt_barang.satuan_besar'
                    },
                    {
                        data: 'sediaan',
                        name: 'mt_barang.sediaan'
                    },
                    {
                        data: 'dosis',
                        name: 'mt_barang.dosis'
                    },
                    {
                        data: 'kode_obat_bpjs',
                        name: 'master_barang_x_master_obat_bpjs.kode_obat_bpjs',
                        render: function(data, type, row) {
                            // Kondisi jika data null atau kosong
                            if (data == null || data == '') {
                                return '<span class="badge badge-danger bg-danger text-light"><i class="bi bi-info-circle"></i>Belum Dimapping</span>';
                            } else {
                                return '<span class="badge badge-success bg-success text-light"><i class="bi bi-info-circle"></i>Sudah Mapping</span>';
                            }
                        }
                    },
                    {
                        data: null,
                        name: 'aksi',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return ` <button class="btn btn-sm btn-primary btn-pilih" data-id="${row.kode_barang}" data-nama="${row.nama_barang}" data-satuan="${row.satuan_besar}" data-sediaan="${row.sediaan}" data-dosis="${row.dosis}"><i class="bi bi-check2-square"></i></button>`;
                        }
                    }
                ]
            });
        });
        $('#tabel_barang_simrs tbody').on('click', '.btn-pilih', function() {
            id_barang = $(this).attr('data-id')
            nama_barang = $(this).attr('data-nama')
            satuan = $(this).attr('data-satuan')
            sediaan = $(this).attr('data-sediaan')
            dosis = $(this).attr('data-dosis')
            var wrapper = $(".draft_barang");
            $(wrapper).append(
                '<div class="row"><div class="col-md-4"><label for="exampleFormControlInput1" class="form-label">Nama Barang</label><input readonly type="text" class="form-control" id="namabarang" name="namabarang" value="' +
                nama_barang +
                '"><input hidden  readonly type="text" class="form-control" id="kodebarang" name="kodebarang" value="' +
                id_barang +
                '"></div><div class="col-md-2"><label for="exampleFormControlInput1" class="form-label">Satuan</label><input readonly type="text" class="form-control" id="satuan" name="satuan" value="' +
                satuan +
                '"></div><div class="col-md-2"><label for="exampleFormControlInput1" class="form-label">Sediaan </label><input readonly type="text" class="form-control" id="sediaan" name="sediaan" value="' +
                sediaan +
                '"></div><div class="col-md-2"><label for="exampleFormControlInput1" class="form-label">Dosis</label><input readonly type="text" class="form-control" id="dosis" name="dosis" value="' +
                dosis +
                '"></div><i class="bi bi-x-square remove_field form-group col-md-1 text-danger" kode2=""></i></div>'
            );
            $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
                e.preventDefault();
                $(this).parent('div').remove();
                x--;
            })
            Swal.fire({
                title: nama_barang + " Berhasil dipilih ...",
                text: "Silahkan scroll kebawah untuk melihat list obat yang sudah dipilih ..",
                icon: "success"
            });
        });
        $('#tabel_barang_bpjs tbody').on('click', '.btn-pilih-bpjs', function() {
            // Ambil data dari atribut tombol
            kode = $(this).attr('data-kode');
            nama = $(this).attr('data-nama');
            restriksi = $(this).attr('data-restriksi');
            generik = $(this).attr('data-generik');
            sedia = $(this).attr('data-sedia');
            $('#namaobatbpjs').val(nama)
            $('#kodeobatbpjs').val(kode)
            $('#generik').val(generik)
            $('#restriksi').val(restriksi)
            $('#dosis').val(sedia)

        });
        function alertsimpandatamapping() {
            Swal.fire({
                title: "Anda yakin data sudah benar ?",
                text: "Data obat akan disimpan ...",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Simpan !"
            }).then((result) => {
                if (result.isConfirmed) {
                    simpanmappobat()
                }
            });
        }
        function simpanmappobat() {
            spinner_on()
            var data_simrs = $('.v_list_barang').serializeArray();
            var data_bpjs = $('.form_obat_bpjs').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    data_simrs: JSON.stringify(data_simrs),
                    data_bpjs: JSON.stringify(data_bpjs),
                },
                url: '<?= route('simpanmappingobat') ?>',
                error: function(data) {
                    spinner_off()
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooops....',
                        text: 'Sepertinya ada masalah......',
                        footer: ''
                    })
                },
                success: function(data) {
                    spinner_off()
                    if (data.kode == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oopss...',
                            text: data.message,
                            footer: ''
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'OK',
                            text: data.message,
                            footer: ''
                        })
                        document.getElementById("form_obat_bpjs").reset();
                        location.reload()
                    }
                }
            });
        }
    </script>
@endsection
