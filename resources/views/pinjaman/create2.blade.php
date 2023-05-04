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

<form method="POST" action="/pinjaman/store" id="frmPinjaman">
    @csrf
    <div class="row" id="step1">
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
                    <th>Gaji Pokok + Tunjangan</th>
                    <td style="text-align: right">
                        <input type="hidden" name="gapok_tunjangan" id="gapok_tunjangan" value="{{ $gaji->gajitunjangan }}">
                        {{ rupiah($gaji->gajitunjangan) }}
                    </td>
                </tr>

                <tr>
                    <th>Tenor Maksimal</th>
                    <td>
                        <?php
                            if($karyawan->status_karyawan == "T"){
                                $tenormax = 20;
                            }else{
                                $tglpinjaman = date_create(date('Y-m-d')); // waktu sekarang
                                $akhirkontrak = date_create($kontrak!= null ? $kontrak->sampai : date('Y-m-d'));
                                $sisakontrak = date_diff( $tglpinjaman, $akhirkontrak );
                               $tenormax = $sisakontrak->m;
                            }
                        ?>
                        {{ $tenormax }} Bulan
                        <input type="hidden" name="tenor_max" id="tenor_max" value="{{ $tenormax }}">
                    </td>
                </tr>
                <tr>
                    <th style="width:40%">Angsuran Maksimal (40% dari Gaji Pokok + Tunjangan)</th>
                    <td style="text-align:right">
                        @php
                        $angsuranmax = ((40/100) * $gaji->gajitunjangan );
                        @endphp
                        {{ rupiah($angsuranmax) }}
                        <input type="hidden" name="angsuran_max" id="angsuran_max" value="{{ $angsuranmax }}">
                    </td>
                </tr>
                <tr>
                    <th>Plafon</th>
                    <td style="text-align: right">
                        @php
                        $plafon = $angsuranmax * $tenormax;
                        @endphp
                        <input type="hidden" name="plafon" id="plafon" value="{{ $angsuranmax * $tenormax }}">
                        {{ rupiah($angsuranmax * $tenormax) }}
                    </td>
                </tr>
                <tr>
                    <th>JMK</th>
                    <td style="text-align: right">
                        <?php
                            $masakerja = $diff->y;
                            if($masakerja >= 3 && $masakerja < 6){
                                $jmlkali=2;
                            }else if($masakerja >= 6 && $masakerja < 9 ){
                                $jmlkali =3;
                            }else if($masakerja >= 9 && $masakerja < 12 ){
                                $jmlkali =4;
                            }else if($masakerja >= 12 && $masakerja < 15 ){
                                $jmlkali =5;
                            }else if($masakerja >= 15 && $masakerja < 18 ){
                                $jmlkali =6;
                            }else if($masakerja >= 18 && $masakerja < 21 ){
                                $jmlkali =7;
                            }else if($masakerja >= 21 && $masakerja < 24 ){
                                $jmlkali =8;
                            }else if($masakerja >= 24 ){
                                $jmlkali =10;
                            }else{
                                $jmlkali = 0.5;
                            }

                            if($masakerja <= 2){
                                $totaljmk = $jmlkali * $gaji->gaji_pokok;
                            }else{
                                $totaljmk = $gaji->gajitunjangan * $jmlkali;
                            }
                        ?>

                        {{ rupiah($totaljmk) }}
                        <input type="hidden" name="jmk" id="jmk" value="{{ $totaljmk }}">
                    </td>
                </tr>
                <tr>
                    <th>JMK Sudah Dibayar</th>
                    <td style="text-align: right">
                        {{ rupiah($jmk!=null ? $jmk->jml_jmk : 0) }}
                        <input type="hidden" name="jmk_sudahbayar" id="jmk_sudahbayar" value="{{ $jmk!=null ? $jmk->jml_jmk : 0 }}">
                    </td>
                </tr>
                <tr>
                    <th style="width:40%">Plafon Maksimal</th>
                    <td style="text-align:right">
                        @php
                        // $plafonmax = ((40/100) * $gaji->gajitunjangan )* 20;
                        $jmksudahdibayar = $jmk!=null ? $jmk->jml_jmk : 0;
                        $plafonjmk = $totaljmk - $jmksudahdibayar;
                        $plafonmax = $plafonjmk < $plafon ? $plafonjmk : $plafon; @endphp {{ rupiah($plafonmax) }} <input type="hidden" name="plafon_max" id="plafon_max" value="{{ $plafonmax }}">
                    </td>
                </tr>

            </table>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tanggal Pinjaman</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tanggal Pinjaman" value="" field="tgl_pinjaman" icon="feather icon-calendar" datepicker />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Jumlah Pinjaman</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Jumlah Pinjaman" value="" field="jml_pinjaman" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Angsuran</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Angsuran" value="{{ $tenormax }}" field="angsuran" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Jumlah Angsuran / Bulan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Jumlah Angsuran" value="" field="jml_angsuran" icon="feather icon-file" right readonly />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Mulai Cicilan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Mulai Cicilan" value="" field="mulai_cicilan" icon="feather icon-calendar" readonly />
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="form-group">
                        <button href="#" class="btn btn-primary btn-block"><i class="feather icon-send mr-1"></i>Submit</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="step2">

    </div>
