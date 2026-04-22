@extends('Template.Main')
@section('container')
    <div class="app-content-header">
        <!--begin::Container-->
        <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">Data Stok Barang</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Data Stok Barang</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Pilih Unit</label>
                        <select class="form-select form-select-lg mb-3" aria-label="Large select example" id="filter_unit">
                            <option value="">
                                SEMUA STOK</option>
                            @foreach ($mt_unit as $u)
                                <option value="{{ $u->kode_unit }}" @if ($u->kode_unit == auth()->user()->unit) selected @endif>
                                    {{ $u->nama_unit }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exampleInputPassword1" class="form-label">Pilih Tipe Barang</label>
                        <select class="form-select form-select-lg mb-3" aria-label="Large select example" id="filtertipe">
                            <option value="">
                                SEMUA TIPE</option>
                            @foreach ($tipe as $u)
                                <option value="{{ $u->kode_tipe }}">
                                    {{ $u->nama_tipe }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Data Kartu Stok</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered w-100" id="tabel_rekap_stok">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th>Nama Barang</th>
                                    <th width="15%" class="text-center">Total Stok</th>
                                    <th width="15%" class="text-center">Tanggal Stok</th>
                                    <th width="15%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalDetailBatch" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-box-seam me-2"></i> Detail Stok Per Batch
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Nama
                                Barang</small>
                            <span id="label_nama_barang" class="h5 fw-bold text-dark mb-0"></span>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block text-uppercase fw-bold" style="font-size: 10px;">Unit
                                Terpilih</small>
                            <span id="label_unit_terpilih" class="badge bg-dark"></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-secondary text-uppercase" style="font-size: 11px;">
                                <tr>
                                    <th class="text-center">No</th>
                                    <th>Informasi Dokumen</th>
                                    <th>Supplier</th>
                                    <th>Batch / ED</th>
                                    <th class="text-end">Stok</th>
                                    <th class="text-center">Jenis</th>
                                    <th class="text-center">Entry</th>
                                </tr>
                            </thead>
                            <tbody id="isi_detail_batch" style="font-size: 13px;">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            var table = $('#tabel_rekap_stok').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('gudang.stokdatabarang') }}",
                    data: function(d) {
                        d.kode_unit = $('#filter_unit').val(); // Ambil dari dropdown unit
                        d.kode_tipe = $('#filtertipe').val(); // Ambil dari dropdown unit
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        class: 'text-center'
                    },
                    {
                        data: 'nama_barang',
                        render: function(data, type, row) {
                            return `<div class="fw-bold text-uppercase">${data}</div>
                        <small class="text-muted">${row.kode_barang}</small>`;
                        }
                    },
                    {
                        data: 'total_stok_barang',
                        class: 'text-center',
                        render: function(data, type, row) {
                            return `${new Intl.NumberFormat('id-ID').format(data)} ${row.satuan}`;
                        }
                    },
                    {
                        data: 'tgl_entry_terakhir',
                        class: 'text-center',
                        render: function(data) {
                            return `<span class="small"><i class="bi bi-calendar3"></i> ${moment(data).format('DD MMM YYYY')}</span>`;
                        }
                    },
                    {
                        data: 'aksi',
                        name: 'aksi',
                        class: 'text-center',
                        orderable: false
                    }
                ],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari barang...",
                }
            });

            // Refresh tabel saat unit diganti
            $('#filter_unit').on('change', function() {
                table.ajax.reload();
            });
            $('#filtertipe').on('change', function() {
                table.ajax.reload();
            });
        });
        $(document).on("click", ".btn-detail-batch", function() {
            var kodebarang = $(this).data('kode');
            var namabarang = $(this).data('nama');
            var kodeunit = $('#filter_unit').val();
            var namaunit = $('#filter_unit option:selected').text();

            $('#label_nama_barang').text(namabarang);
            $('#label_unit_terpilih').text(kodeunit ? namaunit : 'SEMUA UNIT');

            $.ajax({
                url: "{{ route('gudang.detailbatch') }}",
                type: "GET",
                data: {
                    kode_barang: kodebarang,
                    kode_unit: kodeunit
                },
                beforeSend: function() {
                    $('#isi_detail_batch').html(
                        '<tr><td colspan="7" class="text-center py-5"><div class="spinner-border spinner-border-sm text-primary"></div> Memuat data...</td></tr>'
                        );
                    $('#modalDetailBatch').modal('show');
                },
                success: function(response) {
                    let html = '';
                    if (response.length > 0) {
                        $.each(response, function(i, item) {
                            // Logic Warna Expired
                            let diffDays = moment(item.ED).diff(moment(), 'days');
                            let ed_class = diffDays <= 90 ? 'text-danger fw-bold' : 'text-dark';
                            let ed_icon = diffDays <= 90 ?
                                '<i class="bi bi-exclamation-triangle-fill me-1"></i>' : '';

                            // Logic Badge Jenis Dokumen
                            let badge_jenis = item.jenis_dokumen == 'FAKTUR' ? 'bg-success' :
                                'bg-warning text-dark';

                            html += `
                    <tr>
                        <td class="text-center text-muted">${i + 1}</td>
                        <td>
                            <div class="fw-bold"><i class="bi bi-file-earmark-text me-1"></i>${item.nomor_faktur || '-'}</div>
                        </td>
                        <td>
                            <div class="small text-uppercase">${item.nama_supplier || '<span class="text-muted">Tanpa Supplier</span>'}</div>
                        </td>
                        <td>
                            <code class="text-primary fw-bold" style="font-size:14px">${item.nomor_batch}</code>
                            <div class="small ${ed_class}">${ed_icon}${moment(item.ED).format('DD/MM/YYYY')}</div>
                        </td>
                        <td class="text-end">
                            <span class="fs-6 fw-bold text-dark">${new Intl.NumberFormat('id-ID').format(item.stok_sekarang)}</span>
                            <small class="text-muted">${item.satuan}</small>
                        </td>
                        <td class="text-center">
                            <span class="badge ${badge_jenis}" style="font-size:10px">${item.jenis_dokumen}</span>
                        </td>
                        <td class="text-center">
                            <div class="small text-muted">${moment(item.tgl_entry).format('DD/MM/YY')}</div>
                            <div style="font-size:10px" class="text-muted">${moment(item.tgl_entry).format('HH:mm')}</div>
                        </td>
                    </tr>`;
                        });
                    } else {
                        html =
                            '<tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-info-circle me-1"></i> Tidak ada detail batch ditemukan.</td></tr>';
                    }
                    $('#isi_detail_batch').html(html);
                },
                error: function() {
                    $('#isi_detail_batch').html(
                        '<tr><td colspan="7" class="text-center py-5 text-danger font-weight-bold">Gagal mengambil data dari server.</td></tr>'
                        );
                }
            });
        });
    </script>
@endsection
