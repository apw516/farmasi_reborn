<table id="tabel_barang" class="table table-bordered table-hover" style="width:100%">
    <thead>
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>sediaan</th>
            <th>Dosis</th>
            <th class="text-center"></th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>
<input hidden type="text" value="{{ $kode_supplier }}" id="kode_supplier">
<input hidden type="text" value="{{ $kategori_barang }}" id="kategori_barang">
<script>
    $(document).ready(function() {
        $('#tabel_barang').DataTable({
            processing: true,
            serverSide: true, // Fitur server side aktif
            ajax: {
                url: "{{ route('ambilbaranguntukpo') }}",
                type: "GET", // Atau POST sesuai route Anda
                data: function(d) {
                    // Mengambil nilai dari elemen input/select di page Anda
                    d.kode_supplier = $('#kode_supplier').val();
                    d.kategori_barang = $('#kategori_barang').val();
                }
            },
            columns: [{
                    data: 'kode_barang',
                    name: 'kode_barang'
                }, // Nomor urut otomatis
                {
                    data: 'nama_barang',
                    name: 'nama_barang'
                },
                {
                    data: 'satuan_besar',
                    name: 'satuan_besar'
                },
                {
                    data: 'sediaan',
                    name: 'sediaan'
                },
                {
                    data: 'dosis',
                    name: 'dosis'
                },
                {
                    data: null, // Kolom ini tidak terikat data langsung
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        // row adalah objek data untuk baris tersebut
                        return '<button class="btn btn-primary btn-sm pilihobat text-center" ' +
                            'data-kode_barang="' + row.kode_barang + '" ' +
                            'data-nama_barang="' + row.nama_barang + '" ' +
                            'data-nama_satuan="' + row.satuan_besar + '" ' +
                            'nama_satuan_kecil="' + row.satuan + '" ' +
                            'isi="' + row.isi + '" ' +
                            // Tambahkan atribut lain yang dibutuhkan di sini
                            ' data-bs-dismiss="modal"><i class="bi bi-arrow-down-left-square"></i></button>';
                    }
                }
            ]
        });
    })
</script>
