<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }
</style>
<form action="/mutasigudangcabang/store" method="post" id="frmDpb">
    @csrf
    <input type="hidden" id="cektutuplaporan">
    <div class="row">
        <div class="col-12">
            <x-inputtext field="no_dpb" label="Ketikan No. DPB / Nama Karyawan" icon="fa fa-barcode" />
            <input type="hidden" id="no_dpb_val" name="no_dpb_val">
            <input type="hidden" id="jenis_mutasi" name="jenis_mutasi" value="{{ strtoupper($jm) }}">
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="tgl_mutasi_gudang_cabang" label="Tanggal {{ ucwords(strtolower($textjm)) }}"
                icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-12" id="loaddpb">

        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th rowspan="3" align="">Kode</th>
                        <th rowspan="3" style="text-align:center">Nama Barang</th>
                        <th colspan="6" style="text-align:center">{{ ucwords(strtolower($textjm)) }}</th>
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
                    @foreach ($produk as $d)
                        <input type="hidden" name="isipcsdus[]" value="{{ $d->isipcsdus }}">
                        <input type="hidden" name="isipcs[]" value="{{ $d->isipcs }}">
                        <input type="hidden" name="kode_produk[]" value="{{ $d->kode_produk }}">
                        <tr>
                            <td>{{ $d->kode_produk }}</td>
                            <td>{{ $d->nama_barang }}</td>
                            <td style="width: 12%">
                                <input type="text" class="form-control" autocomplete="off" name="jmldus[]">
                            </td>
                            <td>{{ $d->satuan }}</td>
                            <td style="width:12%">
                                <input type="{{ !empty($d->isipack) ? 'text' : 'hidden' }}" autocomplete="off"
                                    class="form-control" name="jmlpack[]">
                            </td>
                            <td>PACK</td>
                            <td style="width: 12%">
                                <input type="text" class="form-control" autocomplete="off" name="jmlpcs[]">
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
                <button type="submit" name="submit" class="btn btn-primary btn-block"><i
                        class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {
        function cektutuplaporan() {
            var tanggal = $("#tgl_mutasi_gudang_cabang").val();
            $.ajax({
                type: "POST",
                url: "/cektutuplaporan",
                data: {
                    _token: "{{ csrf_token() }}",
                    tanggal: tanggal,
                    jenislaporan: "gudangcabang"
                },
                cache: false,
                success: function(respond) {
                    console.log(respond);
                    $("#frmDpb").find("#cektutuplaporan").val(respond);
                }
            });
        }

        $("#tgl_mutasi_gudang_cabang").change(function() {
            cektutuplaporan();
        });

        function loaddpb(no_dpb) {
            $.ajax({
                type: 'POST',
                url: '/dpb/showdpbmutasi',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_dpb: no_dpb
                },
                cache: false,
                success: function(respond) {
                    $("#loaddpb").html(respond);
                }
            });
        }
        $("#frmDpb").find("#no_dpb").autocomplete({
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url: "/getautocompletedpb",
                    type: 'post',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        search: request.term
                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                $("#frmDpb").find("#no_dpb").val(ui.item.label);
                $("#no_dpb_val").val(ui.item.val);
                var no_dpb = ui.item.val;
                loaddpb(no_dpb);
                return false;
            }
        });


        $("#frmDpb").submit(function() {
            var no_dpb = $("#no_dpb_val").val();
            var tgl_mutasi_gudang_cabang = $("#frmDpb").find("#tgl_mutasi_gudang_cabang").val();
            var cektutuplaporan = $("#frmDpb").find("#cektutuplaporan").val();
            if (cektutuplaporan == 1) {
                swal({
                    title: 'Oops',
                    text: 'Laporan Sudah Di Tutup !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $('#frmDpb').find('#tgl_mutasi_gudang_cabang').focus();
                });

                return false;
            } else if (no_dpb == "") {
                swal({
                    title: 'Oops',
                    text: 'No. DPB Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#no_dpb_val").focus();
                });
                return false;
            } else if (tgl_mutasi_gudang_cabang == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#frmDpb").find("#tgl_mutasi_gudang_cabang").focus();
                });
                return false;
            }
        });
    });
</script>
