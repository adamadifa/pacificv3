<form action="/ticket/store" method="POST" id="frmTicket">
    @csrf

    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <textarea name="keterangan" class="form-control" id="keterangan" cols="30" rows="10"></textarea>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
