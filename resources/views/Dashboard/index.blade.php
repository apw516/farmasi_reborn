@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Dashboard Toko</h3>
                </div>
            
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Dashboard v3</li>
                    </ol>
                </div>
                    <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Tanggal Awal</label>
                            <input type="date" class="form-control" id="tanggalawal" aria-describedby="emailHelp" value="{{ $date_start }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                            <input type="date" class="form-control" id="tanggalakhir" aria-describedby="emailHelp" value="{{ $date_end }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-success" style="margin-top:32px"><i class="bi bi-search" style="margin-right:12px"></i> Tampilkan Dashboard</button>
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
            <!--begin::Row-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card mb-4">
                        <div class="card-header border-0">
                            <div class="d-flex justify-content-between">
                                <h3 class="card-title">Total Penjualan</h3>
                                <a href="javascript:void(0);"
                                    class="link-primary link-offset-2 link-underline-opacity-25 link-underline-opacity-100-hover">View
                                    Report</a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <p class="d-flex flex-column">
                                    <span class="fw-bold fs-5"></span> <span>Sales Over Time</span>
                                </p>
                                {{-- <p class="ms-auto d-flex flex-column text-end">
                                    <span class="text-success"> <i class="bi bi-arrow-up"></i> {{ $rataRataKenaikan }}% </span>
                                    <span class="text-secondary">Since Past Year</span>
                                </p> --}}
                            </div>
                            <!-- /.d-flex -->
                            <div class="position-relative mb-4">
                                <div id="sales-chart"></div>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="me-2">
                                    <i class="bi bi-square-fill text-primary"></i> This year
                                </span>
                                <span> <i class="bi bi-square-fill text-secondary"></i> Last year </span>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">Data Customer Terbaru</h3>
                            <div class="card-tools">
                                <a href="#" class="btn btn-sm btn-tool"> <i class="bi bi-download"></i> </a>
                                <a href="#" class="btn btn-sm btn-tool"> <i class="bi bi-list"></i> </a>
                            </div>
                        </div>
                        <div class="card-body">
                           
                        </div>
                    </div>
                </div>
                <!-- /.col-md-6 -->
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>

@endsection
