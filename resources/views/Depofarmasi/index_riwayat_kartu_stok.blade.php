@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Riwayat Kartu Stok</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Riwayat Pelayanan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Pilih Unit</label>
                        <select class="form-select form-select-lg mb-3" aria-label="Large select example" id="filter_unit">
                            @foreach ($mt_unit as $u)
                                <option value="{{ $u->kode_unit }}" @if ($u->kode_unit == auth()->user()->unit) selected @endif>
                                    {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                {{-- <div class="col-md-3">
                    <button class="btn btn-success" style="margin-top:36px" onclick="tampilkandata()"><i
                            class="bi bi-search" style="margin-right:4px"></i> Tampilkan</button>
                </div> --}}
            </div>
            <div class="card">
                <div class="card-header">Data Kartu Stok</div>
                <div class="card-body">
                    <div class="v_data">
                        <table id="tabel_barang" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tgl Stok</th>
                                    <th>No Dokumen</th>
                                    <th>Nama Barang</th>
                                    <th>Stok Last</th>
                                    <th>Stok IN</th>
                                    <th>Stok OUT</th>
                                    <th>Stok Current</th>
                                    <th>Keterangan</th>
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
            var table = $('#tabel_barang').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('stok.data') }}",
                    data: function(d) {
                        d.kode_unit = $('#filter_unit').val(); // Ambil dari dropdown unit
                    }
                },
                columns: [{
                        data: 'no',
                        name: 'no',
                        class: 'text-center'
                    },
                    {
                        data: 'no_dokumen',
                        name: 'no_dokumen'
                    },
                    {
                        data: 'nama_barang',
                        name: 'nama_barang'
                    },
                    {
                        data: 'stok_last',
                        name: 'stok_last',
                        class: 'text-end font-weight-bold'
                    },
                    {
                        data: 'stok_in',
                        name: 'stok_in',
                        class: 'text-end font-weight-bold'
                    },
                    {
                        data: 'stok_out',
                        name: 'stok_out',
                        class: 'text-end font-weight-bold'
                    },
                    {
                        data: 'stok_current',
                        name: 'stok_current',
                        class: 'text-end font-weight-bold'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan',
                        class: 'text-end font-weight-bold'
                    },
                ]
            });

            // Refresh tabel saat unit diganti
            $('#filter_unit').on('change', function() {
                table.ajax.reload();
            });
        });
    </script>
@endsection
