<table id="tabel_kunjungan" class="table table-sm table-bordered table-hover">
    <thead>
        <th>Tanggal masuk</th>
        <th>Nomor RM</th>
        <th>Nama Pasien</th>
        <th>Alamat</th>
        <th>Unit</th>
        <th>Dokter</th>
        <th>SEP</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data as $d)
            <tr>
                <td>{{ $d->tgl_masuk }}</td>
                <td>{{ $d->no_rm }}</td>
                <td>{{ $d->nama_pasien }}</td>
                <td>{{ $d->alamat }}</td>
                <td>{{ $d->nama_unit }}</td>
                <td>{{ $d->dokter }}</td>
                <td>{{ $d->no_sep }}</td>
                <td>
                    <button class="btn btn-success pilihpasien" kode_kunjungan="{{ $d->kode_kunjungan }}"><i
                            class="bi bi-bullseye"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<script>
    $(function() {
        $("#tabel_kunjungan").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "pageLength": 8,
            "searching": true,
            "ordering": false,
        })
    });
    $(".pilihpasien").on('click', function(event) {
        spinner_on()
        kode_kunjungan = $(this).attr('kode_kunjungan')
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kode_kunjungan
            },
            url: '<?= route('ambil_form_pelayanan_obat') ?>',
            error: function(response) {
                spinner_off();
                alert('error')
            },
            success: function(response) {
                spinner_off();
                $('.v_1').attr('hidden',true)
                $('.v_2').removeAttr('hidden',true)
                $('.v_2').html(response);
            }
        });
    })
</script>
