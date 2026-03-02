@foreach ($data as $row)
    <div class="row align-items-center mb-2">
        <div class="col-md-2">
            <label class="form-label font-weight-bold small">Nama Barang</label>
            <input readonly style="font-size:12px" type="text" class="form-control form-control-sm" name="namabarang"
                value="{{ $row->namaracikan }}">
            <input readonly hidden type="text" name="kode_barang" value="{{ $row->id }}">
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">Jenis</label>
            <select class="form-select form-select-sm" name="jenis_obat">
                <option value="Reguler">Reguler</option>
                <option value="Kronis">Kronis</option>
                <option value="Kemo">Kemo</option>
                <option value="PRB">PRB</option>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">Tipe</label>
            <input readonly type="text" class="form-control form-control-sm" name="tipe"
                value="RACIKAN" placeholder="0">
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">Stok</label>
            <input type="number" class="form-control form-control-sm" name="stok"
                value="0" placeholder="0">
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">Iterasi</label>
            <select class="form-select form-select-sm" name="iterasi_obat">
                <option value="0">Non iterasi</option>
                <option value="1">Iterasi</option>
            </select>
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">Qty</label>
            <input readonly type="number" class="form-control form-control-sm" name="qtybeli" value="{{ $row->qtyracikan }}" placeholder="0">
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">S1</label>
            <input type="number" class="form-control form-control-sm" name="signa1" value="0" placeholder="0">
        </div>
        <div class="col-md-1">
            <label class="form-label font-weight-bold small">S2</label>
            <input type="number" class="form-control form-control-sm" name="signa2" value="0" placeholder="0">
        </div>
        <div class="col-md-2">
            <label class="form-label font-weight-bold small">Aturan Pakai</label>
            <textarea class="form-control form-control-sm" name="aturan_pakai" rows="1"></textarea>
        </div>
        <div class="col-md-1 text-center">
            <label class="form-label font-weight-bold small">&nbsp;</label>
            <div>
                <i class="bi bi-x-square remove_field text-danger" style="cursor:pointer; font-size: 1.5rem;"></i>
            </div>
        </div>
    </div>
@endforeach
