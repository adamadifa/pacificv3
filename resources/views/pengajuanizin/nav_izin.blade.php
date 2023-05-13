<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin') ? 'active' : '' }}" href=" /pengajuanizin">Izin Absen</a>
</li>
<li class="nav-item">
    <a class="nav-link  {{ request()->is('pengajuanizin/izinkeluar') ? 'active' : '' }}" href="/pengajuanizin/izinkeluar">Izin Keluar</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/izinpulang') ? 'active' : '' }}" href="/pengajuanizin/izinpulang">Izin Pulang</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/izinterlambat') ? 'active' : '' }}" href="/pengajuanizin/izinterlambat">Izin Terlambat</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/sakit') ? 'active' : '' }}" href="/pengajuanizin/sakit">Sakit</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/cuti') ? 'active' : '' }}" href="/pengajuanizin/cuti">Cuti</a>
</li>
