<style>
    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<form action="#" id="frmeditbarangtemp">
    <input type="hidden" id="harga_dus_old">
    <input type="hidden" id="harga_pack_old">
    <input type="hidden" id="harga_pcs_old">
    <input type="hidden" name="isipcsdus" id="isipcsdus">
    <input type="hidden" name="isipcs" id="isipcs">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_barang" id="kode_barang_pilih" class="form-control">
                    <option value="">Pilih Barang</option>
                    @foreach ($barang as $d)
                    <option value="{{ $d->kode_barang }}" kode_barang="{{ $d->kode_barang }}" nama_barang="{{ $d->nama_barang }}" isipcsdus="{{ $d->isipcsdus }}" isipcs="{{ $d->isipcs }}" harga_dus="{{ rupiah($d->harga_returdus) }}" harga_pack="{{ rupiah($d->harga_returpack) }}" harga_pcs="{{ rupiah($d->harga_returpcs) }}">{{ $d->nama_barang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <x-inputtext label="Dus" field="jml_dus" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Dus" field="harga_dus" icon="feather icon-tag" right readonly />
        </div>
    </div>

    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pack" field="jml_pack" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pack" field="harga_pack" icon="feather icon-tag" right readonly />
        </div>
    </div>


    <div class="row">
        <div class="col-4">
            <x-inputtext label="Pcs" field="jml_pcs" icon="feather icon-file" right />
        </div>
        <div class="col-8">
            <x-inputtext label="Harga / Pcs" field="harga_pcs" icon="feather icon-tag" right readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <a class="btn btn-primary btn-block text-white" id="tambahitem"><i class="feather icon-plus text-white mr-1"></i> Tambahkan</a>
        </div>
    </div>

</form>
<script>
    $(function() {
        $("#frmeditbarangtemp").find("#harga_dus, #harga_pack, #harga_pcs, #jml_dus, #jml_pack, #jml_pcs").maskMoney();

        var kode_pelanggan = $("#kode_pelanggan").val();

        function loadbarangtemp() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/retur/showbarangtempv2'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                , }
                , cache: false
                , success: function(respond) {
                    $("#loadbarangtemp").html(respond);
                    loadtotal();
                }
            });
        }

        function loadtotal() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/loadtotalreturtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    var total = parseInt(respond.replace(/\./g, ''));
                    $("#grandtotal").text(convertToRupiah(total));
                    // $("#total").val(convertToRupiah(grandtotal));
                    // $("#bruto").val(bruto);
                    $("#subtotal").val(total);
                    cektemp();
                }
            });
        }

        function cektemp() {
            var kode_pelanggan = $("#kode_pelanggan").val();
            $.ajax({
                type: 'POST'
                , url: '/cekreturtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_pelanggan: kode_pelanggan
                }
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }

        function convertToRupiah(number) {
            if (number) {
                var rupiah = "";
                var numberrev = number
                    .toString()
                    .split("")
                    .reverse()
                    .join("");
                for (var i = 0; i < numberrev.length; i++)
                    if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                return (
                    rupiah
                    .split("", rupiah.length - 1)
                    .reverse()
                    .join("")
                );
            } else {
                return number;
            }
        }

        $("#tambahitem").click(function(e) {
            e.preventDefault();
            var kode_barang = $("#kode_barang_pilih").val();
            var jml_dus = $("#jml_dus").val();
            var jml_pack = $("#jml_pack").val();
            var jml_pcs = $("#jml_pcs").val();
            var harga_dus = $("#harga_dus").val();
            var harga_pack = $("#harga_pack").val();
            var harga_pcs = $("#harga_pcs").val();
            var isipcsdus = $("#isipcsdus").val();
            var isipcs = $("#isipcs").val();
            var kode_pelanggan = $("#kode_pelanggan").val();

            var jmldus = jml_dus != "" ? parseInt(jml_dus.replace(/\./g, '')) : 0;
            var jmlpack = jml_pack != "" ? parseInt(jml_pack.replace(/\./g, '')) : 0;
            var jmlpcs = jml_pcs != "" ? parseInt(jml_pcs.replace(/\./g, '')) : 0;

            var hargadus = harga_dus != "" ? parseInt(harga_dus.replace(/\./g, '')) : 0;
            var hargapack = harga_pack != "" ? parseInt(harga_pack.replace(/\./g, '')) : 0;
            var hargapcs = harga_pcs != "" ? parseInt(harga_pcs.replace(/\./g, '')) : 0;



            var jumlah = (jmldus * parseInt(isipcsdus)) + (jmlpack * (parseInt(isipcs))) + jmlpcs;
            var subtotal = (jmldus * hargadus) + (jmlpack * hargapack) + (jmlpcs * hargapcs);
            //alert(totalpcs);

            if (kode_barang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Barang Harus Dipilih !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#nama_barang").focus();
                });
                return false;
            } else if (jumlah == "" && !nama_pelanggan.includes('BATAL')) {
                swal({
                    title: 'Oops'
                    , text: 'Qty Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_dus").focus();
                });
                return false;
            } else {
                //Simpan Barang Temp
                $.ajax({
                    type: 'POST'
                    , url: '/retur/storebarangtempv2'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_barang: kode_barang
                        , hargadus: hargadus
                        , hargapack: hargapack
                        , hargapcs: hargapcs
                        , jumlah: jumlah
                        , subtotal: subtotal
                        , kode_pelanggan: kode_pelanggan
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Item Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#mdlinputbarang").modal("hide");
                                loadbarangtemp();

                                $("#kode_barang_pilih").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                //$("#jml_dus").focus();

                            });


                        } else if (respond == 1) {
                            swal({
                                title: 'Oops'
                                , text: 'Item Sudah Ada !'
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {
                                $("#kode_barang_pilih").val("");
                                $("#nama_barang").val("");
                                $("#jml_dus").val("");
                                $("#jml_pack").val("");
                                $("#jml_pcs").val("");

                                $("#harga_dus").val("");
                                $("#harga_pack").val("");
                                $("#harga_pcs").val("");

                                $("#harga_dus_old").val("");
                                $("#harga_pack_old").val("");
                                $("#harga_pcs_old").val("");

                                $("#nama_barang").focus();

                            });
                        } else {
                            swal({
                                title: 'Oops'
                                , text: respond
                                , icon: 'warning'
                                , showConfirmButton: false
                            }).then(function() {

                                $("#jml_dus").focus();

                            });
                        }
                    }
                });
            }
        });

        $("#kode_barang_pilih").change(function(e) {
            e.preventDefault();
            var kode_barang = $('option:selected', this).attr("kode_barang");
            var nama_barang = $('option:selected', this).attr("nama_barang");
            var harga_dus = $('option:selected', this).attr("harga_dus");
            var harga_pack = $('option:selected', this).attr("harga_pack");
            var harga_pcs = $('option:selected', this).attr("harga_pcs");
            var isipcsdus = $('option:selected', this).attr("isipcsdus");
            var isipcs = $('option:selected', this).attr("isipcs");
            var nama_pelanggan = $("#nama_pelanggan").val();
            if ($('#promo').is(":checked")) {
                var harga_dus = 0;
                var harga_pack = 0;
                var harga_pcs = 0;
            }
            if (nama_pelanggan.includes('KPBN')) {
                $("#harga_dus").prop('readonly', false);
                $("#harga_pack").prop('readonly', false);
                $("#harga_pcs").prop('readonly', false);
            } else {
                $("#harga_dus").prop('readonly', true);
                $("#harga_pack").prop('readonly', true);
                $("#harga_pcs").prop('readonly', true);
            }
            $("#harga_dus").val(harga_dus);
            $("#harga_pack").val(harga_pack);
            $("#harga_pcs").val(harga_pcs);

            $("#harga_dus_old").val(harga_dus);
            $("#harga_pack_old").val(harga_pack);
            $("#harga_pcs_old").val(harga_pcs);

            $("#isipcsdus").val(isipcsdus);
            $("#isipcs").val(isipcs);
            if (harga_pack == 0) {
                $("#harga_pack").prop("readonly", true);
                $("#jml_pack").prop("readonly", true);
            } else {
                if (nama_pelanggan.includes('KPBN')) {
                    $("#harga_pack").prop("readonly", false);
                    $("#jml_pack").prop("readonly", false);
                } else {
                    $("#harga_pack").prop("readonly", true);
                    $("#jml_pack").prop("readonly", false);
                }

            }
        });


    });

</script>
