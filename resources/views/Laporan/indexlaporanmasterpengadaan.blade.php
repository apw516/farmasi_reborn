@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Laporan Pembelian Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Laporan Pembelian Barang</li>
                    </ol>
                </div>
                <div class="row">
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
                        <button class="btn btn-success" style="margin-top:32px" onclick="tampilkandata()"><i
                                class="bi bi-search" style="margin-right:12px"></i> Tampilkan Data</button>
                    </div>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="v_r">

            </div>
        </div>
        <!--end::Container-->
    </div>
    <script>
        function tampilkandata() {
            spinner_on()
            tanggalawal = $('#tanggalawal').val()
            tanggalakhir = $('#tanggalakhir').val()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggalawal,
                    tanggalakhir
                },
                url: '<?= route('ambildatalaporanpembelianbarang') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_r').html(response);
                }
            });
        }
    </script>
@endsection
