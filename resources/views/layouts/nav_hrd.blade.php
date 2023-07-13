@if (in_array($level,$lap_hrd) || Auth::user()->pic_presensi == 1)
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">

            <a href="/laporanhrd/presensi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanhrd/presensi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Laporan Presensi Peridoe Gaji
                </li>
            </a>
            <a href="/laporanhrd/rekapterlambat" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanhrd/rekapterlambat']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Keterlambatan
                </li>
            </a>

        </ul>
    </div>
</div>
@endif
