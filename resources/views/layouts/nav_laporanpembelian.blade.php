<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_pembelian))
            <a href="/laporanpembelian" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pembelian
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_pembayaran_pembelian))
            <a href="/laporanpembelian/pembayaran" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/pembayaran']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pembayaran
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_rekappembeliansupplier))
            <a href="/laporanpembelian/rekapsupplier" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/rekapsupplier']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Pembelian Supplier
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_rekappembelian))
            <a href="/laporanpembelian/rekappembelian" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/rekappembelian']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Pembelian
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_kartuhutang))
            <a href="/laporanpembelian/kartuhutang" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/kartuhutang']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kartu Hutang
                </li>
            </a>
            @endif
        </ul>
    </div>
</div>
