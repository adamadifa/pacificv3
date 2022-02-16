<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            <a href="/laporanpenjualan/penjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/penjualan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Penjualan
                </li>
            </a>
            <a href="/laporanretur" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanretur']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Retur
                </li>
            </a>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Kas Besar
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Tunai Kredit
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Analisa Umur Piutang (AUP)
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Kartu Piutang
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Lebih 1 Faktur
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>DPPP
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>DPP
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>REPO
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Rekap Omset Pelanggan
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Rekap Pelanggan
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Rekap Penjualan
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Rekap Kendaraan
            </li>
            <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>Harga Net
            </li>
        </ul>
    </div>
</div>
