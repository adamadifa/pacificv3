@foreach ($detailtemp as $d)
    <tr>
        <td>{{ $d->kode_produk }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td class="text-right">{{ rupiah($d->jumlah) }}</td>
        <td>
            <a href="#" kodeproduk="{{ $d->kode_produk }}" class="danger hapus"><i class="feather icon-trash"></i></a>
        </td>
    </tr>
@endforeach

<script>
    $(function() {

        function tampilkanproduk() {
            $.ajax({
                type: 'GET',
                url: '/permintaanpengiriman/showtemp',
                cache: false,
                success: function(respond) {
                    $("#loadproduktemp").html(respond);
                }
            });
        }

        function hapusproduk(kode_produk) {
            $.ajax({
                type: 'POST',
                url: '/permintaanpengiriman/deletetemp',
                data: {
                    _token: "{{ csrf_token() }}",
                    kode_produk: kode_produk,
                },
                cache: false,
                success: function(respond) {
                    if (respond == 0) {
                        swal(
                            'Deleted!', 'Data Berhasil Dihapus', 'success'
                        )
                        tampilkanproduk();
                    } else {
                        swal(
                            'Failed!', 'Data Gagal Dihapus', 'danger'
                        )
                    }
                }
            });

        }

        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_produk = $(this).attr("kodeproduk");
            swal({
                title: `Anda Yakin Data ini Akan Dihapus ?`,
                text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    hapusproduk(kode_produk);
                }
            });
        });
    });
</script>
