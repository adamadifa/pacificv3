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
            @if ($getcbg == "PCF" AND Auth::user()->id != 88)
            <a href="/laporankeuangan/ledger" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/ledger']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Ledger / Mutasi Bank
                </li>
            </a>
            @else
            @if (Auth::user()->id==115)
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
            @endif
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
            @if (in_array($level,$lap_pinjaman))
            <a href="/laporankeuangan/pinjaman" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/pinjaman']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Pinjaman
                </li>
            </a>
            @endif
            @if (in_array($level,$lap_pinjaman))
            <a href="/laporankeuangan/kartupinjaman" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kartupinjaman']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kartu Pinjaman
                </li>
            </a>
            @endif
            @if (in_array($level,$lap_kasbon))
            <a href="/laporankeuangan/kasbon" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kasbon']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kasbon
                </li>
            </a>
            @endif
            @if (in_array($level,$lap_kasbon))
            <a href="/laporankeuangan/kartukasbon" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kartukasbon']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kartu Kasbon
                </li>
            </a>
            @endif

            @if (in_array($level,$piutangkaryawan_view))
            <a href="/laporankeuangan/piutangkaryawan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/piutangkaryawan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Piutang Karyawan
                </li>
            </a>

            <a href="/laporankeuangan/kartupiutangkaryawan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kartupiutangkaryawan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Kartu Piutang Karyawan
                </li>
            </a>
            <a href="/laporankeuangan/kartupiutangall" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankeuangan/kartupiutangall']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Kartu Piutang
                </li>
            </a>
            @endif
        </ul>
    </div>
</div>
