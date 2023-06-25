<table class="table table-hover-animation">
    <thead>
        <tr>
            <th>No</th>
            <th>NIK</th>
            <th>Nama Karyawan</th>
            <th>Kode Dept</th>
            <th>Jabatan</th>
            <th>Grup</th>
            <th>#</th>
        </tr>
    </thead>
    <tbody id="loadlistkaryawan"></tbody>
</table>


<script>
    $(function() {
        function loadlistkaryawan() {
            var kode_libur = "{{ $kode_libur }}";
            var id_kantor = "{{ $id_kantor }}";
            $.ajax({
                type: 'POST'
                , url: '/harilibur/getlistkaryawan'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_libur: kode_libur
                    , id_kantor: id_kantor
                }
                , cache: false
                , success: function(respond) {
                    $("#loadlistkaryawan").html(respond);
                }
            });
        }

        loadlistkaryawan();
    });

</script>
