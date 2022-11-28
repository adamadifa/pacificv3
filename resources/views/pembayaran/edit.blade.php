<form action="/pembayaran/{{ $hb->nobukti }}/update" id="frmBayaredit" method="POST">
    @csrf
    <div class="form-body">
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Tanggal Bayar" value="{{ $hb->tglbayar }}" field="tglbayar_edit" icon="feather icon-calendar" datepicker />
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <x-inputtext label="Jumlah Bayar" value="{{ rupiah($hb->bayar) }}" field="bayar_edit" icon="feather icon-shopping-cart" right />
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="id_karyawan" id="id_karyawan_edit" class="form-control">
                        <option value="">Salesman Penagih</option>
                        @foreach ($salesman as $d)
                        <option @if ($hb->id_karyawan == $d->id_karyawan)
                            selected
                            @endif value="{{ $d->id_karyawan }}">{{ $d->nama_karyawan }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-12">
                <div class="vs-checkbox-con vs-checkbox-primary">
                    <input type="checkbox" @if ($hb->status_bayar=='voucher')
                    checked
                    @endif class="voucher_edit" name="voucher" value="voucher">
                    <span class="vs-checkbox">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="">Bayar Menggunakan Voucher ?</span>
                </div>
            </div>
        </div>

        <div class="row" id="ketvoucher_edit">
            <div class="col-12">
                <div class="form-group">
                    <select class="form-control" name="ket_voucher" id="ket_voucher_edit">
                        <option @if ($hb->ket_voucher==1)
                            selected
                            @endif value="1">Penghapusan Piutang</option>
                        <option @if ($hb->ket_voucher==2)
                            selected
                            @endif value="2">Diskon Program</option>
                        <option @if ($hb->ket_voucher==3)
                            selected
                            @endif value="3">Penyelesaian Piutang Oleh Salesman</option>
                        <option @if ($hb->ket_voucher==4)
                            selected
                            @endif value="4">Pengalihan Piutang Dgng Jd Piutang Kary</option>
                        <option @if ($hb->ket_voucher==6)
                            selected
                            @endif value="6">Saus Premium TP 5-1</option>
                        <option @if ($hb->ket_voucher==7)
                            selected
                            @endif value="7">PPN KPBPB</option>
                        <option @if ($hb->ket_voucher==8)
                            selected
                            @endif value="8">PPN WAPU</option>
                        <option @if ($hb->ket_voucher==9)
                            selected
                            @endif value="9">PPH PASAL 22</option>
                        <option @if ($hb->ket_voucher==5)
                            selected
                            @endif value="5">Lainnya</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mb-1">
            <div class="col-12">
                <div class="vs-checkbox-con vs-checkbox-primary">

                    <input type="checkbox" @if ($hb->girotocash=='1')
                    checked
                    @endif class="girotocash_edit" name="girotocash" value="1">
                    <span class="vs-checkbox">
                        <span class="vs-checkbox--check">
                            <i class="vs-icon feather icon-check"></i>
                        </span>
                    </span>
                    <span class="">Ganti Giro Menjadi Cash ?</span>
                </div>
            </div>
        </div>
        <div class="row" id="girotolak_edit">
            <div class="col-12">
                <div class="form-group">
                    <select class="form-control" name="id_giro" id="id_giro_edit">
                        <option value="">Silahkan Pilih No. Giro</option>
                        @foreach ($girotolak as $d)
                        <option @if ($hb->id_giro == $d->id_giro)
                            selected
                            @endif value="{{ $d->id_giro }}">{{ $d->no_giro }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <button class="btn btn-primary btn-block"><i class="feather icon-send"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        function cektutuplaporan(tanggal) {
            $.ajax({
                type: "POST"
                , url: "/cektutuplaporan"
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tanggal: tanggal
                    , jenislaporan: "penjualan"
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tglbayar_edit").change(function() {
            cektutuplaporan($(this).val());
        });
        $('.voucher_edit').change(function() {
            if (this.checked) {
                $("#ketvoucher_edit").show();
            } else {
                $("#ketvoucher_edit").hide();
            }

        });

        $('.girotocash_edit').change(function() {
            if (this.checked) {
                $("#girotolak_edit").show();
            } else {
                $("#girotolak_edit").hide();
            }

        });

        function loadvoucher() {
            if ($(".voucher_edit").is(':checked')) {
                $("#ketvoucher_edit").show();
            } else {
                $("#ketvoucher_edit").hide();
            }
        }

        function loadgirotolak() {
            if ($(".girotolak_edit").is(':checked')) {
                $("#girotolak_edit").show();
            } else {
                $("#girotolak_edit").hide();
            }
        }

        loadvoucher();
        loadgirotolak();
        $("#bayar_edit").maskMoney();

        $("#frmBayaredit").submit(function(e) {
            //e.preventDefault();
            var tglbayar = $("#tglbayar_edit").val();
            var bayar = $("#bayar_edit").val();
            var id_karyawan = $("#id_karyawan_edit").val();
            var id_giro = $("#id_giro_edit").val();
            var sisabayar = "{{ $sisabayar }}";
            var jmlbayar = parseInt(bayar.replace(/\./g, ''));
            var cektutuplaporan = $("#cektutuplaporan").val();
            //alert(sisabayar);
            if (cektutuplaporan > 0) {
                swal("Peringatan", "Laporan Periode Ini Sudah Ditutup !", "warning");
                return false;
            } else if (tglbayar == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Bayar Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tglbayar_edit").focus();
                });
                return false;
            } else if (bayar == "" || bayar === "0") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Bayar Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#bayar_edit").focus();
                });
                return false;
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Penagih Harus Diisi  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_karyawan_edit").focus();
                });
                return false;
            } else if ($(".girotocash_edit").is(':checked') && id_giro == "") {
                swal({
                    title: 'Oops'
                    , text: 'No. Giro Harus Dipilih  !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#id_giro_edit").focus();
                });
                return false;
            } else {
                return true;
            }
        });
    });

</script>
