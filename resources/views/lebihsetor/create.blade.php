<form action="/lebihsetor/store" method="POST" id="frmlebihSetor">
    @csrf
    <input type="hidden" id="cektemp">
    <div class="row">
        <div class="col-12">
            <x-inputtext label="Auto" field="kode_ls" icon="feather icon-credit-card" disabled />
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
    <div class="row">
        <div class="col-lg-4 col-sm-12">
            <x-inputtext label="Tanggal" field="tanggal_disetorkan" icon="feather icon-calendar" datepicker />
        </div>
        <div class="col-lg-3 col-sm-12">
            <select name="kode_bank" id="kode_bank" class="form-control">
                <option value="">Pilih Bank</option>
                @foreach ($bank as $d)
                <option value="{{ $d->kode_bank }}">{{ $d->nama_bank }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 col-sm-12">
            <x-inputtext label="Jumlah" field="jumlah" icon="feather icon-file" right />
        </div>

        <div class="col-lg-2 col-sm-12">
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
                        <th>Tanggal</th>
                        <th>Bank</th>
                        <th>Jumlah</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="loadlebihsetortemp"></tbody>
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
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#jumlah").maskMoney();

        function loadlebihsetortemp() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmlebihSetor").find("#bulan").val();
            var tahun = $("#frmlebihSetor").find("#tahun").val();
            $("#loadlebihsetortemp").load('/lebihsetor/' + kode_cabang + '/' + bulan + '/' + tahun + '/showtemp');
            cektemp();
        }

        $("#kode_cabang").change(function() {
            loadlebihsetortemp();
        });

        $("#frmlebihSetor").find("#bulan").change(function() {
            loadlebihsetortemp();
        });

        $("#frmlebihSetor").find("#tahun").change(function() {
            loadlebihsetortemp();
        });

        $("#tambah").click(function(e) {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmlebihSetor").find("#bulan").val();
            var tahun = $("#frmlebihSetor").find("#tahun").val();
            var tanggal_disetorkan = $("#tanggal_disetorkan").val();
            var kode_bank = $("#kode_bank").val();
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
            } else if (tanggal_disetorkan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#tanggal_disetorkan').focus();
                });
            } else if (kode_bank == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bank !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#kode_bank').focus();
                });
            } else if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#jumlah').focus();
                });
            } else {
                $.ajax({
                    type: 'POST'
                    , url: '/lebihsetor/storetemp'
                    , data: {
                        _token: "{{ csrf_token() }}"
                        , kode_bank: kode_bank
                        , kode_cabang: kode_cabang
                        , tanggal_disetorkan: tanggal_disetorkan
                        , jumlah: jumlah
                        , bulan: bulan
                        , tahun: tahun
                    }
                    , cache: false
                    , success: function(respond) {
                        loadlebihsetortemp();
                    }
                });
            }

        });



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
        $("#frmlebihSetor").submit(function() {
            var kode_cabang = $("#kode_cabang").val();
            var bulan = $("#frmlebihSetor").find("#bulan").val();
            var tahun = $("#frmlebihSetor").find("#tahun").val();
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
                    , text: 'Cabang !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $('#kode_cabang').focus();
                });
                return false;
            } else if (bulan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmlebihSetor").find("#bulan").focus();
                });
                return false;
            } else if (tahun == "") {
                swal({
                    title: 'Oops'
                    , text: 'Bulan !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#frmlebihSetor").find("#tahun").focus();
                });
                return false;
            }
        });
    });

</script>
