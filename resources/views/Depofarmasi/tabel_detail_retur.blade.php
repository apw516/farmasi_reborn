<table id="tabel_retur_detail" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Nama Barang</th>
        <th>Qty awal</th>
        <th>Qty retur</th>
        <th>Qty sisa</th>
        <th>Total Retur</th>
    </thead>
    <tbody>
        @foreach($data as $d)
            <tr>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->qty_Awal }}</td>
                <td>{{ $d->qty_retur }}</td>
                <td>{{ $d->qty_sisa }}</td>
                <td>{{ number_format($d->total_retur, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabel_retur_detail").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 12,
            "searching": true,
            "ordering": false,
        })
    });