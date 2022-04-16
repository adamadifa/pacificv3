<table class="table table-hover-animation tabelsupplier" style="width:100% !important" id="tabelsupplier">
    <thead class="thead-dark">
        <tr>
            <th style="width:30%">Kode Supplier</th>
            <th>Nama Supplier</th>
            <th></th>
        </tr>
    </thead>
</table>
<script>
    $(function() {
        $('.tabelsupplier').DataTable({
            processing: true
            , serverSide: true
            , ajax: '/supplier/json', // memanggil route yang menampilkan data json
            bAutoWidth: false
            , bInfo: false
            , columns: [{ // mengambil & menampilkan kolom sesuai tabel database
                    data: 'kode_supplier'
                    , name: 'kode_supplier'
                }
                , {
                    data: 'nama_supplier'
                    , name: 'nama_supplier'
                }, {
                    data: 'action'
                    , name: 'action'
                    , orderable: false
                    , searchable: false
                }

            ],

        });


        function loaddetailkontrabontemp() {
            var kode_supplier = $("#kode_supplier").val();
            $.ajax({
                type: 'GET'
                , url: '/kontrabon/showtemp'
                , data: {
                    kode_supplier: kode_supplier
                }
                , cache: false
                , success: function(respond) {
                    $("#loaddetailkontrabon").html(respond);
                    loadtotal();
                }
            });

        }

        $('.tabelsupplier tbody').on('click', 'a', function() {
            var kode_supplier = $(this).attr("kode_supplier");
            var nama_supplier = $(this).attr("nama_supplier");
            $("#kode_supplier").val(kode_supplier);
            $("#nama_supplier").val(nama_supplier);
            $("#mdlsupplier").modal("hide");
            loaddetailkontrabontemp();
        });
    });

</script>
