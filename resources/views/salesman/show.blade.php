<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->id_karyawan }}</span>
        ID Salesman
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->nama_karyawan }}</span>
        Nama Salesman
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_hp }}</span>
        No. HP
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->alamat_karyawan }}</span>
        Alamat Karyawan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kode_cabang }}</span>
        Cabang
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kategori_salesman }}</span>
        Kategori Salesman
    </li>
    <li class="list-group-item">
        @if ($data->status_aktif_sales ==1)
        <span class="badge badge-pill bg-success float-right"> Aktif </span>
        @else
        <span class="badge badge-pill bg-danger float-right">Non Aktif</span>
        @endif
        Status
    </li>

</ul>
