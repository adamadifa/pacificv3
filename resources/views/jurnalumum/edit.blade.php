<form action="/jurnalumum/{{ Crypt::encrypt($jurnalumum->kode_jurnal) }}/update" method="post" id="frmjurnalumum">
    @csrf
    <table class="table">
        <tr>
            <td>Departemen /Cabang</td>
            <td>{{ $jurnalumum->nama_dept }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ DateToIndo2($jurnalumum->tanggal) }}</td>
        </tr>
    </table>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="kode_akun" id="kode_akun" class="form-control select2">
                    <option value="">Pilih Akun</option>
                    @foreach ($coa as $d)
                    <option {{ $jurnalumum->kode_akun == $d->kode_akun ? 'selected' : '' }} value="{{ $d->kode_akun }}">{{ $d->kode_akun }} {{ $d->nama_akun }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="keterangan" label="Keterangan" value="{{ $jurnalumum->keterangan }}" icon="feather icon-file" />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <x-inputtext field="jumlah" label="Jumlah" value="{{ rupiah($jurnalumum->jumlah) }}" right icon="feather icon-file" />
        </div>
    </div>
    <div class="row" id="tombolsimpan">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>
<script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script src="{{asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
<script>
    var h = document.getElementById('jumlah');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    /* Fungsi formatRupiah */
    function formatRupiah(angka, prefix) {
        var number_string = angka.replace(/[^,\d-]/g, '').toString()
            , split = number_string.split(',')
            , sisa = split[0].length % 3
            , rupiah = split[0].substr(0, sisa)
            , ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // tambahkan titik jika yang di input sudah menjadi angka ribuan
        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? rupiah : '');
    }

    function convertToRupiah(angka) {
        var rupiah = '';
        var angkarev = angka.toString().split('').reverse().join('');
        for (var i = 0; i < angkarev.length; i++)
            if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
        return rupiah.split('', rupiah.length - 1).reverse().join('');
    }

</script>
