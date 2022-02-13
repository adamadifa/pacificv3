<form id="frmUraiananalisa" method="POST" action="/limitkredit/store_uraiananalisa">
    @csrf
    <input type="hidden" name="no_pengajuan" value="{{ $no_pengajuan }}">
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea class="form-control" id="uraian_analisa" name="uraian_analisa" rows="10">@if ($uraian_analisa != null) {{ $uraian_analisa->uraian_analisa }} @endif</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary btn-block"><i class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $("#frmUraiananalisa").submit(function() {
            var uraian_analisa = $("#uraian_analisa").val();
            if (uraian_analisa == "") {
                swal({
                    title: 'Oops'
                    , text: 'Uraian Analisa Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#uraian_analisa").focus();
                });
                return false;
            } else {
                return true;
            }
        });
    });

</script>
