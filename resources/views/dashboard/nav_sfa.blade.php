<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ request()->is(['dashboardsfa']) ? 'active' : '' }}" href="/dashboardsfa">SFA Salesman</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is(['dashboardsfakp']) ? 'active' : '' }}" href="/dashboardsfakp">SFA SMM</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->is(['dashboardsfarsm']) ? 'active' : '' }}" href="/dashboardsfarsm">SFA RSM</a>
    </li>
</ul>
