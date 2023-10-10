<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Cetak Ajuan Limit Kredit</title>
<style>
    @page {
        margin: 20px 20px 10px 30px !important;
        padding: 0px 0px 0px 0px !important;
    }



    .judul {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 20px;
        text-align: center;
        color: #005e2f
    }

    .judul2 {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
        font-size: 16px;


    }

    .huruf {
        font-family: Verdana, Geneva, Tahoma, sans-serif;
    }

    .ukuranhuruf {
        font-size: 12px;
    }

    .datatable3 {
        border: 1px solid #05090e;
        border-collapse: collapse;
        /* font-size: 10px; */
        /*float:left; */
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        width: 100%;


    }

    .datatable3 td {
        border: 1px solid #000000;
        padding: 6px;
        font-size: 12px;

    }

    .datatable3 th {
        border: 1px solid #000000;
        font-weight: bold;
        padding: 4px;
        text-align: center;
        font-size: 12px;
        background-color: green;
        color: white;
    }

    hr.style2 {
        border-top: 3px double #8c8b8b;
    }

    .inline {
        float: left;
        width: 50%;
        height: 170px;
        margin-right: 10px;
    }

</style>
<table style="width:60%" class="datatable3">
    <tr>
        <td>

            <?php
            $path = public_path('pac.png');
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            ?>
            <img src="<?php echo $base64; ?>" width="60" height="70" />


        </td>
        <td>
            <b style="font-size:18px">CV PACIFIC</b><br>
            <div style="font-size:14px; font-family:Tahoma">
                Jln Perintis Kemerdekaan No. 106 Tasikmalaya
                Tlp. (0265) 330794, 337694. Fax (0265) 332329
                Emai: pacific.tasikmalaya@gmail.com
            </div>
            <br>
        </td>
    </tr>
</table>
<h3 class="judul2">ANALISA AJUAN KREDIT</h3>
<h4 class="judul2"><u>KUALITATIF</u></h4>

