<div class="card">
    <div class="card-header">Data Resep</div>
    <div class="card-body">
        <table id="tabelresep" class="table table-sm table-bordered table-hover">
            <thead>
                <th>Tgl entry</th>
                <th>Tgl resep</th>
                <th>No Sep Kunjungan</th>
                <th>No Apotik</th>
                <th>No Resep</th>
                <th>No Kartu</th>
                <th>Nama Pasien</th>
                <th>Jenis Obat</th>
                <th>Iter</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($DATA->response as $d)
                    <tr>
                        <td>{{ $d->TGLENTRY }}</td>
                        <td>{{ $d->TGLRESEP }}</td>
                        <td>{{ $d->NOSEP_KUNJUNGAN }}</td>
                        <td>{{ $d->NOAPOTIK }}</td>
                        <td>{{ $d->NORESEP }}</td>
                        <td>{{ $d->NOKARTU }}</td>
                        <td>{{ $d->NAMA }}</td>
                        <td>
                            @if ($d->KDJNSOBAT == 1)
                                OBAT PRB
                            @elseif($d->KDJNSOBAT == 2)
                                KRONIS
                            @else
                                KEMO
                            @endif
                        </td>
                        <td>
                            @if ($d->FLAGITER == 'False')
                                Tidak
                            @else
                                YA
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-info btn-sm detailresep" noapotik="{{ $d->NOAPOTIK }}"
                                nosep="{{ $d->NOSEP_KUNJUNGAN }}" noresep="{{ $d->NORESEP }}" data-bs-toggle="modal" data-bs-target="#modaldetailresep"><i
                                    class="bi bi-eye"></i></button>
                            <button class="btn btn-danger btn-sm hapusresep" noapotik="{{ $d->NOAPOTIK }}"
                                nosep="{{ $d->NOSEP_KUNJUNGAN }}" noresep="{{ $d->NORESEP }}"><i
                                    class="bi bi-trash3"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modaldetailresep" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Resep</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="v_d_r">

        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
<script>
    $(function() {
        $("#tabelresep").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 8,
            "searching": true,
            "ordering": false,
        })
    });
    $(".detailresep").on('click', function(event) {
        nosep = $(this).attr('noapotik')
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
                $('.v_d_r').html(response);
            }
        });

    })
    $(".hapusresep").on('click', function(event) {
        noapotik = $(this).attr('noapotik')
        noresep = $(this).attr('noresep')
        nosep = $(this).attr('nosep')
        Swal.fire({
            title: "Anda yakin ?",
            text: "Bridging data resep dengan nomor resep : " + noresep + " SEP ASAL : " + nosep +
                " akan dihapus ...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, Hapus ..."
        }).then((result) => {
            if (result.isConfirmed) {
                hapusdataresep(noapotik, noresep, nosep)
            }
        });
    })

    function hapusdataresep(noapotik, noresep, nosep) {
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
                        noresep
                    },
                    url: '<?= route('hapusresepapotekonline') ?>',
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
    }
