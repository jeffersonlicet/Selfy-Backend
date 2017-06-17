<!DOCTYPE html>
<html lang="en">
    <head>
        @include('atoms.head')
    </head>
    <body>
        @include('atoms.landing_head')
        @yield('content')
        @include('atoms.footer')
    </body>
</html>
