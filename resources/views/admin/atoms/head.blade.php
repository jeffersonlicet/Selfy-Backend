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
        <link rel="stylesheet" href="{{ URL::asset('/css/admin.css') }}">
        <!-- Load Js -->
        <script src="{{ URL::asset('/js/libs.js') }}"></script>
        <script src="{{ URL::asset('/js/admin.js') }}"></script>
        <script type="text/javascript">
            var APP_URL = {!! json_encode(url('/')) !!};
        </script>
<div class="modal" id="imagePreviewModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">Photo</h4>
            </div>
            <div class="modal-body">
                <div class="loader wow bounceIn" id="loading-view">
                    <svg class="circular" viewBox="25 25 50 50">
                        <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10"/>
                    </svg>
                </div>
                <img src="" id="imagePreview" class="img-responsive">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>