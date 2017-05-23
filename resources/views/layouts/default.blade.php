<!doctype html>
<html>
    <head>
        @include('atoms.head')
    </head>
    <body>
        <div class="container-fluid">
            <header class="row">
                @include('atoms.header')
            </header>
           <div id="main" class="row">
               @yield('content')
           </div>
            <footer class="row">
                @include('atoms.footer')
            </footer>
        </div>
    </body>
</html>
