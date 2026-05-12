<div class="row row-retur-item border-bottom pb-2 mb-2">
     <div class="col-md-2">
        <label class="small fw-bold">Nomor PO</label>
        <input type="text" name="nomor_po[]" class="form-control form-control-sm"  value=" {{ $kode_po }}">
    </div>
     <div class="col-md-2">
        <label class="small fw-bold">Nomor Faktur</label>
        <input type="text" name="nomor_faktur[]" class="form-control form-control-sm"  value=" {{ $nomor_faktur }}">
    </div>
    <div class="col-md-2">
        <label class="small fw-bold">Nama Barang</label>
        <input type="text" class="form-control form-control-sm" value="{{ $nama_barang}}" readonly>
        <input type="hidden" name="kode_barang[]" value="{{ $kode_barang}}">
        <input type="hidden" name="id_detail[]" value="{{ $id_detail}}">
    </div>
    <div class="col-md-1">
        <label class="small fw-bold">Batch</label>
        <input type="text" name="batch[]" class="form-control form-control-sm" value="{{ $batch}}" readonly>
    </div>
    <div class="col-md-2">
        <label class="small fw-bold">ED</label>
        <input type="date" class="form-control form-control-sm" value="{{ $ed }}">
    </div>
    <div class="col-md-1">
        <label class="small fw-bold">Stok Tersedia</label>
        <input type="text" class="form-control form-control-sm text-danger fw-bold" value="{{ $sediaan}}"
            readonly>
    </div>
    <div class="col-md-1">
        <label class="small fw-bold">Qty Retur</label>
        <input type="number" name="qty_retur[]" class="form-control form-control-sm" max=""
            min="1" required value="0">
    </div>
    <div class="col-md-1">
        <label class="d-block">&nbsp;</label>
        <button type="button" class="btn btn-sm btn-danger btn-remove-item">
            <i class="bi bi-x-square"></i>
        </button>
    </div>
</div>
