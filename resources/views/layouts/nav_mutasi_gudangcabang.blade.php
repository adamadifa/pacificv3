@if (in_array($level,$mutasi_barang_cab_view))
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Mutasi IN</h4>
        </div>
        <ul class="list-group list-group-flush">

            <a href="/mutasigudangcabang/transitin" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/transitin']) ? 'active' : '' }}">
                    <i class="feather icon-truck mr-1"></i>Transit IN
                </li>
            </a>
            <a href="/mutasigudangcabang/retur" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/retur']) ? 'active' : '' }}">
                    <i class="feather icon-repeat mr-1"></i>Retur
                </li>
            </a>
            <a href="/mutasigudangcabang/hutangkirim" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/hutangkirim']) ? 'active' : '' }}">
                    <i class="feather icon-clipboard mr-1"></i>Hutang kirim
                </li>
            </a>
            <a href="/mutasigudangcabang/plttr" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/plttr']) ? 'active' : '' }}">
                    <i class="feather icon-clipboard mr-1"></i>Pelunasan TTR
                </li>
            </a>
            <a href="/repack" style="color:#626262">
                <li class="list-group-item {{ request()->is(['repack']) ? 'active' : '' }}">
                    <i class="feather icon-repeat mr-1"></i>Repack
                </li>
            </a>
        </ul>
    </div>
</div>
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Mutasi OUT</h4>
        </div>
        <ul class="list-group list-group-flush">

            <a href="/mutasigudangcabang/penjualan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/penjualan']) ? 'active' : '' }}">
                    <i class="feather icon-shopping-cart mr-1"></i>Penjualan
                </li>
            </a>
            <a href="/mutasigudangcabang/gantibarang" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/gantibarang']) ? 'active' : '' }}">
                    <i class="feather icon-repeat mr-1"></i>Ganti Barang
                </li>
            </a>
            <a href="/rejectgudang" style="color:#626262">
                <li class="list-group-item {{ request()->is(['rejectgudang']) ? 'active' : '' }}">
                    <i class="feather icon-log-out mr-1"></i>Reject Gudang
                </li>
            </a>
            <a href="/mutasigudangcabang/rejectpasar" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/rejectpasar']) ? 'active' : '' }}">
                    <i class="feather icon-log-out mr-1"></i>Reject Pasar
                </li>
            </a>
            <a href="/mutasigudangcabang/rejectmobil" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/rejectmobil']) ? 'active' : '' }}">
                    <i class="feather icon-log-out mr-1"></i>Reject Mobil
                </li>
            </a>
            <a href="/mutasigudangcabang/plhutangkirim" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/plhutangkirim']) ? 'active' : '' }}">
                    <i class="feather icon-clipboard mr-1"></i>Pelunasan Hutang Kirim
                </li>
            </a>
            <a href="/mutasigudangcabang/ttr" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/ttr']) ? 'active' : '' }}">
                    <i class="feather icon-clipboard mr-1"></i>TTR
                </li>
            </a>
            <a href="/mutasigudangcabang/promosi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['mutasigudangcabang/promosi']) ? 'active' : '' }}">
                    <i class="feather icon-tag mr-1"></i>Promosi
                </li>
            </a>
            <a href="/kirimpusat" style="color:#626262">
                <li class="list-group-item {{ request()->is(['kirimpusat']) ? 'active' : '' }}">
                    <i class="feather icon-truck mr-1"></i>Kirim Pusat
                </li>
            </a>

        </ul>
    </div>
</div>
@endif
