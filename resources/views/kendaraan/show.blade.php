<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_polisi }}</span>
        No. Polisi
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->merk }}</span>
        Merk
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->tipe_kendaraan }}</span>
        Type Kendaraan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->tipe }}</span>
        Type
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_rangka }}</span>
        No. Rangka
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_mesin }}</span>
        No. Mesin
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->atas_nama }}</span>
        Atas Nama
    </li>

    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo_kir != null ? DateToIndo2($data->jatuhtempo_kir) : '' }}</span>
        Jatuh Tempo KIR
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo_pajak_satutahun != null ? DateToIndo2($data->jatuhtempo_pajak_satutahun) : '' }}</span>
        Jatuh Tempo Pajak 1 Tahun
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->jatuhtempo_pajak_limatahun != null ? DateToIndo2($data->jatuhtempo_pajak_limatahun) : '' }}</span>
        Jatuh Tempo Pajak 5 Tahun
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->jenis }}</span>
        Jenis
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kode_cabang }}</span>
        Cabang
    </li>
</ul>
