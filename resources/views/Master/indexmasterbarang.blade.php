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
                <button class="btn btn-success"><i class="bi bi-database-fill-add" style="margin-right:8px "></i>
                    Barang</button>
                <button class="btn btn-warning" onclick="mappingbarang()"><i class="bi bi-database-fill-add"
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
            $('#tabel_barang').DataTable({
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
                    }
                ]
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
    </script>
@endsection
