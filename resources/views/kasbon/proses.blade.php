<form action="/kasbon/{{ Crypt::encrypt($kasbon->no_kasbon) }}/storeproseskasbon" method="POST" id="frmKasbon">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <th>No. Kasbon</th>
                    <th>{{ $kasbon->no_kasbon }}</th>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ date("d-m-Y H:i",strtotime($kasbon->created_at)) }}</td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td>{{ $kasbon->nik }}</td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $kasbon->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $kasbon->nama_jabatan }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $kasbon->nama_dept }}</td>
                </tr>
                <tr>
                    <th>Jumlah Kasbon</th>
                    <td style="text-align: right">{{ rupiah($kasbon->jumlah_kasbon) }}</td>
                </tr>

                <tr>
                    <th>Jatuh Tempo</th>
                    <td>{{ DateToIndo2($kasbon->jatuh_tempo) }}</td>
                </tr>

            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="statusaksi" id="statusaksi" class="form-control">
                    <option value="">Status</option>
                    <option value="0">Pending</option>
                    <option value="1">Diterima</option>
                </select>
            </div>
        </div>
    </div>
    <div class="row" id="tgltransfer">
        <div class="col-12">
            <x-inputtext label="Tanggal Transfer" field="tgl_transfer" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row" id="bank">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="bankpengirim" name="bank">
                    <option value="">Debet Rekening</option>
                    @foreach ($bank as $d)
                    <option value="{{$d->kode_bank}}">{{$d->nama_bank}}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" id="btnSubmit">
                    <i class="feather icon-send"></i>
                    Proses
                </button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#bankpengirim").selectize();

        function diterima() {
            $("#tgltransfer").show();
            $("#bank").show();
        }

        function hidetanggal() {
            $("#tgltransfer").hide();
            $("#bank").hide();
        }

        hidetanggal();

        $("#statusaksi").change(function() {
            var status = $("#statusaksi").val();
            if (status == 1) {
                diterima();
            } else {
                hidetanggal();
            }
        });

        $("#frmKasbon").submit(function(e) {
            var status = $("#statusaksi").val();
            var tgl_transfer = $("#tgl_transfer").val();
            var bankpengirim = $("#bankpengirim").val();
            if (status == "") {
                swal({
                    title: 'Oops'
                    , text: 'Pilih Status Aksi!'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#statusaksi").focus();
                });
                return false;
            } else {
                if (status == 1) {
                    if (tgl_transfer == "") {
                        swal({
                            title: 'Oops'
                            , text: 'Tanggal Transfer Harus Diisi !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            $("#tgl_transfer").focus();
                        });
                        return false;
                    } else if (bankpengirim == "") {
                        swal({
                            title: 'Oops'
                            , text: 'Bank Harus Diisi !'
                            , icon: 'warning'
                            , showConfirmButton: false
                        }).then(function() {
                            $("#bankpengirim").focus();
                        });
                        return false;
                    } else {
                        $("#btnSubmit").prop('disabled', true);
                        return true;
                    }
                } else {
                    $("#btnSubmit").prop('disabled', true);
                    return true;
                }
            }
        });

    });

</script>
