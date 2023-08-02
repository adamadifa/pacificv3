<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date("d-m-y") }}</title>
    <style>
        /* @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        } */


        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 11px;
        }

        .datatable3 td {
            border: 1px solid #000000;
            padding: 6px;
        }

        .datatable3 th {
            border: 2px solid #828282;
            font-weight: bold;
            text-align: left;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        .freeze-table {
            height: 800px;
        }

    </style>
</head>
<body>
    <b style="font-size:14px;">
        LAPORAN PRESENSI
        <br>
        @if ($departemen != null)
        DEPARTEMEN {{ $departemen->nama_dept }}
        @else
        SEMUA DEPARTEMEN
        @endif
        <br>
        @if ($kantor != null)
        KANTOR {{ $kantor->nama_cabang }}
        @else
        SEMUA KANTOR
        @endif
        <br>
        @if ($group != null)
        GRUP {{ $group->nama_group }}
        @else
        SEMUA GRUP
        @endif
    </b>
    <br>
    <div class="freeze-table">
        <table class="datatable3" style="width: 200%">
            <thead bgcolor="#024a75" style="color:white; font-size:12;">
                <tr bgcolor="#024a75" style="color:white; font-size:12;">
                    <th rowspan="2">No</th>
                    <th rowspan="2">Nik</th>
                    <th rowspan="2">Nama karyawan</th>
                    <th rowspan="2">Grup</th>
                    <th colspan="9">DATA KARYAWAN</th>
                    <th rowspan="2">GAJI POKOK</th>
                    <th colspan="6">TUNJANGAN</th>
                    <th colspan="4">INSENTIF UMUM</th>
                    <th colspan="3">INSENTIF MANAGER</th>
                    <th rowspan="2">UPAH</th>
                    <th rowspan="2">JUMLAH<br>INSENTIF</th>

                </tr>
                <tr>

                    <th>TANGGAL MASUK</th>
                    <th>MASA KERJA</th>
                    <th>DEPARTEMEN</th>
                    <th>JABATAN</th>
                    <th>KANTOR <br>CABANG</th>
                    <th>PERUSAHAAN</th>
                    <th>KLASIFIKASI</th>
                    <th>JENIS <br>KELAMIN</th>
                    <th>STATUS</th>
                    <th>JABATAN</th>
                    <th>MASA KERJA</th>
                    <th>TANGGUNG<br> JAWAB</th>
                    <th>MAKAN</th>
                    <th>ISTRI</th>
                    <th>SKILL <br>KHUSUSs</th>
                    <th>MASA KERJA</th>
                    <th>LEMBUR</th>
                    <th>PENEMPATAN</th>
                    <th>KPI</th>
                    <th>RUANG<br> LINGKUP</th>
                    <th>PENEMPATAN</th>
                    <th>KINERJA</th>
                </tr>
            </thead>
            <tbody>
                @php
                $no = 1;
                $totaljam1bulan = 173;
                @endphp
                @foreach ($presensi as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->nik }}</td>
                    <td>{{ $d->nama_karyawan }}</td>
                    <td align="center">{{ $d->nama_group }}</td>
                    <td align="center">{{ date("d-m-Y",strtotime($d->tgl_masuk)) }}</td>
                    <td align="center">
                        @php
                        $awal = date_create($d->tgl_masuk);
                        $akhir = date_create(date('Y-m-d')); // waktu sekarang
                        $diff = date_diff( $awal, $akhir );
                        echo $diff->y . ' tahun, '.$diff->m.' bulan, '.$diff->d.' Hari'
                        @endphp
                    </td>
                    <td align="center">{{ $d->nama_dept }}</td>
                    <td align="center">{{ $d->nama_jabatan }}</td>
                    <td align="center">{{ $d->id_kantor=="PST" ? "PUSAT" : strtoupper($d->nama_cabang) }}</td>
                    <td align="center">{{ $d->id_perusahaan }}</td>
                    <td align="center">{{ $d->klasifikasi }}</td>
                    <td align="center">
                        {{ strtoupper($d->jenis_kelamin == "1" ? "Laki-Laki" : "Perempuan") }}
                    </td>
                    <td align="center">

                        @if ($d->status_kawin==1)
                        BELUM MENIKAH
                        @elseif($d->status_kawin==2)
                        MENIKAH
                        @elseif($d->status_kawin==3)
                        CERAI HIDUP
                        @elseif($d->status_kawin==4)
                        DUDA
                        @elseif($d->status_kawin==5)
                        JANDA
                        @endif
                    </td>
                    <td align="right">{{ !empty($d->gaji_pokok) ? rupiah($d->gaji_pokok) : "" }}</td>
                    <td align="right">{{ !empty($d->t_jabatan) ? rupiah($d->t_jabatan) : "" }}</td>
                    <td align="right">{{ !empty($d->t_masakerja) ? rupiah($d->t_masakerja) : "" }}</td>
                    <td align="right">{{ !empty($d->t_tanggungjawab) ? rupiah($d->t_tanggungjawab) : "" }}</td>
                    <td align="right">{{ !empty($d->t_makan) ? rupiah($d->t_makan) : "" }}</td>
                    <td align="right">{{ !empty($d->t_istri) ? rupiah($d->t_istri) : "" }}</td>
                    <td align="right">{{ !empty($d->t_skill) ? rupiah($d->t_skill) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_masakerja) ? rupiah($d->iu_masakerja) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_lembur) ? rupiah($d->iu_lembur) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_penempatan) ? rupiah($d->iu_penempatan) : "" }}</td>
                    <td align="right">{{ !empty($d->iu_kpi) ? rupiah($d->iu_kpi) : "" }}</td>
                    <td align="right">{{ !empty($d->im_ruanglingkup) ? rupiah($d->im_ruanglingkup) : "" }}</td>
                    <td align="right">{{ !empty($d->im_penempatan) ? rupiah($d->im_penempatan) : "" }}</td>
                    <td align="right">{{ !empty($d->im_kinerja) ? rupiah($d->im_kinerja) : "" }}</td>
                    <td align="right">
                        @php
                        $upah = $d->gaji_pokok + $d->t_jabatan+$d->t_masakerja + $d->t_tanggungjawab + $d->t_makan + $d->t_istri + $d->t_skill;
                        @endphp
                        {{ !empty($upah) ? rupiah($upah) : "" }}
                    </td>
                    <td align="right">
                        @php
                        $jmlinsentif = $d->iu_masakerja + $d->iu_lembur+$d->iu_penempatan + $d->iu_kpi + $d->im_ruanglingkup + $d->im_penempatan + $d->im_kinerja;
                        @endphp
                        {{ !empty($jmlinsentif) ? rupiah($jmlinsentif) : "" }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script type="text/javascript" src="{{ asset('dist/js/freeze/js/freeze-table.js') }}"></script>
<script>
    $(function() {
        $('.freeze-table').freezeTable({
            'scrollable': true
            , 'columnNum': 4
        });
    });

</script>
</html>
