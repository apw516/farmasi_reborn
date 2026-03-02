@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Log kartu Stok</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Log kartu Stok</li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="card mt-3">
                <div class="card-header">Data Log kartu Stok</div>
                <div class="card-body">                    
                    <div class="v_data_barang">
                        <table id="tabel_barang" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tgl Stok</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Unit</th>
                                    <th>Stok tersisa</th>
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
        $(document).ready(function() {
            $('#tabel_barang').DataTable({
                processing: true,
                serverSide: true, // Fitur server side aktif
                ajax: "{{ route('ambildatastok') }}",
                language: {
                    // Mengganti tulisan "Processing..." dengan gambar
                    processing: '<div class="loading-container">' +
                                    '<img src="{{ asset("public/img/fb.gif") }}" width="80">' +
                                    '<p>Sedang mengambil data...</p>' +
                                '</div>'
                },
                columns: [{
                        data: 'tgl_stok',
                        name: 'tgl_stok'
                    }, // Nomor urut otomatis
                    {
                        data: 'kode_barang',
                        name: 'kode_barang'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'unit',
                        name: 'unit'
                    },
                    {
                        data: 'stok_last',
                        name: 'stok_last'
                    }
                ]
            });
        });
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
    </script>
@endsection
