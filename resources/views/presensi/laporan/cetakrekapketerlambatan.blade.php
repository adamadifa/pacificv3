<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Laporan Penjualan {{ date("d-m-y") }}</title>
    <style>
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
        }

        .table-scroll {
            position: relative;
            max-width: 100%;
            margin: auto;
            overflow: hidden;

        }

        .table-wrap {
            width: 100%;
            overflow: auto;
        }

        .table-scroll table {
            width: 100%;
            margin: auto;
            border-collapse: separate;
            border-spacing: 0;
        }


        .clone {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
        }

        .clone th,
        .clone td {
            visibility: hidden
        }

        .clone td,
        .clone th {
            border-color: transparent
        }

        .clone tbody th {
            visibility: visible;
            color: red;
        }

        .clone .fixed-side {
            border: 1px solid #000;
            background: #eee;
            visibility: visible;
        } */

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
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr bgcolor="#024a75" style="color:white; font-size:12;">
                <th>No.</th>
                <th>Tanggal</th>
                <th>Nik</th>
                <th>Nama</th>
                <th>Dept</th>
                <th>Kantor</th>
                <th>Jadwal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Terlambat</th>
                <th>Denda</th>
                <th>Cek</th>
                <th>#</th>

            </tr>
        </thead>
        <tbody id="loadterlambat">

        </tbody>
    </table>
</body>
</html>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    $(function() {

        function getterlambat() {
            var id_kantor = "{{ $kantor != null ? $kantor->kode_cabang : '' }}";
            var kode_dept = "{{ $departemen != null ? $departemen->kode_dept : '' }}";
            var id_group = "{{ $group != null  ? $group->id : '' }}";
            var dari = "{{ $dari }}";
            var sampai = "{{ $sampai }}";

            $.ajax({
                type: 'POST'
                , url: '/laporanhrd/getterlambat'
                , data: {
                    _token: "{{ csrf_token() }}"
                    , id_kantor: id_kantor
                    , kode_dept: kode_dept
                    , id_group: id_group
                    , dari: dari
                    , sampai: sampai
                }
                , cache: false
                , success: function(respond) {
                    $("#loadterlambat").html(respond);
                }
            });
        }

        getterlambat();

    });

</script>
