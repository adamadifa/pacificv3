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

        function loadBpbj() {
            var kode_produk = $("#kode_produk").val();
            $("#loadbpbj").load('/bpbj/' + kode_produk + '/showtemp');
            cekbpbjtemp();
        }

        function buatnomorbpbj() {
            var tgl_mutasi_produksi = $("#tgl_mutasi_produksi").val();
            var kode_produk = $("#kode_produk").val();

            $.ajax({
                type: 'POST'
                , url: '/bpbj/buat_nomor_bpbj'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , tgl_mutasi_produksi: tgl_mutasi_produksi
                    , kode_produk: kode_produk
                }
                , cache: false
                , success: function(respond) {
                    console.log(respond);
                    $("#no_bpbj").val(respond);
                }

            });

        }

        function cekbpbjtemp() {
            var kode_produk = $("#kode_produk").val();
            $.ajax({
                type: 'POST'
                , url: '/bpbj/cekbpbjtemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_produk: kode_produk
                }
                , cache: false
                , success: function(respond) {
                    $("#cekbpbjtemp").val(respond);
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
            buatnomorbpbj();
            loadBpbj();

        });


    });

</script>
