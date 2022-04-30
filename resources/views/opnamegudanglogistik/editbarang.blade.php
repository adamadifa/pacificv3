<form action="/opnamegudanglogistik/{{ Crypt::encrypt($detail->kode_opname_gl) }}/{{ Crypt::encrypt($detail->kode_barang) }}/updatebarang" method="POST">
    @csrf
    <div class="row">
        <div class="col-12">
            <table class="table">
                <tr>
                    <td>Kode Barang</td>
                    <td>{{ $detail->kode_barang }}</td>
                </tr>
                <tr>
                    <td>Nama Barang</td>
                    <td>{{ $detail->nama_barang }}</td>
                </tr>
            </table>
            <div class="row">
                <div class="col-12">
                    <x-inputtext label="Qty" field="qty" icon="feather icon-file" value="{{ desimal($detail->qty) }}" right />
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button type="submit" name="submit" class="btn btn-primary btn-block"><i class="fa fa-send mr-1"></i>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    var h = document.getElementById('harga');
    h.addEventListener('keyup', function(e) {
        h.value = formatRupiah(this.value, '');
        //alert(b);
    });

    var p = document.getElementById('qty');
    p.addEventListener('keyup', function(e) {
        p.value = formatRupiah(this.value, '');
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
