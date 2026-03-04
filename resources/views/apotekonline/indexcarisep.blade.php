@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Cari SEP Apotek</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Cari SEP</li>
                    </ol>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <input style="margin-top:32px" type="text" class="form-control" id="nomorsep"
                                aria-describedby="emailHelp" value="" placeholder="Masukan nomor SEP 19 digit ...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success" style="margin-top:32px" onclick="carisep()"><i class="bi bi-search"
                                style="margin-right:12px"></i> Cari SEP</button>
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
        function carisep() {
            nosep = $('#nomorsep').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    nosep
                },
                url: '<?= route('carisep_apotekonline') ?>',
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
