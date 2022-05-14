@if (in_array($level,$laporan_accounting))
<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            @if (in_array($level,$laporan_rekapbj_acc))
            <a href="/laporanaccounting/rekapbj" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanaccounting/rekapbj']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap BJ <span class="badge bg-success">v1</span>
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_rekapbj_acc))
            <a href="/laporanaccounting/rekappersediaan" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanaccounting/rekappersediaan']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Rekap Persediaan
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_rekapbj_acc))
            <a href="/laporanaccounting/bukubesar" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanaccounting/bukubesar']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Buku Besar
                </li>
            </a>
            @endif
            @if (in_array($level,$laporan_jurnalumum))
            <a href="/laporanaccounting/jurnalumum" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporanaccounting/jurnalumum']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Jurnal Umum
                </li>
            </a>
            @endif
        </ul>
    </div>
</div>
@endif
