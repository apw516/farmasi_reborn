{{-- <table class="table table-sm table-bordered table-hover">
    <thead>
        <th>Tanggal Input</th>
        <th>Tanggal Beli</th>
        <th>Tanggal Terima</th>
        <th>Nomor Faktur</th>
        <th>Nama Supplier</th>
        <th>Total PO</th>
        <th>PPn</th>
        <th>Grandtotal</th>
        <th>Total Hutang</th>
        <th>Status Pembayaran</th>
        <th>Status Tagihan</th>
        <th>Status Retur</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($data_header as $d)
            <tr>
                <td> {{ \Carbon\Carbon::parse($d->tgl_input)->locale('id')->translatedFormat('d F Y') }}</td>
                <td> {{ \Carbon\Carbon::parse($d->tgl_beli)->locale('id')->translatedFormat('d F Y') }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tgl_terima)->locale('id')->translatedFormat('d F Y') }}</td>
                <td>{{ $d->no_faktur }}</td>
                <td>{{ $d->nama_supplier }}</td>
                <td> {{ number_format($d->total_po, 0, ',', '.') }}</td>
                <td> {{ number_format($d->ppn, 0, ',', '.') }}</td>
                <td>{{ number_format($d->gtotal_po, 0, ',', '.') }}</td>
                <td>{{ number_format($d->total_utang, 0, ',', '.') }}</td>
                <td>{{ $d->status_pembayaran }}</td>
                <td>{{ $d->status_tagihan }}</td>
                <td>{{ $d->status_retur }}</td>
                <td>
                    <button class="btn btn-success btn-sm pilihpoheader" idheader="{{ $d->id }}"
                        data-bs-toggle="tooltip" data-bs-title="Input detail PO ..."><i
                            class="bi bi-box-arrow-in-right"></i></button>
                    <button class="btn btn-info btn-sm" data-bs-toggle="tooltip" data-bs-title="lihat detail PO ..."><i
                            class="bi bi-info-square"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table> --}}


@extends('layouts.app') @section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 fs-3">Daftar Purchase Order (PO) Header</h1>
    <ol class="breadcrumb mb-4 small text-muted">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Logistik & Inventori</li>
        <li class="breadcrumb-item active fw-bold text-dark">PO Header</li>
    </ol>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-0 pt-3 pb-0">
            <h6 class="card-title text-uppercase text-muted fw-bold mb-0">Filter Periode & Aksi</h6>
        </div>
        <div class="card-body">
            <form id="formFilterPO" class="row g-3 align-items-end">
                <div class="col-md-3 col-lg-2">
                    <label for="tanggalawal" class="form-label small fw-bold text-muted">Tanggal Input (Awal)</label>
                    <input type="date" class="form-control form-control-sm" id="tanggalawal" name="tanggalawal" value="{{ $tanggal_awal }}">
                </div>
                <div class="col-md-3 col-lg-2">
                    <label for="tanggalakhir" class="form-label small fw-bold text-muted">Tanggal Input (Akhir)</label>
                    <input type="date" class="form-control form-control-sm" id="tanggalakhir" name="tanggalakhir" value="{{ $tanggal_akhir }}">
                </div>
                <div class="col-md-2 col-lg-1">
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary btn-sm px-3" id="btnProsesFilter">
                            <i class="bi bi-funnel me-1"></i> Proses
                        </button>
                    </div>
                </div>
                <div class="col-md-4 col-lg-7 text-end">
                    <a href="{{ route('purchase-order.create') }}" class="btn btn-success btn-sm px-3">
                        <i class="bi bi-plus-circle me-1"></i> Buat PO Baru (Header & Detail)
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="text-center py-5" id="loaderDataTable" style="display:none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="text-muted mt-2">Mengambil data PO...</div>
            </div>

            <div class="table-responsive p-3" id="wrapperDataTable">
                <table id="tbHeaderPO" class="table table-sm table-bordered table-hover align-middle w-100 fs-7">
                    <thead class="table-light text-center text-uppercase fs-8 text-muted fw-bold">
                        <tr>
                            <th width="3%">No</th>
                            <th width="12%">Tgl. Input</th>
                            <th width="10%">No. PO</th>
                            <th>Supplier</th>
                            <th width="8%">Term</th>
                            <th width="8%">Status</th>
                            <th width="12%">Grand Total</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalDetailPO" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-vla text-white">
                <h5 class="modal-title fs-6"><i class="bi bi-search me-2"></i>Detail Purchase Order (PO)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div id="v_detail_po"></div> </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
