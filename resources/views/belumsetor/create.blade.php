<form action="/belumsetor/store" method="POST" id="frmBelumsetor">
    @csrf
    <input type="hidden" id="cektemp">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" field="kode_saldobs" icon="feather icon-credit-card" disabled />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group" id="pilihcabang">
                <select name="kode_cabang" id="kode_cabang" class="form-control ">
                    <option value="">Pilih Cabang</option>
                    @foreach ($cabang as $d)
                    <option value="{{ $d->kode_cabang }}">{{ $d->nama_cabang }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <select class="form-control" id="bulan" name="bulan">
                    <option value="">Bulan</option>
                    <?php
                $bulanini = date("m");
                for ($i = 1; $i < count($bulan); $i++) {
                ?>
                    <option value="<?php echo $i; ?>"><?php echo $bulan[$i]; ?></option>
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
                <select class="form-control" id="tahun" name="tahun">
                    <option value="">Tahun</option>
                    <?php
                $tahunmulai = 2020;
                for ($thn = $tahunmulai; $thn <= date('Y'); $thn++) {
                ?>
                    <option value="<?php echo $thn; ?>"><?php echo $thn; ?></option>
                    <?php
                }
                ?>
                </select>
            </div>
        </div>
    </div>
    <hr>
    <div class="row" id="pilihsalesman">
        <div class="col-lg-4 col-sm-12">
            <div class="form-group  ">
                <select name="id_karyawan" id="id_karyawan" class="form-control">
                    <option value="">Pilih Salesman</option>
                </select>
            </div>
        </div>
        <div class="col-lg-5 col-sm-12">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" right />
        </div>
        <div class="col-lg-3 col-sm-12">
            <div class="form-group">
                <a href="#" id="tambah" class="btn btn-primary"><i class="fa fa-plus"></i></a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <table class="table table-hover-animation">
                <thead class="thead-dark">
                    <tr>
                        <th>ID Salesman</th>
                        <th>Salesman</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="loadbelumsetortemp"></tbody>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-gropu">
                <button type="submit" class="btn btn-primary btn-block">Submit</button>
            </div>
        </div>
    </div>
</form>
<script>
    $(function() {
        $("#jumlah").maskMoney();

        function loadsalesmancabang(kode_cabang) {
            $.ajax({
                type: 'POST'
                , url: '/salesman/getsalescab'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , kode_cabang: kode_cabang
                    , type: 1
                }
                , cache: false
                , success: function(respond) {
                    $("#id_karyawan").html(respond);
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

        $("#kode_cabang").change(function() {
            loadbelumsetortemp();
        });

        $("#frmBelumsetor").find("#bulan").change(function() {
            loadbelumsetortemp();
        });

        $("#frmBelumsetor").find("#tahun").change(function() {
            loadbelumsetortemp();
        });

        $("#tambah").click(function(e) {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmBelumsetor").find("#bulan").val();
            var tahun = $("#frmBelumsetor").find("#tahun").val();
            var id_karyawan = $("#id_karyawan").val();
            var jumlah = $("#jumlah").val();
            if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#kode_cabang').focus();
                });
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#bulan').focus();
                });
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tahun Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#tahun').focus();
                });
            } else if (id_karyawan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Salesman Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#id_karyawan').focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#jumlah').focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/belumsetor/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , id_karyawan: id_karyawan
                        , jumlah: jumlah
                        , bulan: bulan
                        , tahun: tahun
                    }
                    , cache: false
                    , success: function(respond) {
                        loadbelumsetortemp();
                    }
                });
            }

        });

        $("#kode_cabang").change(function() {
            var kode_cabang = $(this).val();
            loadsalesmancabang(kode_cabang);
        });

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
        $("#frmBelumsetor").submit(function() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmBelumsetor").find("#bulan").val();
            var tahun = $("#frmBelumsetor").find("#tahun").val();
            var cek_temp = $("#cektemp").val();
            //alert(cek_temp);
            if (cek_temp == "" || cek_temp == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Data Masih Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#kode_cabang').focus();
                });
                return false;
            } else if (kode_cabang == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cabang Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#kode_cabang').focus();
                });
                return false;
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmBelumsetor").find("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmBelumsetor").find("#tahun").focus();
                });
                return false;
            }
        });
    });

</script>
