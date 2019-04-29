  <!-- Main Header -->
  <header class="main-header">

    <!-- Logo -->
    <a href="{{url('/dashboard')}}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>S</b>CRM</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>{{env('APP_NAME',"FORGINGSAAS")}}</b></span>
    </a>

    <!-- Header Navbar -->
    <nav class="navbar navbar-static-top" role="navigation">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <!-- Navbar Right Menu -->
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          {{-- <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{{ucfirst(user()->locale)}} - Translate <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                @foreach(LangCode() as $key => $value)
                  @if(\File::isDirectory(base_path()."/resources/lang/".$key))
                    <li><a href="{{route('get.lang_update', $key)}}">{{$value}}</a></li>
                    <li class="divider"></li>
                  @endif
                @endforeach
              </ul>
          </li> --}}

          <li>
            {{-- <a href="{{ route('setting.index') }}"><i class="fa fa-gears"></i></a> --}}
          </li>  
          <!-- User Account Menu -->
          <li class="dropdown user user-menu">
            <!-- Menu Toggle Button -->
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <!-- The user image in the navbar-->
              <img src="{{ Auth::user() ? Auth::user()->user_avatar : '' }}" class="user-image avatar-img" alt="User Image">
              <!-- hidden-xs hides the username on small devices so only the image appears. -->
              <span class="hidden-xs">{{ isset(Auth::user()->name) ? Auth::user()->name : '' }}</span>
            </a>
            <ul class="dropdown-menu">
              <!-- The user image in the menu -->
              <li class="user-header">
                <img src="{{ Auth::user() ? Auth::user()->user_avatar : '' }}" class="img-circle avatar-img" alt="User Image">

                <p>
                  {{ isset(Auth::user()->name) ? Auth::user()->name : '' }}
                </p>
              </li>
              <!-- Menu Body -->
              
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  {{-- <a href="{{ route('changepassword.create') }}" class="btn btn-default btn-flat">Change Password</a>  --}}
                </div>
                <div class="pull-right">
                  <a class="btn btn-default btn-flat" href="{{ url('/logout') }}"
                        onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                      Logout
                  </a>
                  <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                      {{ csrf_field() }}
                  </form>
                </div>
              </li>
            </ul>
          </li>
        </ul>
      </div>
    </nav>
  </header>