<div class="card">
    <div class="card-header"> Data Monitoring Klaim</div>
    <div class="card-body">
        <table class="table table-sm table-boredered">
            <tr>
                <td width="8%">Jumlah DATA</td>
                <td> : {{ $DATA->response->rekap->jumlahdata }}</td>
            </tr>
            <tr>
                <td width="8%">Jumlah Biaya Pengajuan</td>
                <td> : Rp {{ number_format($DATA->response->rekap->totalbiayapengajuan , 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td width="8%">Biaya disetujui</td>
                <td> : Rp {{ number_format($DATA->response->rekap->totalbiayasetuju , 0, ',', '.') }}</td>
            </tr>
        </table>
        <table class="table table-sm table-bordered">
            <thead>
                <th>No SEP Apotek</th>
                <th>No SEP Asal</th>
                <th>No Kartu</th>
                <th>Nama Peserta</th>
                <th>No Resep</th>
                <th>Jenis Obat</th>
                <th>Tanggal Pelayanan</th>
                <th>Biaya Pengajuan</th>
                <th>Biaya Setujui</th>
            </thead>
            <tbody>
                @foreach ($DATA->response->rekap->listsep as $d)
                    <tr>
                        <td>{{ $d->nosepapotek }}</td>
                        <td>{{ $d->nosepaasal }}</td>
                        <td>{{ $d->nokartu }}</td>
                        <td>{{ $d->namapeserta }}</td>
                        <td>{{ $d->noresep }}</td>
                        <td>{{ $d->jnsobat }}</td>
                        <td>{{ $d->tglpelayanan }}</td>
                        <td>Rp {{ number_format($d->biayapengajuan, 0, ',', '.') }}
                        </td>
                        <td>Rp {{ number_format($d->biayasetuju, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
