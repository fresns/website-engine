<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="/static/css/bootstrap.min.css?v={{ $versionMd5 }}">
    <link rel="stylesheet" href="/static/css/bootstrap-icons.min.css?v={{ $versionMd5 }}">
    <link rel="stylesheet" href="/static/css/select2.min.css?v={{ $versionMd5 }}">
    <link rel="stylesheet" href="/static/css/select2-bootstrap-5-theme.min.css?v={{ $versionMd5 }}">
    @stack('style')
</head>

<body>
    @yield('body')

    <div class="fresns-tips">
        @include('FsView::commons.tips')
    </div>

    <script src="/static/js/bootstrap.bundle.min.js?v={{ $versionMd5 }}"></script>
    <script src="/static/js/jquery.min.js?v={{ $versionMd5 }}"></script>
    <script src="/static/js/select2.min.js?v={{ $versionMd5 }}"></script>
    <script src="/static/js/fresns-theme-functions.js?v={{ $versionMd5 }}"></script>
    @stack('script')
</body>

</html>
