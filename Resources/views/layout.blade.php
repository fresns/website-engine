<!doctype html>
<html lang="{{ App::getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="Fresns" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Fresns Engine</title>
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ @asset('/static/css/fresns-panel.css') }}">
</head>

<body>

    <main>
        <div class="container-lg p-0 p-lg-3">
            <div class="bg-white shadow-sm mt-4 mt-lg-2 p-3 p-lg-5">
                <div class="row mb-2">
                    <div class="col-7">
                        <h3>@lang('FresnsEngine::fresns.name') <span class="badge bg-secondary fs-9">{{ $version }}</span></h3>
                        <p class="text-secondary">@lang('FresnsEngine::fresns.description')</p>
                    </div>
                    <div class="col-5">
                        <div class="input-group mt-2 mb-4 justify-content-lg-end px-1" role="group">
                            <a class="btn btn-outline-secondary" href="https://github.com/fresns/website" target="_blank" role="button"><i class="bi bi-github"></i> GitHub</a>
                            <a class="btn btn-outline-secondary" href="https://gitee.com/fresns/website" target="_blank" role="button"><i class="bi bi-git"></i> Gitee</a>
                        </div>
                    </div>
                </div>
                @if (session('success'))
                    <div class="alert alert-success mb-3 alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('failure'))
                    <div class="alert alert-danger mb-3 alert-dismissible fade show" role="alert">
                        {{ session('failure') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                <div class="mb-3">
                    <ul class="nav nav-tabs">
                        <li class="nav-item"><a class="nav-link {{ \Route::is(['fresnsengine.setting.key']) ? 'active' : '' }}" href="{{ route('fresnsengine.setting.key') }}">@lang('FresnsEngine::fresns.navKey')</a></li>
                        <li class="nav-item"><a class="nav-link {{ \Route::is(['fresnsengine.setting.general']) ? 'active' : '' }}" href="{{ route('fresnsengine.setting.general') }}">@lang('FresnsEngine::fresns.navGeneral')</a></li>
                    </ul>
                </div>
                <div class="tab-content">
                    @yield('settings')
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="copyright text-center">
            <p class="mt-5 mb-5 text-muted">Powered by Fresns</p>
        </div>
    </footer>

    <script src="{{ @asset('/static/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ @asset('/static/js/jquery-3.6.0.min.js') }}"></script>
</body>

</html>
