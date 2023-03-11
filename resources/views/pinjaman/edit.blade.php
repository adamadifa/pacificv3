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

<form method="POST" action="/pinjaman/{{ Crypt::encrypt($pinjaman->no_pinjaman) }}/update" id="frmPinjaman">
    @csrf
    <div class="row" id="step1">
        <div class="col-12">
            <table class="table table-bordered">
                <tr>
                    <th>NIK</th>
                    <td>
                        <input type="hidden" name="nik" id="nik" value="{{ $pinjaman->nik }}">
                        {{ $pinjaman->nik }}
                    </td>
                </tr>
                <tr>
                    <th>Nama Karyawan</th>
                    <td>{{ $pinjaman->nama_karyawan }}</td>
                </tr>
                <tr>
                    <th>Departmen</th>
                    <td>{{ $pinjaman->nama_dept }}</td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td>{{ $pinjaman->nama_jabatan }}</td>
                </tr>
                <tr>
                    <th>Perusahaan</th>
                    <td>{{ $pinjaman->id_perusahaan=="MP" ? "Makmur Permata" : "CV.Pacific Tasikmalaya" }}</td>
                </tr>
                <tr>
                    <th>Kantor</th>
                    <td>{{ $pinjaman->nama_cabang }}</td>
                </tr>
                <tr>
                    <th>Masa Kerja</th>
                    <td>
                        @php
                        $awal = date_create($pinjaman->tgl_masuk);
                        $akhir = date_create(date($pinjaman->tgl_pinjaman)); // waktu sekarang
                        $diff = date_diff( $awal, $akhir );
                        echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                        @endphp
                    </td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <input type="hidden" name="status_karyawan" id="status_karyawan" value="{{ $pinjaman->status_karyawan }}">
                        {{ $pinjaman->status_karyawan=="T" ? "Karyawan Tetap" : "Karyawan Kontrak" }}
                    </td>
                </tr>
                @if ($pinjaman->status_karyawan == "K")
                <tr>
                    <th>Akhir Kontrak</th>
                    <td>
                        <input type="hidden" name="akhir_kontrak" id="akhir_kontrak" value="{{ $pinjaman->akhir_kontrak }}">
                        {{ $pinjaman->akhir_kontrak != null ? DateToIndo2($pinjaman->akhir_kontrak) : "" }}
                    </td>
                </tr>
                @endif
                <tr>
                    <th>Gaji Pokok + Tunjangan</th>
                    <td style="text-align: right">
                        <input type="hidden" name="gapok_tunjangan" id="gapok_tunjangan" value="{{ $pinjaman->gapok_tunjangan }}">
                        {{ rupiah($pinjaman->gapok_tunjangan) }}
                    </td>
                </tr>
                <tr>
                    <th>Tenor Maksimal</th>
                    <td>
                        <?php
                            $tenormax = $pinjaman->tenor_max;
                        ?>
                        {{ $tenormax }} Bulan
                        <input type="hidden" name="tenor_max" id="tenor_max" value="{{ $tenormax }}">
                    </td>
                </tr>
                <tr>
                    <th style="width:40%">Angsuran Maksimal (40% dari Gaji Pokok + Tunjangan)</th>
                    <td style="text-align:right">
                        @php
                        $angsuranmax = $pinjaman->angsuran_max;
                        @endphp
                        {{ rupiah($angsuranmax) }}
                        <input type="hidden" name="angsuran_max" id="angsuran_max" value="{{ $angsuranmax }}">
                    </td>
                </tr>
                <tr>
                    <th>JMK</th>
                    <td style="text-align: right">
                        <?php
                            $totaljmk = $pinjaman->jmk;
                        ?>

                        {{ rupiah($totaljmk) }}
                        <input type="hidden" name="jmk" id="jmk" value="{{ $totaljmk }}">
                    </td>
                </tr>
                <tr>
                    <th>JMK Sudah Dibayar</th>
                    <td style="text-align: right">
                        {{ rupiah($pinjaman->jmk_sudahbayar!=null ? $pinjaman->jmk_sudahbayar : 0) }}
                        <input type="hidden" name="jmk_sudahbayar" id="jmk_sudahbayar" value="{{ $pinjaman->jmk_sudahbayar!=null ? $pinjaman->jmk_sudahbayar : 0 }}">
                    </td>
                </tr>
                <tr>
                    <th style="width:40%">Plafon Maksimal</th>
                    <td style="text-align:right">
                        @php
                        // $plafonmax = ((40/100) * $gaji->gajitunjangan )* 20;
                        $jmksudahdibayar = $pinjaman->jmk_sudahbayar !=null ? $pinjaman->jmk_sudahbayar : 0;
                        $plafonmax = $totaljmk - $jmksudahdibayar;
                        @endphp
                        {{ rupiah($plafonmax) }}
                        <input type="hidden" name="plafon_max" id="plafon_max" value="{{ $plafonmax }}">
                    </td>
                </tr>

            </table>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Tanggal Pinjaman</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Tanggal Pinjaman" value="{{ $pinjaman->tgl_pinjaman }}" field="tgl_pinjaman" icon="feather icon-calendar" datepicker />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Jumlah Pinjaman</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Jumlah Pinjaman" value="{{ rupiah($pinjaman->jumlah_pinjaman) }}" field="jml_pinjaman" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Angsuran</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Angsuran" value="{{ $pinjaman->angsuran }}" field="angsuran" icon="feather icon-file" right />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Jumlah Angsuran / Bulan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Jumlah Angsuran" value="{{ rupiah($pinjaman->jumlah_angsuran) }}" field="jml_angsuran" icon="feather icon-file" right readonly />
                </div>
            </div>
            <div class="row">
                <div class="col-4">
                    <label for="" class="form-label">Mulai Cicilan</label>
                </div>
                <div class="col-8">
                    <x-inputtext label="Mulai Cicilan" value="{{ $pinjaman->mulai_cicilan }}" field="mulai_cicilan" icon="feather icon-calendar" readonly />
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

            if (parseInt(jmlpinjaman) > parseInt(plafonmax)) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Pinjaman Melebihi Plafon !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jml_pinjaman").val(0);
                    $("#jml_angsuran").val(0);
                });

            } else {

                var angsuranperbulan = parseInt(angsuran) != 0 ? parseInt(jmlpinjaman) / parseInt(angsuran) : 0;
                var cekangsuran = Number.isInteger(angsuranperbulan);
                if (!cekangsuran) {
                    angsuranperbulan = Math.floor(angsuranperbulan / 1000) * 1000;
                }
                $("#jml_angsuran").val(convertToRupiah(angsuranperbulan));
            }

            if (parseInt(angsuran) > parseInt(tenormax)) {
                swal({
                    title: 'Oops'
                    , text: 'Angsuran Tidak Boleh Lebih dari !' + tenormax
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#angsuran").val(0);
                    $("#jml_angsuran").val(0);
                });

            }

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
