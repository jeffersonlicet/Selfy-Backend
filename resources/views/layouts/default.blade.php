<!doctype html>
<html>
    <head>
        @include('atoms.head')
    </head>
    <body>
        <div class="container">
            <header class="row">
                @include('atoms.header')
            </header>
            <div id="main" class="row">
                <a href="javascript:void(0)" class="btn btn-raised active"><code>.active</code></a>
                <a href="javascript:void(0)" class="btn btn-raised btn-default">Default</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-primary">Primary</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-success">Success</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-info">Info</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-warning">Warning</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-danger">Danger</a>
                <a href="javascript:void(0)" class="btn btn-raised btn-link">Link</a>
                @yield('content')
            </div>
            <footer class="row">
                @include('atoms.footer')
            </footer>
        </div>
    </body>
</html>
