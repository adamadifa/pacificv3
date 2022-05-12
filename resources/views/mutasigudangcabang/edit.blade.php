<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }

</style>
<form action="/mutasigudangcabang/{{ Crypt::encrypt($mutasi->no_mutasi_gudang_cabang) }}/update" method="post" id="frmDpb">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>No. Mutasi</td>
                    <td>{{ $mutasi->no_mutasi_gudang_cabang }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>{{ DateToIndo2($mutasi->tgl_mutasi_gudang_cabang) }}</td>
                </tr>
            </table>
            <table class="table">
                <tr>
                    <th>No. DPB</th>
                    <th>Nama Salesman</th>
                    <th>Tujuan</th>
                    <th>No.Kendaraan</th>
                </tr>
                <tr>
                    <td>{{ $mutasi->no_dpb }}</td>
                    <td>{{ $mutasi->nama_karyawan }}</td>
                    <td>{{ $mutasi->tujuan }}</td>
                    <td>{{ $mutasi->no_kendaraan }}</td>
                </tr>
                <tr>
                    <th colspan="2">Driver</th>
                    <th colspan="2">Helper</th>
                </tr>
                <tr>
                    <td colspan="2">{{ $mutasi->nama_driver; }}</td>
                    <td colspan="2">
                        {{ !empty($mutasi->nama_helper_1) ? '(1) '.$mutasi->nama_helper_1 : '' }}
                        {{ !empty($mutasi->nama_helper_2) ? '(2) '.$mutasi->nama_helper_2 : '' }}
                        {{ !empty($mutasi->nama_helper_3) ? '(3) '.$mutasi->nama_helper_3 : '' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_mutasi_gudang_cabang" label="Tanggal {{ ucwords(strtolower($mutasi->jenis_mutasi))}}" value="{{ $mutasi->tgl_mutasi_gudang_cabang }}" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="3" align="">Kode</th>
                        <th rowspan="3" style="text-align:center">Nama Barang</th>
                        <th colspan="6" style="text-align:center">{{ ucwords(strtolower($mutasi->jenis_mutasi)) }}</th>
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
                    @foreach ($detail as $d)
                    @php
                    $jumlah = $d->jumlah / $d->isipcsdus;
                    $jmldus = floor($d->jumlah / $d->isipcsdus);
                    if ($d->jumlah != 0) {
                    $sisadus = $d->jumlah % $d->isipcsdus;
                    } else {
                    $sisadus = 0;
                    }
                    if ($d->isipack == 0) {
                    $jmlpack = 0;
                    $sisapack = $sisadus;
                    $s = "A";
                    } else {
                    $jmlpack = floor($sisadus / $d->isipcs);
                    $sisapack = $sisadus % $d->isipcs;
                    $s = "B";
                    }
                    $jmlpcs = $sisapack;
                    @endphp
                    <input type="hidden" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
                    <input type="hidden" name="isipcs[]" value="{{ $d->isipcs }}">
                    <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
                    <tr>
                        <td>{{ $d->kode_produk }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td style="width: 12%">
                            <input type="text" value="{{ !empty($jmldus) ? $jmldus : '' }}" class="form-control" name="jmldus[]">
                        </td>
                        <td>{{ $d->satuan }}</td>
                        <td style="width:12%">
                            <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" value="{{ !empty($jmlpack) ? $jmlpack : '' }}" class="form-control" name="jmlpack[]">
                        </td>
                        <td>PACK</td>
                        <td style="width: 12%">
                            <input type="text" class="form-control" name="jmlpcs[]" value="{{ !empty($jmlpcs) ? $jmlpcs : '' }}">
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
                <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {



        $("#frmDpb").submit(function() {
            var tgl_mutasi_gudang_cabang = $("#frmDpb").find("#tgl_mutasi_gudang_cabang").val();
            if (tgl_mutasi_gudang_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#tgl_mutasi_gudang_cabang").focus();
                });
                return false;
            }
        });
    });

</script>
