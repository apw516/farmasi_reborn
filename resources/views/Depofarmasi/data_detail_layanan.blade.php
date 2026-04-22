<div class="card">
    <div class="card-header">Kode Layanan : {{ $data_header[0]->kode_layanan_header }} /
        {{ $data_header[0]->nama_penjamin }} <br>
        Tanggal entry : {{ \Carbon\Carbon::parse($data_header[0]->tgl_entry)->translatedFormat('d F Y') }} <br>
        Dokter : {{ $data_header[0]->nama_dokter }} <br>
        Keterangan : {{ $data_header[0]->keterangan }} <br>
        Status Layanan : @if ($data_header[0]->status_layanan == 3)
            Batal
        @else
            Aktif
        @endif
        <br>
        Status Retur : @if ($data_header[0]->status_retur == 'CLS')
            Habis diretur
        @else
            OPEN
        @endif
    </div>
    <div class="card-body">
        <table class="table table-sm table-bordered">
            <thead>
                <th>Nama</th>
                <th>Kategori / Stts</th>
                <th>Qty</th>
                <th>Tarif</th>
                <th>Total Tarif</th>
                <th>-</th>
            </thead>
            <tbody>
                @foreach ($data_detail as $d)
                    @if(strlen($d->nama_barang) > 1 || $d->kode_tarif_detail == 'TX23523')
                    <tr>
                        <td>{{ $d->nama_barang }} {{ $d->nama_tarif }}</td>
                        <td>
                            @if ($d->tipe_anestesi == '80')
                                REGULER
                            @elseif($d->tipe_anestesi == '81')
                                KRONIS
                            @endif
                            @if ($d->status_layanan_detail == 'CLS')
                                / RETUR
                            @endif
                        </td>
                        <td>{{ $d->jumlah_layanan }}</td>
                        <td>Rp {{ number_format($d->total_tarif, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($d->total_layanan, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <button @if ($d->status_layanan_detail == 'CLS') disabled @endif
                                class="btn btn-danger btn-sm retursatuan" idly={{ $d->id }}
                                data-bs-dismiss="modal"><i class="bi bi-trash3"></i></button>
                        </td>
                    </tr>
                    @endif
                @endforeach
                <tr>
                    <td colspan="4">Grandtotal</td>
                    <td>Rp {{ number_format($data_header[0]->total_layanan, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        <div class="btn-group mt-2" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-primary"><i class="bi bi-printer"></i> Nota</button>
            <button type="button" class="btn btn-primary"><i class="bi bi-printer"></i> Etiket</button>
        </div>
        <p class="mt-2">
        <h5>Status Brigding Apotek Online : </h5> <br>
        @if (count($data_bridging) > 0)
            @if ($data_bridging[0]->status_terkirim == 'TERKIRIM')
                <div class="alert alert-success" role="alert">
                    <h5>{{ $data_bridging[0]->status_terkirim }} !</h5><br>
                    Nomor SEP : {{ $data_bridging[0]->noSep_Kunjungan }} <br>
                    Nomor SEP Apt : {{ $data_bridging[0]->noApotik }} <br>
                    Nomor Resep : {{ $data_bridging[0]->noResep }} <br>
                    Jenis Obat :
                    @if ($data_bridging[0]->kdJnsObat == 1)
                        PRB
                    @elseif($data_bridging[0]->kdJnsObat == 2)
                        KRONIS
                    @elseif($data_bridging[0]->kdJnsObat == 3)
                        KEMO
                    @endif
                </div>
            @else
                <div class="alert alert-danger" role="alert">
                    {{ $data_bridging[0]->status_terkirim }} !<br>
                </div>
            @endif
        @else
            <div class="alert alert-danger" role="alert">
                Data Resep tidak bridging dengan APOTEK ONLINE BPJS
            </div>
        @endif
        </p>
    </div>
</div>
<script>
    $(".retursatuan").on('click', function(event) {
        idly = $(this).attr('idly')
        Swal.fire({
            title: "Anda yakin ?",
            text: "Data Obat akan diretur ...",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya retur"
        }).then((result) => {
            if (result.isConfirmed) {
                spinner_on()
                $.ajax({
                    type: 'post',
                    data: {
                        _token: "{{ csrf_token() }}",
                        idly
                    },
                    url: '<?= route('retursatuan') ?>',
                    error: function(response) {
                        spinner_off()
                        Swal.fire({
                            icon: 'error',
                            title: 'Ups!',
                            text: "Sepertinya ada masalah ...",
                        });
                    },
                    success: function(response) {
                        spinner_off()
                        if (response.kode == '500') {
                            // Kondisi jika validasi gagal atau ada error sistem
                            Swal.fire({
                                icon: 'error',
                                title: 'Ups!',
                                text: response.message,
                            });
                        } else {
                            Swal.fire({
                                icon: 'success',
                                title: 'OK!',
                                text: response.message,
                            });
                        }
                    }
                });
            }
        });
    });
</script>
