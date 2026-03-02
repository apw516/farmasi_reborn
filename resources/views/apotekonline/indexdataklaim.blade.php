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
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Bulan</label>
                            <select class="form-select" aria-label="Default select example" id="bulan">
                                <option selected>Open this select menu</option>
                                <option value="1">Januari</option>
                                <option value="2">Februari</option>
                                <option value="3">Maret</option>
                                <option value="4">April</option>
                                <option value="5">Mei</option>
                                <option value="6">Juni</option>
                                <option value="7">Juli</option>
                                <option value="8">Agustus</option>
                                <option value="9">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Tahun</label>
                            <select class="form-select" aria-label="Default select example" id="tahun">
                                <option selected>Open this select menu</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2026">2026</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Jenis Obat</label>
                            <select class="form-select" aria-label="Default select example" id="jenisobat">
                                <option selected>Open this select menu</option>
                                <option value="0">Tampilkan Semua</option>
                                <option value="1">PRB</option>
                                <option value="2">KRONIS</option>
                                <option value="3">KEMO</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Status</label>
                            <select class="form-select" aria-label="Default select example" id="status">
                                <option selected>Open this select menu</option>
                                <option value="1">Belum diverifikasi</option>
                                <option value="2">Sudah diverifikasi</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success" style="margin-top:32px" onclick="tampilkanriwayat()"><i
                                class="bi bi-search" style="margin-right:12px"></i> Tampilkan Riwayat</button>
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
            bulan = $('#bulan').val()
            tahun = $('#tahun').val()
            jenisobat = $('#jenisobat').val()
            status = $('#status').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    bulan,
                    tahun,
                    jenisobat,
                    status
                },
                url: '<?= route('ambil_data_monitoring_klaim') ?>',
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
