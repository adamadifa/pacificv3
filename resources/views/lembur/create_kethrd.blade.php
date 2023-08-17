<form action="/lembur/{{ $lembur->kode_lembur }}/update_kethrd" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea name="keterangan_hrd" id="keterangan_hrd" class="form-control" cols="30" rows="10">{{ $lembur->keterangan_hrd }}</textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary w-100"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
