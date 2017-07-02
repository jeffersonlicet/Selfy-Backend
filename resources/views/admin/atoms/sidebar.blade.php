<div class="nav-side-menu box-shadow--16dp">
    <div class="brand"><img src="{{ URL::asset('/images/admin-logo.png') }}"></div>

    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>

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

            <li class="last-child" data-toggle="collapse" data-target="#user" class="collapsed">
                <a href="#"><i class="material-icons">account_circle</i> Jefferson  <i class="material-icons arrow">arrow_drop_down</i></a>
            </li>

            <ul class="sub-menu collapse" id="user">
                <li><a href="#"><i class="material-icons">exit_to_app</i> Exit</a></li>
            </ul>
        </ul>
    </div>
</div>