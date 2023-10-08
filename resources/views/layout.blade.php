<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ __('FsLang::panel.fresns_panel') }}</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{{ @asset("/static/css/bootstrap.min.css?v={$versionMd5}") }}">
    <link rel="stylesheet" href="{{ @asset("/static/css/bootstrap-icons.min.css?v={$versionMd5}") }}">
    <link rel="stylesheet" href="{{ @asset("/static/css/select2.min.css?v={$versionMd5}") }}">
    <link rel="stylesheet" href="{{ @asset("/static/css/select2-bootstrap-5-theme.min.css?v={$versionMd5}") }}">
    <link rel="stylesheet" href="{{ @asset("/static/css/fresns-panel.css?v={$versionMd5}") }}">
    @stack('css')
</head>

<body>
    <header class="mt-4">
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ !Route::is('web-engine.admin.index') ? 'active' : '' }}" href="{{ $redirectURL }}">{{ __('WebEngine::fresns.view_config') }}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ Route::is('web-engine.admin.index') ? 'active' : '' }}" href="{{ route('web-engine.admin.index') }}">{{ __('WebEngine::fresns.webengine_config') }}</a>
            </li>
        </ul>
    </header>

    <main class="bg-white mb-2 p-3 p-lg-5">
        @yield('body')
    </main>

    <!--fresns tips-->
    <div class="fresns-tips">
        @include('FsView::commons.tips')
    </div>

    <!--imageZoom-->
    <div class="modal fade image-zoom" id="imageZoom" tabindex="-1" aria-labelledby="imageZoomLabel" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="position-relative image-box">
                <img class="img-fluid" src="">
            </div>
        </div>
    </div>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">&copy; <span class="copyright-year"></span> Fresns</p>
        </div>
    </footer>

    <script src="{{ @asset("/static/js/bootstrap.bundle.min.js?v={$versionMd5}") }}"></script>
    <script src="{{ @asset("/static/js/jquery.min.js?v={$versionMd5}") }}"></script>
    <script src="{{ @asset("/static/js/select2.min.js?v={$versionMd5}") }}"></script>
    <script>
        // panel lang
        $(document).ready(function () {
            window.locale = $('html').attr('lang')
            if (window.locale) {
                $.ajax({
                    url: "{{ route('panel.translations', ['locale' => \App::getLocale()]) }}",
                    method: 'get',
                    success(response) {
                        if (response.data) {
                            window.translations = response.data
                        } else {
                            console.error('Failed to get translation')
                        }
                    }
                })
            }
        });

        // copyright-year
        var yearElement = document.querySelector('.copyright-year');
        var currentDate = new Date();
        var currentYear = currentDate.getFullYear();
        if (yearElement) {
            yearElement.textContent = currentYear;
        }

        // set timeout toast hide
        const setTimeoutToastHide = () => {
            $('.toast.show').each((k, v) => {
                setTimeout(function () {
                    $(v).hide();
                }, 1500);
            });
        };
        setTimeoutToastHide();
    </script>
    @stack('script')
</body>

</html>
