<div class="card">
    <div class="card-content">
        <div class="card-body">
            <h4 class="card-title">Laporan</h4>
        </div>
        <ul class="list-group list-group-flush">
            <a href="/laporankomisi" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporankomisi']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Komisi
                </li>
            </a>
            <a href="/laporaninsentif" style="color:#626262">
                <li class="list-group-item {{ request()->is(['laporaninsentif']) ? 'active' : '' }}">
                    <i class="feather icon-file mr-1"></i>Insentif KA Admin
                </li>
            </a>
        </ul>
    </div>
</div>
