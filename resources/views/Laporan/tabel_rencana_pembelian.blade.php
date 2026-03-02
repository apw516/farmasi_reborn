<table id="tabelrencana" class="table table-bordered">
    <thead class="table-primary text-center">
        <tr>
            <th>Barang</th>
            <th>Jml PO ( {{ $months[0]->format('M Y') }} )</th>
            <th>Jml PO ({{ $months[1]->format('M Y') }} )</th>
            <th>Jml PO ({{ $months[2]->format('M Y') }} )</th>
            <th>Sisa Stok</th>
            <th>Prioritas</th>
            <th>Rekomendasi Order</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($dataPO as $item)
            <tr class="{{ $item->status == 'URGENT' ? 'table-danger' : '' }}">
                <td>
                    <strong>{{ $item->nama_barang }}</strong><br>
                    <small class="text-muted">{{ $item->kode_barang }}</small>
                </td>
                <td class="text-center">{{ number_format($item->bulan_1) }}</td>
                <td class="text-center">{{ number_format($item->bulan_2) }}</td>
                <td class="text-center">{{ number_format($item->bulan_3) }}</td>
                <td class="text-center fw-bold">{{ number_format($item->sisa_stok) }}</td>
                <td class="text-center">
                    <span class="badge bg-{{ $item->warna }} p-2 w-100">
                        {{ $item->status }}
                    </span>
                </td>
                <td class="text-center">
                    @if ($item->rekomendasi_beli > 0)
                        <span class="badge rounded-pill bg-dark">+ {{ number_format($item->rekomendasi_beli) }}</span>
                    @else
                        <i class="fa fa-check-circle text-success"></i>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabelrencana").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 8,
            "searching": true,
            "ordering": false,
            // Properti 'dom' menentukan posisi tombol (B=Buttons, f=filter, r=processing, t=table, i=info, p=pagination)
            "dom": 'Bfrtip',
            "buttons": [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Export Excel',
                    className: 'btn-success',
                    title: 'Laporan Rencana Pembelian - ' + new Date().toLocaleDateString()
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF',
                    className: 'btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Cetak',
                    className: 'btn-info'
                }
            ]
        }).buttons().container().appendTo('#tabelrencana_wrapper .col-md-6:eq(0)');
    });
