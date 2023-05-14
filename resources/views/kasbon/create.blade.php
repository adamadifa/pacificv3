<style>
    .form-label {
        font-weight: bold;
        margin-top: 10px;
    }

    .form-group {
        margin-bottom: 5px !important;
    }

    .form-label-group {
        margin-bottom: 5px !important;
    }

</style>
<form method="POST" action="/kasbon/store" id="frmKasbon">
    @csrf
    <input type="hidden" name="id_jabatan" id="id_jabatan" value="{{ $karyawan->id_jabatan }}">
    <div class="row">

        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th>NIK</th>
                    <td>
                        <input type="hidden" name="nik" id="nik" value="{{ $karyawan->nik }}">
                        {{ $karyawan->nik }}
                    </td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $karyawan->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Departmen</th>
                    <td>{{ $karyawan->nama_dept }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $karyawan->nama_jabatan }}</td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>{{ $karyawan->id_perusahaan=="MP" ? "Makmur Permata" : "CV.Pacific Tasikmalaya" }}</td>
                </tr>
                <tr>
                    <th>Kantor</th>
                    <td>{{ $karyawan->nama_cabang }}</td>
                </tr>
                <tr>
                    <th>Masa Kerja</th>
                    <td>
                        @php
                        $awal = date_create($karyawan->tgl_masuk);
                        $akhir = date_create(date('Y-m-d')); // waktu sekarang
                        $diff = date_diff( $awal, $akhir );
                        echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                        @endphp
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <input type="hidden" name="status_karyawan" id="status_karyawan" value="{{ $karyawan->status_karyawan }}">
                        {{ $karyawan->status_karyawan=="T" ? "Karyawan Tetap" : "Karyawan Kontrak" }}
                    </td>
                </tr>
                @if ($karyawan->status_karyawan == "K")
                <tr>
                    <th>Akhir Kontrak</th>
                    <td>
                        <input type="hidden" name="akhir_kontrak" id="akhir_kontrak" value="{{ $kontrak->sampai }}">
                        {{ $kontrak != null ? DateToIndo2($kontrak->sampai) : "" }}
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Max Kasbon</th>
                    <td>
                        <?php
                        $tgl_masuk = $karyawan->tgl_masuk;
                        $hariini = date("Y-m-d");
                        $timeStart = strtotime("$tgl_masuk");
                        $timeEnd = strtotime("$hariini");
                        // Menambah bulan ini + semua bulan pada tahun sebelumnya
                        $numBulan = 1 + (date("Y",$timeEnd)-date("Y",$timeStart))*12;
                        // menghitung selisih bulan
                        $numBulan += date("m",$timeEnd)-date("m",$timeStart);

                        if($numBulan <9){
                            $maxkasbon = 200000;
                        }elseif($numBulan <=15){
                            $maxkasbon = 400000;
                        }else{
                            $maxkasbon = 600000;
                        }

                        $jmlcicilan = $cicilan != null ? $cicilan->jumlah : 0;

                        if(!empty($kasbon_max)){
                            if($kasbon_max > $maxkasbon){
                                $maxkasbon = $maxkasbon;
                            }else{
                                $maxkasbon = $kasbon_max;
                            }
                        }else{
                            $maxkasbon = $maxkasbon;
                        }



                        echo rupiah($maxkasbon);
                        ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Tanggal Kasbon</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Tanggal Kasbon" value="" field="tgl_kasbon" icon="feather icon-calendar" datepicker />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Jumlah Kasbon</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Jumlah Kasbon" value="" field="jml_kasbon" icon="feather icon-file" right />
        </div>
    </div>
    <div class="row">
        <div class="col-4">
            <label for="" class="form-label">Jatuh Tempo</label>
        </div>
        <div class="col-8">
            <x-inputtext label="Jatuh Tempo" value="" field="jatuh_tempo" icon="feather icon-calendar" readonly />
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="form-group">
                <button href="#" class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i>Submit</button>
            </div>
        </div>
    </div>
</form>

<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {
        $("#frmKasbon").submit(function(e) {
            // e.preventDefault();
            var tgl_kasbon = $("#tgl_kasbon").val();
            var jml_kasbon = $("#jml_kasbon").val();
            if (tgl_kasbon == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Kasbon Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_kasbon").focus();
                });
                return false;
            } else if (jml_kasbon == "" || jml_kasbon == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Kasbon Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_kasbon").focus();
                });

                return false;
            }
        });

        $("#jml_kasbon").maskMoney();

        $("#tgl_kasbon").change(function(e) {
            var tgl_kasbon = $(this).val();
            var tanggal = tgl_kasbon.split("-");
            var tgl = tanggal[2];
            var bulan = tanggal[1];
            var tahun = tanggal[0];

            if (tgl == 19 || tgl == 20) {
                swal({
                    title: 'Oops'
                    , text: 'Tidak Bisa Melakukan Pinjaman Pada Tanggal 19 dan 20 !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jatuh_tempo").val("");
                    $("#tgl_kasbon").val("");
                });
                $("#jatuh_tempo").val("");
                $("#tgl_kasbon").val("");
            } else {
                if (tgl <= 18 && bulan <= 10) {
                    var nextbulan = parseInt(bulan) + 1;
                    var nexttahun = parseInt(tahun);
                } else if (tgl <= 18 && bulan == 12) {
                    var nextbulan = 1;
                    var nexttahun = parseInt(tahun) + 1;
                } else if (parseInt(tgl) >= 21 && parseInt(bulan) <= 10) {
                    var nextbulan = parseInt(bulan) + 2;
                    var nexttahun = parseInt(tahun);
                } else if (parseInt(tgl) <= 18 && parseInt(bulan) > 10) {
                    var nextbulan = parseInt(bulan) + 1;
                    var nexttahun = parseInt(tahun);
                } else if (parseInt(tgl) >= 21 && parseInt(bulan) == 12) {
                    var nextbulan = 2;
                    var nexttahun = parseInt(tahun) + 1;
                } else if (parseInt(tgl) >= 21 && parseInt(bulan) < 10) {
                    var nextbulan = 1;
                    var nexttahun = parseInt(tahun) + 1;
                }

                if (nextbulan <= 9) {
                    var nextbulan = "0" + nextbulan;
                }
                var jatuh_tempo = nexttahun + "-" + nextbulan + "-01";
                $("#jatuh_tempo").val(jatuh_tempo);
            }

        });

        $("#jml_kasbon").keyup(function(e) {
            var maxkasbon = "{{ $maxkasbon }}";
            var jmlkasbon = $("#jml_kasbon").val();
            var jml_kasbon = parseInt(jmlkasbon.replace(/\./g, ''));
            if (parseInt(jml_kasbon) > parseInt(maxkasbon)) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Kasbon Tidak Boleh lebih dari ' + maxkasbon
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_kasbon").val(0);
                });
            }
        });
    });

</script>
