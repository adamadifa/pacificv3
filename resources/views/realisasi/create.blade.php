<form action="{{ route('realisasi.store') }}" id="frmrealisasi" enctype="multipart/form-data" method="POST"
    autocomplete="off">
    @csrf
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="bulan" id="bulan" class="form-control">
                    <option value="">Bulan</option>
                    <?php
                    $bl = date("m");
                    for ($i = 1; $i < count($bln); $i++) {
                    ?>
                    <option <?php if ($bl == $i) {
                        echo 'selected';
                    } ?> value="<?php echo $i; ?>"><?php echo $bln[$i]; ?>
                    </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select name="tahun" id="tahun" class="form-control">
                    <option value="">Tahun</option>
                    <?php
                    $tahun = date("Y");
                    $tahunmulai = 2021;
                    for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                    ?>
                    <option <?php if ($tahun == $thn) {
                        echo 'selected';
                    } ?> value="<?php echo $thn; ?>"><?php echo $thn; ?>
                    </option>
                    <?php
                    }
                    ?>
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="im" id="im" class="form-control" placeholder="Internam Memo">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="ajuan" id="ajuan" class="form-control" placeholder="Ajuan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="hidden" name="kode_pelanggan" id="kode_pelanggan" class="form-control"
                    placeholder="Nama Pelanggan">
                <input type="text" name="nama_pelanggan" id="nama_pelanggan" class="form-control"
                    placeholder="Nama Pelanggan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="number" name="nominal" id="nominal" class="form-control money" placeholder="Nominal">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="keterangan" id="keterangan" class="form-control" placeholder="Keterangan">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <input type="text" name="bentuk_hadiah" id="bentuk_hadiah" class="form-control"
                    placeholder="Bentuk Hadiah">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button class="btn btn-primary btn-block" type="submit" id="simpanrealisasi"><i
                        class="feather icon-send"></i> Submit</button>
            </div>
        </div>
    </div>
</form>
<div class="modal fade text-left" id="mdlpelanggan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel18"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document"
        style="max-width: 960px">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="myModalLabel18">Data Pelanggan</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <table class="table table-hover-animation tabelpelanggan" style="width:100% !important"
                    id="tabelpelanggan">
                    <thead class="thead-dark">
                        <tr>
                            <th>Kode Pelanggan</th>
                            <th>Nama Pelanggan</th>
                            <th>Pasar</th>
                            <th>Salesman</th>
                            <th>Kode Cabang</th>
                            <th></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(function() {

        function loadrealisasi() {
            $.ajax({
                type: 'POST',
                url: '{{ route('realisasi.show') }}',
                data: {
                    _token: "{{ csrf_token() }}",
                },
                cache: false,
                success: function(respond) {
                    $("#loadrealisasi").html(respond);
                }
            });
        }

        $("#frmrealisasi").submit(function(e) {
            var im = $("#im").val();
            var nominal = $("#nominal").val();
            var hasil_konfirmasi = $("#hasil_konfirmasi").val();
            var keterangan = $("#keterangan").val();
            var action = $("#action").val();
            var saran = $("#saran").val();
            if (bulan == "") {
                swal({
                    title: 'Oops',
                    text: 'Bulan Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops',
                    text: 'Tahun Harus Dipilih !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#tahun").focus();
                });
                return false;
            } else if (im == "") {
                swal({
                    title: 'Oops',
                    text: 'IM Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#im").focus();
                });
                return false;
            } else if (nominal == "") {
                swal({
                    title: 'Oops',
                    text: 'Nominal Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#nominal").focus();
                });
                return false;
            } else if (keterangan == "") {
                swal({
                    title: 'Oops',
                    text: 'Keterangan Harus Diisi !',
                    icon: 'warning',
                    showConfirmButton: false
                }).then(function() {
                    $("#keterangan").focus();
                });
                return false;
            }
        });

        $('.tabelpelanggan').DataTable({
            processing: true,
            serverSide: true,
            ajax: '/pelanggan/json', // memanggil route yang menampilkan data json
            bAutoWidth: false

                ,
            columns: [{ // mengambil & menampilkan kolom sesuai tabel database
                    data: 'kode_pelanggan',
                    name: 'kode_pelanggan'
                }, {
                    data: 'nama_pelanggan',
                    name: 'nama_pelanggan'
                }, {
                    data: 'pasar',
                    name: 'pasar'
                }, {
                    data: 'nama_karyawan',
                    name: 'karyawan.nama_karyawan'
                }, {
                    data: 'kode_cabang',
                    name: 'kode_cabang'
                }, {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }

            ],

        });

        $('.tabelpelanggan tbody').on('click', 'a', function() {

            var kode_pelanggan = $(this).attr("kode_pelanggan");
            var nama_pelanggan = $(this).attr("nama_pelanggan");
            $("#nama_pelanggan").val(nama_pelanggan);
            $("#kode_pelanggan").val(kode_pelanggan);
            $("#mdlpelanggan").modal("hide");
        });

        $('#nama_pelanggan').focus(function(e) {
            e.preventDefault();
            $('#mdlpelanggan').modal({
                backdrop: 'static',
                keyboard: false
            });
        });

    });
</script>
