<div class="card">
    <div class="card-header">Data Riwayat Pelayanan Peserta</div>
    <div class="card-body">
        <table class="table table-sm">
            <tr>
                <td width="8%">Nomor Kartu</td>
                <td>: {{ $DATA->response->list->nokartu }}</td>
            </tr>
            <tr>
                <td width="8%">Nama Pasien</td>
                <td>: {{ $DATA->response->list->namapeserta }}</td>
            </tr>
            <tr>
                <td width="8%">Tanggal lahir</td>
                <td>: {{ $DATA->response->list->tgllhr }}</td>
            </tr>
        </table>
        <table class="table table-sm table-bordered table-hover mt-2">
            <thead class="bg-light">
                <th>NO SEP</th>
                <th>Tanggal Pelayanan</th>
                <th>Nomor Resep</th>
                <th>Kode Obat</th>
                <th>Nama Obat</th>
                <th>Jumlah Obat</th>
                <th></th>
            </thead>
            <tbody>
                @foreach ($DATA->response->list->history as $d)
                    <tr>
                        <td>{{ $d->nosjp }}</td>
                        <td>{{ $d->tglpelayanan }}</td>
                        <td>{{ $d->noresep }}</td>
                        <td>{{ $d->kodeobat }}</td>
                        <td>{{ $d->namaobat }}</td>
                        <td>{{ $d->jmlobat }}</td>
                        <td>
                            <button class="btn btn-danger btn-sm hapus"><i class="bi bi-trash3"></i></button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
    $(".hapus").on('click', function(event) {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Bridging data resep dengan nomor : a" ,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Deleted!",
                    text: "Your file has been deleted.",
                    icon: "success"
                });
            }
        });
    })
</script>