</form>
<script src="{{asset('app-assets/js/scripts/pickers/dateTime/pick-a-datetime.js')}}"></script>
<script>
    $(function() {

        $("#frmPinjaman").submit(function(e) {
            // e.preventDefault();
            hitungpinjaman();
            var jmlpinjaman = $("#jml_pinjaman").val();
            var jmlangsuran = $("#jml_angsuran").val();
            var tgl_pinjaman = $("#tgl_pinjaman").val();
            var angsuran = $("#angsuran").val();
            if (tgl_pinjaman == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pinjaman Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pinjaman").focus();
                });
                return false;
            } else if (jmlpinjaman == "" || jmlpinjaman == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Pinjaman Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_pinjaman").focus();
                });

                return false;
            } else if (angsuran == "" || angsuran == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Angsuran Tidak Boleh Kosong !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#angsuran").focus();
                });

                return false;
            }

        });
        $("#jml_pinjaman,#jml_angsuran").maskMoney();

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
        $("#angsuran").change(function(e) {
            var angsuranmax = "{{ $angsuranmax }}";
            var jmlangsuran = $("#jml_angsuran").val();
            var jml_angsuran = parseInt(jmlangsuran.replace(/\./g, ''));
            if (parseInt(jml_angsuran) > parseInt(angsuranmax)) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Angsuran Tidak Boleh lebih dari ' + angsuranmax
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_angsuran").val(0);
                    $("#angsuran").val(0);
                });
            }
        });

        function hitungpinjaman() {
            var jmlpinjaman = $("#jml_pinjaman").val();
            var jmlangsuran = $("#jml_angsuran").val();
            var angsuran = $("#angsuran").val();
            var plafonmax = "{{ $plafonmax }}";
            var angsuranmax = "{{ $angsuranmax }}";
            var tenormax = "{{ $tenormax }}";

            if (jmlpinjaman.length === 0) {
                var jmlpinjaman = 0;
            } else {
                var jmlpinjaman = parseInt(jmlpinjaman.replace(/\./g, ''));
            }

            if (angsuran.length === 0) {
                var angsuran = 0;
            } else {
                var angsuran = parseInt(angsuran.replace(/\./g, ''));
            }

            if (jmlangsuran.length === 0) {
                var jmlangsuran = 0;
            } else {
                var jmlangsuran = parseInt(jmlangsuran.replace(/\./g, ''));
            }

            // if (parseInt(jmlpinjaman) > parseInt(plafonmax)) {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Jumlah Pinjaman Melebihi Plafon !'
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#jml_pinjaman").val(0);
            //         $("#jml_angsuran").val(0);
            //     });

            // } else {

            //     var angsuranperbulan = parseInt(angsuran) != 0 ? parseInt(jmlpinjaman) / parseInt(angsuran) : 0;
            //     var cekangsuran = Number.isInteger(angsuranperbulan);
            //     if (!cekangsuran) {
            //         angsuranperbulan = Math.floor(angsuranperbulan / 1000) * 1000;
            //     }
            //     $("#jml_angsuran").val(convertToRupiah(angsuranperbulan));
            // }

            var angsuranperbulan = parseInt(angsuran) != 0 ? parseInt(jmlpinjaman) / parseInt(angsuran) : 0;
            var cekangsuran = Number.isInteger(angsuranperbulan);
            if (!cekangsuran) {
                angsuranperbulan = Math.floor(angsuranperbulan / 1000) * 1000;
            }
            $("#jml_angsuran").val(convertToRupiah(angsuranperbulan));

            // if (parseInt(angsuran) > parseInt(tenormax)) {
            //     swal({
            //         title: 'Oops'
            //         , text: 'Angsuran Tidak Boleh Lebih dari !' + tenormax
            //         , icon: 'warning'
            //         , showConfirmButton: false
            //     }).then(function() {
            //         $("#angsuran").val(0);
            //         $("#jml_angsuran").val(0);
            //     });

            // }

        }

        $("#jml_pinjaman,#angsuran").on('keyup', function() {
            hitungpinjaman();
        });

        $("#tgl_pinjaman").change(function(e) {
            var tgl_pinjaman = $(this).val();
            var tanggal = tgl_pinjaman.split("-");
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
                    $("#mulai_cicilan").val("");
                    $("#tgl_pinjaman").val("");
                });
                $("#mulai_cicilan").val("");
                $("#tgl_pinjaman").val("");
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
                var mulai_cicilan = nexttahun + "-" + nextbulan + "-01";
                $("#mulai_cicilan").val(mulai_cicilan);
            }

        });
    });

</script>
