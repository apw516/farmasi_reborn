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
                <td>Rp. {{ number_format($d->total_retur, 0, ',', '.') }}</td>
                <td>
                    <button class="btn btn-info detailretur" data-bs-toggle="modal" data-bs-target="#modaldetailretur"
                        idreturheader="{{ $d->idreturheader }}"><i class="bi bi-eye"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<!-- Modal -->
<div class="modal fade" id="modaldetailretur" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Retur</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="v_d_r">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
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
    $(".detailretur").on('click', function(event) {
        idreturheader = $(this).attr('idreturheader')
        spinner_on()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                idreturheader
            },
            url: '<?= route('ambildetailretur') ?>',
            error: function(response) {
                spinner_off()
                alert('error')
            },
            success: function(response) {
                spinner_off()
                $('.v_d_r').html(response);
            }
        });
    })
