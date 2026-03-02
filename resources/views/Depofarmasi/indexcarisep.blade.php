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
            <div class="card">
                <div class="card-header">Data Resep / SEP</div>
                <div class="card-body">
                    <table id="tabel_resep" style="font-size:12px" class="table table-sm table-bordered table-hover">
                        <thead>
                            <tr>
                                {{-- <th>Tgl Entry</th> --}}
                                <th>Tgl Resep</th>
                                {{-- <th>Kode Layanan</th> --}}
                                <th>No Kartu</th>
                                <th>Nama</th>
                                <th>No Resep</th>
                                <th>No SEP</th>
                                <th>Nomor SEP Apotek</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data_resep as $item)
                                <tr>
                                    {{-- <td>{{ $item->tglEntry }}</td> --}}
                                    <td>{{ $item->tglResep }}</td>
                                    {{-- <td>{{ $item->kode_layanan_header }} | {{ $item->nama_unit }}</td> --}}
                                    <td>{{ $item->noKartu }}</td>
                                    <td>{{ $item->nama }}</td>
                                    <td>{{ $item->noResep }}</td>
                                    <td>{{ $item->noSep_Kunjungan }}</td>
                                    <td>{{ $item->noApotik }}</td>
                                    <td>
                                        <button class="btn btn-danger hapusresep" idresep="{{ $item->id }}"
                                            nosep="{{ $item->noSep_Kunjungan }}" noapotik="{{ $item->noApotik }}"
                                            noresep="{{ $item->noResep }}"><i class="bi bi-trash3"></i></button>
                                        <button class="btn btn-info detailresep" idresep="{{ $item->id }}"
                                            nosep="{{ $item->noSep_Kunjungan }}" noapotik="{{ $item->noApotik }}"
                                            noresep="{{ $item->noResep }}" data-bs-toggle="modal"
                                            data-bs-target="#modaldetailresep"><i class="bi bi-info-circle"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaldetailresep" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Pelayanan Resep</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(function() {
            $("#tabel_resep").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "pageLength": 8,
                "searching": true,
                "ordering": false,
            })
        });
        $(".hapusresep").on('click', function(event) {
            nosep = $(this).attr('nosep')
            noapotik = $(this).attr('noapotik')
            idresep = $(this).attr('idresep')
            noresep = $(this).attr('noresep')
            Swal.fire({
                title: "Anda yakin ?",
                text: "Data Resep akan dihapus ...",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya Hapus"
            }).then((result) => {
                if (result.isConfirmed) {
                    spinner_on()
                    $.ajax({
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            nosep,
                            noapotik,
                            noresep,
                            idresep
                        },
                        url: '<?= route('hapusresep') ?>',
                        success: function(response) {
                            spinner_off()
                            if (response.kode == '500') {
                                // Kondisi jika validasi gagal atau ada error sistem
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ups!',
                                    text: response.message,
                                });
                            } else {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'OK!',
                                    text: response.message,
                                });
                                location.reload()
                            }
                        }
                    });
                }
            });
        });
        $(".detailresep").on('click', function(event) {
            nosep = $(this).attr('nosep')
            noapotik = $(this).attr('noapotik')
            idresep = $(this).attr('idresep')
            noresep = $(this).attr('noresep')
            // Swal.fire({
            //     title: "Anda yakin ?",
            //     text: "Data Resep akan dihapus ...",
            //     icon: "warning",
            //     showCancelButton: true,
            //     confirmButtonColor: "#3085d6",
            //     cancelButtonColor: "#d33",
            //     confirmButtonText: "Ya Hapus"
            // }).then((result) => {
                // if (result.isConfirmed) {
                    spinner_on()
                    $.ajax({
                        type: 'post',
                        data: {
                            _token: "{{ csrf_token() }}",
                            nosep,
                            noapotik,
                            noresep,
                            idresep
                        },
                        url: '<?= route('detailresep') ?>',
                        success: function(response) {
                            spinner_off()
                            if (response.kode == '500') {
                                // Kondisi jika validasi gagal atau ada error sistem
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ups!',
                                    text: response.message,
                                });
                            } else {
                              
                            }
                        }
                    });
                // }
            // });
        });
    </script>
@endsection
