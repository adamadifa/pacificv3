<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }

</style>
<form action="/dpb/store" method="post" id="frmDpb">
    @csrf
    <div class="row">
        <div class="col-4">
            <x-inputtext label="No. DPB" field="no_dpb" icon="feather icon-file" />
        </div>
        <div class="col-4">
            <x-inputtext field="no_fpb" label="Ketikan No. FPB" icon="fa fa-barcode" />
            <input type="hidden" id="no_fpb_val" name="no_fpb_val">
        </div>
        <div class="col-4">
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
            <div class="form-group">
                <select name="no_polisi" id="no_polisi" class="form-control">
                    <option value="">Pilih Kendaraan</option>
                </select>
            </div>
        </div>
        <div class="col-lg-4 col-sm-12">
            <div class="form-group  ">
                <select name="id_karyawan" id="id_karyawan" class="form-control select2">
                    <option value="">Semua Salesman</option>
                </select>
            </div>
        </div>
        <div class="col-4">
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
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="4" align="">Kode</th>
                        <th rowspan="4" style="text-align:center">Nama Barang</th>
                        <th colspan="6" style="text-align:center">Pengambilan</th>
                    </tr>
                    <tr>
                        <th colspan="6">
                            <x-inputtext label="Tanggal Pengambilan" field="tgl_pengambilan" icon="feather icon-calendar" />
                        </th>
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
                <tbody id="loaddetailfpb">
                    @foreach ($produk as $d)
                    <input type="hidden" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
                    <input type="hidden" name="isipcs[]" value="{{ $d->isipcs }}">
                    <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
                    <tr>
                        <td>{{ $d->kode_produk }}</td>
                        <td>{{ $d->nama_barang }}</td>
                        <td style="width: 12%">
                            <input type="text" autocomplete="off" class="form-control" name="jmldus[]">
                        </td>
                        <td>{{ $d->satuan }}</td>
                        <td style="width:12%">
                            <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" autocomplete="off" class="form-control" name="jmlpack[]">
                        </td>
                        <td>PACK</td>
                        <td style="width: 12%">
                            <input type="text" autocomplete="off" class="form-control" name="jmlpcs[]">
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

        function getcabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/cabang/getcabang'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#kode_cabang").html(respond);
                }
            });
        }

        function getkendaraan(kode_cabang, no_polisi) {
            $.ajax({
                type: 'POST'
                , url: '/kendaraan/getkendaraancab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , no_polisi: no_polisi
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#no_polisi").html(respond);
                }
            });
        }

        function getsalesman(kode_cabang, id_karyawan) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , id_karyawan: id_karyawan
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_karyawan").html(respond);
                }
            });
        }

        function getdriver(kode_cabang, id_driver) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'DRIVER'
                    , id_driver_helper: id_driver
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_driver").html(respond);
                }
            });
        }


        function gethelper1(kode_cabang, id_helper_1) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                    , id_driver_helper: id_helper_1
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_helper_1").html(respond);
                }
            });
        }

        function gethelper2(kode_cabang, id_helper_2) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                    , id_driver_helper: id_helper_2
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_helper_2").html(respond);
                }
            });
        }

        function gethelper3(kode_cabang, id_helper_3) {
            $.ajax({
                type: 'POST'
                , url: '/driverhelper/getdriverhelpercab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , kategori: 'HELPER'
                    , id_driver_helper: id_helper_3
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_helper_3").html(respond);
                }
            });
        }

        function loaddetailfpb(no_fpb) {
            $.ajax({
                type: 'POST'
                , url: '/fpb/getdetailfpb'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fpb: no_fpb
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailfpb").html(respond);
                }
            });
        }

        function loadfpb(no_fpb) {
            $.ajax({
                type: 'POST'
                , url: '/fpb/showfpb'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_fpb: no_fpb
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    var data = respond.split("|");
                    var kode_cabang = data[0];
                    var no_polisi = data[1];
                    var id_karyawan = data[2];
                    var id_driver = data[3];
                    var id_helper_1 = data[4];
                    var id_helper_2 = data[5];
                    var id_helper_3 = data[6];
                    var tujuan = data[7];
                    getcabang(kode_cabang);
                    getkendaraan(kode_cabang, no_polisi);
                    getsalesman(kode_cabang, id_karyawan);
                    getdriver(kode_cabang, id_driver);
                    gethelper1(kode_cabang, id_helper_1);
                    gethelper2(kode_cabang, id_helper_2);
                    gethelper3(kode_cabang, id_helper_3);
                    loaddetailfpb(no_fpb);
                    $("#tujuan").val(tujuan);
                }
            });
        }
        $("#frmDpb").find("#no_fpb").autocomplete({
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url: "/getautocompletefpb"
                    , type: 'post'
                    , dataType: "json"
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , search: request.term
                    }
                    , success: function(data) {
                        response(data);
                    }
                });
            }
            , select: function(event, ui) {
                $("#frmDpb").find("#no_fpb").val(ui.item.label);
                $("#no_fpb_val").val(ui.item.val);
                var no_fpb = ui.item.val;
                loadfpb(no_fpb);
                return false;
            }
        });


        $("#tgl_pengambilan").datepicker({
            dateFormat: 'yy-mm-dd'
        });

        $("#frmDpb").find('#id_karyawan').select2({
            dropdownParent: $('#mdlinput')
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
                }
                , cache: false
                , success: function(respond) {
                    $("#frmDpb").find("#id_driver").html(respond);
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
                    $("#frmDpb").find("#id_helper_1").html(respond);
                    $("#frmDpb").find("#id_helper_2").html(respond);
                    $("#frmDpb").find("#id_helper_3").html(respond);
                }
            });
        }

        $("#frmDpb").find("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
            loadkendaraan(kode_cabang);
            loaddriver(kode_cabang);
            loadhelper(kode_cabang);
        });




        $("#frmDpb").submit(function() {
            var no_dpb = $("#frmDpb").find("#no_dpb").val();
            var no_fpb = $("#frmDpb").find("#no_fpb").val();
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
            } else if (no_dpb.length > 10) {
                swal({
                    title: 'Oops'
                    , text: 'No. DPB Maksimal 10 Character !'
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
