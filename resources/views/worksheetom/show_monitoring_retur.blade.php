<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }
</style>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Kode Produk</th>
                    <th rowspan="2">Nama Barang</th>
                    <th colspan="3" style="text-align: center">Retur</th>
                    <th colspan="3" style="text-align: center" class="bg-success">Pelunasan</th>
                    <th colspan="3" style="text-align: center" class="bg-danger">Sisa</th>
                    <th rowspan="2">Status</th>
                </tr>
                <tr>
                    <th class="text-center">Dus</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Pcs</th>
                    <th class="text-center bg-success">Dus</th>
                    <th class="text-center bg-success">Pack</th>
                    <th class="text-center bg-success">Pcs</th>
                    <th class="text-center bg-danger">Dus</th>
                    <th class="text-center bg-danger">Pack</th>
                    <th class="text-center bg-danger">Pcs</th>
                </tr>
            </thead>
            <tbody id="loadretur">

            </tbody>
        </table>

    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="row">
            <div class="col-6">
                <div class="form-group">
                    <select name="kode_barang" id="kode_barang" class="form-control">
                        <option value="">Pilih Barang</option>
                        @foreach ($detail as $d)
                            <option isipcsdus="{{ $d->isipcsdus }}" isipcs="{{ $d->isipcs }}"
                                value="{{ $d->kode_barang }}">{{ $d->nama_barang }}</option>
                        @endforeach
                    </select>
                    <input type="hidden" id="isipcsdus">
                    <input type="hidden" id="isipcs">
                </div>
            </div>
            <div class="col-2">
                <x-inputtext label="Dus" field="jml_dus" icon="feather icon-file" right />
            </div>
            <div class="col-2">
                <x-inputtext label="Pack" field="jml_pack" icon="feather icon-file" right />
            </div>
            <div class="col-2">
                <x-inputtext label="Pcs" field="jml_pcs" icon="feather icon-file" right />
            </div>

        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext field="no_dpb" label="Ketikan No. DPB " icon="fa fa-barcode" />
                <input type="hidden" id="no_dpb_val" name="no_dpb_val">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-primary w-100" id="tambahpelunasan">
                    <i class="feather icon-send mr-1"></i>Submit
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row mt-2">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">Kode Produk</th>
                    <th rowspan="2">Nama Barang</th>
                    <th colspan="3" style="text-align: center">Retur</th>
                    <th rowspan="2">No. DPB</th>
                    <th rowspan="2">#</th>
                </tr>
                <tr>
                    <th class="text-center">Dus</th>
                    <th class="text-center">Pack</th>
                    <th class="text-center">Pcs</th>
                </tr>
            </thead>
            <tbody id="loadpelunasanretur">
            </tbody>
        </table>
    </div>
</div>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script>
    $(function() {
        $("#no_dpb").autocomplete({
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url: "/getautocompletedpb",
                    type: 'post',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        search: request.term,

                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                $("#no_dpb").val(ui.item.label);
                $("#no_dpb_val").val(ui.item.val);
                var no_dpb = ui.item.val;
                return false;
            }
        });

        $("#tambahpelunasan").click(function(e) {
            e.preventDefault();
            var kode_barang = $("#kode_barang").val();
            var no_dpb = $("#no_dpb_val").val();
            var isipcsdus = $("#isipcsdus").val();
            var isipcs = $("#isipcs").val();

            var jml_dus = $("#jml_dus").val();
            var jml_pack = $("#jml_pack").val();
            var jml_pcs = $("#jml_pcs").val();

            var jmldus = jml_dus != "" ? parseInt(jml_dus.replace(/\./g, '')) : 0;
            var jmlpack = jml_pack != "" ? parseInt(jml_pack.replace(/\./g, '')) : 0;
            var jmlpcs = jml_pcs != "" ? parseInt(jml_pcs.replace(/\./g, '')) : 0;

            var jumlah = (jmldus * parseInt(isipcsdus)) + (jmlpack * (parseInt(isipcs))) + jmlpcs;

            if (kode_barang == "") {
                swal({
                    title: 'Oops',
                    text: 'Barang Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (jumlah == "") {
                swal({
                    title: 'Oops',
                    text: 'Qty Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#jml_dus").focus();
                });
                return false;
            } else if (no_dpb == "") {
                swal({
                    title: 'Oops',
                    text: 'No. DPB Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#no_dpb").focus();
                });
                return false;
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/worksheetom/storepelunasanretur',
                    data: {
                        _token: "{{ csrf_token() }}",
                        no_retur_penj: "{{ $retur->no_retur_penj }}",
                        kode_barang: kode_barang,
                        jumlah: jumlah,
                        no_dpb: no_dpb
                    },
                    cache: false,
                    success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Success',
                                text: 'Item Berhasil Disimpan !',
                                icon: 'success',
                                showConfirmButton: false
                            }).then(function() {
                                // loadbarangtemp();
                                loadpelunasan();
                                $("#kode_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");
                                $("#no_dpb_val").val("");
                                $("#no_dpb").val("");
                            });


                        } else if (respond == 1) {
                            swal({
                                title: 'Oops',
                                text: 'Jumlah Pelunasan Melebihi Jumlah Retur !',
                                icon: 'warning',
                                showConfirmButton: false
                            }).then(function() {
                                $("#kode_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");
                                $("#no_dpb_val").val("");
                                $("#no_dpb").val("");
                            });
                        } else {
                            swal({
                                title: 'Oops',
                                text: respond,
                                icon: 'warning',
                                showConfirmButton: false
                            }).then(function() {

                                $("#jml_dus").focus();

                            });
                        }
                    }
                });
            }
        });

        $("#kode_barang").change(function(e) {
            var isipcsdus = $('option:selected', this).attr("isipcsdus");
            var isipcs = $('option:selected', this).attr("isipcs");

            $("#isipcsdus").val(isipcsdus);
            $("#isipcs").val(isipcs);
        });


        function loadpelunasan() {
            var no_retur_penj = "{{ $retur->no_retur_penj }}";
            $.ajax({
                type: 'POST',
                url: '/worksheetom/showpelunasanretur',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_retur_penj: no_retur_penj,
                },
                cache: false,
                success: function(respond) {
                    loaddetailretur();
                    $("#loadpelunasanretur").html(respond);
                }
            });
        }

        function loaddetailretur() {
            var no_retur_penj = "{{ $retur->no_retur_penj }}";
            $.ajax({
                type: 'POST',
                url: '/worksheetom/showdetailretur',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_retur_penj: no_retur_penj
                },
                cache: false,
                success: function(respond) {
                    $("#no_retur_penj").text(no_retur_penj);
                    $("#loadretur").html(respond);
                }
            });
        }
        loadpelunasan();
        loaddetailretur();
    });
</script>
