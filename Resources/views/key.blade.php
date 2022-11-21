@extends('FresnsEngine::layout')

@section('settings')
    <form class="mt-4" action="{{ route('fresnsengine.setting.key') }}" method="post">
        @csrf
        <div class="row mb-2">
            <div class="alert alert-primary" role="alert">
                @lang('FresnsEngine::fresns.apiDesc')
            </div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">{{ __('FsLang::panel.table_platform') }}:</label>
            <div class="col-lg-5">
                <select class="form-select" disabled>
                    <option value="4" selected>Responsive Web</option>
                </select>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('FsLang::panel.key_select_platform') }}</div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">{{ __('FsLang::panel.table_type') }}:</label>
            <div class="col-lg-5">
                <input class="form-control" type="text" value="{{ __('FsLang::panel.key_option_main_api') }}" disabled>
            </div>
            <div class="col-lg-5 form-text pt-1"></div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">API Host:</label>
            <div class="col-lg-5"><input type="url" name="fresnsengine_apihost" class="form-control" id="apihost" value="{{ old("fresnsengine_apihost", $content['fresnsengine_apihost'] ?? '') }}" required></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.apiHostDesc')</div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">App ID:</label>
            <div class="col-lg-5"><input type="text" name="fresnsengine_appid" class="form-control" id="appid" value="{{ old("fresnsengine_appid", $content['fresnsengine_appid'] ?? '' ) }}" required></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.appIdDesc')</div>
        </div>
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">App Secret:</label>
            <div class="col-lg-5"><input type="text" name="fresnsengine_appsecret" class="form-control" id="appsecret" value="{{ old("fresnsengine_appsecret", $content['fresnsengine_appsecret'] ?? '' ) }}" required></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.appSecretDesc')</div>
        </div>
        <div class="row mb-4">
            <div class="col-lg-2"></div>
            <div class="col-lg-10"><button type="submit" class="btn btn-primary">@lang('FresnsEngine::fresns.save')</button></div>
        </div>
    </form>
@endsection
