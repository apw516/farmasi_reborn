    <form action="{{ route('updatemasterbarang') }}" method="POST" id="formEditBarang">
        @csrf
        <div class="row">
            <div class="col-md-12 form-group">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" class="form-control" value="{{ $barang->kode_barang }}" readonly>
            </div>
            <div class="col-md-12 form-group mt-3">
                <label>Tipe Barang</label>
                <select class="form-select" aria-label="Default select example" name="kode_tipe_barang">
                    @foreach ($tipebarang as $t)
                        <option value="{{ $t->kode_tipe }}" @if ($t->kode_tipe == $barang->kode_tipe) selected @endif>
                            {{ $t->nama_tipe }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" value="{{ $barang->nama_barang }}">
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Satuan Besar</label>
                <select class="form-select" aria-label="Default select example" name="satuan_besar">
                    @foreach ($satuan as $t)
                        <option value="{{ $t->kode_satuan }}" @if ($t->kode_satuan == $barang->satuan_besar) selected @endif>
                            {{ $t->nama_satuan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12 mt-3 form-group">
                <label>Satuan Kecil</label>
                <select class="form-select" aria-label="Default select example" name="satuan">
                    @foreach ($satuan as $t)
                        <option value="{{ $t->kode_satuan }}" @if ($t->kode_satuan == $barang->satuan) selected @endif>
                            {{ $t->nama_satuan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Isi</label>
                <input type="text" name="isi" class="form-control" value="{{ $barang->isi }}">
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Sediaan</label>
                <select class="form-select" aria-label="Default select example" name="sediaan">
                    @foreach ($sediaan as $t)
                        <option value="{{ $t->nama_sediaan }}" @if ($t->nama_sediaan == $barang->sediaan) selected @endif>
                            {{ $t->nama_sediaan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Pabrikan</label>
                <select class="form-select" aria-label="Default select example" name="id_pabrik">
                    <option value="0">Silahkan Pilih</option>
                    @foreach ($pabrikan as $t)
                        <option value="{{ $t->id }}" @if ($t->id == $barang->id_pabrik) selected @endif>
                            {{ $t->nama_pabrik }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Harga Jual</label>
                <input type="text" name="harga_jual_disp" id="harga_jual_disp" class="form-control"
                    value="{{ number_format($barang->harga_jual, 0, ',', '.') }}">
                <input hidden type="text" name="harga_jual" id="harga_jual" class="form-control"
                    value="{{ $barang->harga_jual }}">
                <label hidden id="labelasli">Harga Jual</label>
            </div>
            <div class="col-md-12 mt-3 form-group">
                <label>Dosis</label>
                <input type="text" name="dosis" class="form-control" value="{{ $barang->dosis }}">
            </div>
        </div>
        <div class="modal-footer px-0 pb-0">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btnUpdateBarang">Simpan Perubahan</button>
        </div>
    </form>
    <script>
     
        const inputMask = document.getElementById('harga_jual_disp');
        const inputAsli = document.getElementById('harga_jual');
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
