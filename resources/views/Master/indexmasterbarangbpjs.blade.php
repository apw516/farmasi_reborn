@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Obat BPJS</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Obat BPJS</li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <button class="btn btn-success" onclick="downloaddata()"><i class="bi bi-database-fill-add"
                    style="margin-right:8px "></i>
                Download Data ke BPJS</button>
            <div class="card mt-3">
                <div class="card-header">Data Master Obat</div>
                <div class="card-body">
                    <div class="v_data_barang">
                        <table id="tabel_barang" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tgl Download</th>
                                    <th>Kode Obat</th>
                                    <th>Nama Obat</th>
                                    <th>Generik</th>
                                    <th>Restriksi</th>
                                    <th>Dosis</th>
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
                ajax: "{{ route('ambilbarangbpjs') }}",
                columns: [{
                        data: 'tgl_download',
                        name: 'tgl_download'
                    }, // Nomor urut otomatis
                    {
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
