<div class="row">
    <div class="col-4">
        <div class="row">
            <div class="col-12">
                <table class="table table-bordered">
                    <tr>
                        <th>Kode Evaluasi</th>
                        <td>{{ $evaluasi->kode_evaluasi }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal</th>
                        <td>{{ DateToIndo2($evaluasi->tanggal) }}</td>
                    </tr>
                    <tr>
                        <th>Jam</th>
                        <td>{{ date('H:i', strtotime($evaluasi->jam)) }}</td>
                    </tr>
                    <tr>
                        <th>Tempat</th>
                        <td>{{ $evaluasi->tempat }}</td>
                    </tr>
                    <tr>
                        <th>Peserta</th>
                        <td>{!! $evaluasi->peserta !!}</td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <button class="btn btn-danger w-100" id="resetBtn"><i class="feather icon-refresh-ccw mr-1"></i>
                    Reset</button>
            </div>
        </div>
    </div>
    <div class="col-8">
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <input type="hidden" id="kode_agenda">
                    <x-inputtext label="Agenda" field="agenda" icon="feather icon-file" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <textarea name="hasil_pembahasan" id="hasil_pembahasan" class="form-control" placeholder="Hasil Pembahasan"
                        cols="30" rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <textarea name="action_plan" id="action_plan" class="form-control" placeholder="Action Plan" cols="30"
                        rows="3"></textarea>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <x-inputtext label="Due Date" field="due_date" icon="feather icon-calendar" datepicker />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <x-inputtext label="PIC" field="pic" icon="feather icon-calendar" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <select name="status" id="status" class="form-control">
                        <option value="">Status</option>
                        <option value="1">Open</option>
                        <option value="2">On Progress</option>
                        <option value="3">Closed</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row mt-1" id="Addbtn">
            <div class="col-12">
                <button class="btn btn-primary w-100" id="btnTambah"><i class="feather icon-plus mr-1"></i>
                    Tambahkan</button>
            </div>
        </div>
        <div class="row mt-1" id="Updatebtn">
            <div class="col-12">
                <button class="btn btn-info w-100" id="btnUpdate"><i class="feather icon-refresh-ccw mr-1"></i>
                    Update</button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Agenda</th>
                    <th>Hasil Pembahasan</th>
                    <th>Action Plan</th>
                    <th>PIC</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="load_detailevaluasi"></tbody>
        </table>
    </div>
</div>
<script src="{{ asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js') }}"></script>
<script>
    $(function() {

        $("#Updatebtn").hide();

        function loaddetailevaluasi() {
            var kode_evaluasi = "{{ $evaluasi->kode_evaluasi }}";
            $("#load_detailevaluasi").load('/worksheetom/' + kode_evaluasi + '/getdetailevaluasi');
        }

        loaddetailevaluasi();

        $("#resetBtn").click(function(e) {
            e.preventDefault();
            $("#Addbtn").show();
            $("#Updatebtn").hide();
            $("#agenda").focus();
            $("#kode_agenda").val("");
            $("#agenda").val("");
            $("#hasil_pembahasan").val("");
            $("#action_plan").val("");
            $("#due_date").val("");
            $("#pic").val("");
            $("#status").val("");
        });

        $("#btnTambah,#btnUpdate").click(function(e) {
            var kode_agenda = $("#kode_agenda").val();
            var agenda = $("#agenda").val();
            var hasil_pembahasan = $("#hasil_pembahasan").val();
            var action_plan = $("#action_plan").val();
            var due_date = $("#due_date").val();
            var pic = $("#pic").val();
            var status = $("#status").val();

            if (agenda == "") {
                swal({
                    title: 'Oops',
                    text: 'Agenda Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#agenda").focus();
                });
            } else if (hasil_pembahasan == "") {
                swal({
                    title: 'Oops',
                    text: 'Hasil Pembahasan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#hasil_pembahasan").focus();
                });
            } else if (action_plan == "") {
                swal({
                    title: 'Oops',
                    text: 'Hasil Pembahasan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#hasil_pembahasan").focus();
                });
            } else if (due_date == "") {
                swal({
                    title: 'Oops',
                    text: 'Hasil Pembahasan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#hasil_pembahasan").focus();
                });
            } else if (status == "") {
                swal({
                    title: 'Oops',
                    text: 'Hasil Pembahasan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#hasil_pembahasan").focus();
                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/worksheetom/storedetailevaluasi',
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_agenda: kode_agenda,
                        kode_evaluasi: "{{ $evaluasi->kode_evaluasi }}",
                        agenda: agenda,
                        hasil_pembahasan: hasil_pembahasan,
                        action_plan: action_plan,
                        due_date: due_date,
                        pic: pic,
                        status: status
                    },
                    cache: false,
                    success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Berhasil',
                                text: 'Peserta Berhasil DItambahkan !',
                                icon: 'success',
                                showConfirmButton: false
                            }).then(function() {
                                $("#agenda").focus();
                                $("#kode_agenda").val("");
                                $("#agenda").val("");
                                $("#hasil_pembahasan").val("");
                                $("#action_plan").val("");
                                $("#due_date").val("");
                                $("#pic").val("");
                                $("#status").val("");
                                loaddetailevaluasi();
                                $("#Addbtn").show();
                                $("#Updatebtn").hide();
                            });
                        } else {
                            swal({
                                title: 'Oops',
                                text: 'Peserta Gagal DItambahkan !',
                                icon: 'warning',
                                showConfirmButton: false
                            }).then(function() {
                                $("#agenda").focus();
                                $("#agenda").val("");
                                $("#kode_agenda").val("");
                                $("#hasil_pembahasan").val("");
                                $("#action_plan").val("");
                                $("#due_date").val("");
                                $("#pic").val("");
                                $("#status").val("");
                            });
                        }
                    }
                });
            }
        });




    });
</script>
