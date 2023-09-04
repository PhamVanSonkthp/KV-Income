<header id="header">
    <div class="header-wrapper m-0 bg-white">
        <div class="header-wrapper__title text-primary">
            @if(strpos($_SERVER['REQUEST_URI'], 'detail') !== false || strpos($_SERVER['REQUEST_URI'], 'create') !== false || strpos($_SERVER['REQUEST_URI'], 'edit') !== false)
                <div class="back__url">
                    <a class="page-link" href="{{ route('administrator.'.$prefixView.'.index') }}">
                        <i class="fa-solid fa-chevron-left text-secondary"></i>
                    </a>
                </div>
            @endif
            <span>{{ $title }}</span>
            @if(request()->route()->getName() != 'administrator.dashboard.index')
                    @if(strpos($_SERVER['REQUEST_URI'], 'create') !== false)
                        <div class="action__form">
                            <button class="custom_button text-center btn__create" onclick="create{{ $prefixView }}()">Create</button>
                            <a href="{{ route('administrator.'.$prefixView.'.index') }}" class="custom_button text-center">Cancel</a>
                        </div>
                    @elseif(strpos($_SERVER['REQUEST_URI'], 'detail') !== false)
                        @if((strpos($_SERVER['REQUEST_URI'], 'history-datas') === false) && (strpos($_SERVER['REQUEST_URI'], 'salaries') === false))
                            <div class="action__form">
                                <a href="{{ route('administrator.'.$prefixView.'.edit', ['id' => $id]) }}" class="custom_button btn__filter text-center">Update</a>
                                @if(auth()->id() != $id)
                                <a href="{{ route('administrator.'.$prefixView.'.delete', ['id' => $id]) }}" data-url="{{ route('administrator.'.$prefixView.'.delete', ['id' => $id]) }}" class="custom_button btn__reset text-center action_delete delete">Delete</a>
                                @endif
                            </div>
                        @endif
                    @elseif(strpos($_SERVER['REQUEST_URI'], 'edit') !== false)
                        <div class="action__form">
                            <button onclick="update{{ $prefixView }}({{ $id }})" class="custom_button btn__filter text-center">Save</button>
                            <a href="{{ route('administrator.'.$prefixView.'.detail', ['id' => $id]) }}" class="custom_button text-center">Cancel</a>
                        </div>
                    @else
                        <div class="search__form">
                            <form action="" autocomplete="off">
                                @if(strpos(route('administrator.orders.index'), $_SERVER['REQUEST_URI']))
                                    <input type="text" name="key" value="{{ request('key') }}" class="form-control" placeholder="Search id, bill code">
                                @elseif(strpos(route('administrator.audits.index'), $_SERVER['REQUEST_URI']))
                                    <input type="text" name="key" value="{{ request('key') }}" class="form-control" placeholder="Search admin user">
                                @else
                                    <input type="text" name="key" value="{{ request('key') }}" class="form-control" placeholder="Search id, name {{$title}}">
                                @endif
                                <span class="icon" onclick="onSearch()">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </form>
                            <div class="show_list"></div>
                        </div>
                    @endif
            @endif
        </div>
        <div class="header-wrapper__info">
            <div class="user__info">
                <div class="user__info-box">
                    <div class="user__name">{{ auth()->user()->name }}</div>
                    <a href="{{ route('administrator.logout') }}" class="user__avatar">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-log-in"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path><polyline points="10 17 15 12 10 7"></polyline><line x1="15" y1="12" x2="3" y2="12"></line></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="sidebar__menu bg-white">
        <div class="sidebar__menu--logo">
            <a href="{{ route('administrator.dashboard.index') }}">
                <img src="{{\App\Models\Helper::logoImagePath()}}" alt="" class="all_logo">
            </a>
        </div>
        <ul class="sidebar__menu--main bg-primary">
            <li>
                <a href="{{ route('administrator.roles.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.roles.index'), $_SERVER['REQUEST_URI'])) || (strpos(route('administrator.roles.create'), $_SERVER['REQUEST_URI'])) || (isset($id) && !empty($id) && (strpos(route('administrator.roles.detail', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (isset($id) && !empty($id) && (strpos(route('administrator.roles.edit', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (strpos($_SERVER['REQUEST_URI'], 'roles?') !== false) ? 'active' : '' }}">
                    <span class="content">Admin Group</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('administrator.users.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.users.index'), $_SERVER['REQUEST_URI'])) || (strpos(route('administrator.users.create'), $_SERVER['REQUEST_URI'])) || (isset($id) && !empty($id) && (strpos(route('administrator.users.detail', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (isset($id) && !empty($id) && (strpos(route('administrator.users.edit', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (strpos($_SERVER['REQUEST_URI'], 'users?') !== false) ? 'active' : '' }}">
                    <span class="content">Admin Users</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('administrator.audits.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.audits.index'), $_SERVER['REQUEST_URI'])) || (isset($id) && !empty($id) && (strpos(route('administrator.audits.detail', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (strpos($_SERVER['REQUEST_URI'], 'history-datas?') !== false) ? 'active' : '' }}">
                    <span class="content">Admin Activity Logs</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('administrator.employees.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.employees.index'), $_SERVER['REQUEST_URI'])) || (strpos(route('administrator.employees.create'), $_SERVER['REQUEST_URI'])) || (isset($id) && !empty($id) && (strpos(route('administrator.employees.detail', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (isset($id) && !empty($id) && (strpos(route('administrator.employees.edit', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (strpos($_SERVER['REQUEST_URI'], 'employees?') !== false) ? 'active' : '' }}">
                    <span class="content">Staffs</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('administrator.system_branches.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.system_branches.index'), $_SERVER['REQUEST_URI'])) || (strpos(route('administrator.system_branches.create'), $_SERVER['REQUEST_URI'])) || (isset($id) && !empty($id) && (strpos(route('administrator.system_branches.detail', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (isset($id) && !empty($id) && (strpos(route('administrator.system_branches.edit', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (strpos($_SERVER['REQUEST_URI'], 'system-branches?') !== false) ? 'active' : '' }}">
                    <span class="content">Branches</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('administrator.orders.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.orders.index'), $_SERVER['REQUEST_URI'])) || (strpos(route('administrator.orders.create'), $_SERVER['REQUEST_URI'])) || (isset($id) && !empty($id) && (strpos(route('administrator.orders.detail', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (isset($id) && !empty($id) && (strpos(route('administrator.orders.edit', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (strpos($_SERVER['REQUEST_URI'], 'orders?') !== false) ? 'active' : '' }}">
                    <span class="content">Orders</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>
            <li>
                <a href="{{ route('administrator.salaries.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.salaries.index'), $_SERVER['REQUEST_URI'])) || (strpos($_SERVER['REQUEST_URI'], 'salaries?') !== false) || (strpos($_SERVER['REQUEST_URI'], 'salaries/detail') !== false) || (isset($id) && !empty($id) && (strpos(route('administrator.salaries.branch', ['id' => $id]), $_SERVER['REQUEST_URI']))) || (isset($id) && !empty($id) && (strpos($_SERVER['REQUEST_URI'], 'branch/'.$id.'?') !== false)) ? 'active' : '' }}">
                    <span class="content">Employee Salaries</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>

            <li>
                <a href="{{ route('administrator.password.index') }}" class="sidebar-menu-item text-primary {{ (strpos(route('administrator.password.index'), $_SERVER['REQUEST_URI'])) ? 'active' : '' }}">
                    <span class="content">Setting</span>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </li>

        </ul>
    </div>
</header>

<script>
    function onSearch() {
        addUrlParameterObjects([
            {name: "key", value: $('input[name="key"]').val()},
        ])
    }
</script>
