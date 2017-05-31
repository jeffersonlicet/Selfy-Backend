<meta charset="utf-8">
<meta name="description" content="">
<meta name="author" content="Scotch">
    <title>{{$pageTitle}}</title>
        <!-- Load CSS -->
        <link rel="stylesheet" href="{{ URL::asset('/css/app.css') }}">
        <link rel="stylesheet" href="{{ URL::asset('/css/theme.css') }}">
        <!-- Load Js -->
        <script src="{{ URL::asset('/js/libs.js') }}"></script>
        <script src="{{ URL::asset('/js/app.js') }}"></script>

        @if ($metaTags)
        <meta property="al:android:url" content="http://getselfy.net/android">
        <meta property="al:android:package" content="com.sparkly.selfy">
        <meta property="al:android:app_name" content="Selfy">
        <meta property="og:title" content="Selfy for android" />
        <meta property="og:type" content="website" />
        @endif
