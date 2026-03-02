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
                <div class="row">
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
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="v_data">

            </div>
        </div>
        <!--end::Container-->
    </div>
    <script>
        $(document).ready(function() {
            tampilkanriwayat()
        })

        function tampilkanriwayat() {
            nomorkartu = $('#nomorkartu').val()
            tglawal = $('#tanggalawal').val()
            tglakhir = $('#tanggalakhir').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tglawal,
                    tglakhir,
                    nomorkartu
                },
                url: '<?= route('ambilriwayat_pelayananpeserta') ?>',
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
