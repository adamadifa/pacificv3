@if (in_array($level, $lap_hrd) || Auth::user()->pic_presensi == 1)
    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">Laporan</h4>
            </div>
            <ul class="list-group list-group-flush">

                <a href="/laporanhrd/presensi" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanhrd/presensi']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Presensi
                    </li>
                </a>
                <a href="/laporanhrd/presensipsm" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanhrd/presensipsm']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Presensi P/S/M
                    </li>
                </a>
                <a href="/laporanhrd/rekapterlambat" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanhrd/rekapterlambat']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Rekap Keterlambatan
                    </li>
                </a>
                <a href="/laporanhrd/rekapsisacuti" style="color:#626262">
                    <li class="list-group-item {{ request()->is(['laporanhrd/rekapsisacuti']) ? 'active' : '' }}">
                        <i class="feather icon-file mr-1"></i>Rekap Sisa Cuti
                    </li>
                </a>

                @if ($level == 'admin' || $level == 'manager hrd' || $level == 'spv presensi' || $level == 'manager accounting')
                    <a href="/laporanhrd/gaji" style="color:#626262">
                        <li class="list-group-item {{ request()->is(['laporanhrd/gaji']) ? 'active' : '' }}">
                            <i class="feather icon-file mr-1"></i>Gaji
                        </li>
                    </a>
                @endif

            </ul>
        </div>
    </div>
@endif
