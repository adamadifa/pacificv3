<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            <a href="/laporankeuangan/kaskecil" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kaskecil']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kas Kecil
                </li>
            </a>
            <a href="/laporankeuangan/ledger" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/ledger']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Ledger / Mutasi Bank
                </li>
            </a>
            <a href="/laporankeuangan/saldokasbesar" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/saldokasbesar']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Saldo Kas Besar
                </li>
            </a>
            <a href="/laporankeuangan/penjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/penjualan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Penjualan
                </li>
            </a>
            <a href="/laporankeuangan/uanglogam" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/uanglogam']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Uang Logam
                </li>
            </a>
            <a href="/laporankeuangan/rekapbg" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/rekapbg']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap BG
                </li>
            </a>
        </ul>
    </div>
</div>
