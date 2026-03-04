@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Purchase Order</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Order</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="v_1">
                <button class="btn btn-success" onclick="ambilformheader()"><i class="bi bi-folder-plus"
                        style="margin-right:8px"></i> PO Header</button>
                <div hidden class="v_form_header mt-2">
                    <div class="card">
                        <div class="card-header">Form PO Header</div>
                        <div class="card-body p-2">
                            <form action="" class="formpoheader">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div class="row g-1">
                                        <div class="col-md-7">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Pilih Supplier</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="supplier_search" name="supplier_search">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Supplier ID</label>
                                                <input readonly type="text" class="form-control form-control-sm"
                                                    id="supplier_id" name="supplier_id">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Alamat</label>
                                                <textarea rows="3" readonly class="form-control form-control-sm" id="alamat_supplier" name="alamat_supplier"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Telp</label>
                                                <input readonly type="text" class="form-control form-control-sm"
                                                    id="telp_supplier" name="telp_supplier">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="row g-1">
                                        <div class="col-md-8">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Tgl Pembelian</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="tanggalbeli" value="{{ $today }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Termin</label>
                                                <input type="text" class="form-control form-control-sm" name="termin">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Tgl Penerimaan</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    name="tanggalterima" value="{{ $today }}">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Tipe Pembelian</label>
                                                <select class="form-select form-select-sm" id="tipepembelian" name="tipepembelian">
                                                    <option value="K">Kredit</option>
                                                    <option value="T">Tunai</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Kategori Barang</label>
                                                <select class="form-select form-select-sm" name="kategoribarang" id="kategoribarang">
                                                    @foreach ($tipe as $t)
                                                        <option value="{{ $t->kode_tipe }}">{{ $t->kode_tipe }} |
                                                            {{ $t->nama_tipe }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Nomor Faktur</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    name="nomorfaktur" id="nomorfaktur">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-5">
                                    <div class="row g-1">
                                        <div class="col-6 d-flex align-items-center justify-content-between mb-1">
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="checkbox" id="materai" name="materai">
                                                <label class="form-check-label small" for="materai">Materai</label>
                                            </div>
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="checkbox" id="ppn" name="ppn">
                                                <label class="form-check-label small" for="ppn">PPN</label>
                                            </div>
                                            <div class="form-check form-check-inline mb-0">
                                                <input class="form-check-input" type="checkbox" id="pph" name="pph">
                                                <label class="form-check-label small" for="ppn">PPH</label>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="mb-1 row g-0 align-items-center">
                                                <label class="col-sm-2 form-label small mb-0">Total Pembelian</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control form-control-sm text-end"
                                                        name="total">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Potongan (%)</label>
                                                <div class="input-group input-group-sm">
                                                    <input type="text" class="form-control form-control-sm text-end" name="potonganpersen">
                                                    <span class="input-group-text p-1">%</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="mb-1">
                                                <label class="form-label small mb-0">Potongan (Rp)</label>
                                                <input type="text" class="form-control form-control-sm text-end" name="potongantunai">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1 row g-0 align-items-center">
                                                <label class="col-sm-2 form-label small mb-0">Sub Grand Total</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm text-end fw-bold" name="subgrandtotal" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1 row g-0 align-items-center">
                                                <label class="col-sm-2 form-label small mb-0">PPN</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm text-end fw-bold" name="nominalppn" readonly>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1 row g-0 align-items-center">
                                                <label class="col-sm-2 form-label small mb-0 text-dark fw-bold">Grand
                                                    Total</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm text-end border-primary"
                                                        readonly name="grandtotal">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-1 row g-0 align-items-center">
                                                <label class="col-sm-2 form-label small mb-0 text-dark fw-bold">TOTAL UTANG</label>
                                                <div class="col-sm-8">
                                                    <input type="text"
                                                        class="form-control form-control-sm text-end border-primary"
                                                        readonly name="totalutang">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </form>
                            <div class="card-header mt-5">Pilih Barang</div>
                            <div class="card-body">
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <button class="btn btn-success" onclick="simpanpoheader()"><i class="bi bi-floppy"
                                    style="margin-right:8px"></i>
                                Simpan</button>
                            <button class="btn btn-danger" onclick="batal()"><i class="bi bi-arrow-clockwise"
                                    style="margin-right:8px"></i> Batal</button>
                        </div>
                    </div>
                </div>
                <div hidden class="card mt-2">
                    <div class="card-header bg-light">Data PO Header</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" id="tanggalawal"
                                        aria-describedby="emailHelp" value="{{ $date_start }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Tanggal Akhir</label>
                                    <input type="date" class="form-control" id="tanggalakhir"
                                        aria-describedby="emailHelp" value="{{ $date_end }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-success" style="margin-top:31px"
                                    onclick="caridataerimabarang()"><i class="bi bi-search" style="margin-right:8px"></i>
                                    Tampilkan</button>
                            </div>
                        </div>
                        <div class="v_data_po_header mt-2">

                        </div>
                    </div>
                </div>
            </div>
            <div hidden class="v_2">
                <button class="btn btn-danger" onclick="kembali()">Kembali</button>
                <div class="v_detail">

                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script>
        $(document).ready(function() {
            caridataerimabarang()
        })

        function kembali() {
            $('.v_1').removeAttr('hidden', true)
            $('.v_2').attr('hidden', true)
        }

        function caridataerimabarang() {
            tanggalawal = $('#tanggalawal').val()
            tanggalakhir = $('#tanggalakhir').val()
            spinner_on()
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggalawal,
                    tanggalakhir
                },
                url: '<?= route('ambildatatgpoheader') ?>',
                error: function(response) {
                    spinner_off()
                    alert('error')
                },
                success: function(response) {
                    spinner_off()
                    $('.v_data_po_header').html(response);
                }
            });
        }
        $(document).ready(function() {
            $("#supplier_search").autocomplete({
                source: "{{ route('supplier.search') }}",
                minLength: 2, // Mulai mencari setelah 2 karakter
                select: function(event, ui) {
                    // Set ID supplier ke hidden input saat dipilih
                    $("#supplier_id").val(ui.item.id);
                    $("#alamat_supplier").val(ui.item.alamat);
                    $("#telp_supplier").val(ui.item.telp);
                }
            });
        });

        function ambilformheader() {
            $('.v_form_header').removeAttr('hidden', true)
        }

        function batal() {
            $('.v_form_header').attr('hidden', true)
        }

        function simpanpoheader() {
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
                            simpanpoheaderfinal()
                        } else if (result.isDenied) {

                        }
                    });
                }
            });
        }

        function simpanpoheaderfinal() {
            var data = $('.formpoheader').serializeArray();
            $.ajax({
                type: 'post',
                data: {
                    _token: "{{ csrf_token() }}",
                    data: JSON.stringify(data)
                },
                url: '<?= route('simpanpoheader') ?>',
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

        // const inputMask = document.getElementById('totalpo_mask');
        // const inputAsli = document.getElementById('totalpo_asli');
        // const labelAsli = document.getElementById('label_asli');

        // const inputMask2 = document.getElementById('ppn_mask');
        // const inputAsli2 = document.getElementById('ppn_asli');
        // const labelAsli2 = document.getElementById('label_asli_ppn');

        // const inputMask3 = document.getElementById('totalhutang_mask');
        // const inputAsli3 = document.getElementById('totalhutang_asli');
        // const labelAsli3 = document.getElementById('label_asli_totalhutang');
        // inputMask.addEventListener('keyup', function(e) {
        //     // 1. Ambil angka saja dari input
        //     let nominal = this.value.replace(/[^,\d]/g, '').toString();

        //     // 2. Masukkan angka bersih ke input hidden & label
        //     inputAsli.value = nominal;
        //     labelAsli.innerText = nominal;

        //     // 3. Ubah tampilan input menjadi format ribuan
        //     this.value = formatRupiah(nominal);
        // });
        // inputMask2.addEventListener('keyup', function(e) {
        //     // 1. Ambil angka saja dari input
        //     let nominal = this.value.replace(/[^,\d]/g, '').toString();

        //     // 2. Masukkan angka bersih ke input hidden & label
        //     inputAsli2.value = nominal;
        //     labelAsli2.innerText = nominal;

        //     // 3. Ubah tampilan input menjadi format ribuan
        //     this.value = formatRupiah(nominal);
        // });
        // inputMask3.addEventListener('keyup', function(e) {
        //     // 1. Ambil angka saja dari input
        //     let nominal = this.value.replace(/[^,\d]/g, '').toString();

        //     // 2. Masukkan angka bersih ke input hidden & label
        //     inputAsli3.value = nominal;
        //     labelAsli3.innerText = nominal;

        //     // 3. Ubah tampilan input menjadi format ribuan
        //     this.value = formatRupiah(nominal);
        // });
        // /* Fungsi Format Ribuan */
        // function formatRupiah(angka) {
        //     let number_string = angka.replace(/[^,\d]/g, '').toString(),
        //         split = number_string.split(','),
        //         sisa = split[0].length % 3,
        //         rupiah = split[0].substr(0, sisa),
        //         ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        //     if (ribuan) {
        //         separator = sisa ? '.' : '';
        //         rupiah += separator + ribuan.join('.');
        //     }
        //     return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        // }
    </script>
@endsection
