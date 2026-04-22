<div class="p-4 border-bottom bg-light">
    <div class="row g-3">
        <div class="col-md-6 border-end pe-4">
            <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem;">Informasi Purchase
                Order</small>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">No. PO</div>
                <div class="col-sm-8 fw-bold text-primary fs-6">{{ $header->kode_po }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">Tanggal Input</div>
                <div class="col-sm-8 fw-medium">
                    {{ \Carbon\Carbon::parse($header->tgl_input)->format('d F Y') }}
                    <small class="text-muted">({{ \Carbon\Carbon::parse($header->tgl_input)->format('H:i') }}
                        WIB)</small>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">Status</div>
                <div class="col-sm-8">
                    @if ($header->status_po == 'CLS')
                        <span class="badge rounded-pill bg-success small">Aktif (Open)</span>
                    @elseif($header->status_po == 2)
                        <span class="badge rounded-pill bg-secondary small">Selesai (Closed)</span>
                    @else
                        <span class="badge rounded-pill bg-danger small">Batal (Canceled)</span>
                    @endif
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">Jatuh tempo</div>
                <div class="col-sm-8 fw-medium">{{ $header->tgl_jatuh_tempo ? : '-' }} hari</div>
            </div>
            <div class="row">
                <div class="col-sm-4 text-muted small">User Input</div>
                <div class="col-sm-8 text-muted small">{{ $header->nama_user_input ?: $header->user_input }}</div>
            </div>
        </div>

        <div class="col-md-6 ps-4">
            <small class="text-muted d-block text-uppercase fw-bold mb-2" style="font-size: 0.75rem;">Informasi
                Supplier</small>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">Nama Supplier</div>
                <div class="col-sm-8 fw-bold text-dark fs-6">{{ $header->nama_supplier }} <small
                        class="text-muted">({{ $header->kode_supplier }})</small></div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">Alamat</div>
                <div class="col-sm-8 fw-medium small text-secondary">{{ $header->alamat_supplier ?: '-' }}</div>
            </div>
            <div class="row mb-2">
                <div class="col-sm-4 text-muted small">Kontak</div>
                <div class="col-sm-8 fw-medium">{{ $header->telepon_supplier ?: '-' }}</div>
            </div>
            <div class="row">
                <div class="col-sm-4 text-muted small">Email</div>
                <div class="col-sm-8 fw-medium text-vla small">{{ $header->email_supplier ?: '-' }}</div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive p-3">
    <table class="table table-sm table-bordered table-hover align-middle w-100 fs-7 shadow-sm border">
        <thead class="table-vla text-center text-uppercase fs-8 text-white fw-bold">
            <tr>
                <th width="3%">No</th>
                <th>Nama Barang</th>
                <th width="10%">Qty</th>
                <th width="12%">Satuan Besar</th>
                <th width="12%">Isi</th>
                {{-- <th width="10%">Total</th> --}}
                <th width="12%">Harga Beli</th>
                <th width="10%">Disc (%)</th>
                <th width="15%">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($details as $key => $d)
                <tr>
                    <td class="text-center text-muted">{{ $key + 1 }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $d->nama_barang }}</div>
                        {{-- <div class="text-uppercase small text-secondary">{{ $d->nama_dagang ?: '-' }}</div> --}}
                        <small class="text-muted font-monospace">{{ $d->kode_barang }}</small>
                    </td>
                    <td class="text-center fw-bold fs-6 text-primary">{{ number_format($d->qty, 0, ',', '.') }}</td>
                    <td class="text-center">
                        <span
                            class="badge bg-light text-dark border font-monospace">{{ $d->nama_satuan_besar ?: $d->satuan_besar }}</span>
                    </td>
                    <td class="text-center text-muted small font-monospace">
                        {{ $d->isi ?: 1 }}
                    </td>
                    {{-- <td class="text-center text-muted small font-monospace">
                        {{ $d->isi ?: 1 }} / {{ $d->qty_kecil ?: 1 }}
                        <small class="d-block text-lowercase">({{ $d->nama_satuan_sedang ?: $d->satuan_sedang }} /
                            {{ $d->nama_satuan_kecil ?: $d->satuan_kecil }})</small>
                    </td> --}}
                    <td class="text-end fw-medium">Rp {{ number_format($d->hrg_satuan, 0, ',', '.') }}</td>
                    <td class="text-center text-danger fw-bold">{{ $d->diskon ?: 0 }} %</td>
                    <td class="text-end fw-bold fs-6 text-success bg-light">Rp
                        {{ number_format($d->sub_total, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted small">
                        <i class="bi bi-info-circle me-1"></i> Tidak ada item barang dalam Purchase Order ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
        <tfoot class="table-light fw-bold fs-7">
            @php 
                $total = $header->total_po * $header->ppn;
                $pajak = $total / 100;
            @endphp
            <tr>
                <td colspan="2" class="text-end border-0">TOTAL ITEM / QTY:</td>
                <td class="text-center text-primary fs-6 border-0">{{ number_format($total_qty, 0, ',', '.') }}</td>
                <td colspan="3" class="text-end border-0 fs-6">Sub Total:</td>
                <td colspan="2" class="text-end text-success fs-5 bg-white border border-success">
                    Rp {{ number_format($header->total_po, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-end border-0"></td>
                <td class="text-center text-primary fs-6 border-0"></td>
                <td colspan="3" class="text-end border-0 fs-6">Materai:</td>
                <td colspan="2" class="text-end text-success fs-5 bg-white border border-success">
                    Rp {{ number_format($header->materai, 0, ',', '.') }}
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-end border-0"></td>
                <td class="text-center text-primary fs-6 border-0"></td>
                <td colspan="3" class="text-end border-0 fs-6">PPN ( {{ $header->ppn }} %) : </td>
                <td colspan="2" class="text-end text-success fs-5 bg-white border border-success">
                    Rp  {{ number_format($pajak, 0, ',', '.') }} 
                </td>
            </tr>
            <tr>
                <td colspan="2" class="text-end border-0"></td>
                <td class="text-center text-primary fs-6 border-0"></td>
                <td colspan="3" class="text-end border-0 fs-6">GRAND TOTAL PO:</td>
                <td colspan="2" class="text-end text-success fs-5 bg-white border border-success">
                    Rp {{ number_format($header->gtotal_po, 0, ',', '.') }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- @if ($header->catatan) --}}
{{-- <div class="p-3 border-top bg-white">
    <small class="text-muted d-block text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Catatan / Keterangan
        PO</small>
    <p class="mb-0 fw-medium text-secondary small p-2 bg-light rounded border"></p>
</div> --}}
{{-- @endif --}}

<style>
    .fs-7 {
        font-size: 0.9rem !important;
    }

    .fs-8 {
        font-size: 0.8rem !important;
    }

    .table-vla {
        background-color: #6f42c1;
    }

    /* Warna tema KynovaPharma */
    #tbDetailPO thead th {
        vertical-align: middle;
    }
</style>
