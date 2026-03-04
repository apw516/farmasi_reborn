<div class="card">
    <div class="card-header">Riwayat Pemberian Obat Oleh Rumah Sakit</div>
    <div class="card-body">
        @foreach ($data1 as $d)
            <div class="card-header bg-light">{{ \Carbon\Carbon::parse($d->tgl_masuk)->translatedFormat('d F Y') }} /
                {{ $d->unit_tujuan }} / {{ $d->nama_dokter }}</div>
            <div class="card-body">
                <table class="table table-sm table-hover" id="tabelriwayatt">
                    @foreach ($data as $dd)
                        @if ($dd->kode_kunjungan == $d->kode_kunjungan)
                            <tr>
                                <td>
                                    {{ $dd->nama_barang }} / qty : {{ $dd->jumlah_layanan }} / Aturan Pakai :
                                    {{ $dd->aturan_pakai }}
                                    <br>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </table>
            </div>
        @endforeach
    </div>
</div>
