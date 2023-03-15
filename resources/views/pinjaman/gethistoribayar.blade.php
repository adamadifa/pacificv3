@php
$total = 0;
@endphp
@foreach ($histori as $d)
@php
$total+= $d->jumlah;
@endphp
<tr>
    <td>{{ $d->no_bukti }}</td>
    <td>{{ date("d-m-Y",strtotime($d->tgl_bayar))}}</td>
    <td style="text-align: right">{{ rupiah($d->jumlah) }}</td>
    <td>{{ $d->keterangan }}</td>
    <td>{{ $d->name }}</td>
    <td>
        @if ($loop->first)
        @if (empty($d->kode_potongan))
        <a href="#" no_bukti="{{ $d->no_bukti }}" class="delete-confirm ml-1">
            <i class="feather icon-trash danger"></i>
        </a>
        @endif
        @endif
    </td>
</tr>
@endforeach
<tr style="font-weight:bold">
    <td colspan="2">TOTAL</td>
    <td style="text-align: right" id="totalbayar">{{ rupiah($total) }}</td>
    <td colspan="3"></td>
</tr>
<script>
    $(function() {
        function convertToRupiah(number) {
            if (number) {
                var rupiah = "";
                var numberrev = number
                    .toString()
                    .split("")
                    .reverse()
                    .join("");
                for (var i = 0; i < numberrev.length; i++)
                    if (i % 3 == 0) rupiah += numberrev.substr(i, 3) + ".";
                return (
                    rupiah
                    .split("", rupiah.length - 1)
                    .reverse()
                    .join("")
                );
            } else {
                return number;
            }
        }

        function loadrencanabayar() {
            var no_pinjaman = "{{ $no_pinjaman }}";
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/getrencanabayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadrencanabayar").html(respond);
                }
            });
        }

        function loadhistoribayar() {
            var no_pinjaman = "{{ $no_pinjaman }}";
            $.ajax({
                type: 'POST'
                , url: '/pinjaman/gethistoribayar'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , no_pinjaman: no_pinjaman
                }
                , cache: false
                , success: function(respond) {
                    $("#loadhistoribayar").html(respond);
                    loadsisatagihan();
                }
            });
        }

        function loadsisatagihan() {
            var jml_pinjaman = $("#jmlpinjaman").text();
            var totalbayar = $("#totalbayar").text();

            var jp = parseInt(jml_pinjaman.replace(/\./g, ''));
            var tb = parseInt(totalbayar.replace(/\./g, ''));

            var sisa = jp - tb;
            $("#jmlbayar").text(convertToRupiah(tb));
            $("#sisatagihan").text(convertToRupiah(sisa));

        }




        $('.delete-confirm').click(function(event) {
            event.preventDefault();

            swal({
                    title: `Are you sure you want to delete this record?`
                    , text: "If you delete this, it will be gone forever."
                    , icon: "warning"
                    , buttons: true
                    , dangerMode: true
                , })
                .then((willDelete) => {
                    if (willDelete) {
                        var no_bukti = $(this).attr('no_bukti');
                        $.ajax({
                            type: 'POST'
                            , url: '/pembayaranpinjaman/delete'
                            , data: {
                                _token: "{{ csrf_token() }}"
                                , no_bukti: no_bukti
                            }
                            , cache: false
                            , success: function(respond) {
                                loadrencanabayar();
                                loadhistoribayar();
                            }
                        });
                    }
                });
        });

    });

</script>
