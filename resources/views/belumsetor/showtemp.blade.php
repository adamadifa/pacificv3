@foreach ($detailtemp as $d)
<tr>
    <td>{{ $d->id_karyawan }}</td>
    <td>{{ $d->nama_karyawan }}</td>
    <td class="text-right">{{ rupiah($d->jumlah) }}</td>
    <td>
        <a href="#" data-id="{{ $d->id }}" class="danger hapus"><i class="feather icon-trash"></i></a>
    </td>
</tr>
@endforeach

<script>
    $(function() {

        function cektemp() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmBelumsetor").find("#bulan").val();
            var tahun = $("#frmBelumsetor").find("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/belumsetor/cektemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang
                    , bulan: bulan
                    , tahun: tahun
                }
                , cache: false
                , success: function(respond) {
                    $("#cektemp").val(respond);
                }
            });

        }

        function loadbelumsetortemp() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmBelumsetor").find("#bulan").val();
            var tahun = $("#frmBelumsetor").find("#tahun").val();
            $("#loadbelumsetortemp").load('/belumsetor/' + kode_cabang + '/' + bulan + '/' + tahun + '/showtemp');
            cektemp();
        }

        function hapusproduk(id) {
            $.ajax({
                type: 'POST'
                , url: '/belumsetor/deletetemp'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id: id
                , }
                , cache: false
                , success: function(respond) {
                    if (respond == 0) {
                        swal(
                            'Deleted!', 'Data Berhasil Dihapus', 'success'
                        )
                        loadbelumsetortemp();
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
            var id = $(this).attr("data-id");
            swal({
                title: `Anda Yakin Data ini Akan Dihapus ?`
                , text: "Jika dihapus Data Ini Akan Hilang "
                , icon: "warning"
                , buttons: true
                , dangerMode: true
            , }).then((willDelete) => {
                if (willDelete) {
                    hapusproduk(id);
                }
            });
        });
    });

</script>
