<form action="/fpb/store" method="post" id="frmFpb">
    @csrf
    <div class="row">
        <div class="col-4">
            <x-inputtext label="No. FPB" field="no_fpb" icon="feather icon-file" />
        </div>
        <div class="col-8">
            <x-inputtext label="Tgl Permintaan" field="tgl_permintaan" icon="feather icon-calendar" datepicker />
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $d)
                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <div class="form-group  ">
                <select name="id_karyawan" id="id_karyawan" class="form-control select2">
                    <option value="">Semua Salesman</option>
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12">
            <div class="form-group">
                <select name="no_polisi" id="no_polisi" class="form-control">
                    <option value="">Pilih Kendaraan</option>
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12">
            <div class="form-group">
                <select name="id_driver" id="id_driver" class="form-control select2">
                    <option value="">Pilih Driver</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <div class="form-group">
                <select name="id_helper_1" id="id_helper_1" class="form-control select2">
                    <option value="">Pilih Helper</option>
                </select>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <select name="id_helper_2" id="id_helper_2" class="form-control select2">
                    <option value="">Pilih Helper</option>
                </select>
            </div>
        </div>
        <div class="col-4">
            <div class="form-group">
                <select name="id_helper_3" id="id_helper_3" class="form-control select2">
                    <option value="">Pilih Helper</option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tujuan" field="tujuan" icon="feather icon-map" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered" id="tabelbarang">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="2" align="">Kode</th>
                        <th rowspan="2" style="text-align:center">Nama Barang</th>
                        <th rowspan="2" style="text-align:center">Saldo</th>
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
                <tbody style="font-size:12px">
                    @foreach ($produk as $d)
                    @php
                    $saldoawal_gs = ($d->saldo_awal_gs + $d->sisamutasi) / $d->isipcsdus;
                    //$saldoawal_gs = ($d->saldo_awal_gs) / $d->isipcsdus;
                    $pusat = $d->pusat / $d->isipcsdus;
                    $transit_in = $d->transit_in / $d->isipcsdus;
                    $retur = $d->retur / $d->isipcsdus;
                    $lainlain_in = $d->lainlain_in / $d->isipcsdus;
                    $repack = $d->repack / $d->isipcsdus;
                    $penyesuaian_in = $d->penyesuaian_in / $d->isipcsdus;
                    $penjualan = $d->penjualan / $d->isipcsdus;
                    $promosi = $d->promosi / $d->isipcsdus;
                    $reject_pasar = $d->reject_pasar / $d->isipcsdus;
                    $reject_mobil = $d->reject_mobil / $d->isipcsdus;
                    $reject_gudang = $d->reject_gudang / $d->isipcsdus;
                    $transit_out = $d->transit_out / $d->isipcsdus;
                    $lainlain_out = $d->lainlain_out / $d->isipcsdus;
                    $penyesuaian_out = $d->penyesuaian_out / $d->isipcsdus;

                    $sisamutasi = ($saldoawal_gs + $pusat + $transit_in + $retur + $lainlain_in + $repack + $penyesuaian_in) - ($penjualan + $promosi + $reject_pasar + $reject_mobil + $reject_gudang + $transit_out + $lainlain_out + $penyesuaian_out);

                    $sm = ($d->saldo_awal_gs + $d->sisamutasi + $d->pusat + $d->transit_in + $d->retur + $d->lainlain_in + $d->repack + $d->penyesuaian_in) - ($d->penjualan + $d->promosi + $d->reject_pasar + $d->reject_mobil + $d->reject_gudang + $d->transit_out + $d->lainlain_out + $d->penyesuaian_out);

                    @endphp

                    <tr>
                        <td>{{ $d->kode_produk }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td align="right">
                            <input type="hidden" class="isipcsdus" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
                            <input type="hidden" class="isipcs" name="isipcs[]" value="{{ $d->isipcs }}">
                            <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
                            <input type="hidden" class="sm" value="{{ $sm }}">
                            <?php
                                if (!empty($sisamutasi)) {
                                    echo desimal($sisamutasi);
                                }
                                ?>
                        </td>
                        <td style="width: 12%">
                            <input type="text" autocomplete="off" class="form-control jmldus" name="jmldus[]">
                        </td>
                        <td>{{ $d->satuan }}</td>
                        <td style="width:12%">
                            <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" autocomplete="off" class="form-control jmlpack" name="jmlpack[]">
                        </td>
                        <td>PACK</td>
                        <td style="width: 12%">
                            <input type="text" autocomplete="off" class="form-control jmlpcs" name="jmlpcs[]">
                        </td>
                        <td>PCS
                            <input type="hidden" class="total">
                        </td>
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
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        $("#frmFpb").find('#id_karyawan').select2({
            dropdownParent: $('#mdlinput')
        });

        var $tblrows = $("#tabelbarang tbody tr");
        $tblrows.each(function(index) {
            var $tblrow = $(this);
            $tblrow.find('.jmldus,.jmlpack,.jmlpcs').on('keyup', function() {
                var jmldus = $tblrow.find(".jmldus").val();
                var jmlpack = $tblrow.find(".jmlpack").val();
                var jmlpcs = $tblrow.find(".jmlpcs").val();
                var isipcsdus = $tblrow.find(".isipcsdus").val();
                var isipcs = $tblrow.find(".isipcs").val();

                var sm = $tblrow.find(".sm").val();


                if (sm == "") {
                    sm = 0;
                }
                if (jmldus == "") {
                    jmldus = 0;
                }

                if (jmlpack == "") {
                    jmlpack = 0;
                }

                if (jmlpcs == "") {
                    jmlpcs = 0;
                }
                var total = (parseInt(jmldus) * parseInt(isipcsdus)) + (parseInt(jmlpack) * parseInt(isipcs)) + parseInt(jmlpcs);

                if (total > sm) {
                    alert('Tidak Bisa Melebih Stok Gudang');
                    $tblrow.find(".jmldus").val("");
                    $tblrow.find(".jmlpack").val("");
                    $tblrow.find(".jmlpcs").val("");
                }
                $tblrow.find(".total").val(total);
            });
        });

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#frmFpb").find("#id_karyawan").html(respond);
                }
            });
        }

        function loadkendaraan(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/kendaraan/getkendaraancab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#frmFpb").find("#no_polisi").html(respond);
                }
            });
        }

        function loaddriver(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'DRIVER'
                }
                , cache: false
                , success: function(respond) {
                    $("#frmFpb").find("#id_driver").html(respond);
                }
            });
        }

        function loadhelper(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                }
                , cache: false
                , success: function(respond) {
                    $("#frmFpb").find("#id_helper_1").html(respond);
                    $("#frmFpb").find("#id_helper_2").html(respond);
                    $("#frmFpb").find("#id_helper_3").html(respond);
                }
            });
        }

        $("#frmFpb").find("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
            loadkendaraan(kode_cabang);
            loaddriver(kode_cabang);
            loadhelper(kode_cabang);
        });

        $("#frmFpb").submit(function() {
            var no_fpb = $("#frmFpb").find("#no_fpb").val();
            var kode_cabang = $("#frmFpb").find("#kode_cabang").val();
            var id_karyawan = $("#frmFpb").find("#id_karyawan").val();
            var no_polisi = $("#no_polisi").val();
            var tgl_permintaan = $("#tgl_permintaan").val();
            var tujuan = $("#tujuan").val();
            if (no_fpb == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. FPB Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#no_fpb").focus();
                });

                return false;
            } else if (tgl_permintaan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tgl Permintaan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#tgl_permintaan").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#kode_cabang").focus();
                });

                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#id_karyawan").focus();
                });

                return false;
            } else if (no_polisi == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Polisi Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#no_polisi").focus();
                });

                return false;
            } else if (tujuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tujuan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#tujuan").focus();
                });

                return false;
            }
        });
    });

</script>
