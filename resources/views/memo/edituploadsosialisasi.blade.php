<form method="POST" action="/memo/uploadsosialisasi/update" id="frmUploadsosialisasi">
    @csrf
    <input type="hidden" value="{{ $sosialisasi->id_sosialisasi }}" name="id_sosialisasi">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea name="link" id="link" cols="30" rows="10" class="form-control" placeholder="Link Daftar Hadir Sosialisasi">{{ $sosialisasi->link }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Update</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $("#frmUploadsosialisasi").submit(function(e) {
            var link = $("#link").val();
            if (link == "") {
                swal({
                    title: 'Oops'
                    , text: 'Link  Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#link").focus();
                });
                return false;
            }
        });
    });

</script>
