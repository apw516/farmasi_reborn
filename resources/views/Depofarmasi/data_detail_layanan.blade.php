<div class="card">
    <div class="card-header">Kode Layanan : {{ $data_header[0]->kode_layanan_header }} /
        {{ $data_header[0]->nama_penjamin }} <br>
        Tanggal entry : {{ \Carbon\Carbon::parse($data_header[0]->tgl_entry)->translatedFormat('d F Y') }} <br>
        Dokter : {{ $data_header[0]->nama_dokter }} <br>
        Keterangan : {{ $data_header[0]->keterangan }} <br>
        Status Layanan : @if ($data_header[0]->status_layanan == 3)
            Batal
        @else
            Aktif
        @endif
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered">
            <thead>
                <th>Nama</th>
                <th>Kategori</th>
                <th>Qty</th>
                <th>Tarif</th>
                <th>Total Tarif</th>
            </thead>
            <tbody>
                @foreach ($data_detail as $d)
                    <tr>
                        <td>{{ $d->nama_barang }} {{ $d->nama_tarif }}</td>
                        <td>@if($d->tipe_anestesi == '80' ) REGULER @elseif($d->tipe_anestesi == '81') KRONIS @endif</td>
                        <td>{{ $d->jumlah_layanan }}</td>
                        <td>Rp {{ number_format($d->total_tarif, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->total_layanan, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4">Grandtotal</td>
                    <td>Rp {{ number_format($data_header[0]->total_layanan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        <p class="mt-2">
        <h5>Status Brigding Apotek Online : </h5> <br>
        @if (count($data_bridging) > 0)
            @if ($data_bridging[0]->status_terkirim == 'TERKIRIM')
                <div class="alert alert-success" role="alert">
                    <h5>{{ $data_bridging[0]->status_terkirim }} !</h5><br>
                    Nomor SEP : {{ $data_bridging[0]->noSep_Kunjungan }} <br>
                    Nomor SEP Apt : {{ $data_bridging[0]->noApotik }} <br>
                    Nomor Resep : {{ $data_bridging[0]->noResep }} <br>
                    Jenis Obat :
                    @if ($data_bridging[0]->kdJnsObat == 1)
                        PRB
                    @elseif($data_bridging[0]->kdJnsObat == 2)
                        KRONIS
                    @elseif($data_bridging[0]->kdJnsObat == 3)
                        KEMO
                    @endif
                </div>
            @else
                <div class="alert alert-danger" role="alert">
                    {{ $data_bridging[0]->status_terkirim }} !<br>
                </div>
            @endif
        @else
            <div class="alert alert-danger" role="alert">
                Data Resep tidak bridging dengan APOTEK ONLINE BPJS
            </div>
        @endif
        </p>
    </div>
</div>
