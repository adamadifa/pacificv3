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
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="2" align="">Kode</th>
                        <th rowspan="2" style="text-align:center">Nama Barang</th>
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
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        $("#frmFpb").find('#id_karyawan').select2({
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
            } else if (no_dpb.length > 10) {
                swal({
                    title: 'Oops'
                    , text: 'No. FPB Maksimal 10 Character !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#no_dpb").focus();
                });

                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#kode_cabang").focus();
                });
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmFpb").find("#id_karyawan").focus();
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
            } else if (tgl_permintaan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pengambilan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_permintaan").focus();
                });
                return false;
            }
        });
    });

</script>
