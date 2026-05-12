@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Master Barang</h3>
                </div>

                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Master Barang</li>
                    </ol>
                </div>

            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="v1">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modaladdmasterbarang"><i
                        class="bi bi-database-fill-add" style="margin-right:8px "></i>
                    Barang</button>
                <button hidden class="btn btn-warning" onclick="mappingbarang()"><i class="bi bi-database-fill-add"
                        style="margin-right:8px "></i>
                    Mapping DPHO BPJS</button>
                <div class="card mt-3">
                    <div class="card-header">Data Master Barang</div>
                    <div class="card-body">
                        <div class="v_data_barang">
                            <table id="tabel_barang" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Kode Obat BPJS</th>
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
                </div>
            </div>
            <div hidden class="v2">
                <button class="btn btn-danger" onclick="back()"><i class="bi bi-backspace"></i> Kembali</button>
                <div class="card mt-3">
                    <div class="card-header">Pilih Master Barang</div>
                    <div class="card-body">
                        <div class="v_data_barang">
                            <table id="tabel_barang_belum_mapping" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Satuan</th>
                                        <th>sediaan</th>
                                        <th>Dosis</th>
                                        <th>Aturan Pakai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card mt-4 shadow-sm">
                    <div class="card-header bg-info text-white">Form Mapping Barang</div>
                    <div class="card-body">
                        <form action="" class="formbarangmapping">
                            <div id="dynamic-form-wrapper" class="mt-4">
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-success" onclick="simpanmapping()"><i class="bi bi-floppy"></i>
                            Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaleditbarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Master Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="isi_modal">
                    <!-- Form akan muncul di sini via AJAX -->
                    <div class="text-center">Memuat data...</div>
                </div>

            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="modaladdmasterbarang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Input Master Barang</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('simpanmasterbarang') }}" method="POST" id="formmasterbarang">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <label>Tipe Barang</label>
                                <select class="form-select" aria-label="Default select example" name="kode_tipe_barang">
                                    @foreach ($tipebarang as $t)
                                        <option value="{{ $t->kode_tipe }}">
                                            {{ $t->nama_tipe }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label>Kelompok Barang</label>
                                <select class="form-select" aria-label="Default select example" name="kode_kelompok_barang">
                                    @foreach ($kelompokbarang as $t)
                                        <option value="{{ $t->nama_kelompok }}">
                                            {{ $t->nama_kelompok }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mt-3 form-group">
                                <label>Nama Generik</label>
                                <input type="text" name="nama_generik" class="form-control" value="">
                            </div>
                            <div class="col-md-6 mt-3 form-group">
                                <label>Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control" value="">
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Satuan Besar</label>
                                <select class="form-select" aria-label="Default select example" name="satuan_besar">
                                    @foreach ($satuan as $t)
                                        <option value="{{ $t->kode_satuan }}">{{ $t->nama_satuan }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3 mt-3 form-group">
                                <label>Satuan Kecil</label>
                                <select class="form-select" aria-label="Default select example" name="satuan">
                                    @foreach ($satuan as $t)
                                        <option value="{{ $t->kode_satuan }}">
                                            {{ $t->nama_satuan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Isi</label>
                                <input type="text" name="isi" class="form-control" value="0">
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Sediaan</label>
                                <select class="form-select" aria-label="Default select example" name="sediaan">
                                    @foreach ($sediaan as $t)
                                        <option value="{{ $t->nama_sediaan }}">
                                            {{ $t->nama_sediaan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Pabrikan</label>
                                <select class="form-select" aria-label="Default select example" name="id_pabrik">
                                    <option value="0">Silahkan Pilih</option>
                                    @foreach ($pabrikan as $t)
                                        <option value="{{ $t->id }}">
                                            {{ $t->nama_pabrik }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Dosis</label>
                                <input type="text" name="dosis" class="form-control" value="0">
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Kelompok Tarif</label>
                                <select class="form-select" aria-label="Default select example" name="kelompok_tarif">
                                    <option value="0">Silahkan Pilih</option>
                                    @foreach ($kelompoktarif as $t)
                                        <option value="{{ $t->kelompok_tarif_id }}">
                                            {{ $t->kelompok_tarif_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Harga Beli</label>
                                <input type="text" name="harga_beli_disp" id="harga_beli_disp" class="form-control"
                                    value="0">
                                <input hidden type="text" name="harga_beli" id="harga_beli" class="form-control"
                                    value="0">
                                <label hidden id="labelasli">Harga Jual</label>
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>Harga jual</label>
                                <input type="text" name="harga_jual_baru_disp" id="harga_jual_baru_disp"
                                    class="form-control" value="0">
                                <input hidden type="text" name="harga_jual_baru" id="harga_jual_baru"
                                    class="form-control" value="0">
                                <label hidden id="labelasli">Harga Jual</label>
                            </div>
                            <div class="col-md-3 mt-3 form-group">
                                <label>HNA</label>
                                <input type="text" name="hna_disp" id="hna_disp" class="form-control"
                                    value="0">
                                <input hidden type="text" name="hna" id="hna" class="form-control"
                                    value="0">
                                <label hidden id="labelasli">Harga Jual</label>
                            </div>
                            <div class="col-md-4 mt-4">
                                <label>Aturan Pakai</label>
                                <textarea type="text" name="aturan_pakai" class="form-control" value=""></textarea>
                            </div>
                        </div>
                        <div class="modal-footer px-0 pb-0 mt-4">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary" id="btnsimpanbarang">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            // Gunakan fungsi terpisah agar rapi
            initFormSubmit('#formEditBarang', 'Memperbarui data...');
            initFormSubmit('#formmasterbarang', 'Menyimpan data baru...');
        });

        function initFormSubmit(formId, loadingText) {
            // .off() memastikan event tidak menumpuk jika script terpanggil 2x
            $(document).off('submit', formId).on('submit', formId, function(e) {
                e.preventDefault();
                let form = $(this);
                let formData = form.serialize();
                let btn = form.find('button[type="submit"]');
                let modalId = form.closest('.modal').attr('id'); // Otomatis deteksi ID modal
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Pastikan data sudah benar sebelum disimpan",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Tampilkan Loading
                        Swal.fire({
                            title: 'Mohon Tunggu',
                            text: loadingText,
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        btn.attr('disabled', true);

                        $.ajax({
                            url: form.attr(
                                'action'), // Mengambil route langsung dari atribut action form
                            type: "POST",
                            data: formData,
                            success: function(response) {
                                if (response.status == 'success') {
                                    // Tutup modal secara dinamis

                                    if (modalId) {
                                        $('#' + modalId).modal('hide');
                                    }

                                    $('.modal-backdrop').remove();
                                    $('body').removeClass('modal-open').css('padding-right',
                                        '');

                                    // Reload DataTables (jika ada)
                                    if ($.fn.DataTable.isDataTable('#tabel_barang')) {
                                        $('#tabel_barang').DataTable().ajax.reload(null, false);
                                    }

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message ||
                                            'Data telah disimpan.',
                                        showConfirmButton: false,
                                        timer: 1500,
                                        timerProgressBar: true
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: response.message || 'Terjadi kesalahan.'
                                    });
                                }
                            },
                            error: function(xhr) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Kesalahan Sistem',
                                    text: 'Error ' + xhr.status +
                                        ': Silahkan hubungi IT APW LAB.'
                                });
                            },
                            complete: function() {
                                btn.attr('disabled', false);
                            }
                        });
                    }
                });
            });
        }
    </script>
    <script>
        function mappingbarang() {
            $('.v1').attr('hidden', true)
            $('.v2').removeAttr('hidden', true)
        }

        function back() {
            $('.v1').removeAttr('hidden', true)
            $('.v2').attr('hidden', true)
            location.reload()
        }
        $(document).ready(function() {
            var tableBarang = $('#tabel_barang').DataTable({
                processing: true,
                serverSide: true, // Fitur server side aktif
                ajax: "{{ route('ambilbarang') }}",
                columns: [{
                        data: 'kode_barang',
                        name: 'kode_barang'
                    }, // Nomor urut otomatis
                    {
                        data: 'kodeobat',
                        name: 'kodeobat'
                    },
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
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ]
            });
            $('body').on('click', '.deletebarang', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: "Anda yakin ?",
                    text: "Data Obat akan dihapus ...",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya Hapus"
                }).then((result) => {
                    if (result.isConfirmed) {
                        spinner_on()
                        $.ajax({
                            type: 'post',
                            data: {
                                _token: "{{ csrf_token() }}",
                                id
                            },
                            url: '<?= route('hapusobat') ?>',
                            error: function(response) {
                                spinner_off()
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ups!',
                                    text: response.message,
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
                                    tableBarang.ajax.reload(null, false);
                                }
                            }
                        });
                    }
                });
            });
        });
        $(document).ready(function() {
            $('#tabel_barang_belum_mapping').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('ambilbarangbelummapping') }}",
                autoWidth: false,
                responsive: false,
                columns: [{
                        data: 'kode_barang',
                        name: 'kode_barang'
                    },
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
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `<button type="button" class="btn btn-sm btn-primary btn-pilih" 
                            data-kode="${row.kode_barang}" 
                            data-nama="${row.nama_barang}">
                            Pilih
                        </button>`;
                        }
                    }
                ]
            });
        });

        $(document).on('click', '.btn-pilih', function() {
            let kode = $(this).data('kode');
            let nama = $(this).data('nama');
            // Generate ID unik menggunakan timestamp agar tidak bentrok
            let uniqueId = Date.now();
            let htmlForm = `
                <div class="card mb-2 shadow-sm item-form-mapping" id="form-${uniqueId}">
                    <div class="card-body p-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Kode</label>
                                <input type="text" name="kode_barang" class="form-control form-control-sm bg-light" value="${kode}" readonly>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Nama Barang</label>
                                <input type="text" name="nama_barang" class="form-control form-control-sm bg-light" value="${nama}" readonly>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold">Mapping Ref DPHO</label>
                                <select name="ref_dpho" class="form-control select-dpho" style="width: 100%"></select>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-sm btn-outline-danger btn-remove-form w-100" 
                                    data-id="form-${uniqueId}" title="Hapus baris ini">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            // Tambahkan form ke wrapper
            $('#dynamic-form-wrapper').append(htmlForm);

            // Inisialisasi Select2/Autocomplete pada element yang baru dibuat
            initSelect2Dpho($(`#form-${uniqueId} .select-dpho`));
        });

        // Fungsi untuk menghapus form
        $(document).on('click', '.btn-remove-form', function() {
            let targetId = $(this).data('id');
            $(`#${targetId}`).fadeOut(300, function() {
                $(this).remove();
            });
        });

        // Fungsi Inisialisasi Autocomplete (Select2)
        function initSelect2Dpho(element) {
            element.select2({
                placeholder: 'Cari di ref_dpho...',
                ajax: {
                    url: "{{ route('cari.dpho') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });
            $(document).on('select2:open', () => {
                document.querySelector('.select2-search__field').focus();
            });
        }

        function simpanmapping() {
            Swal.fire({
                title: "Anda yakin data sudah benar ?",
                text: "Data obat akan disimpan ...",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, Simpan !"
            }).then((result) => {
                if (result.isConfirmed) {
                    simpanmappobat()
                }
            });
        }

        function simpanmappobat() {
            spinner_on()
            var dataobat = $('.formbarangmapping').serializeArray();
            $.ajax({
                async: true,
                type: 'post',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}",
                    dataobat: JSON.stringify(dataobat),
                },
                url: '<?= route('simpanmappingbaru') ?>',
                error: function(data) {
                    spinner_off()
                    Swal.fire({
                        icon: 'error',
                        title: 'Ooops....',
                        text: 'Sepertinya ada masalah......',
                        footer: ''
                    })
                },
                success: function(data) {
                    spinner_off()
                    if (data.kode == 500) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Oopss...',
                            text: data.message,
                            footer: ''
                        })
                    } else {
                        Swal.fire({
                            icon: 'success',
                            title: 'OK',
                            text: data.message,
                            footer: ''
                        })
                        document.getElementById("formbarangmapping").reset();
                        location.reload()
                    }
                }
            });
        }
        $('body').on('click', '.editbarang', function() {
            var id = $(this).data('id');
            $('#isi_modal').html('<div class="text-center">Memuat data...</div>');
            $.ajax({
                url: "{{ url('barang/edit') }}/" + id, // Sesuaikan route
                type: "GET",
                success: function(response) {
                    // Masukkan form yang dikirim dari controller ke dalam modal
                    $('#isi_modal').html(response);
                },
                error: function() {
                    $('#isi_modal').html('<div class="alert alert-danger">Gagal mengambil data.</div>');
                }
            });
        });



        const inputMask = document.getElementById('harga_beli_disp');
        const inputAsli = document.getElementById('harga_beli');
        const inputMask2 = document.getElementById('harga_jual_baru_disp');
        const inputAsli2 = document.getElementById('harga_jual_baru');
        const inputMask3 = document.getElementById('hna_disp');
        const inputAsli3 = document.getElementById('hna');
        const labelAsli = document.getElementById('labelasli');
        inputMask.addEventListener('keyup', function(e) {
            // 1. Ambil angka saja dari input
            let nominal = this.value.replace(/[^,\d]/g, '').toString();
            // 2. Masukkan angka bersih ke input hidden & label
            inputAsli.value = nominal;
            labelAsli.innerText = nominal;
            // 3. Ubah tampilan input menjadi format ribuan
            this.value = formatRupiah(nominal);
        });
        inputMask2.addEventListener('keyup', function(e) {
            // 1. Ambil angka saja dari input
            let nominal = this.value.replace(/[^,\d]/g, '').toString();
            // 2. Masukkan angka bersih ke input hidden & label
            inputAsli2.value = nominal;
            labelAsli.innerText = nominal;
            // 3. Ubah tampilan input menjadi format ribuan
            this.value = formatRupiah(nominal);
        });
        inputMask3.addEventListener('keyup', function(e) {
            // 1. Ambil angka saja dari input
            let nominal = this.value.replace(/[^,\d]/g, '').toString();
            // 2. Masukkan angka bersih ke input hidden & label
            inputAsli3.value = nominal;
            labelAsli.innerText = nominal;
            // 3. Ubah tampilan input menjadi format ribuan
            this.value = formatRupiah(nominal);
        });

        function formatRupiah(angka) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }
            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
    </script>
@endsection
