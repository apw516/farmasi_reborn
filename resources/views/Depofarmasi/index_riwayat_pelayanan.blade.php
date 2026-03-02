@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Riwayat Pelayanan</h3>
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
                        <label for="exampleInputPassword1" class="form-label">Tanggal Awal</label>
                        <input type="date" class="form-control" id="tanggalawal" value="{{ $today }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Tanggal Akhir</label>
                        <input type="date" class="form-control" id="tanggalakhir" value="{{ $today }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-success" style="margin-top:32px" onclick="tampilkandata()"><i
                            class="bi bi-search" style="margin-right:4px"></i> Tampilkan</button>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Data Resep</div>
                <div class="card-body">
                    <div class="v_data">

                    </div>
                
                </div>
            </div>
        </div>
    </div>

    <script>
     
        $(document).ready(function() {
            tampilkandata()
        })

        function tampilkandata() {
            tglawal = $('#tanggalawal').val()
            tglakhir = $('#tanggalakhir').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tglawal,
                    tglakhir
                },
                url: '<?= route('ambildatariwayatpelayanan') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_data').html(response);
                }
            });
        }
    </script>
@endsection
