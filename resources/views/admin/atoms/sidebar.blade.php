<div class="nav-side-menu box-shadow--16dp">
    <div class="brand"><img src="{{ URL::asset('/images/admin-logo.png') }}"></div>
    <i class="material-icons toggle-btn sidebar-toggler" data-toggle="collapse" data-target="#menu-content">menu</i>
    <div class="menu-list">
        <ul id="menu-content" class="menu-content collapse out">
            <li @if(Route::currentRouteName()  === 'DashboardIndex') class="active" @endif><a href="{{ action('Admin\AdminController@index') }}"><i class="material-icons">dashboard</i> Dashboard</a></li>
            <li><a href="#"><i class="material-icons">group</i> Users</a></li>

            <li  data-toggle="collapse" data-target="#photos" class="collapsed" aria-expanded="true">
                <a href="#"><i class="material-icons">photo_library</i> Photos  <i class="material-icons arrow">arrow_drop_down</i></a>
            </li>

            <ul class="sub-menu collapse in" id="photos" aria-expanded="true">
                <li><a href="#"><i class="material-icons">trending_up</i> Stats</a></li>
                <li><a href="#"><i class="material-icons">flag</i> Reported</a></li>
            </ul>

            <li  @if(Route::currentRouteName()  === 'AdminPlay') class="active collapsed" @else class="collapsed" @endif data-toggle="collapse" data-target="#challenges"  aria-expanded="true">
                <a href="#"><i class="material-icons">recent_actors</i> Challenges  <i class="material-icons arrow">arrow_drop_down</i></a>
            </li>

            <ul class="sub-menu collapse in" id="challenges" aria-expanded="true">
                <li @if(Route::currentRouteName()  === 'AdminPlay') class="active" @endif><a href="{{ action('Admin\AdminController@play') }}"><i class="material-icons">play_arrow</i> Manage Play</a></li>
                <li><a href="#"><i class="material-icons">place</i> Manage Spots</a></li>
            </ul>

            <li><a href="#"><i class="material-icons">label</i> Hashtags</a></li>
            <li @if(Route::currentRouteName()  === 'AdminPlaces') class="active" @endif><a href="{{ action('Admin\AdminController@places') }}"><i class="material-icons">my_location</i> Places</a></li>

            <li><a href="{{action('Admin\AdminController@meliDashboard')}}"><i class="material-icons">shop</i> Mercadolibre</a></li>

            <li class="last-child collapsed" data-toggle="collapse" data-target="#user">
                <a href="#"><img id="avatar-sidebar" class="img-circle" src="{{ str_replace('.jpg', 'm.jpg', \Auth::user()->avatar) }}" width="25"> Jefferson  <i class="material-icons arrow">arrow_drop_down</i></a>
            </li>

            <ul class="sub-menu collapse" id="user">
                <li><a href="#"><i class="material-icons">exit_to_app</i> Exit</a></li>
            </ul>
        </ul>
    </div>
</div>