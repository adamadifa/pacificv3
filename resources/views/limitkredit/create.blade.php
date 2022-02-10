@extends('layouts.midone')
@section('titlepage', 'Input Pengajuan Limit Kredit')
@section('content')
<div class="content-wrapper">
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">Pengajuan Limit</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/limitkredit">Limit Kredit</a></li>
                            <li class="breadcrumb-item"><a href="#">Pengajuan Limit</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="content-body">
    <form class="form" id="formLimit" action="/limitkredit/store" method="POST">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Data Pengajuan</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Auto" field="no_pengajuan" icon="feather icon-credit-card" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Tanggal Pengajuan" field="tgl_pengajuan" icon="feather icon-calendar" datepicker />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Kode Pelanggan" field="kode_pelanggan" icon="fa fa-barcode" value="{{ $pelanggan->kode_pelanggan }}" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="No. KTP" field="nik" icon="fa fa-credit-card" value="{{ $pelanggan->nik }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Nama Pelanggan" field="nama_pelanggan" icon="fa fa-barcode" value="{{ $pelanggan->nama_pelanggan }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Alamat Pelanggan" field="alamat_pelanggan" icon="feather icon-map" value="{{ $pelanggan->alamat_pelanggan }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Alamat Toko" field="alamat_toko" icon="feather icon-map" value="{{ $pelanggan->alamat_toko }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12 col-lg-6">
                                        <x-inputtext label="Latitude" field="latitude" icon="feather icon-map" value="{!! $pelanggan->latitude !!}" />
                                    </div>
                                    <div class="col-sm-12 col-lg-6">
                                        <x-inputtext label="Longitude" field="longitude" icon="feather icon-map" value="{!! $pelanggan->longitude !!}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="No. HP" field="no_hp" icon="feather icon-phone" value="{{ $pelanggan->no_hp }}" />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select id="hari" name="hari" class="form-control">
                                                <option value="">Hari Kunjungan</option>
                                                <option @if ($pelanggan->hari=="Senin")
                                                    selected
                                                    @endif value="Senin">Senin</option>
                                                <option @if ($pelanggan->hari=="Selasa")
                                                    selected
                                                    @endif value="Selasa">Selasa</option>
                                                <option @if ($pelanggan->hari=="Rabu")
                                                    selected
                                                    @endif value="Rabu">Rabu</option>
                                                <option @if ($pelanggan->hari=="Kamis")
                                                    selected
                                                    @endif value="Kamis">Kamis</option>
                                                <option @if ($pelanggan->hari=="Jumat")
                                                    selected
                                                    @endif value="Jumat">Jumat</option>
                                                <option @if ($pelanggan->hari=="Sabtu")
                                                    selected
                                                    @endif value="Sabtu">Sabtu</option>
                                                <option @if ($pelanggan->hari=="Minggu")
                                                    selected
                                                    @endif value="Minggu">Minggu</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Cabang" field="kode_cabang" icon="fa fa-bank" value="{{ $pelanggan->kode_cabang }}" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Salesman" field="id_karyawan" icon="feather icon-users" value="{{ $pelanggan->nama_karyawan }}" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Jumlah Ajuan Kredit" field="jumlah" icon="feather icon-file" right />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <textarea name="uraian_analisa" placeholder="Uraian Analisa" class="form-control" id="uraian_analisa" cols="30" rows="10"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="status_outlet" id="status_outlet" class="form-control">
                                                <option value="">Status Outlet</option>
                                                <option @if ($pelanggan->status_outlet==1)
                                                    selected
                                                    @endif value="1">New Outlet</option>
                                                <option @if ($pelanggan->status_outlet==2)
                                                    selected
                                                    @endif value="2">Exsiting Outlet</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="type_outlet" id="type_outlet" class="form-control">
                                                <option value="">Type Outlet</option>
                                                <option @if ($pelanggan->type_outlet==1)
                                                    selected
                                                    @endif value="1">Grosir</option>
                                                <option @if ($pelanggan->type_outlet==2)
                                                    selected
                                                    @endif value="2">Retail</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="jatuhtempo" id="jatuhtempo" class="form-control">
                                                <option value="">Jatuh Tempo</option>
                                                <option @if ($pelanggan->jatuhtempo==14)
                                                    selected
                                                    @endif value="14">14</option>
                                                <option @if ($pelanggan->jatuhtempo==30)
                                                    selected
                                                    @endif value="30">30</option>
                                                <option @if ($pelanggan->jatuhtempo==45)
                                                    selected
                                                    @endif value="45">45</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="cara_pembayaran" id="cara_pembayaran" class="form-control">
                                                <option value="">Cara Pembayaran</option>
                                                <option @if ($pelanggan->cara_pembayaran==1)
                                                    selected
                                                    @endif value="1">Bank Transfer</option>
                                                <option @if ($pelanggan->cara_pembayaran==2)
                                                    selected
                                                    @endif value="2">Advance Cash</option>
                                                <option @if ($pelanggan->cara_pembayaran==3)
                                                    selected
                                                    @endif value="3">Cheque / Bilyet Giro</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="kepemilikan" id="kepemilikan" class="form-control">
                                                <option value="">Kepemilikan</option>
                                                <option @if ($pelanggan->kepemilikan=="Milik Sendiri")
                                                    selected
                                                    @endif value="Milik Sendiri">Milik Sendiri</option>
                                                <option @if ($pelanggan->kepemilikan=="Sewa")
                                                    selected
                                                    @endif value="Sewa">Sewa</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="divider divider-left">
                                    <div class="divider-text">Top Up Terakhir</div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-sm-12">
                                        @if ($lasttopup != null)
                                        @php
                                        $lasttopupdate = $lasttopup->tgl_pengajuan;
                                        $tgl1 = new DateTime($lasttopup->tgl_pengajuan);
                                        $tgl2 = new DateTime(date('Y-m-d'));
                                        $lama_topup = $tgl2->diff($tgl1)->days + 1;

                                        // tahun
                                        $y = $tgl2->diff($tgl1)->y;

                                        // bulan
                                        $m = $tgl2->diff($tgl1)->m;

                                        // hari
                                        $d = $tgl2->diff($tgl1)->d;

                                        $usia_topup = $y . " tahun " . $m . " bulan " . $d . " hari";
                                        @endphp
                                        @else
                                        @php
                                        $lasttopupdate = "";
                                        $usia_topup = "";
                                        $lama_topup=0;
                                        @endphp
                                        @endif
                                        <input type="hidden" value="{{ $lama_topup }}" id="lama_topup" name="lama_topup">
                                        <x-inputtext label="Terakhir Top Up" field="lasttopup" icon="feather icon-calendar" value="{{ $lasttopupdate }}" readonly />
                                    </div>
                                    <div class="col-lg-6 col-sm-12">

                                        <x-inputtext label="Lama Top Up" field="lama_topup" icon="feather icon-file" value="{{ $usia_topup }}" readonly />
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="histori_transaksi" id="histori_transaksi" class="form-control">
                                                <option value="">Histori Pembayaran Transaksi (6 Bulan Terakhir)</option>
                                                <option value="< 14 Hari">
                                                    < 14 Hari</option>
                                                <option value="= 14 Hari">= 14 Hari</option>
                                                <option value="> 14 Hari">> 14 Hari</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="lama_langganan" id="lama_langganan" class="form-control">
                                                <option value="">Lama Langganan</option>
                                                <option @if ($pelanggan->lama_langganan=="< 2 Tahun") selected @endif value="< 2 Tahun">
                                                        < 2 Tahun</option>
                                                <option @if ($pelanggan->lama_langganan=="> 2 Tahun") selected @endif value="> 2 Tahun">> 2 Tahun</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="lama_usaha" id="lama_usaha" class="form-control">
                                                <option value="">Lama Usaha</option>
                                                <option @if ($pelanggan->lama_usaha=="< 2 Tahun") selected @endif value="< 2 Tahun">
                                                        < 2 Tahun</option>
                                                <option @if ($pelanggan->lama_usaha=="2-5 Tahun") selected @endif value="2-5 Tahun">
                                                    2-5 Tahun</option>
                                                <option @if ($pelanggan->lama_usaha=="> 5 Tahun") selected @endif value="> 5 Tahun">> 5 Tahun</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <select name="jaminan" id="jaminan" class="form-control">
                                                <option value="">Jaminan</option>
                                                <option @if ($pelanggan->jaminan==1)
                                                    selected
                                                    @endif value="1">Ada</option>
                                                <option @if ($pelanggan->jaminan==2)
                                                    selected
                                                    @endif value="2">Tidak Ada</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <x-inputtext label="Omset Toko" field="omset_toko" icon="feather icon-file" right value="{{ rupiah($pelanggan->omset_toko) }}" />
                                    </div>
                                </div>
                                <div class="divider divider-left">
                                    <div class="divider-text">Jumlah Faktur Belum Lunas</div>
                                </div>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>No. Faktur</th>
                                            <th>Tanggal</th>
                                            <th>Piutang</th>
                                            <th>Jml Bayar</th>
                                            <th>Sisa Bayar</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $no = 1;
                                        @endphp
                                        @foreach ($listfaktur as $d)
                                        @php
                                        $sisabayar = $d->nettopiutang - $d->jmlbayar;
                                        @endphp
                                        <tr>
                                            <td>{{ $d->no_fak_penj }}</td>
                                            <td>{{ date("d-m-y",strtotime($d->tgltransaksi)) }}</td>
                                            <td class="text-right">{{ rupiah($d->nettopiutang) }}</td>
                                            <td class="text-right">{{ rupiah($d->jmlbayar) }}</td>
                                            <td class="text-right">{{ rupiah($sisabayar) }}</td>
                                        </tr>
                                        @php
                                        $no++;
                                        @endphp
                                        @endforeach
                                    </tbody>
                                </table>
                                <input type="hidden" value="<?php echo $no - 1; ?>" id="jml_faktur" name="jml_faktur">
                                <table class="table">
                                    <tr>
                                        <th>Skor</th>
                                        <td id="totalscore"></td>
                                    </tr>
                                    <tr>
                                        <th>Keterangan</th>
                                        <td id="rekomendasi"></td>
                                    </tr>
                                </table>
                                <div class="row">
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary mr-1 mb-1"><i class="fa fa-send mr-1"></i> Simpan</button>
                                        <a href="{{ url()->previous() }}" class="btn btn-outline-warning mr-1 mb-1"><i class="fa fa-arrow-left mr-2"></i>Kembali</a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
@push('myscript')
<script>
    $(function() {
        $("#jumlah").maskMoney();
        $("#omset_toko").maskMoney();

        function loadSkor() {
            var status_outlet = $("#status_outlet").val();
            var type_outlet = $("#type_outlet").val();
            var jatuhtempo = $("#jatuhtempo").val();
            var cara_pembayaran = $("#cara_pembayaran").val();
            var kepemilikan = $("#kepemilikan").val();
            var lamatopup = $("#lama_topup").val();
            var lamalangganan = $("#lama_langganan").val();
            var lamausaha = $("#lama_usaha").val();
            var jaminan = $("#jaminan").val();
            var historitransaksi = $("#histori_transaksi").val();
            var omsettoko = $("#omset_toko").val();
            var jumlah = $("#jumlah").val();
            var jmlfaktur = $("#jml_faktur").val();
            var ot = omsettoko.replace(/\./g, '');
            var jm = jumlah.replace(/\./g, '');


            var ratioomset = Math.round(parseInt(ot) / parseInt(jm));

            if (ratioomset < 3) {
                score_omset = 0.35;
            } else if (ratioomset >= 3) {
                score_omset = 0.50;
            } else {
                score_omset = 0;
            }

            if (jmlfaktur == 1) {
                score_faktur = 0.50;
            } else if (jmlfaktur == 2) {
                score_faktur = 0.40;
            } else if (jmlfaktur == 3) {
                score_faktur = 0.30;
            } else if (jmlfaktur > 3) {
                score_faktur = 0.20;
            } else {
                score_faktur = 0;
            }

            if (status_outlet == 1) {
                score_outlet = 1.05;
            } else if (status_outlet == 2) {
                score_outlet = 1.50;
            } else {
                score_outlet = 0.00;
            }

            if (jaminan == 1) {
                score_jaminan = 1.00;
            } else if (jaminan == 2) {
                score_jaminan = 0.70;
            } else {
                score_jaminan = 0.00;
            }

            if (type_outlet == 1) {
                score_typeoutlet = 0.50;
            } else if (type_outlet == 2) {
                score_typeoutlet = 0.35;
            } else {
                score_typeoutlet = 0.00;
            }

            if (jatuhtempo == 14) {
                score_top = 1.00;
            } else if (jatuhtempo == 30) {
                score_top = 0.70;
            } else if (jatuhtempo == 45) {
                score_top = 0.40;
            } else {
                score_top = 0.00;
            }

            if (cara_pembayaran == 1) {
                score_carabayar = 0.50;
            } else if (cara_pembayaran == 2) {
                score_carabayar = 0.35;
            } else if (cara_pembayaran == 3) {
                score_carabayar = 0.20;
            } else {
                score_carabayar = 0.00;
            }
            //alert(score_carabayar);
            if (kepemilikan == "Milik Sendiri") {
                score_kepemilikan = 1.00;
            } else if (kepemilikan == "Sewa") {
                score_kepemilikan = 0.70;
            } else {
                score_kepemilikan = 0.00;
            }

            if (historitransaksi == "< 14 Hari") {
                score_ht = 1.00;
            } else if (historitransaksi == "= 14 Hari") {
                score_ht = 0.70;
            } else if (historitransaksi == "> 14 Hari") {
                score_ht = 0.40;
            } else {
                score_ht = 0.00;
            }

            if (lamausaha == "< 2 Tahun") {
                score_lamausaha = 0.40;
            } else if (lamausaha == "2-5 Tahun") {
                score_lamausaha = 0.70;
            } else if (lamausaha == "> 5 Tahun") {
                score_lamausaha = 1.00;
            } else {
                score_lamausaha = 0.00;
            }

            if (lamalangganan == "< 2 Tahun") {
                score_lamalangganan = 0.70;
            } else if (lamalangganan == "> 2 Tahun") {
                score_lamalangganan = 1.00;
            } else {
                score_lamalangganan = 0.00;
            }

            if (lamatopup == 0) {
                score_lamatopup = 0.50;
            } else if (lamatopup <= 31) {
                score_lamatopup = 0.50;
            } else if (lamatopup > 31) {
                score_lamatopup = 0.35;
            } else {
                score_lamatopup = 0.00;
            }

            var totalscore = parseFloat(score_outlet) + parseFloat(score_top) + parseFloat(score_carabayar) + parseFloat(score_kepemilikan) + parseFloat(score_lamausaha) + parseFloat(score_jaminan) +
                parseFloat(score_lamatopup) + parseFloat(score_lamalangganan) + parseFloat(score_ht) + parseFloat(score_omset) + parseFloat(score_faktur) + parseFloat(score_typeoutlet);
            var scoreakhir = totalscore.toFixed(2);
            $("#skor").val(scoreakhir);
            var rekomendasi = "";
            if (scoreakhir <= 2) {
                rekomendasi = "Tidak Layak";
            } else if (scoreakhir > 2 && scoreakhir <= 4) {
                rekomendasi = "Tidak Disarankan";
            } else if (scoreakhir > 4 && scoreakhir <= 6.75) {
                rekomendasi = "Beresiko";
            } else if (scoreakhir > 6.75 && scoreakhir <= 8.5) {
                rekomendasi = "Layak Dengan Pertimbangan";
            } else if (scoreakhir > 8.5 && scoreakhir <= 10) {
                rekomendasi = "Layak";
            }
            $("#totalscore").text(scoreakhir);
            $("#rekomendasi").text(rekomendasi);
        }

        loadSkor();

        $("#lama_usaha").change(function() {
            loadSkor();
        });

        $("#type_outlet").change(function() {
            loadSkor();
        });


        $("#histori_transaksi").change(function() {
            loadSkor();
        });

        $("#lama_langganan").change(function() {
            loadSkor();
        });

        $("#jaminan").change(function() {
            loadSkor();
        });

        $("#status_outlet").change(function() {
            loadSkor();
        });

        $("#jatuhtempo").change(function() {
            loadSkor();
        });

        $("#cara_pembayaran").change(function() {
            loadSkor();
        });

        $("#kepemilikan").change(function() {
            loadSkor();
        });

        $("#omset_toko").on('keyup keydown change', function() {
            var jumlah = $("#jumlah").val();
            $(this).prop('readonly', true);
            if (jumlah == "" || jumlah == 0) {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Ajuan Kredit Harus Diisi Dulu !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {

                    $("#jumlah").focus();
                });

            }
            loadSkor();
        });

        $("#jumlah").on('keyup keydown change', function() {
            var jumlah = $("#jumlah").val();
            if (jumlah == "" || jumlah == 0) {
                $("#omset_toko").prop('readonly', true);
            } else {
                $("#omset_toko").prop('readonly', false);
            }
            loadSkor();
        });


        $("#formLimit").submit(function() {
            var tgl_pengajuan = $("#tgl_pengajuan").val();
            var alamat_pelanggan = $("#alamat_pelanggan").val();
            var nik = $("#nik").val();
            var alamat_toko = $("#alamat_toko").val();
            var longitude = $("#longitude").val();
            var latitude = $("#latitude").val();
            var no_hp = $("#no_hp").val();
            var hari = $("#hari").val();
            var status_outlet = $("#status_outlet").val();
            var type_outlet = $("#type_outlet").val();
            var jatuhtempo = $("#jatuhtempo").val();
            var cara_pembayaran = $("#cara_pembayaran").val();
            var kepemilikan = $("#kepemilikan").val();
            var lama_topup = $("#lama_topup").val();
            var lama_langganan = $("#lama_langganan").val();
            var lama_usaha = $("#lama_usaha").val();
            var jaminan = $("#jaminan").val();
            var histori_transaksi = $("#histori_transaksi").val();
            var omset_toko = $("#omset_toko").val();
            var jumlah = $("#jumlah").val();
            var uraian_analisa = $("#uraian_analisa").val();
            //alert(hari);
            if (tgl_pengajuan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Tanggal Pengajuan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#tgl_pengajuan").focus();
                });
                return false;
            } else if (alamat_pelanggan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Alamat Pelanggan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#alamat_pelanggan").focus();
                });
                return false;
            } else if (alamat_toko == "") {
                swal({
                    title: 'Oops'
                    , text: 'Alamat Toko Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#alamat_toko").focus();
                });
                return false;
            } else if (longitude == "") {
                swal({
                    title: 'Oops'
                    , text: 'Longitude Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#longitude").focus();
                });
                return false;
            } else if (latitude == "") {
                swal({
                    title: 'Oops'
                    , text: 'Latitude Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#latitude").focus();
                });
                return false;
            } else if (no_hp == "") {
                swal({
                    title: 'Oops'
                    , text: 'No HP Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#no_hp").focus();
                });
                return false;
            } else if (jumlah == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jumlah Ajuan Kredit Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jumlah").focus();
                });
                return false;
            } else if (status_outlet == "") {
                swal({
                    title: 'Oops'
                    , text: 'Status Outlet Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#status_outlet").focus();
                });
                return false;
            } else if (type_outlet == "") {
                swal({
                    title: 'Oops'
                    , text: 'Type Outlet Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#type_outlet").focus();
                });
                return false;
            } else if (jatuhtempo == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jatuh Tempo Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jatuhtempo").focus();
                });
                return false;
            } else if (cara_pembayaran == "") {
                swal({
                    title: 'Oops'
                    , text: 'Cara Pembayaran Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#cara_pembayaran").focus();
                });
                return false;
            } else if (kepemilikan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Kepemilikan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#kepemilikan").focus();
                });
                return false;
            } else if (histori_transaksi == "") {
                swal({
                    title: 'Oops'
                    , text: 'Histori Transaksi Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#histori_transaksi").focus();
                });
                return false;
            } else if (lama_langganan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Lama Langganan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#lama_langganan").focus();
                });
                return false;
            } else if (lama_usaha == "") {
                swal({
                    title: 'Oops'
                    , text: 'Lama Usaha Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#lama_usaha").focus();
                });
                return false;
            } else if (jaminan == "") {
                swal({
                    title: 'Oops'
                    , text: 'Jaminan Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#jaminan").focus();
                });
                return false;
            } else if (omset_toko == "") {
                swal({
                    title: 'Oops'
                    , text: 'Omset Toko Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#omset_toko").focus();
                });
                return false;
            } else if (uraian_analisa == "") {
                swal({
                    title: 'Oops'
                    , text: 'Uraian Analisa Harus Diisi !'
                    , icon: 'warning'
                    , showConfirmButton: false
                }).then(function() {
                    $("#uraian_analisa").focus();
                });
                return false;
            } else {
                return true;
            }
        });
    });

</script>
@endpush
