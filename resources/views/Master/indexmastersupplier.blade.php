@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Supplier Obat</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Supplier Obat</li>
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
                Supplier</button>
            <div class="card mt-3">
                <div class="card-header">Data Master Supplier</div>
                <div class="card-body">
                    <div class="v_data_barang">
                        <table id="tabel_data" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Kode Supplier</th>
                                    <th>Kategori Supplier</th>
                                    <th>Nama Supplier</th>
                                    <th>Alamat Supplier</th>
                                    <th>Kontak Person</th>
                                    <th>Nomor telepon</th>
                                    <th>Termin</th>
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
            $('#tabel_data').DataTable({
                processing: true,
                serverSide: true, // Fitur server side aktif
                ajax: "{{ route('ambilsupplier') }}",
                columns: [{
                        data: 'kode_supplier',
                        name: 'kode_supplier'
                    }, // Nomor urut otomatis
                    {
                        data: 'kategori_supplier',
                        name: 'kategori_supplier'
                    },
                    {
                        data: 'nama_supplier',
                        name: 'nama_supplier'
                    },
                    {
                        data: 'alamat_supplier',
                        name: 'alamat_supplier'
                    },
                    {
                        data: 'cp',
                        name: 'cp'
                    },
                    {
                        data: 'tlp',
                        name: 'tlp,'
                    }, 
                    {
                        data: 'termin',
                        name: 'termin,'
                    }
                ]
            });
        });
    </script>
@endsection
