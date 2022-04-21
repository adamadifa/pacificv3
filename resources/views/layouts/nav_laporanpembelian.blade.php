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

            @if (in_array($level,$laporan_auh))
            <a href="/laporanpembelian/auh" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/auh']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Analisa Umur Hutang (AUH)
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_bahankemasan))
            <a href="/laporanpembelian/bahankemasan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/bahankemasan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Bahan & Kemasan
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_rekapbahankemasan))
            <a href="/laporanpembelian/rekapbahankemasan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/rekapbahankemasan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Bahan Kemasan / Supplier
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_jurnalkoreksi))
            <a href="/laporanpembelian/jurnalkoreksi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/jurnalkoreksi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Jurnal Koreksi
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_rekapakunpembelian))
            <a href="/laporanpembelian/rekapakun" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/rekapakun']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Akun
                </li>
            </a>
            @endif

            @if (in_array($level,$laporan_rekapkontrabon))
            <a href="/laporanpembelian/rekapkontrabon" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanpembelian/rekapkontrabon']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Kontrabon
                </li>
            </a>
            @endif

        </ul>
    </div>
</div>
