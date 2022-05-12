<table class="table table-bordedanger">
    <tr>
        <td>No. DPB</td>
        <td>{{ $dpb->no_dpb }}</td>
    </tr>
    <tr>
        <td>Tanggal</td>
        <td>{{ DateToIndo2($dpb->tgl_pengambilan) }}</td>
    </tr>
    <tr>
        <td>Nama Salesman</td>
        <td>{{ $dpb->id_karyawan }} - {{ $dpb->nama_karyawan }}</td>
    </tr>
    <tr>
        <td>Cabang</td>
        <td>{{ $dpb->kode_cabang }}</td>
    </tr>
    <tr>
        <td>No. Kendaraan</td>
        <td>{{ $dpb->no_kendaraan }}</td>
    </tr>
    <tr>
        <td>Tujuan</td>
        <td>{{ $dpb->tujuan }}</td>
    </tr>
    <tr>
        <td>Driver</td>
        <td>{{ $dpb->nama_driver }}</td>
    </tr>
    <tr>
        <td>Helper</td>
        <td>
            {{ !empty($dpb->nama_helper_1) ? '(1) '.$dpb->nama_helper_1 : '' }}
            {{ !empty($dpb->nama_helper_2) ? '(2) '.$dpb->nama_helper_2 : '' }}
            {{ !empty($dpb->nama_helper_3) ? '(3) '.$dpb->nama_helper_3 : '' }}
        </td>
    </tr>
</table>
