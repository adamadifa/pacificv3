<table class="table table-hover-animation tabelbarang" style="width: 100%" id="tabelbarang">
    <thead class="thead-dark">
        <tr>
            <th>Kode Barang</th>
            <th>Nama Barang</th>
            <th>Satuan</th>
            <th>Jenis Barang</th>
            <th></th>
        </tr>
    </thead>
</table>
<script>
    $(function() {
        $('.tabelbarang').DataTable({
            processing: true
            , serverSide: true
            , ajax: '/barangpembelian/' + '{{ $kode_dept }}' + '/json', // memanggil route yang menampilkan data json
            bAutoWidth: false
            , bInfo: false
            , columns: [{ // mengambil & menampilkan kolom sesuai tabel database
                    data: 'kode_barang'
                    , name: 'kode_barang'
                }
                , {
                    data: 'nama_barang'
                    , name: 'nama_barang'
                }, {
                    data: 'satuan'
                    , name: 'satuan'
                }, {
                    data: 'jenis_barang'
                    , name: 'jenis_barang'
                }, {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }

            ],

        });

        $('.tabelbarang tbody').on('click', 'a', function() {
            var kode_barang = $(this).attr("kode_barang");
            var nama_barang = $(this).attr("nama_barang");
            var jenis_barang = $(this).attr("jenis_barang");
            $("#kode_barang").val(kode_barang);
            $("#nama_barang").val(nama_barang + " - (" + jenis_barang + ")");
            $("#mdlbarang").modal("hide");
        });

    });

</script>
