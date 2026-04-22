<div class="table-responsive">
    <table class="table table-sm table-bordered table-hover align-middle" id="tabel_pencarian_stok">
        <thead class="table-dark">
            <tr>
                <th class="text-center" width="5%">No</th>
                <th width="12%">Batch</th>
                <th>Nama Barang</th>
                <th class="text-center" width="20%">Expired Date</th>
                <th class="text-center" width="15%">Stok</th>
                <th class="text-center" width="10%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sediaan as $index => $item)
                @php
                    $today = \Carbon\Carbon::now();
                    $edDate = \Carbon\Carbon::parse($item->ED);
                    $daysRemaining = $today->diffInDays($edDate, false);

                    // Menentukan warna badge berdasarkan sisa hari
                    $badgeClass = 'bg-success'; // Masih lama
                    if ($daysRemaining <= 0) {
                        $badgeClass = 'bg-danger'; // Sudah Expired
                        $keterangan = 'Expired!';
                    } elseif ($daysRemaining <= 90) {
                        $badgeClass = 'bg-warning text-dark'; // < 3 bulan
                        $keterangan = $daysRemaining . ' hari lagi';
                    } else {
                        $keterangan = $daysRemaining . ' hari lagi';
                    }
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $index + 1 }}</td>
                    <td>
                        <span class="d-block fw-bold text-uppercase"
                            style="font-size: 0.9rem;">{{ $item->nomor_batch }}</span>
                        <small class="text-muted">{{ $item->nomor_faktur }}</small>

                    </td>
                    <td>
                        <span class="d-block fw-bold text-uppercase"
                            style="font-size: 0.9rem;">{{ $item->nama_barang }}</span>
                        <small class="text-muted">{{ $item->nama_tipe }} | {{ $item->sediaan }}</small>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $badgeClass }} d-block mb-1" style="font-size: 0.85rem;">
                            {{ date('d M Y', strtotime($item->ED)) }}
                        </span>
                        <small class="fw-bold {{ $daysRemaining <= 90 ? 'text-danger' : 'text-muted' }}">
                            <i class="bi bi-clock-history"></i> {{ $keterangan }}
                        </small>
                    </td>
                    <td class="text-center">
                        <div class="fw-bold">{{ number_format($item->stok_sekarang, 0, ',', '.') }}</div>
                        <small class="text-muted">{{ $item->satuan }}</small>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-primary btn-sm rounded-pill btn-pilih-stok shadow-sm"
                            data-kode="{{ $item->kode_barang }}" data-nama="{{ $item->nama_barang }}"
                            data-batch="{{ $item->nomor_batch }}" data-ed="{{ $item->ED }}"
                            data-stok_tersedia={{ $item->stok_sekarang }} data-nomor_faktur={{ $item->nomor_faktur }}
                            data-satuan="{{ $item->satuan }}" data-stok="{{ $item->stok_sekarang }}"
                            data-id_sediaan="{{ $item->id_sediaan }}">
                            <i class="bi bi-plus-lg"></i> Pilih
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-danger fw-bold py-4">
                        <i class="bi bi-exclamation-octagon fs-4 d-block mb-2"></i>
                        Stok Kosong atau Unit Belum Dipilih.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
    $(document).ready(function() {
        if (!$.fn.DataTable.isDataTable('#tabel_pencarian_stok')) {
            $('#tabel_pencarian_stok').DataTable({
                "pageLength": 5,
                "lengthMenu": [5, 10, 25],
                "ordering": true,
                "language": {
                    "search": "",
                    "searchPlaceholder": "Cari Batch atau Nama Barang...",
                    "emptyTable": "Data tidak ditemukan",
                    "zeroRecords": "Barang tidak ditemukan"
                },
                "dom": '<"row"<"col-sm-12"f>>t<"row"<"col-sm-12"p>>' // Menyederhanakan tampilan search
            });
        }
    });
</script>
