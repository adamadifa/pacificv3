<table class="table" id="tabelbarang">
    <thead>
        <tr>
            <th>Kode Produk</th>
            <th>Nama Barang</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($barang as $d)
        <tr>
            <td>{{ $d->kode_produk }}</td>
            <td>{{ $d->nama_barang }}</td>
            <td><a href="#" class="pilihbarang" kode_produk="{{ $d->kode_produk }}" nama_barang="{{ $d->nama_barang }}"><i class="feather icon-external-link success"></i></a></td>
        </tr>
        @endforeach
    </tbody>
</table>


<script>
    $(function() {

        function loadFsthp() {
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            $("#loadfsthp").load('/fsthp/' + kode_produk + '/' + unit + '/' + shift + '/showtemp');
            cekfsthptemp();
        }

        function cekfsthptemp() {
            var kode_produk = $("#kode_produk").val();
            var unit = $("#unit").val();
            var shift = $("#shift").val();
            $.ajax({
                type: 'POST'
                , url: '/fsthp/cekfsthptemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_produk: kode_produk
                    , unit: unit
                    , shift: shift
                }
                , cache: false
                , success: function(respond) {
                    $("#cekfsthptemp").val(respond);
                }
            });
        }

        function buatnomorfsthp() {
            var tgl_mutasi_produksi = $("#tgl_mutasi_produksi").val();
            var kode_produk = $("#kode_produk").val();
            var shift = $("#shift").val();
            $.ajax({
                type: 'POST'
                , url: '/fsthp/buat_nomor_fsthp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_mutasi_produksi: tgl_mutasi_produksi
                    , kode_produk: kode_produk
                    , shift: shift
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#no_fsthp").val(respond);
                }

            });
        }


        $("#tabelbarang").DataTable();
        $("#tabelbarang").find(".pilihbarang").click(function(e) {
            e.preventDefault();
            var kode_produk = $(this).attr('kode_produk');
            var nama_barang = $(this).attr('nama_barang');
            $("#kode_produk").val(kode_produk);
            $("#nama_barang").val(nama_barang);
            $("#mdlpilihbarang").modal("hide");
            buatnomorfsthp();
            loadFsthp();

        });


    });

</script>
