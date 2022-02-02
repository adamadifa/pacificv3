<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kode_barang }}</span>
        Kode Barang
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kode_produk }}</span>
        Kode Produk
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->nama_barang }}</span>
        Nama Barang
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->satuan }}</span>
        Satuan
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->kategori }}</span>
        Kategori
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->isipcsdus }}</span>
        Jml Pcs / Dus
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->isipack }}</span>
        Jml Pack / Dus
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-primary float-right">{{ $data->isipcs }}</span>
        Jml Pcs / Pack
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-success float-right">{{ rupiah($data->harga_dus) }}</span>
        Harga / Dus
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-success float-right">{{ rupiah($data->harga_pack) }}</span>
        Harga / Pack
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-success float-right">{{ rupiah($data->harga_pcs) }}</span>
        Harga / Pcs
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-danger float-right">{{ rupiah($data->harga_returdus) }}</span>
        Harga / Dus
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-danger float-right">{{ rupiah($data->harga_returpack) }}</span>
        Harga / Pack
    </li>
    <li class="list-group-item">
        <span class="badge badge-pill bg-danger float-right">{{ rupiah($data->harga_returpcs) }}</span>
        Harga / Pcs
    </li>
</ul>
