@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Pengadaan Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pengadaan Barang</li>
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
            <div class="card">
                <div class="card-header">Buat Rencana Pengadaan</div>
                <div class="card-body">
                    <div class="row">
                        {{-- <div class="col-md-2">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Tanggal Awal</label>
                                <input type="date" class="form-control" id="tanggalawal"
                                    aria-describedby="emailHelp">
                            </div>
                        </div> --}}
                        <div class="col-md-2">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" id="tanggalakhir"
                                    aria-describedby="emailHelp">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-success" style="margin-top:32px" onclick="buatrencana()"><i class="bi bi-cpu" style="margin-right:12px"></i> Buat Rencana Pengadaan</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="v_hasil mt-2">

            </div>
        </div>
        <!--end::Container-->
    </div>
    <script>
        function buatrencana() {
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
                url: '<?= route('ambildatarencanapengadaan') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_hasil').html(response);
                }
            });
        }
    </script>
@endsection
