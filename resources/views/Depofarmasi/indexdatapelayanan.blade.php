@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data Pelayanan</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Pelayanan Resep</li>
                    </ol>
                </div>
            </div>
            <!--end::Row-->
        </div>
        <!--end::Container-->
    </div>
    <div class="app-content">
        <!--begin::Container-->
        <div class="container-fluid">
            <div class="v_1">
                <div class="card">
                    <div class="card-header">Tentukan tanggal kunjungan pasien</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Pilih Unit</label>
                                    <select class="form-select" aria-label="Default select example" id="pilihunit">
                                        <option selected value="semua">Tampilkan Semua Unit</option>
                                        @foreach ($unit as $d)
                                            @if ($d->kode_unit > 4000 && $d->kode_unit < 4014)
                                                <option value="{{ $d->kode_unit }}">{{ $d->nama_unit }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Tanggal Awal</label>
                                    <input type="date" class="form-control" id="tanggalawal" aria-describedby="emailHelp"
                                        value="{{ $date_start }}">
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
                                <button class="btn btn-success" style="margin-top:31px" onclick="carikunjungan()"><i
                                        class="bi bi-search" style="margin-right:8px"></i> Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mt-2">
                    <div class="card-header">Data Kunjungan Pasien</div>
                    <div class="card-body">
                        <div class="v_data_pasien">
                            <table id="tabel_kunjungan" class="table table-sm table-hover table-bordered align-middle"
                                style="width:100%">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Kode Layanan</th>
                                        <th>Tgl Masuk</th>
                                        <th>Unit Pengirim</th>
                                        <th>Pasien</th>
                                        <th>Penjamin</th>
                                        <th>No SEP</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Bridging</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div hidden class="v_2">

            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetailLayanan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i> Rincian Pelayanan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <th>No. Layanan</th>
                                    <td>: <span id="det_kode_header" class="fw-bold"></span></td>
                                </tr>
                                <tr>
                                    <th>Nama Pasien</th>
                                    <td>: <span id="det_nama_pasien"></span></td>
                                </tr>
                                <tr>
                                    <th>Keterangan</th>
                                    <td>: <span id="det_keterangan"></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" id="tabel_rincian_obat">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Nama Barang / Tindakan</th>
                                    <th class="text-center">Qty</th>
                                    <th>Satuan</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-end">Subtotal</th>
                                    <th>Aturan Pakai</th>
                                </tr>
                            </thead>
                            <tbody id="isi_rincian_pelayanan">
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="5" class="text-end">Total Tagihan :</td>
                                    <td class="text-end text-primary" id="det_total_seluruh">0</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">Bridging BPJS :</td>
                                    <td colspan="2" class=" text-dark" id="ket_bridging">0</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">SEP ASAL :</td>
                                    <td colspan="2" class="text-dark" id="ket_sep_asal">0</td>
                                </tr>
                                <tr>
                                    <td colspan="5" class="text-end">SEP APOTEK :</td>
                                    <td colspan="2" class=" text-dark" id="ket_sep_apotik">0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            carikunjungan()
        })

        function carikunjungan() {
            let unit = $('#pilihunit').val();
            let tgl_awal = $('#tanggalawal').val();
            let tgl_akhir = $('#tanggalakhir').val();
            if ($.fn.DataTable.isDataTable('#tabel_kunjungan')) {
                $('#tabel_kunjungan').DataTable().destroy();
            }
            $('#tabel_kunjungan').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('get.riwayat.pelayanan') }}",
                    data: {
                        unit,
                        tgl_awal,
                        tgl_akhir
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        className: 'text-center',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode_layanan_header',
                        name: 'kode_layanan_header'
                    },
                    {
                        data: 'tgl_entry',
                        name: 'tgl_entry',
                        searchable: false, // Tambahkan ini agar tidak dicari
                        orderable: true
                    },
                    {
                        data: 'nama_unit_asal',
                        name: 'nama_unit_asal'
                    },
                    {
                        data: null,
                        render: function(data) {
                            return `<strong>${data.nama_pasien}</strong><br><small class="text-muted">${data.no_rm}</small>`;
                        }
                    },
                    {
                        data: 'nama_penjamin',
                        name: 'nama_penjamin',
                        defaultContent: '-'
                    },
                    {
                        data: 'no_Sep',
                        name: 'no_Sep',
                        defaultContent: '-'
                    },
                    {
                        data: 'status_layanan',
                        searchable: false, // Tambahkan ini agar tidak dicari
                        orderable: true,
                        className: 'text-center',
                        render: function(data, type, row) {
                            // Contoh Logika Warna Status
                            let badge = 'bg-secondary';
                            if (data == '1') badge = 'bg-info'; // Masuk
                            if (data == '2') badge = 'bg-success'; // Selesai
                            return `<span class="badge ${badge}">${row.label_status || 'Proses'}</span>`;
                        }
                    },
                    {
                        data: 'status_bridging',
                        className: 'text-center',
                        searchable: false, // Tambahkan ini agar tidak dicari
                        orderable: true,
                        // render: function(data) {
                        //     return data == '1' ?
                        //         '<i class="bi bi-check-circle-fill text-success" title="Terkirim"></i>' :
                        //         '<i class="bi bi-x-circle-fill text-danger" title="Gagal/Belum"></i>';
                        // }
                    },
                    {
                        data: 'id_ly_header',
                        className: 'text-center',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let data2 = row.kode_layanan_header;
                            let data3 = row.nama_pasien;
                            let data4 = row.keterangan_resep;
                            return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary" onclick="lihatDetail('${data}','${data2}','${data3}','${data4}')" title="Detail">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="batalLayanan('${data}','${data2}','${data3}','${data4}')" title="Batal">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                        }
                    }
                ],
                language: {
                    processing: '<div class="spinner-border text-primary" role="status"></div>',
                    search: "_INPUT_",
                    searchPlaceholder: "Cari Pasien/RM..."
                }
            });
        }

        function lihatDetail(id_header, kode_layanan_header, nama_pasien, keterangan) {
            $.ajax({
                url: "{{ route('get.detail.pelayanan') }}", // Pastikan route ini ada
                type: "GET",
                data: {
                    id_header: id_header,
                    kode_layanan_header: kode_layanan_header,
                    nama_pasien: nama_pasien,
                    keterangan: keterangan,
                },
                beforeSend: function() {
                    $('#isi_rincian_pelayanan').html(
                        '<tr><td colspan="7" class="text-center">Memuat rincian...</td></tr>');
                    $('#modalDetailLayanan').modal('show');
                },
                success: function(response) {
                    // Update Header Info
                    $('#det_kode_header').text(kode_layanan_header);
                    $('#det_nama_pasien').text(nama_pasien);
                    $('#det_keterangan').text(keterangan);
                    let html = '';
                    let grandTotal = 0;
                    let sep_asal_val = '-';
                    let keterangan_b_val = '-';
                    let sep_apotek_val = '-';
                    if (response.data.length > 0) {
                        $.each(response.data, function(i, item) {
                            let subtotal = item.total_layanan || 0;
                            grandTotal += parseFloat(subtotal);
                            if (item.sepasal) {
                                sep_asal_val = item.sepasal;
                            }
                            if (item.status_terkirim) {
                                keterangan_b_val = item.status_terkirim;
                            }
                            if (item.noApotik) {
                                sep_apotek_val = item.noApotik;
                            }
                            html += `<tr>
                        <td class="text-center">${i + 1}</td>
                        <td>
                            <div class="fw-bold">${item.nama_barang || item.kode_barang}</div>
                            <small class="text-muted">${item.kategori_resep || '-'}</small>
                        </td>
                        <td class="text-center">${item.jumlah_layanan}</td>
                        <td>${item.satuan_barang}</td>
                        <td class="text-end text-muted">${new Intl.NumberFormat('id-ID').format(item.total_tarif)}</td>
                        <td class="text-end fw-bold">${new Intl.NumberFormat('id-ID').format(subtotal)}</td>
                        <td><span class="badge bg-light text-dark border">${item.aturan_pakai || '-'}</span></td>
                    </tr>`;
                        });
                    } else {
                        html =
                            '<tr><td colspan="7" class="text-center text-danger">Tidak ada data rincian.</td></tr>';
                    }

                    $('#isi_rincian_pelayanan').html(html);
                    $('#det_total_seluruh').text(new Intl.NumberFormat('id-ID').format(grandTotal));
                    $('#ket_bridging').text(keterangan_b_val);
                    $('#ket_sep_asal').text(sep_asal_val);
                    $('#ket_sep_apotik').text(sep_apotek_val);
                },
                error: function() {
                    alert('Gagal mengambil detail pelayanan.');
                }
            });
        }

        function batalLayanan(id_header, kode_layanan_header, nama_pasien, keterangan) {
            Swal.fire({
                title: 'Batal Layanan?',
                text: "Layanan " + kode_layanan_header + " akan dibatalkan dan stok obat akan dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Batalkan!',
                cancelButtonText: 'Tidak'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('layanan.batal') }}", // Ganti dengan route Anda
                        type: "POST",
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: id_header,
                            kode_layanan_header: kode_layanan_header
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire('Berhasil!', response.message, 'success');
                                $('#tabel_kunjungan').DataTable().ajax.reload(); // Reload tabel
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                        }
                    });
                }
            });
        }
    </script>
@endsection
