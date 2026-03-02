@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master DPHO</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master DPHO</li>
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
                                    <th>Supplier</th>
                                    <th>Alamat Supplier</th>
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
                    },
                    {
                        data: 'nama_supplier',
                        name: 'mt_supplier.nama_supplier'
                    },
                    {
                        data: 'alamat_supplier',
                        name: 'mt_supplier.alamat_supplier'
                    }
                ]
            });
        });
    </script>
@endsection
