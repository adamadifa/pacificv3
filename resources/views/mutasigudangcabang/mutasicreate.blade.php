<form action="/mutasigudangcabang/mutasistore" method="post" id="frmDpb">
    @csrf
    <input type="hidden" id="jenis_mutasi" name="jenis_mutasi" value="{{ strtoupper($jm) }}">
    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="form-group  ">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $c)
                        <option {{ Request('kode_cabang') == $c->kode_cabang ? 'selected' : '' }}
                            value="{{ $c->kode_cabang }}">{{ strtoupper($c->nama_cabang) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_mutasi_gudang_cabang" label="Tanggal {{ ucwords(strtolower($textjm)) }}"
                icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="3" align="">Kode</th>
                        <th rowspan="3" style="text-align:center">Nama Barang</th>
                        <th colspan="6" style="text-align:center">{{ ucwords(strtolower($textjm)) }}</th>
                    </tr>
                    <tr>
                        <th colspan="6" style="text-align:center;">Kuantitas</th>
                    </tr>
                    <tr>
                        <th style="text-align:center">Jumlah</th>
                        <th style="text-align:center">Satuan</th>
                        <th style="text-align:center">Jumlah</th>
                        <th style="text-align:center">Satuan</th>
                        <th style="text-align:center">Jumlah</th>
                        <th style="text-align:center">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($produk as $d)
                        <input type="hidden" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
                        <input type="hidden" name="isipcs[]" value="{{ $d->isipcs }}">
                        <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
                        <tr>
                            <td>{{ $d->kode_produk }}</td>
                            <td>{{ $d->nama_barang }}</td>
                            <td style="width: 12%">
                                <input type="text" class="form-control" name="jmldus[]">
                            </td>
                            <td>{{ $d->satuan }}</td>
                            <td style="width:12%">
                                <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" class="form-control"
                                    name="jmlpack[]">
                            </td>
                            <td>PACK</td>
                            <td style="width: 12%">
                                <input type="text" class="form-control" name="jmlpcs[]">
                            </td>
                            <td>PCS</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button type="submit" name="submit" class="btn btn-primary btn-block"><i
                        class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        $("#frmDpb").submit(function() {
            var kode_cabang = $("#frmDpb").find("#kode_cabang").val();
            var tgl_mutasi_gudang_cabang = $("#frmDpb").find("#tgl_mutasi_gudang_cabang").val();
            if (kode_cabang == "") {
                swal({
                    title: 'Oops',
                    text: 'Cabang Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#kode_cabang").focus();
                });
                return false;
            } else if (tgl_mutasi_gudang_cabang == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#tgl_mutasi_gudang_cabang").focus();
                });
                return false;
            }
        });
    });
</script>
