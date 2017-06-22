<header class="main-header">
    <a href="{{config('selfy-admin.routePrefix')}}" class="logo">
        {{config('selfy-admin.appName')}}
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top" role="navigation">
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
                @if (!Auth::guest())
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="{{ Auth::user()->avatar }}" class="user-image" alt="User Image">
                            <span class="hidden-xs">{{ Auth::user()->username }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="User Image">
                                <p>
                                    {{ Auth::user()->username }}
                                </p>
                            </li>
                            <li class="user-footer">
                                <div class="pull-left">
                                    <a href="#" class="btn btn-default btn-flat">{{trans('selfy-admin.editMyProfile')}}</a>
                                </div>
                                <div class="pull-right">
                                    <a href="{{ route('SelfyAdminLogout') }}" class="btn btn-default btn-flat">{{trans('selfy-admin.LogoutText')}}</a>
                                </div>
                            </li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </nav>
</header>