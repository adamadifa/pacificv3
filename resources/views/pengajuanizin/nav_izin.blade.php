<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin') ? 'active' : '' }}" href=" /pengajuanizin">Izin Absen</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#home">Izin Keluar</a>
</li>
<li class="nav-item">
    <a class="nav-link {{ request()->is('pengajuanizin/izinpulang') ? 'active' : '' }}" href="/pengajuanizin/izinpulang">Izin Pulang</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#home">Sakit</a>
</li>
<li class="nav-item">
    <a class="nav-link" href="#home">Cuti</a>
</li>
