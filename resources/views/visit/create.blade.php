<form action="{{ route('visit.store') }}" id="frmvisit" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Tanggal Visit" field="tgl_visit" datepicker icon="feather icon-calendar" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="no_fak_penj" id="no_fak_penj" class="form-control" placeholder="No Faktur">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="nominal" id="nominal" class="form-control" placeholder="Nominal">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="hasil_konfirmasi" id="hasil_konfirmasi" class="form-control"
                    placeholder="Hasil Konfirmasi">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="catatan" id="catatan" class="form-control" placeholder="Catatan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="saran" id="saran" class="form-control" placeholder="Saran">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="action" id="action" class="form-control" placeholder="Action KA Admin">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" id="simpanvisit"><i
                        class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {

        function loadvisit() {
            $.ajax({
                type: 'POST',
                url: '{{ route('visit.show') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                cache: false,
                success: function(respond) {
                    $("#loadvisit").html(respond);
                }
            });
        }

        $("#frmvisit").submit(function(e) {
            var tgl_visit = $("#tgl_visit").val();
            var no_fak_penj = $("#no_fak_penj").val();
            var nominal = $("#nominal").val();
            var hasil_konfirmasi = $("#hasil_konfirmasi").val();
            var catatan = $("#catatan").val();
            var action = $("#action").val();
            var saran = $("#saran").val();
            if (tgl_visit == "") {
                swal({
                    title: 'Oops',
                    text: 'Tanggal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tgl_visit").focus();
                });
                return false;
            } else if (no_fak_penj == "") {
                swal({
                    title: 'Oops',
                    text: 'No Faktur Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#no_fak_penj").focus();
                });
                return false;
            } else if (nominal == "") {
                swal({
                    title: 'Oops',
                    text: 'Nominal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nominal").focus();
                });
                return false;
            } else if (hasil_konfirmasi == "") {
                swal({
                    title: 'Oops',
                    text: 'Hasil Konfirmasi Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#hasil_konfirmasi").focus();
                });
                return false;
            }
        });

    });
</script>
