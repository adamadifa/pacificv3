<style>
    ul.ui-autocomplete {
        z-index: 1100;
    }
</style>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <tr>
                <th>Kode Program</th>
                <td>{{ $program->kode_program }}</th>
            </tr>
            <tr>
                <th>Tanggal</th>
                <td>{{ DateToIndo2($program->tanggal) }}</th>
            </tr>
            <tr>
                <th>Nama Program</th>
                <td>{{ $program->nama_program }}</td>
            </tr>
            <tr>
                <th>Produk</th>
                <td>
                    @php
                        $produk = unserialize($program->kode_produk);
                    @endphp

                    @foreach ($produk as $d)
                        {{ $d }},
                    @endforeach

                </td>
            </tr>
            <tr>
                <th>Jml Target</th>
                <td>{{ rupiah($program->jml_target) }}</td>
            </tr>
            <tr>
                <th>Periode</th>
                <td>
                    {{ date('d-m-Y', strtotime($program->dari)) }} s/d
                    {{ date('d-m-Y', strtotime($program->sampai)) }}
                </td>
            </tr>
            <tr>
                <th>Reward</th>
                <td>{{ $program->nama_reward }}</td>
            </tr>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-9">
        <div class="form-group">
            <x-inputtext field="kode_pelanggan" label="Ketikan Nama Pelanggan / Kode Pelangan " icon="fa fa-users" />
            <input type="hidden" id="kode_pelanggan_val" name="kode_pelanggan_val">
        </div>
    </div>
    <div class="col-3">
        <div class="form-group">
            <button class=" btn btn-primary" id="tambahpeserta"><i class="feather icon-plus mr-1"></i> Tambah</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kode Pelanggan</th>
                    <th>Nama Pelanggan</th>
                    <th>Cabang</th>
                    <th>Salesman</th>
                    <th>Realisasi</th>
                    <th>Sisa</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="loadpeserta"></tbody>
        </table>
    </div>
</div>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js') }}"></script>
<script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js') }}"></script>
<script>
    $(function() {
        $("#kode_pelanggan").autocomplete({
            source: function(request, response) {
                // Fetch data
                $.ajax({
                    url: "/getautocompletepelanggan",
                    type: 'post',
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}",
                        search: request.term,

                    },
                    success: function(data) {
                        response(data);
                    }
                });
            },
            select: function(event, ui) {
                $("#kode_pelanggan").val(ui.item.label);
                $("#kode_pelanggan_val").val(ui.item.val);
                var kode_pelanggan = ui.item.val;
                return false;
            }
        });


        function loadpeserta() {
            var kode_program = "{{ $program->kode_program }}";
            $("#loadpeserta").load('/worksheetom/' + kode_program + '/getpeserta');
        }

        loadpeserta();
        $("#tambahpeserta").click(function(e) {
            e.preventDefault();
            var kode_pelanggan = $("#kode_pelanggan_val").val();
            var kode_program = "{{ $program->kode_program }}";
            if (kode_pelanggan == "") {
                swal({
                    title: 'Oops',
                    text: 'Pelanggan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#kode_pelanggan").focus();
                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: '/worksheetom/storepeserta',
                    cache: false,
                    data: {
                        _token: "{{ csrf_token() }}",
                        kode_program: kode_program,
                        kode_pelanggan: kode_pelanggan
                    },
                    success: function(respond) {
                        if (respond == 0) {
                            swal({
                                title: 'Berhasil',
                                text: 'Peserta Berhasil DItambahkan !',
                                icon: 'success',
                                showConfirmButton: false
                            }).then(function() {
                                $("#kode_pelanggan").focus();
                                $("#kode_pelanggan").val("");
                                $("#kode_pelanggan_val").val("");
                            });
                            loadpeserta();
                        } else {
                            swal({
                                title: 'Oops',
                                text: 'Peserta Gagal DItambahkan !',
                                icon: 'warning',
                                showConfirmButton: false
                            }).then(function() {
                                $("#kode_pelanggan").focus();
                                $("#kode_pelanggan").val("");
                                $("#kode_pelanggan_val").val("");
                            });
                        }
                    }
                });
            }
        });
    });
</script>
