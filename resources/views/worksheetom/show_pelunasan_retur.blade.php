@foreach ($pelunasanretur as $d)
    @php
        $jmldus = floor($d->jumlah / $d->isipcsdus);
        $sisadus = $d->jumlah % $d->isipcsdus;

        if ($d->isipack == 0) {
            $jmlpack = 0;
            $sisapack = $sisadus;
        } else {
            $jmlpack = floor($sisadus / $d->isipcs);
            $sisapack = $sisadus % $d->isipcs;
        }

        $jmlpcs = $sisapack;
    @endphp
    <tr>
        <td>{{ $d->kode_produk }}</td>
        <td>{{ $d->nama_barang }}</td>
        <td class="text-center">{{ !empty($jmldus) ? $jmldus : '' }}</td>
        <td class="text-center">{{ !empty($jmlpack) ? $jmlpack : '' }}</td>
        <td class="text-center">{{ !empty($jmlpcs) ? $jmlpcs : '' }}</td>
        <td>{{ $d->no_dpb }}</td>
        <td>
            <a href="#" kode_barang="{{ $d->kode_barang }}" no_dpb="{{ $d->no_dpb }}" class="hapus"><i
                    class="feather icon-trash danger"></i></a>
        </td>
    </tr>
@endforeach
<script>
    $(function() {

        function loaddetailretur() {
            var no_retur_penj = "{{ $no_retur_penj }}";
            $.ajax({
                type: 'POST',
                url: '/worksheetom/showdetailretur',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_retur_penj: no_retur_penj
                },
                cache: false,
                success: function(respond) {
                    $("#no_retur_penj").text(no_retur_penj);
                    $("#loadretur").html(respond);
                }
            });
        }

        function loadpelunasan() {
            var no_retur_penj = "{{ $no_retur_penj }}";
            $.ajax({
                type: 'POST',
                url: '/worksheetom/showpelunasanretur',
                data: {
                    _token: "{{ csrf_token() }}",
                    no_retur_penj: no_retur_penj,
                },
                cache: false,
                success: function(respond) {
                    loaddetailretur();
                    $("#loadpelunasanretur").html(respond);
                }
            });
        }
        $(".hapus").click(function(e) {
            e.preventDefault();
            var no_retur_penj = "{{ $no_retur_penj }}";
            var kode_barang = $(this).attr("kode_barang");
            var no_dpb = $(this).attr("no_dpb");

            swal({
                    title: `Anda Yakin Data ini Akan Dihapus ?`,
                    text: "Jika dihapus Data Ini Akan Hilang Dari Keranjang",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'POST',
                            url: '/worksheetom/deletepelunasanretur',
                            data: {
                                _token: "{{ csrf_token() }}",
                                no_retur_penj: no_retur_penj,
                                kode_barang: kode_barang,
                                no_dpb: no_dpb
                            },
                            cache: false,
                            success: function(respond) {

                                if (respond == 0) {
                                    swal('Deleted!', 'Data Berhasil Dihapus', 'success')
                                } else {
                                    swal('Oops!', 'Data Gagal Dihapus', 'error')
                                }

                                loadpelunasan();
                            }
                        });
                    }
                });
        });
    });
</script>
