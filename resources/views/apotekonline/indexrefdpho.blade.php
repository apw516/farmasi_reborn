@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Referensi DPHO</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Referensi DPHO</li>
                    </ol>
                </div>
                {{-- <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Nomor Kartu</label>
                            <input type="text" class="form-control" id="nomorkartu" aria-describedby="emailHelp"
                                value="">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggalawal" aria-describedby="emailHelp"
                                value="{{ $date_start }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggalakhir" aria-describedby="emailHelp"
                                value="{{ $date_end }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success" style="margin-top:32px" onclick="tampilkanriwayat()"><i class="bi bi-search"
                                style="margin-right:12px"></i> Tampilkan Riwayat</button>
                    </div>
                </div> --}}
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="container-fluid">
                <button class="btn btn-success" onclick="downloaddata()"><i class="bi bi-database-fill-add"
                        style="margin-right:8px "></i>
                    UPDATE DATA DPHO </button>
                <div class="card mt-3">
                    <div class="card-header">Data Barang</div>
                    <div class="card-body">
                        <div class="v_data_barang">
                            <table id="tabel_barang" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Kode Obat BPJS</th>
                                        <th>Nama Barang ( SIMRS )</th>
                                        <th>Nama Generik Lengkap ( BPJS )</th>
                                        <th>Nama Generik</th>
                                        <th>Restriki</th>
                                        {{-- <th>Supplier</th>
                                    <th>Alamat Supplier</th> --}}
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
        <!--end::Container-->
    </div>
    <script>
        function downloaddata() {
            Swal.fire({
                title: "Anda yakin ?",
                text: "Data yang sudah terdownload sebelumnya akan diupdate !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, update !"
            }).then((result) => {
                if (result.isConfirmed) {
                    get_ref_dpho()
                }
            });
        }

        function get_ref_dpho() {
            spinner_on()
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                url: '<?= route('downloadrefdpho') ?>',
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
                        location.reload()
                    }
                }
            });
        }
        $(document).ready(function() {
            $('#tabel_barang').DataTable({
                processing: true,
                serverSide: true, // Fitur server side aktif
                ajax: "{{ route('ambilbarangdpho') }}",
                columns: [{
                        data: 'kode_barang',
                        name: 'simrs.kode_barang'
                    }, // Nomor urut otomatis
                    {
                        data: 'kodeobat',
                        name: 'kodeobat'
                    },
                    {
                        data: 'nama_simrs',
                        name: 'simrs.nama_barang'
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
                    }
                    // {
                    //     data: 'nama_supplier',
                    //     name: 'mt_supplier.nama_supplier'
                    // },
                    // {
                    //     data: 'alamat_supplier',
                    //     name: 'mt_supplier.alamat_supplier'
                    // }
                ]
            });
        });
    </script>
@endsection
