<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('pageTitle')</title>
    <link href="{{ asset('/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/admin.css') }}" rel="stylesheet">
    <!-- Fonts -->
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
@yield('styles')
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script>
        var GLOBALS = {
            site_url: '{{url('')}}',
            datablesLang: '//cdn.datatables.net/plug-ins/1.10.7/i18n/English.json',
            prefix: '{{ config('selfy-admin.routePrefix', '/') }}',
            token: '{{ csrf_token() }}'
        };
    </script>
    @if(config('app.locale') == 'es')
        <script>
            GLOBALS.datablesLang = '//cdn.datatables.net/plug-ins/1.10.7/i18n/Spanish.json';
        </script>
    @endif()
</head>
<body class="skin-blue sidebar-mini">
<div class="wrapper">
    @include('admin.partials.nav')
    <aside class="main-sidebar">
        @include('admin.partials.sidebar')
    </aside>
    <div class="content-wrapper">
        <section class="content-header">
            @yield('contentHeader')
        </section>
        <section class="content">
        @yield('content')
        <!-- delete form for elements -->
            {!! Form::open(['id' => 'deleteForm', 'method' => 'DELETE']) !!}
            {!! Form::close() !!}
        </section>
    </div>
    <footer class="main-footer">
        <div class="pull-right hidden-xs">
            <b>Version</b> {{ config('laravel-admin.version', '1.0') }}
        </div>
        <strong>{{ config('laravel-admin.copyright', 'Copyright © Laravel Admin - All rights reserved.') }}</strong>
    </footer>
</div>
<!-- Scripts -->
<script src="https://code.jquery.com/jquery-2.2.4.min.js" integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>
<script src="{{ asset('/js/all.js') }}"></script>
@yield('scripts')
</body>
</html>
