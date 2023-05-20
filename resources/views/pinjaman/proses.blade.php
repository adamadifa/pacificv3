<form action="/pinjaman/{{ Crypt::encrypt($pinjaman->no_pinjaman) }}/storeprosespinjaman" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <th>No. Pinjaman</th>
                    <th>{{ $pinjaman->no_pinjaman }}</th>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ date("d-m-Y H:i",strtotime($pinjaman->created_at)) }}</td>
                </tr>
                <tr>
                    <th>NIK</th>
                    <td>{{ $pinjaman->nik }}</td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $pinjaman->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $pinjaman->nama_jabatan }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $pinjaman->nama_dept }}</td>
                </tr>
                <tr>
                    <th>Jumlah Pinjaman</th>
                    <td style="text-align: right">{{ rupiah($pinjaman->jumlah_pinjaman) }}</td>
                </tr>
                <tr>
                    <th>Angsuran</th>
                    <td>{{ $pinjaman->angsuran }} Bulan</td>
                </tr>
                <tr>
                    <th>Angsuran/Bulan</th>
                    <td style="text-align: right">{{ rupiah($pinjaman->jumlah_angsuran) }}</td>
                </tr>
                <tr>
                    <th>Mulai Cicilan</th>
                    <td>{{ DateToIndo2($pinjaman->mulai_cicilan) }}</td>
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
                    <option value="2">Ditolak</option>
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
                    <option value="">Bank Penerima</option>
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

    });

</script>
