@foreach ($detail as $d)
@php
$total = $d->qty * $d->harga;
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->kode_barang }}</td>
    <td>{{ $d->nama_barang }}</td>
    <td>{{ strtoupper($d->satuan) }}</td>
    <td>{{ $d->keterangan }}</td>
    <td class="text-right">{{ desimal($d->qty) }}</td>
    <td class="text-right">{{ desimal($d->harga) }}</td>
    <td class="text-right">{{ desimal($total) }}</td>
    <td>
        <a href="#" kode_barang="{{ $d->kode_barang }}" no_urut="{{ $d->no_urut }}" class="hapus"><i class="feather icon-trash danger"></i></a>
    </td>
</tr>
@endforeach
<script>
    $(function() {

        function cektemp() {
            $.ajax({
                type: 'POST'
                , url: '/pemasukangudanglogistik/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });
        }


        function loaddetail() {
            $("#loaddetailPemasukan").load("/pemasukangudanglogistik/showtemp");
            cektemp();
        }


        $(".hapus").click(function(e) {
            e.preventDefault();
            var kode_barang = $(this).attr("kode_barang");
            var no_urut = $(this).attr("no_urut");
            event.preventDefault();
            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`
                    , text: "Jika dihapus Data Ini Akan Hilang "
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST'
                            , url: '/pemasukangudanglogistik/deletetemp'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , kode_barang: kode_barang
                                , no_urut: no_urut
                            }
                            , cache: false
                            , success: function(respond) {
                                if (respond == 0) {
                                    swal(
                                        'Deleted!'
                                        , 'Data Berhasil Dihapus'
                                        , 'success'
                                    )
                                    loaddetail();
                                } else {
                                    swal(
                                        'Deleted!'
                                        , 'Data Gagal Dihapus'
                                        , 'danger'
                                    )
                                }
                            }
                        });
                    }
                });
        });
    });

</script>
