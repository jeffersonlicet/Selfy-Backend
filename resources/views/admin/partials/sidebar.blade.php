<section class="sidebar">
    <div class="user-panel">
        <div class="pull-left image">
            <img src="{{ Auth::user()->avatar }}" class="img-circle" alt="{{ Auth::user()->username }}">
        </div>
        <div class="pull-left info">
            <p>{{ Auth::user()->username }}</p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
    <ul class="sidebar-menu">
        @if(isset($activeMenu))
            {!! $menu->render('sidebar', $activeMenu) !!}
        @else
            {!! $menu->render('sidebar') !!}
        @endif
    </ul>
</section>