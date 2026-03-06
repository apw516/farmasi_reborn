    <div class="row">
        <div hidden class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Kode
                    Barang</label>
                <input readonly type="text" value="{{ $dataarray['kode_barang'] }}" class="form-control form-control-sm"
                    id="list_kodebarang" name="list_kodebarang" placeholder="kode barang ...">
            </div>
        </div>
        <div class="col-md-3">
            <div class="mb-3">
                <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
                <input readonly type="text" class="form-control form-control-sm" id="list_nama_barang"
                    name="list_nama_barang" placeholder="qty barang ..." value="{{ $dataarray['nama_barang'] }}">
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">QTY</label>
                <input readonly type="text" class="form-control form-control-sm" id="list_qty" name="list_qty"
                    placeholder="qty barang ..." value="{{ $dataarray['qty'] }}">
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Satuan</label>
                <select class="form-select form-select-sm" aria-label="Default select example" id="list_satuan"
                    name="list_satuan">
                    @foreach ($satuana as $s)
                        <option value="{{ $s->kode_satuan }}" @if ($dataarray['satuan'] == $s->kode_satuan) selected @endif>
                            {{ $s->nama_satuan }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Hrg
                    Satuan</label>
                <input readonly type="text" class="form-control form-control-sm" id="list_hrgasatuan"
                    name="list_hrgasatuan" placeholder="harga satuan ..." value="{{ $dataarray['hrgasatuan'] }}">
                <input hidden type="email" class="form-control form-control-sm" id="list_hrgasatuanasli"
                    name="list_hrgasatuanasli" placeholder="harga satuan ..." value="{{ $dataarray['hrgasatuanasli']}}">
                <label hidden for="exampleFormControlInput1" id="labelasli" class="form-label">Diskon</label>
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Diskon</label>
                <input readonly type="text" class="form-control form-control-sm" id="list_diskon" name="list_diskon"
                    placeholder="diskon ..." value="{{ $dataarray['diskon'] }}">
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">No. Batch</label>
                <input readonly type="text" class="form-control form-control-sm" id="list_nobatch"
                    name="list_nobatch" placeholder="no batch ..." value="{{ $dataarray['nobatch'] }}">
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Expired
                    date</label>
                <input readonly type="date" class="form-control form-control-sm" id="list_ed" name="list_ed"
                    placeholder="name@example.com" value="{{ $dataarray['ed'] }}">
            </div>
        </div>
        <div class="col-md-2">
            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Sub Total</label>
                <input readonly type="text" class="form-control form-control-sm" id="list_subtotal_mask"
                    name="list_subtotal_mask" placeholder="qty barang ..." value="{{ $dataarray['subtotal_format'] }}">
                <input hidden readonly type="text" class="form-control" id="list_subtotal_asli"
                    name="list_subtotal_asli" placeholder="qty barang ..." value="{{ $dataarray['subtotal'] }}">
            </div>
        </div>
        <div class="col-md-1">
            <div class="mb-3">
                <button type="button" class="btn btn-danger remove_field" style="margin-top:33px"><i
                        class="bi bi-x-circle"></i></button>
            </div>
        </div>
    </div>
