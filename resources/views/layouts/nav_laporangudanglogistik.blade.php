<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Gudang Logistik</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_pemasukan_gl))
            <a href="/laporangudanglogistik/pemasukan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudanglogistik/pemasukan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Lapoaran Pemasukan Barang
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_pengeluaran_gl))
            <a href="/laporangudanglogistik/pemasukan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporangudanglogistik/pengeluaran']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Lapoaran Pengeluaran Barang
                </li>
            </a>
            @endif

        </ul>
    </div>
</div>
