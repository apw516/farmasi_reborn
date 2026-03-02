<table class="table table-sm table-bordered table-hover">
    <thead>
        <th>Tanggal Input</th>
        <th>Tanggal Beli</th>
        <th>Tanggal Terima</th>
        <th>Nomor Faktur</th>
        <th>Nama Supplier</th>
        <th>Total PO</th>
        <th>PPn</th>
        <th>Grandtotal</th>
        <th>Total Hutang</th>
        <th>Status Pembayaran</th>
        <th>Status Tagihan</th>
        <th>Status Retur</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data_header as $d)
            <tr>
                <td> {{ \Carbon\Carbon::parse($d->tgl_input)->locale('id')->translatedFormat('d F Y') }}</td>
                <td> {{ \Carbon\Carbon::parse($d->tgl_beli)->locale('id')->translatedFormat('d F Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tgl_terima)->locale('id')->translatedFormat('d F Y') }}</td>
                <td>{{ $d->no_faktur }}</td>
                <td>{{ $d->nama_supplier }}</td>
                <td> {{ number_format($d->total_po, 0, ',', '.') }}</td>
                <td> {{ number_format($d->ppn, 0, ',', '.') }}</td>
                <td>{{ number_format($d->gtotal_po, 0, ',', '.') }}</td>
                <td>{{ number_format($d->total_utang, 0, ',', '.') }}</td>
                <td>{{ $d->status_pembayaran }}</td>
                <td>{{ $d->status_tagihan }}</td>
                <td>{{ $d->status_retur }}</td>
                <td>
                    <button class="btn btn-success btn-sm pilihpoheader" idheader="{{ $d->id }}"
                        data-bs-toggle="tooltip" data-bs-title="Input detail PO ..."><i
                            class="bi bi-box-arrow-in-right"></i></button>
                    <button class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-title="lihat detail PO ..."><i
                            class="bi bi-info-square"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    $(".pilihpoheader").on('click', function(event) {
        $('.v_1').attr('hidden', true)
        $('.v_2').removeAttr('hidden', true)
        id = $(this).attr('idheader')
        tanggalawal = $('#tanggalawal').val()
        tanggalakhir = $('#tanggalakhir').val()
        spinner_on()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                id
            },
            url: '<?= route('ambilformdetailpo') ?>',
            error: function(response) {
                spinner_off()
                alert('error')
            },
            success: function(response) {
                spinner_off()
                $('.v_detail').html(response);
            }
        });
    });
</script>
