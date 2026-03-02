<div class="card mt-2">
    <div class="card-header">Data Purchase Order</div>
    <div class="card-body">
        <table class="table table-sm">
            <tr>
                <td>Tanggal Input</td>
                <td>: {{ \Carbon\Carbon::parse($data_header[0]->tgl_input)->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
            <tr>
                <td>Nomor PO</td>
                <td>: {{ $data_header[0]->kode_po }}</td>
            </tr>
            <tr>
                <td>Nomor Faktur</td>
                <td>: {{ $data_header[0]->no_faktur }}</td>
            </tr>
            <tr>
                <td>Nama Supplier</td>
                <td>: {{ $data_header[0]->nama_supplier }}</td>
            </tr>
            <tr>
                <td>Tanggal Beli / Tanggal Terima</td>
                <td>: {{ \Carbon\Carbon::parse($data_header[0]->tgl_beli)->locale('id')->translatedFormat('d F Y') }} /
                    {{ \Carbon\Carbon::parse($data_header[0]->tgl_terima)->locale('id')->translatedFormat('d F Y') }}
                </td>
            </tr>
        </table>
        <div class="card mt-2">
            <div class="card-header">List Barang</div>
            <div class="card-body">
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalcaribarang"><i
                        class="bi bi-search"></i> Cari Barang</button>
                <form action="" method="post" class="v_list_barang  mt-2 mt-2">
                    <div class="draft_barang">
                        <div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer">
                <button class="btn btn-success" onclick="prosespodetail()"><i class="bi bi-cpu"></i> Proses</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modalcaribarang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Silahkan Pilih Barang</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="v_data_barang">
                    <table id="tabel_barang" class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Kode Barang</th>
                                <th>Nama Barang</th>
                                <th>Satuan</th>
                                <th>sediaan</th>
                                <th>Dosis</th>
                                <th>Aturan Pakai</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#tabel_barang').DataTable({
            processing: true,
            responsive: false,
            autoWidth: false,
            serverSide: true, // Fitur server side aktif
            ajax: "{{ route('ambilbarang') }}",
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
                    data: 'aturan_pakai',
                    name: 'aturan_pakai'
                }, {
                    data: null, // Kolom ini tidak terikat data langsung
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<button class="btn btn-primary btn-sm pilihobat" ' +
                            'data-kode_barang="' + row.kode_barang + '" ' +
                            'data-nama_barang="' + row.nama_barang + '" ' +
                            'data-sediaan="' + row.sediaan + '" ' +
                            // Tambahkan atribut lain yang dibutuhkan di sini
                            '>Pilih</button>';
                    }
                }
            ]
        });
    });
    $('body').off('click', '.pilihobat').on('click', '.pilihobat', function(event) {
        event.preventDefault();

        // Ambil data dari atribut data-* tombol yang diklik
        var kode_barang = $(this).data('kode_barang');
        var nama_barang = $(this).data('nama_barang');
        var sediaan = $(this).data('sediaan');
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
            '<label class="form-label font-weight-bold small">Sediaan</label>' +
            '<input readonly style="font-size:12px" type="text" class="form-control form-control-sm" name="sediaan" value="' +
            sediaan +
            '">' +
            '</div>' +
            '<div class="col-md-2">' +
            '<label class="form-label font-weight-bold small">Batch</label>' +
            '<input readonly style="font-size:12px" type="text" class="form-control form-control-sm" name="batch" value="">' +
            '</div>' +
            '<div class="col-md-2">' +
            '<label class="form-label font-weight-bold small">ED</label>' +
            '<input style="font-size:12px" type="date" class="form-control form-control-sm" name="ed" value="">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">isi</label>' +
            '<input style="font-size:12px" type="text" class="form-control form-control-sm" name="isi" value="">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">isi satuan kecil</label>' +
            '<input style="font-size:12px" type="text" class="form-control form-control-sm" name="isi_satuan_kecil" value="">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">QTY PO</label>' +
            '<input style="font-size:12px" type="text" class="form-control form-control-sm" name="qty_po" value="">' +
            '</div>' +
            '<div class="col-md-1">' +
            '<label class="form-label font-weight-bold small">Harga satuan</label>' +
            '<input style="font-size:12px" type="text" class="form-control form-control-sm" name="harga_satuan" value="">' +
            '</div>' +
            // 4. Kolom Signa 1
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
    // Event Handler untuk Remove Field (menggunakan delegation juga)
    $('.draft_barang').on("click", ".remove_field", function(e) {
        e.preventDefault();
        $(this).closest('.row').remove();
    });

    function prosespodetail() {
        Swal.fire({
            title: "Anda yakin ?",
            text: "Pastikan data sudah terisi dengan benar",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "OK"
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: "apakah data sudah benar ?",
                    showDenyButton: false,
                    showCancelButton: true,
                    confirmButtonText: "Ya, Simpan ",
                    denyButtonText: `Don't save`
                }).then((result) => {
                    if (result.isConfirmed) {
                        prosespodetailfinal()
                    } else if (result.isDenied) {

                    }
                });
            }
        });
    }
</script>
