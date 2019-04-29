@php
  // $package = PermissionCheck::getPackage();
  // $firm_modules = PermissionCheck::getPermissions();
@endphp

<aside class="main-sidebar">
  <section class="sidebar">
        <div class="user-panel">
          <div class="pull-left image">
              <img src="{{ Auth::user() ? Auth::user()->user_avatar : '' }}" onerror="this.src='/img/avatar.png'" class="img-circle avatar-img" alt="User Image">
          </div>
          <div class="pull-left info">
              <p>{{ isset(Auth::user()->name) ? Auth::user()->name : '' }}</p>
              <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
          </div>
        </div>

        <form action="#" method="get" class="sidebar-form">
          <div class="input-group">
              <input type="text" name="q" class="form-control" placeholder="Search...">
                <span class="input-group-btn">
                  <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
          </div>
        </form>

        @php
          $sidelinks = PermissionCheck::getSideLinks();
          // dump($sidelinks['module_groups']);
        @endphp
      <ul class="sidebar-menu">

        <li class='treeview 
          {{ request()->is('dashboard*') ? 'active' : '' }}'>
          <a href="/dashboard"><i class="fa fa-dashboard"></i> Dashboard</a>
        </li>
        @foreach($sidelinks['modules'] as $module)
          @php
            $groups = $sidelinks['module_groups'][strtolower($module->name)];
            $group_id = $module->id;
          @endphp

          @if(count($groups) == 0)
              @php $name = strtolower($module->name);  @endphp
              {{-- <li class='treeview {{ request()->is("$name*") ? "active" : ""  }}'>
                <a href="{{route($module->url)}}">
                  <i class="fa {{$module->icon}}"></i>
                  <span>{{$module->name}}</span>
                </a> 
              </li> --}}
          @else
              <li class='treeview {{ request()->is("$module->url*") ? "active" : ""  }}'>
                <a href="javascript:void(0);">
                  <i class="fa {{$module->icon}}"></i> 
                  <span>{{$module->name}}</span>
                    <span class="pull-right-container">
                      <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>

                <ul class="treeview-menu">
                    @foreach($groups as $group)
                      @if(array_value_match($group->permission, $sidelinks['permissions']))
                        <li class='{{ request()->is("$module->url/$group->url**") ? "active" : ""  }}'>
                          <a href='{{route($group->route)}}'>
                            <i class="fa {{$group->icon}}"></i>  {{$group->display_name}}</a>
                        </li>
                      @endif
                    @endforeach                   
                </ul>
              </li>
          @endif
        @endforeach
      </ul><!--end .main-menu -->
    </section>
</aside>
