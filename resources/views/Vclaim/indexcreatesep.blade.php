@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Vclaim Create SEP</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard v3</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container">
            <form>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exampleInputEmail1" class="form-label">No Bpjs</label>
                        <input type="email" class="form-control form-control-sm" id="nobpjs" name="nobpjs"
                            aria-describedby="emailHelp">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">No RM</label>
                        <input type="password" class="form-control form-control-sm" id="norm" name="norm">
                    </div>
                </div>
                <button type="button" class="btn btn-primary" onclick="createsep()">Create SEP</button>
            </form>
        </div>
        <!--end::Container-->
    </div>
    <script>
        function createsep() {
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                url: '<?= route('createsep') ?>',
                success: function(response) {
                }
            });
        }
    </script>
@endsection
