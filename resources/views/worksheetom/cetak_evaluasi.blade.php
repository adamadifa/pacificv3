<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Cetak Evaluasi Cabang {{ date('d-m-y') }}</title>
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
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <h4>MINUTE OF MEETING - EVALUASI OM & SMM</h4>
    <p>
    <table class="datatable3">
        <tr>
            <th>Cabang</th>

            <td>{{ $evaluasi->kode_cabang }}</td>
        </tr>
        <tr>
            <th>Tanggal</th>

            <td>{{ DateToIndo2($evaluasi->tanggal) }}</td>
        </tr>
        <tr>
            <th>Jam</th>

            <td>{{ $evaluasi->jam }}</td>
        </tr>
        <tr>
            <th style="vertical-align: top">Peserta</th>
            <td style="vertical-align: top">{!! $evaluasi->peserta !!}</td>
        </tr>
        <tr>
            <th>Tempat</th>
            <td>{{ $evaluasi->tempat }}</td>
        </tr>
    </table>
    </p>
    <p>
    <table class="datatable3">
        <thead bgcolor="#024a75" style="color:white; font-size:12;">
            <tr>
                <th>No.</th>
                <th>Agenda</th>
                <th>Hasil Pembahasan</th>
                <th>Action Plan</th>
                <th>PIC</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($detailevaluasi as $d)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $d->agenda }}</td>
                    <td>{{ $d->hasil_pembahasan }}</td>
                    <td>{{ $d->action_plan }}</td>
                    <td>{{ $d->pic }}</td>
                    <td>{{ DateToIndo2($d->due_date) }}</td>
                    <td>
                        @if ($d->status == '1')
                            <span class="badge bg-danger">Open</span>
                        @elseif ($d->status == '2')
                            <span class="badge bg-info">On Progress</span>
                        @elseif ($d->status == '3')
                            <span class="badge bg-success">Close</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    </p>
</body>

</html>
