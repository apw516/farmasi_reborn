<table id="tabellaporan" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Nama Barang</th>
        <th>Tahun</th>
        <th>Bulan</th>
        <th>Jumlah PO</th>
        <th>Total barang</th>
    </thead>
    <tbody>
        @foreach ($laporan as $d)
            <tr>
                <td>{{ $d->nama_barang }}</td>
                <td>{{ $d->tahun }}</td>
                <td>{{ $d->bulan }}</td>
                <td>{{ $d->jumlah_po }}</td>
                <td>{{ $d->total_barang_dipesan / $d->isi }} {{ $d->satuan }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table id="tabellaporan2" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Nama Barang</th>
        <th>Tahun</th>
        <th>Bulan</th>
        <th>Jumlah Stok terpakai</th>
    <tbody>
        @foreach ($penjualan as $dd)
            <tr>
                <td>{{ $dd->nama_barang }}</td>
                <td>{{ $dd->tahun }}</td>
                <td>{{ $dd->bulan }}</td>
                <td>{{ $dd->jumlah_layanan / $dd->isi }} {{ $dd->satuan_besar }}</td>
            </tr>
        @endforeach
    </tbody>
    </thead>
</table>
<script>
    $(function() {
        $("#tabellaporan").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 8,
            "searching": true,
            "ordering": false,
        })
    });
    $(function() {
        $("#tabellaporan2").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 8,
            "searching": true,
            "ordering": false,
        })
    });
