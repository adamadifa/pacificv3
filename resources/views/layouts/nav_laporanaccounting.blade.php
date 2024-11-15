@if (in_array($level, $laporan_accounting))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Laporan</h4>
            </div>
            <ul class="list-group list-group-flush">
                @if (in_array($level, $laporan_rekapbj_acc))
                    <a href="/laporanaccounting/rekapbj" style="color:#626262">
                        <li class="list-group-item {{ request()->is(['laporanaccounting/rekapbj']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap BJ <span class="badge bg-success">v1</span>
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_rekapbj_acc))
                    <a href="/laporanaccounting/rekappersediaan" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporanaccounting/rekappersediaan']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Rekap Persediaan
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_jurnalumum))
                    <a href="/laporanaccounting/jurnalumum" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporanaccounting/jurnalumum']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Jurnal Umum
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_costratio))
                    <a href="/laporanaccounting/costratio" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporanaccounting/costratio']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Cost Ratio
                        </li>
                    </a>
                @endif

            </ul>
        </div>
    </div>
@endif
@if (in_array($level, $laporan_lk))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Laporan LK</h4>
            </div>
            <ul class="list-group list-group-flush">
                @if (in_array($level, $laporan_rekapbj_acc))
                    <a href="/laporanaccounting/bukubesar" style="color:#626262">
                        <li
                            class="list-group-item {{ request()->is(['laporanaccounting/bukubesar']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Buku Besar
                        </li>
                    </a>
                @endif

                @if (in_array($level, $laporan_rekapbj_acc))
                    <a href="/laporanaccounting/neraca" style="color:#626262">
                        <li class="list-group-item {{ request()->is(['laporanaccounting/neraca']) ? 'active' : '' }}">
                            <i class="fa fa-balance-scale mr-1"></i>Neraca
                        </li>
                    </a>
                @endif
                @if (in_array($level, $laporan_rekapbj_acc))
                    <a href="/laporanaccounting/labarugi" style="color:#626262">
                        <li class="list-group-item {{ request()->is(['laporanaccounting/labarugi']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Laba Rugi
                        </li>
                    </a>
                @endif
            </ul>
        </div>
    </div>
@endif
@if (in_array($level, $lpc_menu))
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Laporan LPC</h4>
            </div>
            <ul class="list-group list-group-flush">
                <a href="/visit/laporanVisit" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['visit/laporanVisit']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Visit Pelanggan
                    </li>
                </a>
                <a href="/realisasi/laporanRealisasi" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['realisasi/laporanRealisasi']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Realisasi Program
                    </li>
                </a>
                <a href="/jaminan/laporanJaminan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['jaminan/laporanJaminan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Jaminan
                    </li>
                </a>
                <a href="/karyawan/laporanKaryawan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['karyawan/laporanKaryawan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Sumber Daya Manusia
                    </li>
                </a>
                <a href="/kendaraan/laporanKendaraan" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['kendaraan/laporanKendaraan']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Sumber Daya Kendaraan
                    </li>
                </a>
            </ul>
        </div>
    </div>
@endif
