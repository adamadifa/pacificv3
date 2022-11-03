@if (in_array($level,$laporan_ga))
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_servicekendaraan))
            <a href="/laporanga/servicekendaraan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanga/servicekendaraan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Service Kendaraan
                </li>
            </a>
            @endif
            @if (in_array($level,$rekap_badstokga))
            <a href="/laporanga/rekapbadstok" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanga/rekapbadstok']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Bad Stock
                </li>
            </a>
            @endif
        </ul>
    </div>
</div>
@endif
