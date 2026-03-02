<table id="tableracikan" class="table table-sm table-bordered table-hover" style="font-size:15px">
    <thead>
        <th>Nama Racikan</th>
        <th>Keterangan</th>
        <th>Aturan Pakai</th>
        <th>Qty racikan</th>
        <th>Unit Pengirim</th>
        <th>Dokter</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->namaracikan }}</td>
                <td>{{ $d->keterangan_detail }}</td>
                <td>{{ $d->aturanpakai }}</td>
                <td>{{ $d->qtyracikan }}</td>
                <td>{{ $d->nama_unit_kirim }}</td>
                <td>{{ $d->nama_dokter }}</td>
                <td>
                    <button class="btn btn-success btn-sm pilihracikan" idtemplate="{{ $d->id }}"><i
                            class="bi bi-box-arrow-down"></i></button>
                    <button class="btn btn-danger btn-sm hapusracikan" idtemplate="{{ $d->id }}"><i
                            class="bi bi-trash3"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tableracikan").DataTable({
            "responsive": true,
            "lengthChange": true,
            "autoWidth": false,
            "pageLength": 8,
            "searching": true,
            "ordering": false,
        })
    });
    $(".pilihracikan").on('click', function(event) {
        idtemplate = $(this).attr('idtemplate')
        spinner_on()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                idtemplate
            },
            url: '<?= route('ambilobatracik') ?>',
            error: function(response) {
                spinner_off()
                alert('error')
            },
            success: function(response) {
                spinner_off()
                $('.draft_barang').append(response.html);
            }
        });
    })
