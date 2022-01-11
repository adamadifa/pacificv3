<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_polisi }}</span>
        No. Polisi
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->type }}</span>
        Type Kendaraan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->model }}</span>
        Model
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->tahun }}</span>
        Tahun
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_mesin }}</span>
        No. Mesin
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_stnk }}</span>
        No. STNK
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ date("d-m-Y",strtotime($data->pajak)) }}</span>
        Tanggal Pajak
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->atas_nama }}</span>
        Atas Nama
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ date("d-m-Y",strtotime($data->keur)) }}</span>
        Tanggal KEUR
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->no_uji }}</span>
        No. Uji
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ date("d-m-Y",strtotime($data->kir)) }}</span>
        Tanggal KIR
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ date("d-m-Y",strtotime($data->stnk)) }}</span>
        Tanggal STNK
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ date("d-m-Y",strtotime($data->sipa)) }}</span>
        Tanggal SIPA
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->pemakai }}</span>
        Pengguna
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->jabatan }}</span>
        Jabatan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->keterangan }}</span>
        Keterangan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->status }}</span>
        Status Kendaraan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kode_cabang }}</span>
        Kode Cabang
    </li>
</ul>
