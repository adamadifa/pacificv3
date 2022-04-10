<form action="/barangpembelian/store" method="post" id="frmInputbarangpembelian">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Kode Barang" field="kode_barang" icon="feather icon-credit-card" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Nama Barang" field="nama_barang" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Satuan" field="satuan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="jenis_barang" name="jenis_barang">
                    <option value="">Pilih Jenis Barang</option>
                    <option value="BAHAN BAKU">BAHAN BAKU</option>
                    {{-- <option {{ Request('jenis_barang') == 'BAHAN PEMBANTU' ? 'selected' : '' }} value="BAHAN PEMBANTU">BAHAN PEMBANTU</option> --}}
                    <option value="KEMASAN">KEMASAN</option>
                    <option value="Bahan Tambahan">BAHAN TAMBAHAN</option>
                    <option value="LAINNYA">LAINNYA</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="kode_kategori" name="kode_kategori">
                    <option value="">Pilih Kategori</option>
                    @foreach ($kategori_barang_pembelian as $d)
                    <option value="{{ $d->kode_kategori }}">{{ strtoupper($d->kategori) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="kode_dept" name="kode_dept">
                    <option value="">Pilih Departemen</option>
                    @foreach ($departemen as $d)
                    <option value="{{ $d->kode_dept }}">{{ strtoupper($d->nama_dept) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    {{-- <div class="row">
        <div class="col-12">
            <div class="form-group">
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-success">
                                <input type="radio" name="status" value="Aktif" checked>
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Aktif</span>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2">
                        <fieldset>
                            <div class="vs-radio-con vs-radio-danger">
                                <input type="radio" name="status" value="Non Aktif">
                                <span class="vs-radio">
                                    <span class="vs-radio--border"></span>
                                    <span class="vs-radio--circle"></span>
                                </span>
                                <span class="">Non Aktif</span>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>
    </div> --}}
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block">Submit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#frmInputbarangpembelian").submit(function() {
            var kode_barang = $("#kode_barang").val();
            var nama_barang = $("#frmInputbarangpembelian").find("#nama_barang").val();
            var satuan = $("#satuan").val();
            var jenis_barang = $("#frmInputbarangpembelian").find("#jenis_barang").val();
            var kode_kategori = $("#frmInputbarangpembelian").find("#kode_kategori").val();
            var kode_dpet = $("#frmInputbarangpembelian").find("#kode_dept").val();

            if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Barang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_barang").focus();
                });
                return false;
            } else if (nama_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Nama Barang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmInputbarangpembelian").find("#nama_barang").focus();
                });
                return false;
            } else if (satuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Satuan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#satuan").focus();
                });
                return false;
            } else if (jenis_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jenis Barang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    ("#frmInputbarangpembelian").find("#jenis_barang").focus();
                });
                return false;
            } else if (kode_kategori == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kategori Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    ("#frmInputbarangpembelian").find("#kode_kategori").focus();
                });
                return false;
            } else if (kode_dpet == "") {
                swal({
                    title: 'Oops'
                    , text: 'Departemen Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    ("#frmInputbarangpembelian").find("#kode_dept").focus();
                });
                return false;
            }
        });
    });

</script>
