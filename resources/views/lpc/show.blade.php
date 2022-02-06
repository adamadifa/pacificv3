@foreach ($lpc as $d)
    <tr>
        <td>{{$loop->iteration}}</td>
        <td>{{$d->kode_cabang}}</td>
        <td>{{$bln[$d->bulan]}}</td>
        <td>{{$d->tahun}}</td>
        <td>{{date("d-m-Y",strtotime($d->tgl_lpc))}}</td>
        <td>
            <a class="ml-1 edit" href="#"><i class="feather icon-edit success"></i></a>
            <a class="ml-1 hapus" href="#"><i class="feather icon-trash danger"></i></a>
            <a class="ml-1 approve" href="#"><i class=" feather icon-check info"></i></a>
        </td>
    </tr>
@endforeach
