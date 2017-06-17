<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="Sparkly Team">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<link rel="apple-touch-icon" sizes="57x57" href="{{ URL::asset('/images/apple-icon-57x57.png') }}">
<link rel="apple-touch-icon" sizes="60x60" href="{{ URL::asset('/images/apple-icon-60x60.png') }}">
<link rel="apple-touch-icon" sizes="72x72" href="{{ URL::asset('/images/apple-icon-72x72.png') }}">
<link rel="apple-touch-icon" sizes="76x76" href="{{ URL::asset('/images/apple-icon-76x76.png') }}">
<link rel="apple-touch-icon" sizes="114x114" href="{{ URL::asset('/images/apple-icon-114x114.png') }}">
<link rel="apple-touch-icon" sizes="120x120" href="{{ URL::asset('/images/apple-icon-120x120.png') }}">
<link rel="apple-touch-icon" sizes="144x144" href="{{ URL::asset('/images/apple-icon-144x144.png') }}">
<link rel="apple-touch-icon" sizes="152x152" href="{{ URL::asset('/images/apple-icon-152x152.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ URL::asset('/images/apple-icon-180x180.png') }}">
<link rel="icon" type="image/png" sizes="192x192"  href="{{ URL::asset('/images/android-icon-192x192.png') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ URL::asset('/images/favicon-32x32.png') }}">
<link rel="icon" type="image/png" sizes="96x96" href="{{ URL::asset('/images/favicon-96x96.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ URL::asset('/images/favicon-16x16.png') }}">
<link rel="manifest" href="{{ URL::asset('/images/manifest.json') }}">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="msapplication-TileImage" content="/ms-icon-144x144.png') }}">
<meta name="theme-color" content="#ffffff">
<meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$pageTitle}}</title>
        <!-- Load CSS -->
        <link rel="stylesheet" href="{{ URL::asset('/css/core.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('/css/app.css') }}">

        <!-- Load Js -->
        <script src="{{ URL::asset('/js/libs.js') }}"></script>
        <script src="{{ URL::asset('/js/app.js') }}"></script>

        @if ($metaTags)
            <meta property="al:android:url" content="http://getselfy.net">
            <meta property="al:android:package" content="com.sparkly.selfy">
            <meta property="al:android:app_name" content="Selfy">
            <meta property="og:title" content="Selfy for android" />
            <meta property="og:type" content="website" />
        @endif

        <!-- Facebook metaTags -->
        <meta property="og:url" content="http://www.getselfy.net" />
        <meta property="og:title" content="Selfy" />
        <meta property="og:description" content="The smart and challenging photo sharing app." />
        <meta property="og:image" content="{{ URL::asset('/images/fb_image_sc.jpg') }}" />
        <meta property="fb:app_id" content="823207314366069" />

        <script type="text/javascript">
            var APP_URL = {!! json_encode(url('/')) !!};
        </script>