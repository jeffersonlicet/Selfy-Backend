<!doctype html>
<html>
    <head>
        @include('admin.atoms.head')
    </head>
    <body>
        @include('admin.atoms.sidebar')
        <div class="container-fluid with-sidebar">
           <div id="main" class="row">
               @yield('content')
           </div>
            @include('admin.atoms.footer')
        </div>
    </body>
</html>
