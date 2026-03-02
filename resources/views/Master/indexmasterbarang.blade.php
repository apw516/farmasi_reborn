@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Barang</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Barang</li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <button class="btn btn-success"><i class="bi bi-database-fill-add" style="margin-right:8px "></i>
                Barang</button>
            <div class="card mt-3">
                <div class="card-header">Data Master Barang</div>
                <div class="card-body">
                    <div class="v_data_barang">
                        <table id="tabel_barang" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>sediaan</th>
                                    <th>Dosis</th>
                                    <th>Aturan Pakai</th>
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
        $(document).ready(function() {
            $('#tabel_barang').DataTable({
                processing: true,
                serverSide: true, // Fitur server side aktif
                ajax: "{{ route('ambilbarang') }}",
                columns: [{
                        data: 'kode_barang',
                        name: 'kode_barang'
                    }, // Nomor urut otomatis
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'satuan_besar',
                        name: 'satuan_besar'
                    },
                    {
                        data: 'sediaan',
                        name: 'sediaan'
                    },
                    {
                        data: 'dosis',
                        name: 'dosis'
                    },
                    {
                        data: 'aturan_pakai',
                        name: 'aturan_pakai'
                    }
                ]
            });
        });
    </script>
@endsection
