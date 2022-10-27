<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Effective Call {{ date("d-m-y") }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;500&display=swap');

        body {
            font-family: 'Poppins'
        }

        .datatable3 {
            border: 2px solid #D6DDE6;
            border-collapse: collapse;
            font-size: 14px;
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

    </style>
</head>
<body>
    <b style="font-size:14px;">
        @if ($cabang!=null)
        @if ($cabang->kode_cabang=="PST")
        PACIFIC PUSAT
        @else
        PACIFIC CABANG {{ strtoupper($cabang->nama_cabang) }}
        @endif
        @else
        PACIFC ALL CABANG
        @endif
        <br>
        LAPORAN Effective Call<br>
        PERIODE {{ DateToIndo2($dari) }} s/d {{ DateToIndo2($sampai) }}
        <br>

        <br />
    </b>
    <br>
    <table class="datatable3">
        <thead>
            <tr>
                <th>ID Salesman</th>
                <th>Nama Salesman</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ec as $d)
            <tr>
                <td>{{ $d->id_karyawan }}</td>
                <td>{{ $d->nama_karyawan }}</td>
                <td style="text-align: right">
                    <form action="/laporanpenjualan/cetak" method="post" id="frmEc" target="_blank">
                        @csrf
                        <input type="hidden" name="dari" value="{{ $dari }}">
                        <input type="hidden" name="sampai" value="{{ $sampai }}">
                        <input type="hidden" name="id_karyawan" value="{{ $d->id_karyawan }}">
                        <input type="hidden" name="kode_cabang" value="{{ $cabang->kode_cabang }}">
                        <input type="hidden" name="jenislaporan" value="formatsatubaris">
                        <a href="#" class="detailsalesman">{{ rupiah($d->ec) }}</a>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
@include('layouts.script')
<script>
    $(function() {
        $(".detailsalesman").click(function() {
            $(this).closest("form").submit();
        });
    });

</script>
</html>
