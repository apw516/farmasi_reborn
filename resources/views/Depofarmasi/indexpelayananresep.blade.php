@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Pelayanan Resep</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pelayanan Resep</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="v_1">
                <div class="card">
                    <div class="card-header">Tentukan tanggal kunjungan pasien</div>
                    <div class="card-body">
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
                                    <input type="date" class="form-control" id="tanggalakhir"
                                        aria-describedby="emailHelp" value="{{ $date_end }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success" style="margin-top:31px" onclick="carikunjungan()"><i
                                        class="bi bi-search" style="margin-right:8px"></i> Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">Data Kunjungan Pasien</div>
                    <div class="card-body">
                        <div class="v_data_pasien">

                        </div>
                    </div>
                </div>
            </div>
            <div hidden class="v_2">

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            carikunjungan()
        })
        function carikunjungan() {
            tanggalawal = $('#tanggalawal').val()
            tanggalakhir = $('#tanggalakhir').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggalawal,
                    tanggalakhir
                },
                url: '<?= route('ambildatakunjungan') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_data_pasien').html(response);
                }
            });
        }
    </script>
@endsection
