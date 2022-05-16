<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan Penjualan</h4>
        </div>
        <ul class="list-group list-group-flush">
            <a href="/laporanpenjualan/penjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/penjualan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Penjualan
                </li>
            </a>
            @if (Auth::user()->level != "staff keuangan 2" AND Auth::user()->level != "admin pajak")


            <a href="/laporanretur" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanretur']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Retur
                </li>
            </a>
            <a href="/laporankasbesarpenjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankasbesarpenjualan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kas Besar
                </li>
            </a>
            <a href="/laporanpenjualan/tunaikredit" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/tunaikredit']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Tunai Kredit
                </li>
            </a>
            <a href="/laporanpenjualan/kartupiutang" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/kartupiutang']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kartu Piutang
                </li>
            </a>
            <a href="/laporanpenjualan/aup" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/aup']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Analisa Umur Piutang
                </li>
            </a>

            <a href="/laporanpenjualan/lebihsatufaktur" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/lebihsatufaktur']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Lebih 1 Faktur
                </li>
            </a>
            <a href="/laporanpenjualan/dppp" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/dppp']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>DPPP
                </li>
            </a>
            <a href="/laporanpenjualan/dpp" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/dpp']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Data Pengambilan Pelanggan (DPP)
                </li>
            </a>
            {{-- <li class="list-group-item">
                <i class="feather icon-file mr-1"></i>REPO
            </li> --}}
            <a href="/laporanpenjualan/rekapomsetpelanggan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/rekapomsetpelanggan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Omset Pelanggan
                </li>
            </a>
            <a href="/laporanpenjualan/rekappelanggan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/rekappelanggan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Pelanggan
                </li>
            </a>
            <a href="/laporanpenjualan/rekappenjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/rekappenjualan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Penjualan
                </li>
            </a>
            <a href="/laporankendaraan/rekapkendaraan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankendaraan/rekapkendaraan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Kendaraan
                </li>
            </a>
            @if (in_array($level,$harga_net))
            <a href="/laporanpenjualan/harganet" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpenjualan/harganet']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Harga Net
                </li>
            </a>
            @endif
            @endif
        </ul>
    </div>
</div>
@if (in_array($level, $laporan_komisi))
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan Komisi</h4>
        </div>
        <ul class="list-group list-group-flush">
            <a href="#" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankomisi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Komisi <span class="badge bg-danger"><i class="fa fa-stop-circle-o"></i> Sedang Proses Maintenaince</span>
                </li>
            </a>
            <a href="/laporaninsentif" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporaninsentif']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Insentif KA Admin
                </li>
            </a>
        </ul>
    </div>
</div>
@endif
