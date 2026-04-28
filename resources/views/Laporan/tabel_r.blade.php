<style>
    #tableStok thead th {
        vertical-align: middle !important;
        text-align: center;
        white-space: nowrap;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    #tableStok tbody td {
        vertical-align: middle;
    }

    .badge {
        padding: 0.5em 0.8em;
    }

    /* Memberi warna baris yang butuh pesanan */
    #tableStok tbody tr:has(.badge-success) {
        background-color: rgba(40, 167, 69, 0.05);
    }

    #tableStok {
        width: 100% !important;
        border-collapse: collapse !important;
        margin: 0 !important;
    }

    /* Memastikan wrapper scroll tidak merusak layout */
    .dataTables_scrollHeadInner,
    .dataTables_scrollTable {
        width: 100% !important;
    }
</style>
<table id="tableStok" class="table table-striped table-hover table-bordered shadow-sm">
    <thead class="bg-dark text-white text-center align-middle">
        <tr>
            <th rowspan="2" class="align-middle  text-center">Nama Barang</th>
            <th rowspan="2" class="align-middle  text-center">Satuan</th>
            <th rowspan="2" class="align-middle  text-center">Harga</th>
            <th colspan="3" class="bg-white text-center">Histori Keluar</th>
            <th rowspan="2" class="align-middle bg-light  text-center">Sisa
                Stok<br><small>{{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}</small></th>
            <th colspan="4" class="bg-light text-center">Analisa Kebutuhan</th>
            <th rowspan="2" class="align-middle bg-light  text-center">Pesanan</th>
            <th rowspan="2" class="align-middle  text-center">Nominal</th>
        </tr>
        <tr class="bg-light text-dark">
            <th>{{ $m44 }}</th>
            <th>{{ $m33 }}</th>
            <th>{{ $m22 }}</th>
            <th title="Rata-rata 3 Bulan">Avg (A)</th>
            <th title="Buffer 30%">Buf (B)</th>
            <th title="Leadtime 7 Hari">LT (C)</th>
            <th title="A + B + C - Sisa Stok">Keb (E)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($stokKeluar as $a)
            @php
                $keluar1 = $a->bulan_ke_1 ?? 0;
                $keluar2 = $a->bulan_ke_2 ?? 0;
                $keluar3 = $a->bulan_ke_3 ?? 0;

                $rataRata = ($keluar1 + $keluar2 + $keluar3) / 3;
                $bufferStock = $rataRata * 0.3;
                $leadTimeQty = $rataRata * (7 / 30);
                $sisaStok = $a->sisa_stok_sekarang ?? 0;

                $kebutuhan = $rataRata + $bufferStock + $leadTimeQty - $sisaStok;
                $kebutuhan1 = $kebutuhan;
                if ($kebutuhan <= 0) {
                    $kebutuhan1 = '-';
                } else {
                    $kebutuhan1 = ceil($kebutuhan);
                }
                $jumlahPesanan = $kebutuhan > 0 ? ceil($kebutuhan) : 0;
                $nominal = $a->hna * $jumlahPesanan;
            @endphp
            <tr>
                <td class="font-weight-bold">{{ $a->nama_barang }}</td>
                <td class="text-center text-dark"><span class="badge badge-secondary text-dark">{{ $a->satuan }}</span></td>
                <td class="text-right">{{ number_format($a->hna, 0, ',', '.') }}</td>
                <td class="text-center">{{ $keluar3 }}</td>
                <td class="text-center">{{ $keluar2 }}</td>
                <td class="text-center">{{ $keluar1 }}</td>
                <td class="text-center font-weight-bold {{ $sisaStok <= $bufferStock ? 'text-danger' : 'text-dark' }}">
                    {{ number_format($sisaStok, 0) }}
                </td>
                <td class="text-center">{{ number_format($rataRata,0,',','.') }}</td>
                <td class="text-center text-muted">{{ ceil($bufferStock) }}</td>
                <td class="text-center text-muted">{{ ceil($leadTimeQty) }}</td>
                <td class="text-center">{{ $kebutuhan1 }}</td>
                <td class="text-center">
                    @if ($jumlahPesanan > 0)
                        <span class="badge badge-success text-dark"
                            style="font-size: 1rem;">{{ $jumlahPesanan }}</span>
                    @else
                        <span class="text-muted">-</span>
                    @endif
                </td>
                <td class="text-right font-weight-bold">Rp. {{ number_format($nominal, 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot class="bg-light font-weight-bold">
        <tr>
            <td colspan="12" class="text-right">Total Estimasi Pengadaan:</td>
            <td class="text-right text-primary" id="totalNominal">0</td>
        </tr>
    </tfoot>
</table>
<script>
    $(function() {
        let table = $("#tableStok").DataTable({
            "dom": "<'row'<'col-sm-12 col-md-6'B><'col-sm-12 col-md-6'f>>" +
                "<'row'<'col-sm-12'tr>>" +
                "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            "buttons": [{
                    extend: 'excel',
                    className: 'btn-sm btn-success',
                    text: '<i class="fas fa-file-excel"></i> Export Excel'
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-danger',
                    text: '<i class="fas fa-file-pdf"></i> Export PDF'
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-dark',
                    text: '<i class="fas fa-print"></i> Print'
                }
            ],
            "pageLength": 25,
            "scrollX": true,
            "fixedHeader": true,
            "autoWidth": false, // Matikan autoWidth agar CSS yang pegang kendali
            "language": {
                "search": "_INPUT_",
                "searchPlaceholder": "Cari barang...",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ item",
                "paginate": {
                    "next": "»",
                    "previous": "«"
                }
            },
          "footerCallback": function(row, data, start, end, display) {
    var api = this.api();

    // Fungsi untuk membersihkan semua karakter non-angka (kecuali minus jika ada)
    var intVal = function(i) {
        if (typeof i === 'string') {
            // Menghapus 'Rp', titik ribuan, spasi, dan karakter non-digit lainnya
            let clean = i.replace(/[^\d]/g, ''); 
            return clean ? parseInt(clean) : 0;
        }
        return typeof i === 'number' ? i : 0;
    };

    // Menghitung total dari kolom ke-12 (index 0)
    // Gunakan page: 'current' jika ingin total berubah saat tabel di-filter
    let total = api
        .column(12, { page: 'all' }) 
        .data()
        .reduce(function(a, b) {
            return intVal(a) + intVal(b);
        }, 0);

    // Update footer
    $(api.column(12).footer()).html(
        'Rp ' + total.toLocaleString('id-ID')
    );
}
        });
        // Hitung ulang lebar kolom saat window di-resize (termasuk zoom)
        $(window).on('resize', function() {
            table.columns.adjust();
        })
    });
</script>
