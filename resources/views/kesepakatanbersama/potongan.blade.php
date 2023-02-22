<div class="row">
    <div class="col-8">
        <div class="form-group">
            <input type="text" class="form-control" name="keterangan" autocomplete="off" id="keterangan" placeholder="Keterangan">
        </div>
    </div>
    <div class="col-4">
        <div class="form-group">
            <input type="text" class="form-control text-right" autocomplete="off" name="jumlah" id="jumlah" placeholder="Jumlah">
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="form-group">
            <button class="btn btn-primary btn-block" id="tambahpotongan"><i class="feather icon-plus mr-1"></i>Tambah</button>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>No.</th>
                    <th>Keterangan</th>
                    <th>Jumlah</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="loadpotongan">

            </tbody>
        </table>
    </div>
</div>

<script>
    $(function() {
        function loadpotongan() {
            var no_kb = "{{ $no_kb }}";
            $.ajax({
                type: 'POST'
                , url: '/kesepakatanbersama/getpotongan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_kb: no_kb
                }
                , cache: false
                , success: function(respond) {
                    $("#loadpotongan").html(respond);
                }
            });
        }

        loadpotongan();
        $("#jumlah").maskMoney();
        $("#tambahpotongan").click(function(e) {
            e.preventDefault();
            var keterangan = $("#keterangan").val();
            var jumlah = $("#jumlah").val();
            var no_kb = "{{ $no_kb }}";

            if (keterangan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Keterangan Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
                return false;
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
                return false;
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/kesepakatanbersama/storepotongan'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , keterangan: keterangan
                        , jumlah: jumlah
                        , no_kb: no_kb
                    }
                    , cache: false
                    , success: function(respond) {
                        if (respond == 0) {

                            swal({
                                title: 'Success'
                                , text: 'Data Berhasil Disimpan!'
                                , icon: 'success'
                                , showConfirmButton: false
                            }).then(function() {
                                loadpotongan();
                                $("#keterangan").focus();
                                $("#keterangan").val("");
                                $("#jumlah").val("");
                            });
                        }
                    }
                });
            }
        });
    });

</script>
