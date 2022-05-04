<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Gudang Logistik</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_pemasukan_gl))
            <a href="/laporangudanglogistik/pemasukan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudanglogistik/pemasukan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pemasukan Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_pengeluaran_gl))
            <a href="/laporangudanglogistik/pengeluaran" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudanglogistik/pengeluaran']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pengeluaran Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_persediaan_gl))
            <a href="/laporangudanglogistik/persediaan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudanglogistik/persediaan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Persediaan Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_persediaanopname_gl))
            <a href="/laporangudanglogistik/persediaanopname" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudanglogistik/persediaanopname']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Persediaan Opname
                </li>
            </a>
            @endif
        </ul>
    </div>
</div>

<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Gudang Bahan</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_pemasukan_gb))
            <a href="/laporangudangbahan/pemasukan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudangbahan/pemasukan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pemasukan Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_pengeluaran_gb))
            <a href="/laporangudangbahan/pengeluaran" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudangbahan/pengeluaran']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Pengeluaran Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_persediaan_gb))
            <a href="/laporangudangbahan/persediaan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudangbahan/persediaan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Persediaan Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_kartugudang))
            <a href="/laporangudangbahan/kartugudang" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudangbahan/kartugudang']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kartu Gudang Bahan / Kemasan
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_rekappersediaan))
            <a href="/laporangudangbahan/rekappersediaan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudangbahan/rekappersediaan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Persediaan
                </li>
            </a>
            @endif


        </ul>
    </div>
</div>
