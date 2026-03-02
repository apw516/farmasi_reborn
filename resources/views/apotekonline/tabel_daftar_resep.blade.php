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
