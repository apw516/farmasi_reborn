    <script>
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    </script>
    <table id="tabel_resep" style="font-size:12px" class="table table-sm table-bordered table-hover">
        <thead>
            <tr>
                <th>Tgl Kunjungan</th>
                <th>Kode Layanan Header</th>
                <th>Unit kirim</th>
                <th>Unit terima</th>
                <th>Nomor RM</th>
                <th>Nama Pasien</th>
                {{-- <th>Alamat</th> --}}
                <th>No SEP</th>
                {{-- <th>No Resep</th> --}}
                <th>Status Bridging</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data_resep as $item)
                <tr>
                    <td>{{ $item->tgl_entry }}</td>
                    <td>{{ $item->kode_layanan_header }} | {{ $item->keterangan }}</td>
                    <td>{{ $item->nama_unit_pengirim }}</td>
                    <td>{{ $item->nama_unit_penerima }}</td>
                    <td>{{ $item->no_rm }}</td>
                    <td>{{ $item->nama_pasien }}</td>
                    {{-- <td>{{ $item->alamat_pasien }}</td> --}}
                    <td>{{ $item->no_sep }}</td>
                        {{-- <td>{{ $item->noResep }}</td> --}}
                    <td>
                        @if (strlen($item->status_terkirim) == 0)
                            Tidak Bridging
                        @else
                            {{ $item->status_terkirim }}
                        @endif
                    </td>
                    <td>
                        <button class="btn btn-info detailresep" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-custom-class="custom-tooltip" data-bs-title="klik untuk melihat detail resep ..."
                            idheader="{{ $item->idheader }}"><i class="bi bi-info-square"></i></button>
                        <button class="btn btn-danger returresep" data-bs-toggle="tooltip" data-bs-placement="top"
                            data-bs-custom-class="custom-tooltip" data-bs-title="klik untuk retur resep ..."
                            idheader="{{ $item->idheader }}"><i class="bi bi-trash3"></i></button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <!-- Modal -->
    <div class="modal fade" id="modaldetailresep" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Pelayanan Resep</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="v_detail_resep">

                    </div>
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
        $(".returresep").on('click', function(event) {
            idheader = $(this).attr('idheader')
            Swal.fire({
                title: "Data resep akan diretur ?",
                text: "Anda bisa membatalkan proses ini dengan klik batal",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya!",
                cancelButtonText: "Batal",
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Anda yakin ?",
                        text: "data resep akan diretur, termasuk data yang sudah terkirim ke apotek online bpjs ( jika ada )",
                        showDenyButton: true,
                        showCancelButton: false,
                        confirmButtonText: "ya retur semua ...",
                        denyButtonText: `batal !`
                    }).then((result) => {
                        if (result.isConfirmed) {
                            retur(idheader)
                        } else if (result.isDenied) {
                            Swal.fire("Batal retur pelayanan ...", "", "info");
                        }
                    });
                }
            });
        })

        function retur(id) {
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    id
                },
                url: '<?= route('returresep') ?>',
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
            $('#modaldetailresep').modal('show');
            idheader = $(this).attr('idheader')
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    idheader
                },
                url: '<?= route('ambildetailpelayananresep') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_detail_resep').html(response);
                }
            });
        });
    </script>
