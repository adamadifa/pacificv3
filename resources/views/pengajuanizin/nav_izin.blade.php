<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin') ? 'active' : '' }}" href=" /pengajuanizin">Izin Absen
        @if (!empty($pi->tidakmasuk))
        <span class="badge bg-danger">{{ $pi->tidakmasuk }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link  {{ request()->is('pengajuanizin/izinkeluar') ? 'active' : '' }}" href="/pengajuanizin/izinkeluar">Izin Keluar
        @if (!empty($pi->keluar))
        <span class="badge bg-danger">{{ $pi->keluar }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/izinpulang') ? 'active' : '' }}" href="/pengajuanizin/izinpulang">Izin Pulang
        @if (!empty($pi->pulang))
        <span class="badge bg-danger">{{ $pi->pulang }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/izinterlambat') ? 'active' : '' }}" href="/pengajuanizin/izinterlambat">
        @if (!empty($pi->terlambat))
        <span class="badge bg-danger">{{ $pi->terlambat }}</span>
        @endif
        Izin Terlambat</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/sakit') ? 'active' : '' }}" href="/pengajuanizin/sakit">Sakit
        @if (!empty($pi->sakit))
        <span class="badge bg-danger">{{ $pi->sakit }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/cuti') ? 'active' : '' }}" href="/pengajuanizin/cuti">
        Cuti
        @if (!empty($pi->cuti))
        <span class="badge bg-danger">{{ $pi->cuti }}</span>
        @endif
    </a>
</li>

<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/koreksipresensi') ? 'active' : '' }}" href="/pengajuanizin/koreksipresensi">
        Koreksi Presensi
        @if (!empty($pi->koreksi))
        <span class="badge bg-danger">{{ $pi->koreksi }}</span>
        @endif
    </a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/perjalanandinas') ? 'active' : '' }}" href="/pengajuanizin/perjalanandinas">
        Perjalanan Dinas

    </a>
</li>
