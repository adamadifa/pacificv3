<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link {{ request()->is(['dashboardsfa']) ? 'active' : '' }}" href="/dashboardsfa">SFA Salesman</a>
    </li>
    @if ($level == 'rsm' || $level == 'manager marketing' || $level == 'direktur' || $level == 'admin')
        <li class="nav-item">
            <a class="nav-link {{ request()->is(['dashboardsfakp']) ? 'active' : '' }}" href="/dashboardsfakp">SFA SMM</a>
        </li>
    @endif

    @if ($level == 'manager marketing' || $level == 'direktur' || $level == 'admin')
        <li class="nav-item">
            <a class="nav-link {{ request()->is(['dashboardsfarsm']) ? 'active' : '' }}" href="/dashboardsfarsm">SFA
                RSM</a>
        </li>
    @endif

    @if ($level == 'manager marketing' || $level == 'direktur' || $level == 'admin')
        <li class="nav-item">
            <a class="nav-link {{ request()->is(['dashboardsfagm']) ? 'active' : '' }}" href="/dashboardsfagm">SFA
                GM</a>
        </li>
    @endif
</ul>
