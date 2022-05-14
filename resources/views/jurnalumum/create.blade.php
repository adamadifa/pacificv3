<form action="/jurnalumum/store" method="post" id="frmjurnalumum">
    <input type="hidden" name="kode_dept" value="{{ $kode_dept }}">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tanggal" label="Tanggal Jurnal Umum" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Keterangan" field="keterangan" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Pilih Akun</option>
                    @foreach ($coa as $d)
                    <option value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" right />
        </div>
    </div>
    <div class="form-group">
        <ul class="list-unstyled mb-0">
            <li class="d-inline-block mr-2">
                <fieldset>
                    <div class="vs-radio-con vs-radio-success">
                        <input type="radio" name="status_dk" value="D">
                        <span class="vs-radio">
                            <span class="vs-radio--border"></span>
                            <span class="vs-radio--circle"></span>
                        </span>
                        <span class="">Debet</span>
                    </div>
                </fieldset>
            </li>
            <li class="d-inline-block mr-2">
                <fieldset>
                    <div class="vs-radio-con vs-radio-danger">
                        <input type="radio" name="status_dk" checked value="K">
                        <span class="vs-radio">
                            <span class="vs-radio--border"></span>
                            <span class="vs-radio--circle"></span>
                        </span>
                        <span class="">Kredit</span>
                    </div>
                </fieldset>
            </li>
        </ul>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    var h = document.getElementById('jumlah');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d-]/g, '').toString()
            , split = number_string.split(',')
            , sisa = split[0].length % 3
            , rupiah = split[0].substr(0, sisa)
            , ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    function convertToRupiah(angka) {
        var rupiah = '';
        var angkarev = angka.toString().split('').reverse().join('');
        for (var i = 0; i < angkarev.length; i++)
            if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
        return rupiah.split('', rupiah.length - 1).reverse().join('');
    }

</script>
<script>
    $(function() {

        // function cektutuplaporan() {
        //     var tgltransaksi = $("#tgl_jurnalumum").val();
        //     $.ajax({
        //         type: "POST"
        //         , url: "/cektutuplaporan"
        //         , data: {
        //             _token: "{{ csrf_token() }}"
        //             , tanggal: tgltransaksi
        //             , jenislaporan: "pembelian"
        //         }
        //         , cache: false
        //         , success: function(respond) {
        //             console.log(respond);
        //             $("#cektutuplaporan").val(respond);
        //         }
        //     });
        // }

        // $("#tgl_jurnalumum").change(function() {
        //     cektutuplaporan();
        // });

        $("#frmjurnalumum").submit(function(e) {
            var tanggal = $("#tanggal").val();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var kode_akun = $("#kode_akun").val();
            // var cektutuplaporan = $("#cektutuplaporan").val();
            // if (cektutuplaporan > 0) {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Laporan Periode Ini Sudah Ditutup !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#tgl_jurnalumum").focus();
            //     });
            //     return false;
            // } else
            if (tanggal == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tanggal").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
                return false;
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
                return false;
            } else if (kode_akun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kode Akun  Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kode_akun").focus();
                });
                return false;
            }
        });
    });

</script>
