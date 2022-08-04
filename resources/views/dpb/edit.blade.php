<form action="/dpb/{{ Crypt::encrypt($dpb->no_dpb) }}/update" method="post" id="frmDpb">
    @csrf
    <div class="row">
        <div class="col-3">
            <x-inputtext label="No. DPB" readonly field="no_dpb" value="{{ $dpb->no_dpb }}" icon="feather icon-file" />
        </div>
        <div class="col-3">
            <div class="form-group">
                <select name="kode_cabang" id="kode_cabang" class="form-control">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $d)
                    <option {{ $dpb->kode_cabang == $d->kode_cabang ? 'selected' : '' }} value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-sm-12">
            <div class="form-group  ">
                <select name="id_karyawan" id="id_karyawan" class="form-control">
                    <option value="">Semua Salesman</option>
                </select>
            </div>
        </div>
        <div class="col-lg-3 col-sm-12">
            <div class="form-group">
                <select name="no_polisi" id="no_polisi" class="form-control">
                    <option value="">Pilih Kendaraan</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-3">
            <div class="form-group">
                <select name="id_driver" id="id_driver" class="form-control select2">
                    <option value="">Pilih Driver</option>
                </select>
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-8">
                    <div class="form-group">
                        <select name="id_helper_1" id="id_helper_1" class="form-control select2">
                            <option value="">Pilih Helper</option>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Jumlah" field="jml_helper" value="{{ $dpb->jml_helper }}" icon="feather icon-file" right />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Persentase" field="persentase_helper" value="" icon="feather icon-percent" right />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-8">
                    <div class="form-group">
                        <select name="id_helper_2" id="id_helper_2" class="form-control select2">
                            <option value="">Pilih Helper</option>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Jumlah" field="jml_helper_2" value="{{ $dpb->jml_helper_2 }}" icon="feather icon-file" right />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Persentase" field="persentase_helper_2" value="" icon="feather icon-percent" right />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-3">
            <div class="row">
                <div class="col-8">
                    <div class="form-group">
                        <select name="id_helper_3" id="id_helper_3" class="form-control select2">
                            <option value="">Pilih Helper</option>
                        </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Jumlah" field="jml_helper_3" value="{{ $dpb->jml_helper_3 }}" icon="feather icon-file" right />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <x-inputtext label="Persentase" field="persentase_helper_3" value="" icon="feather icon-percent" right />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tujuan" value="{{ $dpb->tujuan }}" field="tujuan" icon="feather icon-map" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="4" align="">Kode</th>
                        <th rowspan="4" style="text-align:center">Nama Barang</th>
                        <th colspan="6" class="bg-info" style="text-align:center">Pengambilan</th>
                        <th colspan="6" class="bg-success" style="text-align:center">Pengembalian</th>
                        <th colspan="6" rowspan="2" class="bg-danger" style="text-align:center">Barang Keluar</th>
                    </tr>
                    <tr>
                        <th colspan="6" class="bg-info">
                            <x-inputtext label="Tanggal Pengambilan" value="{{ $dpb->tgl_pengambilan }}" tanggal field="tgl_pengambilan" icon="feather icon-calendar" />
                        </th>
                        <th colspan="6" class="bg-success">
                            <x-inputtext label="Tanggal Pengembalian" value="{{ $dpb->tgl_pengembalian }}" tanggal field="tgl_pengembalian" icon="feather icon-calendar" />
                        </th>

                    </tr>
                    <tr>
                        <th colspan="6" style="text-align:center;" class="bg-info">Kuantitas</th>
                        <th colspan="6" style="text-align:center;" class="bg-success">Kuantitas</th>
                        <th colspan="6" style="text-align:center;" class="bg-danger">Kuantitas</th>
                    </tr>
                    <tr>
                        <th class="bg-info" style="text-align:center">Jumlah</th>
                        <th class="bg-info" style="text-align:center">Satuan</th>
                        <th class="bg-info" style="text-align:center">Jumlah</th>
                        <th class="bg-info" style="text-align:center">Satuan</th>
                        <th class="bg-info" style="text-align:center">Jumlah</th>
                        <th class="bg-info" style="text-align:center">Satuan</th>

                        <th class="bg-success" style="text-align:center">Jumlah</th>
                        <th class="bg-success" style="text-align:center">Satuan</th>
                        <th class="bg-success" style="text-align:center">Jumlah</th>
                        <th class="bg-success" style="text-align:center">Satuan</th>
                        <th class="bg-success" style="text-align:center">Jumlah</th>
                        <th class="bg-success" style="text-align:center">Satuan</th>

                        <th class="bg-danger" style="text-align:center">Jumlah</th>
                        <th class="bg-danger" style="text-align:center">Satuan</th>
                        <th class="bg-danger" style="text-align:center">Jumlah</th>
                        <th class="bg-danger" style="text-align:center">Satuan</th>
                        <th class="bg-danger" style="text-align:center">Jumlah</th>
                        <th class="bg-danger" style="text-align:center">Satuan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $totalbarangkeluar = 0;
                    @endphp
                    @foreach ($produk as $d)
                    @php
                    $isipcsdus = $d->isipcsdus;
                    $isipack = $d->isipack;
                    $isipcs = $d->isipcs;

                    $jmlpengambilan = ROUND($d->jml_pengambilan * $isipcsdus);
                    $jmlpengambilan_dus = floor($jmlpengambilan / $isipcsdus);

                    if ($jmlpengambilan != 0) {
                    $sisadus_pengambilan = $jmlpengambilan % $isipcsdus;
                    } else {
                    $sisadus_pengambilan = 0;
                    }
                    if ($isipack == 0) {
                    $jmlpack_pengambilan = 0;
                    $sisapack_pengambilan = $sisadus_pengambilan;
                    } else {
                    $jmlpack_pengambilan = floor($sisadus_pengambilan / $isipcs);
                    $sisapack_pengambilan = $sisadus_pengambilan % $isipcs;
                    }

                    $jmlpcs_pengambilan = $sisapack_pengambilan;


                    $jmlpengembalian = ROUND($d->jml_pengembalian * $isipcsdus);
                    $jmlpengembalian_dus = floor($jmlpengembalian / $isipcsdus);

                    if ($jmlpengembalian != 0) {
                    $sisadus_pengembalian = $jmlpengembalian % $isipcsdus;
                    } else {
                    $sisadus_pengembalian = 0;
                    }
                    if ($isipack == 0) {
                    $jmlpack_pengembalian = 0;
                    $sisapack_pengembalian = $sisadus_pengembalian;
                    } else {
                    $jmlpack_pengembalian = floor($sisadus_pengembalian / $isipcs);
                    $sisapack_pengembalian = $sisadus_pengembalian % $isipcs;
                    }

                    $jmlpcs_pengembalian = $sisapack_pengembalian;

                    $jmlbarangkeluar = ROUND($d->jml_penjualan * $isipcsdus);

                    $jmlbarangkeluar_dus = floor($jmlbarangkeluar / $isipcsdus);

                    if ($jmlbarangkeluar != 0) {
                    $sisadus_barangkeluar = $jmlbarangkeluar % $isipcsdus;
                    } else {
                    $sisadus_barangkeluar = 0;
                    }
                    if ($isipack == 0) {
                    $jmlpack_barangkeluar = 0;
                    $sisapack_barangkeluar = $sisadus_barangkeluar;
                    } else {
                    $jmlpack_barangkeluar = floor($sisadus_barangkeluar / $isipcs);
                    $sisapack_barangkeluar = $sisadus_barangkeluar % $isipcs;
                    }

                    $jmlpcs_barangkeluar = $sisapack_barangkeluar;




                    $totalbarangkeluar += $d->jml_penjualan;
                    @endphp
                    <input type="hidden" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
                    <input type="hidden" name="isipcs[]" value="{{ $d->isipcs }}">
                    <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">

                    <tr>
                        <td>{{ $d->kode_produk }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td style="width: 5%" class="bg-info">
                            <input type="text" autocomplete="off" class="form-control" name="jmlduspengambilan[]" value="{{ !empty($jmlpengambilan_dus) ? $jmlpengambilan_dus : '' }}">
                        </td>
                        <td class="bg-info text-white">{{ $d->satuan }}</td>
                        <td style="width:5%" class="bg-info">
                            <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" autocomplete="off" class="form-control" name="jmlpackpengambilan[]" value="{{ !empty($jmlpack_pengambilan) ? $jmlpack_pengambilan : '' }}">
                        </td>
                        <td class="bg-info text-white">PACK</td>
                        <td style="width: 5%" class="bg-info">
                            <input type="text" class="form-control" autocomplete="off" name="jmlpcspengambilan[]" value="{{ !empty($jmlpcs_pengambilan) ? $jmlpcs_pengambilan : '' }}">
                        </td>
                        <td class="bg-info text-white">PCS</td>
                        <td style="width: 5%" class="bg-success">
                            <input type="text" class="form-control" autocomplete="off" name="jmlduspengembalian[]" value="{{ !empty($jmlpengembalian_dus) ? $jmlpengembalian_dus : '' }}">
                        </td>
                        <td class="bg-success text-white">{{ $d->satuan }}</td>
                        <td style="width:5%" class="bg-success">
                            <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" autocomplete="off" class="form-control" name="jmlpackpengembalian[]" value="{{ !empty($jmlpack_pengembalian) ? $jmlpack_pengembalian : '' }}">
                        </td>
                        <td class="bg-success text-white">PACK</td>
                        <td style="width: 5%" class="bg-success">
                            <input type="text" class="form-control" name="jmlpcspengembalian[]" autocomplete="off" value="{{ !empty($jmlpcs_pengembalian) ? $jmlpcs_pengembalian : '' }}">
                        </td>
                        <td class="bg-success text-white">PCS</td>
                        <td style="width: 5%" class="bg-danger">
                            <input type="text" class="form-control" name="jmldusbarangkeluar[]" autocomplete="off" value="{{ !empty($jmlbarangkeluar_dus) ? $jmlbarangkeluar_dus : '' }}">
                        </td>
                        <td class="bg-danger text-white">{{ $d->satuan }}</td>
                        <td style="width:5%" class="bg-danger">

                            <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" class="form-control" name="jmlpackbarangkeluar[]" value="{{ !empty($jmlpack_barangkeluar) ? $jmlpack_barangkeluar : '' }}">

                        </td>
                        <td class="bg-danger text-white">PACK</td>
                        <td style="width: 5%" class="bg-danger">
                            <input type="text" class="form-control" name="jmlpcsbarangkeluar[]" autocomplete="off" value="{{ !empty($jmlpcs_barangkeluar) ? $jmlpcs_barangkeluar : '' }}">
                        </td>
                        <td class="bg-danger text-white">PCS</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <input type="hidden" name="totalbarangkeluar" id="totalbarangkeluar_dus" value="{{ $totalbarangkeluar }}">
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

<script>
    $(function() {

        var kode_cabang = $("#frmDpb").find("#kode_cabang").val();
        loadsalesmancabang(kode_cabang);
        loadkendaraan(kode_cabang);
        loaddriver(kode_cabang);
        loadhelper1(kode_cabang);
        loadhelper2(kode_cabang);
        loadhelper3(kode_cabang);
        $("#tgl_pengambilan").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        $("#tgl_pengembalian").datepicker({
            dateFormat: 'yy-mm-dd'
        });




        function loadhelperjumlah() {
            var id_helper_1 = $("#id_helper_1").val();
            var id_helper_2 = $("#id_helper_2").val();
            var id_helper_3 = $("#id_helper_3").val();
            if (id_helper_1 == "") {
                $("#jml_helper").prop("readonly", true);
                $("#persentase_helper").prop("readonly", true);
            } else {
                $("#jml_helper").prop("readonly", false);
                $("#persentase_helper").prop("readonly", false);
            }

            if (id_helper_2 == "") {
                $("#jml_helper_2").prop("readonly", true);
                $("#persentase_helper_2").prop("readonly", true);
            } else {
                $("#jml_helper_2").prop("readonly", false);
                $("#persentase_helper_2").prop("readonly", false);
            }

            if (id_helper_3 == "") {
                $("#jml_helper_3").prop("readonly", true);
                $("#persentase_helper_3").prop("readonly", true);
            } else {
                $("#jml_helper_3").prop("readonly", false);
                $("#persentase_helper_3").prop("readonly", false);
            }
        }

        $("#id_helper_1, #id_helper_2, #id_helper_3").change(function() {
            loadhelperjumlah();
        });



        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: "{{ $dpb->id_karyawan }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_karyawan").html(respond);
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
                    , no_polisi: "{{ $dpb->no_kendaraan }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#no_polisi").html(respond);
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
                    , id_driver_helper: "{{ $dpb->id_driver }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_driver").html(respond);
                }
            });
        }

        function loadhelper1(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                    , id_driver_helper: "{{ $dpb->id_helper }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_helper_1").html(respond);
                    var cek = $("#id_helper_1").val();
                    if (cek == "") {
                        $("#jml_helper").prop("readonly", true);
                        $("#persentase_helper").prop("readonly", true);
                    }

                }
            });
        }

        function loadhelper2(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                    , id_driver_helper: "{{ $dpb->id_helper_2 }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_helper_2").html(respond);
                    var cek = $("#id_helper_2").val();
                    if (cek == "") {
                        $("#jml_helper_2").prop("readonly", true);
                        $("#persentase_helper_2").prop("readonly", true);
                    }
                }
            });
        }

        function loadhelper3(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                    , id_driver_helper: "{{ $dpb->id_helper_3 }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_helper_3").html(respond);
                    var cek = $("#id_helper_3").val();
                    if (cek == "") {
                        $("#jml_helper_3").prop("readonly", true);
                        $("#persentase_helper_3").prop("readonly", true);
                    }
                }
            });
        }

        $("#frmDpb").find("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
            loadkendaraan(kode_cabang);
            loaddriver(kode_cabang);
            loadhelper1(kode_cabang);
            loadhelper2(kode_cabang);
            loadhelper3(kode_cabang);
        });

        $("#frmDpb").submit(function() {
            var no_dpb = $("#frmDpb").find("#no_dpb").val();
            var kode_cabang = $("#frmDpb").find("#kode_cabang").val();
            var id_karyawan = $("#frmDpb").find("#id_karyawan").val();
            var no_polisi = $("#no_polisi").val();
            var tgl_pengambilan = $("#tgl_pengambilan").val();
            var tujuan = $("#tujuan").val();
            if (no_dpb == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. DPB Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#no_dpb").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#kode_cabang").focus();
                });
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#id_karyawan").focus();
                });
                return false;
            } else if (no_polisi == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Kendaraan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_polisi").focus();
                });
                return false;
            } else if (tujuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tujuan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tujuan").focus();
                });
                return false;
            } else if (tgl_pengambilan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pengambilan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengambilan").focus();
                });
                return false;
            }
        });
    });

</script>
