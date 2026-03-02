<button class="btn btn-danger" onclick="kembali()">Kembali</button>
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card mb-2">
            <div class="card-header">Data Pasien</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Nomor RM : {{ $data_kunjungan[0]->no_rm }}</td>
                        <td>Penjamin : {{ $data_kunjungan[0]->nama_penjamin }}</td>
                        <td>Nomor SEP : {{ $data_kunjungan[0]->no_sep }}</td>
                    </tr>
                    <tr>
                        <td>Nama Pasien : {{ $data_kunjungan[0]->nama_pasien }}</td>
                        <td colspan="2">Alamat : {{ $data_kunjungan[0]->alamat }}</td>
                    </tr>
                    <tr>
                        <td>Poli Tujuan : {{ $data_kunjungan[0]->nama_unit }}</td>
                        <td colspan="2"> Dokter : {{ $data_kunjungan[0]->dokter }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">Buat Resep</div>
            <div class="card-body">
                <div class="card mt-2">
                    <div class="card-header">Silahkan Pilih Obat</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Pencarian obat reguler...</label>
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control"
                                            placeholder="Masukan nama pencarian obat ..."
                                            aria-label="Recipient’s username" aria-describedby="basic-addon2"
                                            id="input_pencarian_nama">
                                        <span class="btn btn-success input-group-text" id="tombol_cari"><i
                                                class="bi bi-search"></i> Cari
                                            Obat</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    {{-- <label for="exampleInputEmail1" class="form-label">Pencarian obat Racikan...</label> --}}
                                    {{-- <div class="input-group mb-3">
                                        <input type="text" class="form-control"
                                            placeholder="Masukan nama pencarian obat ..."
                                            aria-label="Recipient’s username" aria-describedby="basic-addon2"
                                            id="input_pencarian_nama">
                                        <span class="btn btn-success input-group-text" id="tombol_cari"><i
                                                class="bi bi-search"></i> Cari
                                            Obat Racikan</span>
                                    </div> --}}
                                    <button class="btn btn-success ambilobatracik" id=""
                                        style="margin-top:32px" data-bs-toggle="modal"
                                        data-bs-target="#modalobatracikan"><i class="bi bi-card-checklist"></i> List
                                        Obat Racikan </button>
                                    <button class="btn btn-warning" id="" style="margin-top:32px"
                                        data-bs-toggle="modal" data-bs-target="#modalbuatracikan"><i
                                            class="bi bi-plus-square"></i> Buat Racikan </button>
                                </div>
                            </div>
                        </div>
                        <table id="tabel_barang" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Tgl Stok</th>
                                    <th>Nama Barang</th>
                                    <th>Stok tersisa</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                        <div class="card">
                            <div class="card-header bg-light">List obat dipilih</div>
                            <input hidden type="text" id="kode_kunjungan"
                                value="{{ $data_kunjungan[0]->kode_kunjungan }}">
                            <div class="card-body">
                                <form action="" method="post" class="v_list_barang  mt-2 mt-2">
                                    <div class="draft_barang">
                                        <div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success" onclick="simpanresep()"><i class="bi bi-floppy"
                                style="margin-right:4px "></i> Simpan</button>
                        <button class="btn btn-danger" onclick="kembali()">Kembali</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalobatracikan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Silahkan Pilih Obat Racikan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="v_t_racikan">

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalbuatracikan" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Buat Obat Racikan</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="vv">
                    <div class="card">
                        <div class="card-header bg-warning">Header Racikan</div>
                        <div class="card-body">
                            <form action="" class="form_header_racikan">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Nama
                                                Racikan</label>
                                            <input type="text" class="form-control" id="namaracikan"
                                                name="namaracikan" placeholder="Ketik nama racikan ...">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Tipe
                                                Racikan</label><br>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tiperacikan"
                                                    id="tiperacikan" value="1" checked>
                                                <label class="form-check-label" for="radioDefault1">
                                                    Non - Powder
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="tiperacikan"
                                                    id="tiperacikan2" value="2">
                                                <label class="form-check-label" for="radioDefault2">
                                                    Powder
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="exampleFormControlInput1" class="form-label">Pilih sediaan</label>
                                        <select class="form-select" aria-label="Default select example"
                                            name="sediaan" id="sediaan">
                                            <option value="0">Silahkan Pilih Sediaan</option>
                                            <option value="1">Kapsul</option>
                                            <option value="2">Kertas Perkamen</option>
                                            <option value="3">Pot Salep</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">QTY
                                                Racikan</label>
                                            <input type="text" class="form-control" id="qtyracikan"
                                                name="qtyracikan" placeholder="Ketik qty racikan ..." value="0">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Aturan
                                                Pakai</label>
                                            <textarea type="text" class="form-control" id="aturanpakai" name="aturanpakai"
                                                placeholder="masukan aturan pakai ..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer">
                            <table class="table table-sm">
                                <tr>
                                    <td class="bg-warning">Cari Komponen Obat</td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="input-group mb-1">
                                            <input type="text" class="form-control"
                                                placeholder="Masukan nama obat ..." aria-label="Recipient’s username"
                                                aria-describedby="basic-addon2" id="input_pencarian_nama_komponen">
                                            <span class="btn btn-success input-group-text"
                                                id="tombol_cari_komponen"><i class="bi bi-search"></i> Cari
                                                Obat</span>
                                        </div>
                                        <div id="emailHelp" class="form-text mb-3 fw-bold fst-italic text-danger">
                                            *Silahkan masukan nama obat untuk memulai pencarian ...</div>
                                        <table id="tabel_barang2" class="table table-bordered table-hover mt-1"
                                            style="font-size:12px">
                                            <thead>
                                                <tr>
                                                    <th>Tgl Stok</th>
                                                    <th>Nama Barang</th>
                                                    <th>Satuan</th>
                                                    <th>Stok tersisa</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-body">
                            <form action="" class="form_komponen">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Nama
                                                Barang</label>
                                            <input readonly type="text" class="form-control"
                                                id="komponen_namabarang" name="komponen_namabarang"
                                                placeholder="nama barang ...">
                                            <input hidden readonly type="text" class="form-control"
                                                id="komponen_kodebarang" name="komponen_kodebarang"
                                                placeholder="kode barang ...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Satuan</label>
                                            <input readonly type="text" class="form-control"
                                                id="komponen_satuanbarang" name="komponen_satuanbarang"
                                                placeholder="satuan barang ...">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Stok</label>
                                            <input readonly type="text" class="form-control"
                                                id="komponen_stokbarang" name="komponen_stokbarang"
                                                placeholder="stok barang ...">
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Dosis</label>
                                            <input value="0" type="text" class="form-control"
                                                id="komponen_dosis" name="komponen_dosis"
                                                placeholder="dosis awal ...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <label for="exampleFormControlInput1" class="form-label">Dosis
                                                Racik</label>
                                            <input value="0" type="text" class="form-control"
                                                name="komponen_dosisracik" id="komponen_dosisracik"
                                                placeholder="nama barang ...">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-success tombolproses" id="tombolproses"
                                            style="margin-top:32px" onclick="prosesracikan()"><i
                                                class="bi bi-bullseye"></i> Proses</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="container">
                            <div class="card-header bg-warning">List Komponen obat yang sudah dipilih</div>
                            <div class="card-body">
                                <form action="" class="formdatakomponen">
                                    <div class="v_list_komponen"></div>
                                </form>
                            </div>
                            <div class="card-footer">
                                <button class="btn btn-success" onclick="simpanobatracikan()"><i
                                        class="bi bi-floppy"></i> Simpan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    var table2 = $('#tabel_barang2').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 4, // Menampilkan 4 data per halaman
        lengthMenu: [4, 6, 8], // Opsi jumlah data yang
        ajax: {
            url: "{{ route('ambildatastokdepo') }}",
            type: 'GET',
            // --- TAMBAHKAN BAGIAN INI ---
            data: function(d) {
                d.keyword = $('#input_pencarian_nama_komponen').val(); // Ambil nilai dari input form
                // d.kode_unit = $('#input_kode_unit').val(); // Contoh jika ada parameter lain
            }
            // -----------------------------
        },
        deferLoading: 0, // Menginstruksikan DataTables bahwa data di server belum dimuat
        language: {
            processing: '<div class="loading-container">' +
                '<img src="{{ asset('public/img/fb.gif') }}" width="80">' +
                '<p>Sedang mengambil data...</p>' +
                '</div>'
        },
        columns: [{
                data: 'tgl_stok',
                name: 'tgl_stok'
            },
            {
                data: 'nama_barang',
                name: 'nama_barang'
            },
            {
                data: 'satuan',
                name: 'satuan'
            },
            {
                data: 'stok_current',
                name: 'stok_current'
            },
            {
                data: null, // Kolom ini tidak terikat data langsung
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // row adalah objek data untuk baris tersebut
                    return '<button class="btn btn-success btn-sm pilihkomponen" ' +
                        'data-kode_barang="' + row.kode_barang + '" ' +
                        'data-nama_barang="' + row.nama_barang + '" ' +
                        'data-stok_barang="' + row.stok_current + '" ' +
                        'data-satuan_barang="' + row.satuan + '" ' +
                        // Tambahkan atribut lain yang dibutuhkan di sini
                        '><i class="bi bi-layer-backward"></i></button>';
                }
            }
        ]
    });
    var table = $('#tabel_barang').DataTable({
        processing: true,
        serverSide: true,
        searching: false,
        pageLength: 4, // Menampilkan 4 data per halaman
        lengthMenu: [4, 6, 8], // Opsi jumlah data yang
        ajax: {
            url: "{{ route('ambildatastokdepo') }}",
            type: 'GET',
            // --- TAMBAHKAN BAGIAN INI ---
            data: function(d) {
                d.keyword = $('#input_pencarian_nama').val(); // Ambil nilai dari input form
                // d.kode_unit = $('#input_kode_unit').val(); // Contoh jika ada parameter lain
            }
            // -----------------------------
        },
        deferLoading: 0, // Menginstruksikan DataTables bahwa data di server belum dimuat
        language: {
            processing: '<div class="loading-container">' +
                '<img src="{{ asset('public/img/fb.gif') }}" width="80">' +
                '<p>Sedang mengambil data...</p>' +
                '</div>'
        },
        columns: [{
                data: 'tgl_stok',
                name: 'tgl_stok'
            },
            {
                data: 'nama_barang',
                name: 'nama_barang'
            },
            {
                data: 'stok_current',
                name: 'stok_current'
            },
            {
                data: null, // Kolom ini tidak terikat data langsung
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    // row adalah objek data untuk baris tersebut
                    return '<button class="btn btn-primary btn-sm pilihobat" ' +
                        'data-kode_barang="' + row.kode_barang + '" ' +
                        'data-nama_barang="' + row.nama_barang + '" ' +
                        'data-stok_barang="' + row.stok_current + '" ' +
                        // Tambahkan atribut lain yang dibutuhkan di sini
                        '>Pilih</button>';
                }
            }
        ]
    });
    $('#input_pencarian_nama_komponen').on('keypress', function(e) {
        // 13 adalah kode tombol untuk Enter
        if (e.keyCode === 13) {
            e.preventDefault(); // Mencegah perilaku default (misal: submit form)
            table2.ajax.reload(); // Reload tabel
        }
    });
    $('#input_pencarian_nama').on('keypress', function(e) {
        // 13 adalah kode tombol untuk Enter
        if (e.keyCode === 13) {
            e.preventDefault(); // Mencegah perilaku default (misal: submit form)
            table.ajax.reload(); // Reload tabel
        }
    });
    $('#tombol_cari').click(function() {
        $('#tabel_barang').DataTable().ajax.reload(); // Reload tabel dengan parameter baru
    });
    $('#tombol_cari_komponen').click(function() {
        $('#tabel_barang2').DataTable().ajax.reload(); // Reload tabel dengan parameter baru
    });

    function kembali() {
        $('.v_1').removeAttr('hidden', true)
        $('.v_2').attr('hidden', true)
    }

    function simpanresep() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Data Resep akan disimpan !",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya Simpan"
        }).then((result) => {
            if (result.isConfirmed) {
                spinner_on()
                simpanresep2()
            }
        });
    }

    function simpanresep2() {
        kode_kunjungan = $('#kode_kunjungan').val()
        var data = $('.v_list_barang').serializeArray();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kode_kunjungan,
                data: JSON.stringify(data)
            },
            url: '<?= route('simpanresep') ?>',
            error: function(response) {
                spinner_off()
                alert('error')
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
                    location.reload()
                }
            }
        });
    }
    $(".ambilobatracik").on('click', function(event) {
        spinner_on()
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}"
            },
            url: '<?= route('ambillistobatracikan') ?>',
            error: function(response) {
                spinner_off()
                alert('error')
            },
            success: function(response) {
                spinner_off()
                $('.v_t_racikan').html(response);
            }
        });
    });
    $(".pilihobat").on('click', function(event) {
        kode_barang = $(this).attr('kode_barang')
        kode_bpjs = $(this).attr('kode_bpjs')
        nama_barang = $(this).attr('nama_barang')
        nama_generik = $(this).attr('nama_generik')
        aturan_pakai = $(this).attr('aturan_pakai')
        var wrapper = $(".draft_barang");
        $(wrapper).append(
            '<div class="row"><div class="col-md-4"><label for="exampleFormControlInput1" class="form-label">Nama Barang</label><input readonly type="text" class="form-control" id="namabarang" name="namabarang" value="' +
            nama_barang +
            '"><input hidden readonly type="text" class="form-control" id="kode_barang" name="kode_barang" value="' +
            kode_barang +
            '"><input hidden readonly type="text" class="form-control" id="kodebpjs" name="kodebpjs" value="' +
            kode_bpjs +
            '"><input hidden readonly type="text" class="form-control" id="nama_generik" name="nama_generik" value="' +
            nama_generik +
            '"></div><div class="col-md-1"><label for="exampleFormControlInput1" class="form-label">Qty beli</label><input type="text" class="form-control" id="qtybeli" name="qtybeli" value="0"></div><div class="col-md-1"><label for="exampleFormControlInput1" class="form-label">Signa 1</label><input type="text" class="form-control" id="signa1" name="signa1" value="0"></div><div class="col-md-1"><label for="exampleFormControlInput1" class="form-label">Signa 2</label><input type="text" class="form-control" id="signa2" name="signa2" value="0"></div><div class="col-md-3"><label for="exampleFormControlInput1" class="form-label">Aturan Pakai</label><textarea type="text" class="form-control" id="aturan_pakai" name="aturan_pakai">' +
            aturan_pakai +
            '</textarea></div><i class="bi bi-x-square remove_field form-group col-md-1 text-danger" kode2=""></i></div>'
        );
        $(wrapper).on("click", ".remove_field", function(e) { //user click on remove
            e.preventDefault();
            $(this).parent('div').remove();
            x--;
        })
        Swal.fire({
            title: nama_barang + " Berhasil dipilih ...",
            text: "Silahkan scroll kebawah untuk melihat list obat yang sudah dipilih ..",
            icon: "success"
        });
    });
    $('body').off('click', '.pilihkomponen').on('click', '.pilihkomponen', function(event) {
        event.preventDefault();

        var kode_barang = $(this).data('kode_barang');
        var nama_barang = $(this).data('nama_barang');
        var stok_barang = $(this).data('stok_barang');
        var satuan_barang = $(this).data('satuan_barang');
        $('#komponen_namabarang').val(nama_barang)
        $('#komponen_kodebarang').val(kode_barang)
        $('#komponen_satuanbarang').val(satuan_barang)
        $('#komponen_stokbarang').val(stok_barang)
        $('#komponen_dosis').val(0)
        $('#komponen_dosisracik').val(0)
        komponen_dosis
        komponen_dosisracik
        Swal.fire({
            title: nama_barang + " Berhasil dipilih",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
        });
        $('#tombolproses').focus();
    });
    $('body').off('click', '.pilihobat').on('click', '.pilihobat', function(event) {
        event.preventDefault();

        // Ambil data dari atribut data-* tombol yang diklik
        var kode_barang = $(this).data('kode_barang');
        var nama_barang = $(this).data('nama_barang');
        var stok_barang = $(this).data('stok_barang');
        // var kode_bpjs = $(this).data('kode_bpjs');
        // var aturan_pakai = $(this).data('aturan_pakai');

        // --- Logika untuk append ke draft_barang ---
        var wrapper = $(".draft_barang");
        var wrapper = $(".draft_barang");
        $(wrapper).append(
            '<div class="row align-items-center mb-2">' +
            // 1. Kolom Nama Barang (Readonly)
            '<div class="col-md-2">' +
            '<label class="form-label font-weight-bold small">Nama Barang</label>' +
            '<input readonly style="font-size:12px" type="text" class="form-control form-control-sm" name="namabarang" value="' +
            nama_barang +
            '">' +
            '<input hidden type="text" name="kode_barang" value="' + kode_barang + '">' +
            '</div>' +

            // 2. Kolom Jenis Obat (Select) - TAMBAHAN
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">Jenis</label>' +
            '<select class="form-select" name="jenis_obat">' +
            '<option value="Reguler">Reguler</option>' +
            '<option value="Kronis">Kronis</option>' +
            '<option value="Kemo">Kemo</option>' +
            '<option value="PRB">PRB</option>' +
            '</select>' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">Tipe</label>' +
            '<input readonly type="text" class="form-control form-control-sm" name="tipe" value="NON-RACIKAN" placeholder="0">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">Stok</label>' +
            '<input type="number" class="form-control form-control-sm" name="stok" value="' + stok_barang +
            '" placeholder="0">' +
            '</div>' +
            // 2. Kolom Jenis Obat (Select) - TAMBAHAN
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">Iterasi</label>' +
            '<select class="form-select" name="iterasi_obat">' +
            '<option value="0">Non iterasi</option>' +
            '<option value="1">Iterasi</option>' +
            '</select>' +
            '</div>' +

            // 3. Kolom Qty Beli
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">Qty</label>' +
            '<input type="number" class="form-control form-control-sm" name="qtybeli" value="0" placeholder="0">' +
            '</div>' +

            // 4. Kolom Signa 1
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">S1</label>' +
            '<input type="number" class="form-control form-control-sm" name="signa1" value="0" placeholder="0">' +
            '</div>' +

            // 5. Kolom Signa 2
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">S2</label>' +
            '<input type="number" class="form-control form-control-sm" name="signa2" value="0" placeholder="0">' +
            '</div>' +

            // 6. Kolom Aturan Pakai
            '<div class="col-md-2">' +
            '<label class="form-label font-weight-bold small">Aturan Pakai</label>' +
            '<textarea class="form-control form-control-sm" name="aturan_pakai" rows="1"></textarea>' +
            '</div>' +

            // 7. Kolom Hapus
            '<div class="col-md-1 text-center">' +
            '<label class="form-label font-weight-bold small">&nbsp;</label>' +
            '<div><i class="bi bi-x-square remove_field text-danger" style="cursor:pointer; font-size: 1.5rem;"></i></div>' +
            '</div>' +
            '</div>'
        );
        // --- Swal Success ---
        Swal.fire({
            title: nama_barang + " Berhasil dipilih",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
        });
    });
    $('.draft_barang').on("click", ".remove_field", function(e) {
        e.preventDefault();
        $(this).closest('.row').remove();
    });
    function prosesracikan() {
        spinner_on()
        var dataheader = $('.form_header_racikan').serializeArray();
        var datakomponen = $('.form_komponen').serializeArray();
        kode_kunjungan = $('#kode_kunjungan').val()
        $.ajax({
            async: true,
            type: 'post',
            dataType: 'json',
            data: {
                _token: "{{ csrf_token() }}",
                dataheader: JSON.stringify(dataheader),
                datakomponen: JSON.stringify(datakomponen),
                kode_kunjungan
            },
            url: '<?= route('proseskomponenracik') ?>',
            error: function(data) {
                spinner_off()
                Swal.fire({
                    icon: 'error',
                    title: 'Ooops....',
                    text: 'Sepertinya ada masalah......',
                    footer: ''
                })
            },
            success: function(response) {
                spinner_off()
                if (response.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Ups!',
                        text: response.message,
                    });
                } else {
                    Swal.fire({
                        icon: "success",
                        title: response.message,
                        text: "Silahkan cek dosis racik dan stoknya ...",
                        showConfirmButton: false,
                        timer: 1500
                    });
                    let newRow = `
                        <div class="row mb-2 item-obat border p-2">
                        <div class="col-sm-3">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
                                <input type="email" class="form-control" id="list_nama_barang" name="list_nama_barang" aria-describedby="emailHelp" value="${response.data.nama_barang}">
                                <input hidden type="email" class="form-control" id="list_kode_barang" name="list_kode_barang" aria-describedby="emailHelp" value="${response.data.kode_barang}">
                            </div>
                        </div>
                            <div class="col-sm-2">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Satuan</label>
                                    <input type="email" class="form-control" id="list_satuan_barang" name="list_satuan_barang" aria-describedby="emailHelp" value="${response.data.satuan_barang}">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Stok</label>
                                    <input type="email" class="form-control" id="list_stok_current_barang" name="list_stok_current_barang" aria-describedby="emailHelp" value="${response.data.stok_current}">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">QTY</label>
                                    <input type="email" class="form-control" id="list_qty_barang" name="list_qty_barang" aria-describedby="emailHelp" value="${response.data.jumlah}">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Dosis</label>
                                    <input readonly type="email" class="form-control" id="list_dosis_barang" name="list_dosis_barang" aria-describedby="emailHelp" value="${response.data.dosis_awal}">
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Racik</label>
                                    <input readonly type="email" class="form-control" id="list_dosis_racik_barang" name="list_dosis_racik_barang" aria-describedby="emailHelp" value="${response.data.dosis_racik}">
                                </div>
                            </div>
                        <div class="col-1 text-end">
                            <button type="button" class="btn btn-danger btn-sm btn-hapus"><i class="bi bi-x-circle"></i></button>
                        </div>
                        </div>`;
                    $('.v_list_komponen').append(newRow);
                }
            }
        });
    }
    $(document).on('click', '.btn-hapus', function() {
        $(this).closest('.item-obat').remove();
    });

    function simpanobatracikan() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Data racikan akan disimpan ...",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, simpan"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "Pastikan data racikan sudah dibuat dengan benar",
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: "Ya, simpan data racikan",
                    denyButtonText: `Batal`
                }).then((result) => {
                    if (result.isConfirmed) {
                        simpandata()
                    } else if (result.isDenied) {
                        Swal.fire("Changes are not saved", "", "info");
                    }
                });
            }
        });
    }

    function simpandata() {
        kode_kunjungan = $('#kode_kunjungan').val()
        var dataheader = $('.form_header_racikan').serializeArray();
        var datakomponen = $('.formdatakomponen').serializeArray();
        $.ajax({
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
                kode_kunjungan,
                dataheader: JSON.stringify(dataheader),
                datakomponen: JSON.stringify(datakomponen)
            },
            url: '<?= route('simpanobatracikan') ?>',
            error: function(response) {
                spinner_off()
                alert('error')
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
                    $('#modalbuatracikan').modal('toggle')
                    $('.modal-backdrop').remove();
                    Swal.fire({
                        icon: 'success',
                        title: 'OK!',
                        text: response.message,
                    });
                    clearFormByClass('vv');
                }
            }
        });
    }

    function clearFormByClass(className) {
        let container = $('.' + className);
        container.find('input:text, input:password, input[type=number], input[type=email], textarea').val('');
        container.find('select').prop('selectedIndex', 0).trigger('change');
        container.find('input:checkbox, input:radio').prop('checked', false);
        container.find('.is-invalid').removeClass('is-invalid');
        container.find('.text-danger').empty();
    }
</script>
