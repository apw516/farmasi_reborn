<form class="form_isi_mutasi">
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Nama Barang</label>
        <input readonly class="form-control" id="nama_barang" name="nama_barang" aria-describedby="emailHelp"
            value="{{ $barang->nama_barang }}">
        <input hidden readonly class="form-control" id="kode_barang" name="kode_barang" aria-describedby="emailHelp"
            value="{{ $barang->kode_barang }}">
        <input hidden readonly class="form-control" id="idsediaan" name="idsediaan" aria-describedby="emailHelp"
            value="{{ $data->id }}">
    </div>
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Nomor Batch</label>
        <input readonly class="form-control" id="nomor_batch" name="nomor_batch" aria-describedby="emailHelp"
            value="{{ $data->nomor_batch }}">
    </div>
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Expired Date</label>
        <input readonly class="form-control" id="ed" name="ed" aria-describedby="emailHelp"
            value="{{ $data->ED }}">
    </div>
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Stok Tersedia</label>
        <input readonly type="email" class="form-control" id="stoktersedian" name="stoktersedian" aria-describedby="emailHelp" value="{{ $data->stok_sekarang }}">
    </div>
    <div class="mb-3">
        <label for="exampleInputEmail1" class="form-label">Jumlah Mutasi</label>
        <input type="email" class="form-control" id="jumlahmutasi" name="jumlahmutasi" aria-describedby="emailHelp" value="0">
    </div>
    <div class="mb-3">
        <label for="exampleInputPassword1" class="form-label">Tujuan</label>
        <select class="form-select" aria-label="Default select example" id="unittujuan" name="unittujuan">
            <option selected>Silahkan Pilih</option>
            @foreach($unit as $u)
            <option value="{{ $u->kode_unit}}">{{ $u->nama_unit}}</option>
            @endforeach
        </select>
    </div>
</form>
