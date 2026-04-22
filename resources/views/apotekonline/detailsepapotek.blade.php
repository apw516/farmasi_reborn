<div class="card">
    <div class="card-header bg-info">Detail SEP</div>
    <div class="card-body">
        <table class="table table-sm mr-3 ml-3">
            <tr>
                <td width="8%" class="fw-bold">Nomor SEP Apotek</td>
                <td class="fst-italic">: {{ $DATA->response->noSepApotek }}</td>
                <td width="8%" class="fw-bold">Nomor SEP Asal</td>
                <td class="fst-italic">: {{ $DATA->response->noSepAsal }}</td>
                <td width="15%" class="fw-bold">Nomor Resep</td>
                <td class="fst-italic">: {{ $DATA->response->noresep }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Nomor Kartu</td>
                <td class="fst-italic">: {{ $DATA->response->nokartu }}</td>
                <td class="fw-bold">Nama Pasien</td>
                <td class="fst-italic">: {{ $DATA->response->nmpst }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Jenis Obat</td>
                <td class="fst-italic">: {{ $DATA->response->nmjnsobat }}</td>
                <td class="fw-bold">Tanggal Pelayanan</td>
                <td class="fst-italic">: {{ $DATA->response->tglpelayanan }}</td>
            </tr>
        </table>
        <table class="table table-sm table-bordered mt-4 table-striped">
            <thead class="bg-light">
                <th>Nama Obat</th>
                <th>Tipe Obat</th>
                <th>Signa 1</th>
                <th>Signa 2</th>
                <th>Jumlah Hari</th>
                <th>Permintaan</th>
                <th>Jumlah Obat</th>
            </thead>
            <tbody>
                @foreach ($DATA->response->listobat as $d)
                    <tr>
                        <td>{{ $d->namaobat }}</td>
                        <td>{{ $d->tipeobat }}</td>
                        <td>{{ $d->signa1 }}</td>
                        <td>{{ $d->signa2 }}</td>
                        <td>{{ $d->hari }}</td>
                        <td>{{ $d->permintaan }}</td>
                        <td>{{ $d->jumlah }}</td>
                        {{-- <td>{{ $d->harga}}</td> --}}
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <button class="btn btn-danger btn-sm hapusresep" noapotik="{{ $DATA->response->noSepApotek }}"
            nosep="{{ $DATA->response->noSepAsal }}" noresep="{{ $DATA->response->noresep }}"><i
                class="bi bi-trash3"></i></button>
    </div>
</div>
<script>
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
</script>
