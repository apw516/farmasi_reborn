<table id="tabelretur" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Tanggal Retur</th>
        <th>Kode Retur</th>
        <th>Kode Layanan Header</th>
        <th>Nomor RM</th>
        <th>Nama Pasien</th>
        <th>Unit</th>
        <th>Total</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ \Carbon\Carbon::parse($d->tgl_retur)->translatedFormat('l, d F Y H:i') }}</td>
                <td>{{ $d->kode_retur_header }}</td>
                <td>{{ $d->kode_layanan_header }}</td>
                <td>{{ $d->no_rm }}</td>
                <td>{{ $d->nama_pasien }}</td>
                <td>{{ $d->nama_unit }}</td>
                <td>{{ number_format($d->total_retur, 0, ',', '.') }}</td>
                <td>
                    <button class="btn btn-info"><i class="bi bi-eye"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelretur").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });
