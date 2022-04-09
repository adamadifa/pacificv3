<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_kaskecil))
            <a href="/laporankeuangan/kaskecil" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kaskecil']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kas Kecil
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_ledger))
            <a href="/laporankeuangan/ledger" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/ledger']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Ledger / Mutasi Bank
                </li>
            </a>
            @else
            <a href="/laporankeuangan/ledger" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/ledger']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i> Mutasi Bank
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_saldokasbesar))
            <a href="/laporankeuangan/saldokasbesar" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/saldokasbesar']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Saldo Kas Besar
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_lpu))
            <a href="/laporankeuangan/lpu" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/lpu']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Penerimaan Uang (LPU)
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_penjualan_keuangan))
            <a href="/laporankeuangan/penjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/penjualan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Penjualan
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_uanglogam))
            <a href="/laporankeuangan/uanglogam" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/uanglogam']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Uang Logam
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_rekapbg))
            <a href="/laporankeuangan/rekapbg" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/rekapbg']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap BG
                </li>
            </a>
            @endif
        </ul>
    </div>
</div>
