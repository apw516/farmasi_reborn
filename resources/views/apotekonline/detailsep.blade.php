<div class="card">
    <div class="card-header bg-info">Detail SEP</div>
    <div class="card-body">
        <table class="table table-sm mr-3 ml-3">
            <tr>
                <td width="8%" class="fw-bold">Nomor SEP</td>
                <td class="fst-italic">: {{ $DATA->response->noSep }}</td>
                <td width="8%" class="fw-bold">Tanggal SEP</td>
                <td class="fst-italic">: {{ $DATA->response->tglsep }}</td>
                <td width="15%" class="fw-bold">Tanggal Pulang SEP</td>
                <td class="fst-italic">: {{ $DATA->response->tglplgsep }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Nomor Kartu</td>
                <td class="fst-italic">: {{ $DATA->response->nokartu }}</td>
                <td class="fw-bold">Nama Pasien</td>
                <td class="fst-italic">: {{ $DATA->response->namapeserta }}</td>
                <td class="fw-bold">Tanggal Lahir / Jenis Kelamin</td>
                <td class="fst-italic">: {{ $DATA->response->tgllhr }} / {{ $DATA->response->jnskelamin }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Asal Resep</td>
                <td class="fst-italic">: {{ $DATA->response->nmfaskesasalresep }}</td>
                <td class="fw-bold">Jenis Peserta</td>
                <td class="fst-italic">: {{ $DATA->response->nmjenispeserta }}</td>
                <td class="fw-bold">Jenis Pelayanan</td>
                <td class="fst-italic">: {{ $DATA->response->jnspelayanan }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Poliklinik </td>
                <td class="fst-italic">: {{ $DATA->response->poli }}</td>
                <td class="fw-bold">Dokter</td>
                <td class="fst-italic">: {{ $DATA->response->namadokter }}</td>
                <td class="fw-bold">Diagnosa</td>
                <td class="fst-italic">: {{ $DATA->response->nmdiag }}</td>
            </tr>
        </table>
    </div>
    <div class="card-footer">

    </div>
</div>