<div class="inline">
    <table class="datatable3" style="width:90%">
        <tr>
            <td>No.Pengajuan</td>
            <td>{{ $limitkredit->no_pengajuan }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>{{ DateToIndo2($limitkredit->tgl_pengajuan) }}</td>
        </tr>
        <tr>
            <td>Cabang</td>
            <td>{{ $limitkredit->kode_cabang }}</td>
        </tr>
        <tr>
            <td>Salesman</td>
            <td>{{ $limitkredit->nama_karyawan }}</td>
        </tr>
        <tr>
            <td>Alamat KTP</td>
            <td>{{ ucwords(strtolower($limitkredit->alamat_pelanggan)) }}</td>
        </tr>
    </table>
</div>
<div class="inline">
    <table class="datatable3" style="width:100%">
        <tr>
            <td>ID Pelanggan</td>
            <td>{{ $limitkredit->kode_pelanggan }}</td>
        </tr>
        <tr>
            <td>Pelanggan</td>
            <td>{{ $limitkredit->nama_pelanggan }}</td>
        </tr>
        <tr>
            <td>Alamat Toko</td>
            <td>{{ $limitkredit->alamat_toko }}</td>
        </tr>
        <tr>
            <td>Koordinat</td>
            <td>{{ $limitkredit->latitude }},{{ $limitkredit->longitude }}</td>
        </tr>
    </table>
</div>

<h4 class="judul2"><u>KUANTITATIF</u></h4>

<div class="inline">
    <table class="datatable3" style="width:100%">
        <tr>
            <td>Status Pelanggan</td>
            <td>
                @if ($limitkredit->status_outlet==1)
                New Outlet
                @else
                Existing Outlet
                @endif
            </td>
        </tr>
        <tr>
            <td>Cara Pembayaran</td>
            <td>
                @if ($limitkredit->cara_pembayaran==1)
                Bank Transfer
                @elseif($limitkredit->cara_pembayaran==2)
                Advance Cash
                @else
                Cheque / Bilyet Giro
                @endif
            </td>
        </tr>
        <tr>
            <td>Histori Pembayaran Transaksi (6 Bulan Terakhir)</td>
            <td>{{ $limitkredit->histori_transaksi }}</td>
        </tr>
        <tr>
            <td>Terakhir Top UP</td>
            <td>
                @php
                $k = "<"; $l=">" ; @endphp @if ($limitkredit->lama_topup >= 31)
                    {{ $l }} 1 Bulan
                    @else
                    {{ $k }} 1 Bulan @endif </td>
        </tr>
        <tr>
            <td>Lama Usaha</td>
            <td>{{ $limitkredit->lama_usaha }}</td>
        </tr>
        <tr>
            <td>Jumlah Faktur</td>
            <td>{{ $limitkredit->jml_faktur }}</td>
        </tr>

    </table>
</div>
<div class="inline">
    <table class="datatable3" style="width:80%">
        <tr>
            <td>TOP</td>
            <td>{{ $limitkredit->jatuhtempo }} Hari</td>
        </tr>
        <tr>
            <td>Tempat Usaha</td>
            <td>{{ $limitkredit->kepemilikan }}</td>
        </tr>
        <tr>
            <td>Omset Toko</td>
            <td style="text-align: right">{{ rupiah($limitkredit->last_omset) }}</td>
        </tr>
        <tr>
            <td>Mulai Langganan</td>
            <td>{{ $limitkredit->lama_langganan }}</td>
        </tr>
        <tr>
            <td>Type Outlet</td>
            <td>
                @if ($limitkredit->type_outlet==1)
                Grosir
                @else
                Retail
                @endif
            </td>
        </tr>
    </table>
</div>
<table class="datatable3" style="margin-top:200px; width:40%">
    <tr>
        <td>Limit Kredit Sebelumnya</td>
        <td style="text-align: right">{{ rupiah($limitkredit->last_limit) }}</td>
    </tr>
    <tr>
        <td>Pengajuan Tambahan</td>
        <td style="text-align: right">{{ rupiah($limitkredit->jumlah - $limitkredit->last_limit) }}</td>
    </tr>
</table>

<table class="datatable3" style="margin-top:30px;">
    <tr>
        <td>Total Limit</td>
        <td style="text-align: right">{{ rupiah($limitkredit->jumlah) }}</td>
        <td rowspan="4" valign="top">
            @foreach ($komentar as $d)
            {{ $d->name }} ({{ $d->level }}) - {{ $d->uraian_analisa }}<br>
            @endforeach
        </td>
    </tr>
    <tr>
        <td>Level Otorisasi</td>
        <td>
            @if ($limitkredit->jumlah > 15000000)
            Direktur
            @elseif($limitkredit->jumlah > 10000000)
            General Manager
            @elseif($limitkredit->jumlah >5000000)
            RSM
            @elseif($limitkredit->jumlah > 2000000)
            Kepala Penjualan
            @endif
        </td>
    </tr>
    <tr>
        <td>Total Skor</td>
        <td style="text-align: right">{{ desimal($limitkredit->skor) }}</td>
    </tr>
    <tr>
        <td>Rekomendasi</td>
        <td>
            <?php
            $scoreakhir =  $limitkredit->skor;
            if ($scoreakhir <= 2) {
            $rekomendasi = "Tidak Layak";
            } else if ($scoreakhir > 2 && $scoreakhir <= 4) {
            $rekomendasi = "Tidak Disarankan";
            } else if ($scoreakhir > 4 && $scoreakhir <= 6.75) {
            $rekomendasi = "Beresiko";
            } else if ($scoreakhir > 6.75 && $scoreakhir <= 8.5) {
            $rekomendasi = "Layak Dengan Pertimbangan";
            } else if ($scoreakhir > 8.5 && $scoreakhir <= 10) {
            $rekomendasi = "Layak";
            }
            echo $rekomendasi;
            ?>
        </td>
    </tr>
</table>
<div style="margin-top:30px"></div>
<table class="datatable3" style="width:80%">
    <tr>
        <td colspan="2" align="center" style="width: 100px;">Diajukan Oleh</td>
        <td colspan="2" align="center" style="width: 100px;">Disetujui Cabang</td>
        <td colspan="3" align="center" style="width: 200px;">Disetujui Pusat</td>
    </tr>
    <tr>
        <td style="height: 100px; width:90px"></td>
        <td style="height: 100px; width:90px"></td>
        <td style="height: 100px; width:90px"></td>
        <td style="height: 100px; width:90px"></td>
        <td style="height: 100px; width:90px"></td>
        <td style="height: 100px; width:90px"></td>
        <td style="height: 100px; width:90px"></td>
    </tr>
    <tr>
        <td></td>
        <td></td>
        <td>{{ $limitkredit->time_kacab}}</td>
        <td>{{ $limitkredit->time_kacab}}</td>
        <td>{{ $limitkredit->time_mm}}</td>
        <td>{{ $limitkredit->time_gm}}</td>
        <td>{{ $limitkredit->time_dirut}}</td>
    </tr>
    <tr style="text-align: center;">
        <td>Salesman</td>
        <td>Driver</td>
        <td>SMM</td>
        <td>OM</td>
        <td>RSM</td>
        <td>GM</td>
        <td>Direktur</td>
    </tr>
</table>
</div>
