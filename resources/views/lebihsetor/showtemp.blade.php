@foreach ($detailtemp as $d)
<tr>
    <td>{{ date("d-m-Y",strtotime($d->tanggal_disetorkan)) }}</td>
    <td>{{ $d->nama_bank }}</td>
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
            var bulan = $("#frmlebihSetor").find("#bulan").val();
            var tahun = $("#frmlebihSetor").find("#tahun").val();
            $.ajax({
                type: 'POST'
                , url: '/lebihsetor/cektemp'
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

        function loadlebihsetortemp() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmlebihSetor").find("#bulan").val();
            var tahun = $("#frmlebihSetor").find("#tahun").val();
            $("#loadlebihsetortemp").load('/lebihsetor/' + kode_cabang + '/' + bulan + '/' + tahun + '/showtemp');
            cektemp();
        }

        function hapusproduk(id) {
            $.ajax({
                type: 'POST'
                , url: '/lebihsetor/deletetemp'
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
                        loadlebihsetortemp();
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