<style>
    /* Styling Custom untuk KynovaPharma */
    .fs-7 { font-size: 0.9rem !important; }
    .fs-8 { font-size: 0.8rem !important; }
    #tbHeaderPO thead th {
        vertical-align: middle;
        letter-spacing: 0.5px;
    }
    #tbHeaderPO tbody td {
        padding: 8px;
    }
    div.dataTables_wrapper div.dataTables_processing {
        top: 60px !important;
        background: rgba(255, 255, 255, 0.9) !important;
        box-shadow: none !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
    $(document).ready(function() {
        // --- 1. Konfigurasi DataTable Server-side KynovaPharma ---
        var table = $('#tbHeaderPO').DataTable({
            processing: true, // Menampilkan spinner loader bawaan
            serverSide: true, // Mengaktifkan mode Server-side
            responsive: true, // Responsif di layar HP/Tablet
            autoWidth: false,
            pageLength: 25, // Default jumlah baris per halaman
            order: [[2, 'desc']], // Urutan default berdasarkan No. PO (Kolom 2) secara Descending
            
            // Konfigurasi request AJAX Serverside
            ajax: {
                url: "{{ route('logistik.po_header.data') }}", // Route JSON Server-side di Controller
                type: 'POST',
                data: function(d) {
                    // Kirim parameter filter tanggal ke Controller
                    d._token = "{{ csrf_token() }}";
                    d.tanggalawal = $('#tanggalawal').val();
                    d.tanggalakhir = $('#tanggalakhir').val();
                },
                beforeSend: function() {
                    $('#wrapperDataTable').hide(); // Sembunyikan tabel dulu
                    $('#loaderDataTable').show(); // Tampilkan loader custom
                },
                complete: function() {
                    $('#loaderDataTable').hide(); // Sembunyikan loader
                    $('#wrapperDataTable').show(); // Tampilkan tabel kembali
                },
                error: function(xhr, error, thrown) {
                    $('#loaderDataTable').hide();
                    Swal.fire('Error DataTable', 'Gagal mengambil data PO Header. ' + thrown, 'error');
                }
            },
            
            // Definisi Kolom sesuai Response JSON dari Controller (Yajra)
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'tgl_input', name: 'tgl_input', className: 'text-center text-nowrap' },
                { data: 'no_po', name: 'no_po', className: 'text-center fw-bold text-primary' },
                { data: 'nama_supplier', name: 'nama_supplier' }, // Menampilkan Nama & Kode Supplier (Formatting via Controller)
                { data: 'term_payment', name: 'term_payment', className: 'text-center' },
                { 
                    data: 'status_po', 
                    name: 'status_po', 
                    className: 'text-center',
                    render: function(data, type, row) {
                        // Formatting visual status_po (Jika di Controller belum di-format)
                        if (data == 1) return '<span class="badge rounded-pill bg-success small">Aktif</span>';
                        if (data == 2) return '<span class="badge rounded-pill bg-secondary small">Selesai</span>';
                        return '<span class="badge rounded-pill bg-danger small">Batal</span>';
                    }
                },
                { data: 'grand_total', name: 'grand_total' }, // Menampilkan Rp Grand Total (Formatting via Controller)
                { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: 'text-center bg-light' }
            ],
            
            // Konfigurasi Bahasa/UI DataTables
            language: {
                processing: '<div class="spinner-border text-primary" role="status"></div>',
                lengthMenu: "Tampilkan _MENU_ data per halaman",
                zeroRecords: "Tidak ada data PO Header yang ditemukan pada periode ini.",
                info: "Menampilkan halaman _PAGE_ dari _PAGES_",
                infoEmpty: "Tidak ada data tersedia",
                infoFiltered: "(disaring dari _MAX_ total data)",
                search: "Cari berdasarkan No. PO/Supplier/User:",
                paginate: {
                    first: "Awal",
                    last: "Akhir",
                    next: "Lanjut",
                    previous: "Mundur"
                }
            }
        });

        // --- 2. Logic Tombol Proses Filter Tanggal ---
        $('#btnProsesFilter').on('click', function() {
            // Cek validasi tanggal
            let tglAwal = $('#tanggalawal').val();
            let tglAkhir = $('#tanggalakhir').val();

            if (!tglAwal || !tglAkhir) {
                Swal.fire('Filter Gagal', 'Silahkan pilih Tanggal Input (Awal & Akhir) terlebih dahulu.', 'warning');
                return;
            }

            if (tglAwal > tglAkhir) {
                Swal.fire('Filter Gagal', 'Tanggal Input (Awal) tidak boleh lebih besar dari Tanggal Input (Akhir).', 'warning');
                return;
            }

            // Reload DataTable dengan parameter tanggal yang baru
            table.ajax.reload(); // ajax.reload() akan memanggil ulang fungsi data() di atas
        });
    });

    // --- 3. Contoh Fungsi JavaScript Aksi (View Detail PO) ---
    function viewDetailPO(noPO) {
        // Tampilkan Modal
        $('#modalDetailPO').modal('show');
        $('#v_detail_po').html(''); // Kosongkan konten lama
        
        // Loader AJAX
        let loader = '<div class="text-center py-5"><div class="spinner-border text-vla" role="status"></div><div class="text-muted mt-2 small">Mengambil detail PO...</div></div>';
        $('#v_detail_po').html(loader);

        // Ambil data detail via AJAX
        $.ajax({
            type: 'POST',
            url: '{{ route('logistik.purchase-order.ambil_detail_po') }}', // Buat route & controller ini
            data: {
                _token: "{{ csrf_token() }}",
                no_po: noPO
            },
            error: function() {
                Swal.fire('Error', 'Gagal mengambil detail Purchase Order.', 'error');
                $('#modalDetailPO').modal('hide');
            },
            success: function(response) {
                // Tampilkan respon HTML (tabel detail, form header, dll) ke dalam modal
                $('#v_detail_po').html(response);
            }
        });
    }

    // --- 4. Fungsi Hapus PO ---
    function hapusPO(noPO) {
        Swal.fire({
            title: 'Hapus Purchase Order?',
            text: "Apakah Anda yakin ingin menghapus PO dengan nomor: " + noPO + "? Tindakan ini tidak dapat dibatalkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // AJAX Hapus
                $.ajax({
                    type: 'POST',
                    url: '{{ route('logistik.purchase-order.hapus') }}', // Buat route & controller ini
                    data: {
                        _token: "{{ csrf_token() }}",
                        no_po: noPO
                    },
                    error: function() {
                        Swal.fire('Error', 'Gagal menghapus Purchase Order.', 'error');
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            Swal.fire('Terhapus!', response.message, 'success');
                            // Reload DataTable tanpa reset paging
                            $('#tbHeaderPO').DataTable().ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    }
                });
            }
        });
    }
</script>
@endpush