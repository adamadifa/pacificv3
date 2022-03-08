<table class="table">
    <tr>
        <td>Kode Target</td>
        <td>{{$kode_target}}</td>
    </tr>
    <tr>
        <td>Salesman</td>
        <td>{{$id_karyawan}} - {{$salesman->nama_karyawan}}</td>
    </tr>
    <tr>
        <td>Kode Produk</td>
        <td>{{$kode_produk}}</td>
    </tr>
</table>
<div class="row">
    <div class="col-12">
        <x-inputtext field="jmltarget" label="Jumlah Target" value="{{ $target!=null ? $target->jumlah_target : 0}}" icon="feather icon-file" right />
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <button class="btn btn-block btn-primary" id="updatetarget"><i class="feather icon-send"></i> Update Target</button>
        </div>
    </div>
</div>

<script>
    $(function() {

        function loaddetailtarget() {
            var kode_target = "{{$kode_target}}";
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/show'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailtarget").html(respond);
                }
            });

        }

        function updatetarget() {
            var kode_target = "{{$kode_target}}";
            var kode_produk = "{{$kode_produk}}";
            var id_karyawan = "{{$id_karyawan}}";
            var jmltarget = $("#jmltarget").val();
            $.ajax({
                type: 'POST'
                , url: '/targetkomisi/update'
                , data: {
                    _token: "{{csrf_token()}}"
                    , kode_target: kode_target
                    , kode_produk: kode_produk
                    , id_karyawan: id_karyawan
                    , jmltarget: jmltarget
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    if (respond == 0) {
                        swal("Success", "Berhasil Di Update", "success");
                    } else {
                        swal("Oops", "Gagal Di Update", "warning");
                    }
                    loaddetailtarget();
                    $("#mdlkoreksitarget").modal("hide");
                }
            });

        }
        $("#updatetarget").click(function(e) {
            e.preventDefault();
            updatetarget();

        });

    });

</script>
