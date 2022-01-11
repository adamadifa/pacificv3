<link rel="stylesheet" type="text/css" href="{{asset('app-assets/css/report/report.css')}}">
<h4>
    DATA PELANGGAN
    <br>
    @if (!empty($cbg))
    CABANG {{ $cbg }}
    @endif
    <br>
    @if ($salesman != null)
    SALESMAN {{ $salesman->nama_karyawan }}
    @endif
    <br>
    @if (!empty($dari) AND !empty($sampai))
    PERIODE {{ date("d-m-y",strtotime($dari)) }} s/d {{ date("d-m-y",strtotime($sampai)) }}
    @endif
</h4>


<table class="datatable3" style="width:150%">
    <thead bgcolor="#024a75" style="color:white; font-size:12;">
        <tr bgcolor="#024a75" style="color:white; font-size:12;">
            <th width="10px">No</th>
            <th style="width:5%">Kode Pelanggan</th>
            <th>NIK</th>
            <th style="width:6%">Nama Pelanggan</th>
            <th style="width:4%">Tgl Lahir</th>
            <th>HP</th>
            <th style="width:10%">Alamat</th>
            <th style="width:10%">Alamat Toko</th>
            <th>Pasar</th>
            <th>Hari</th>
            <th>Cabang</th>
            <th>Salesman</th>
            <th>Latitude</th>
            <th>Longitude</th>
            <th>Limit</th>
            <th>Omset</th>
            <th>Jatuh Tempo</th>
            <th>Kepemilikan</th>
            <th>Lama Berjualan</th>
            <th>Status Pelanggan</th>
            <th>Tanggal Input</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($pelanggan as $d)
        <tr>
            <td style="text-align: center">{{ $loop->iteration }}</td>
            <td>{{ $d->kode_pelanggan }}</td>
            <td>{{ $d->nik }}</td>
            <td>{{ strtoupper($d->nama_pelanggan) }}</td>
            <td>{{ date("d-m-y",strtotime($d->tgl_lahir)) }}</td>
            <td>{{ $d->no_hp }}</td>
            <td>{{ ucwords(strtolower($d->alamat_pelanggan)) }}</td>
            <td>{{ ucwords(strtolower($d->alamat_toko)) }}</td>
            <td>{{ $d->pasar }}</td>
            <td>{{ $d->hari }}</td>
            <td>{{ $d->kode_cabang }}</td>
            <td>{{ $d->nama_karyawan }}</td>
            <td>{{ $d->latitude }}</td>
            <td>{{ $d->longitude }}</td>
            <td align="right">{{ rupiah($d->limitpel) }}</td>
            <td align="right">{{ rupiah($d->omset_toko) }}</td>
            <td>{{ $d->jatuhtempo }} Hari</td>
            <td>{{ $d->kepemilikan }}</td>
            <td>{{ $d->lama_usaha }}</td>
            <td>
                @if ($d->status_pelanggan ==1)
                Aktif
                @else
                Tidak Aktif
                @endif
            </td>
            <td>{{ $d->time_stamps }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
