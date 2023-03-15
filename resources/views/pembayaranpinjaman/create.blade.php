<div class="row">
    <div class="col-12">
        <x-inputtext label="Tanggal Bayar" field="tgl_bayar" icon="feather icon-calendar" datepicker />
    </div>
</div>
<div class="row">
    <div class="col-12">
        <x-inputtext label="Jumlah Bayar" field="jumlah" icon="feather icon-file" right />
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <a href="#" class="btn btn-primary w-100" id="bayar"><i class="feather icon-send mr-1"></i> Bayar</a>
        </div>
    </div>
</div>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();

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

        function loadrencanabayar() {
            var no_pinjaman = "{{ $no_pinjaman }}";
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/getrencanabayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrencanabayar").html(respond);
                }
            });
        }

        function loadhistoribayar() {
            var no_pinjaman = "{{ $no_pinjaman }}";
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/gethistoribayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadhistoribayar").html(respond);
                    loadsisatagihan();
                }
            });
        }

        function loadsisatagihan() {
            var jml_pinjaman = $("#jmlpinjaman").text();
            var totalbayar = $("#totalbayar").text();

            var jp = parseInt(jml_pinjaman.replace(/\./g, ''));
            var tb = parseInt(totalbayar.replace(/\./g, ''));

            var sisa = jp - tb;
            $("#jmlbayar").text(convertToRupiah(tb));
            $("#sisatagihan").text(convertToRupiah(sisa));

        }




        $("#bayar").click(function(e) {
            e.preventDefault();
            var tgl_bayar = $("#tgl_bayar").val();
            var jumlah = $("#jumlah").val();
            var jmlbayar = $("#jmlbayar").text();
            var jml_pinjaman = $("#jmlpinjaman").text();
            var jp = parseInt(jml_pinjaman.replace(/\./g, ''));
            var jb = parseInt(jmlbayar.replace(/\./g, ''));
            var sisa = jp - jb;
            var jm = parseInt(jumlah.replace(/\./g, ''));


            if (tgl_bayar == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Bayar Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_bayar").focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else if (jm > sisa) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Tidak Boleh Lebih Dari Sisa Tagihan !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/pembayaranpinjaman/store'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , no_pinjaman: "{{ $no_pinjaman }}"
                        , tgl_bayar: tgl_bayar
                        , jumlah: jumlah
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Success'
                                , text: 'Data Berhasil Disimpan !'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                loadrencanabayar();
                                loadhistoribayar();
                                $("#mdlinputbayarpinjaman").modal("hide");
                            });
                        }
                    }
                });
            }
        });
    });

</script>
