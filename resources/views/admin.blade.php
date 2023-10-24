@extends('WebEngine::layout')

@section('body')
    <form action="{{ route('web-engine.admin.update') }}" method="post">
        @csrf
        @method('put')

        <!-- service config -->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">{{ __('WebEngine::fresns.webengine_config') }}:</label>
            <div class="col-lg-5">
                <div class="input-group mb-3">
                    <label class="input-group-text">{{ __('WebEngine::fresns.webengine_status') }}</label>
                    <div class="form-control bg-white">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="webengine_status" id="webengine_status_activate" value="true" @if(($params['webengine_status'] ?? '')) checked @endif>
                            <label class="form-check-label" for="webengine_status_activate">{{ __('FsLang::panel.option_activate') }}</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="webengine_status" id="webengine_status_deactivate" value="false" @if(!($params['webengine_status'] ?? '')) checked @endif>
                            <label class="form-check-label" for="webengine_status_deactivate">{{ __('FsLang::panel.option_deactivate') }}</label>
                        </div>
                    </div>
                </div>
                <!-- api config -->
                <div id="accordionApiType">
                    <!--api_type-->
                    <div class="input-group mb-3">
                        <label class="input-group-text">{{ __('WebEngine::fresns.webengine_api_type') }}</label>
                        <div class="form-control bg-white">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="webengine_api_type" id="api_local" value="local" data-bs-toggle="collapse" data-bs-target=".local_key_setting:not(.show)" aria-expanded="true" aria-controls="local_key_setting" @if(($params['webengine_api_type'] ?? '') == 'local') checked @endif>
                                <label class="form-check-label" for="api_local">{{ __('FsLang::panel.option_local') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="webengine_api_type" id="api_remote" value="remote" data-bs-toggle="collapse" data-bs-target=".remote_key_setting:not(.show)" aria-expanded="false" aria-controls="remote_key_setting" @if(($params['webengine_api_type'] ?? '') == 'remote') checked @endif>
                                <label class="form-check-label" for="api_remote">{{ __('FsLang::panel.option_remote') }}</label>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ __('FsLang::panel.table_platform') }}</span>
                        <select class="form-select">
                            @foreach ($params['platforms'] as $platform)
                                <option value="{{ $platform['id'] }}" @if ($platform['id'] != 4) disabled @else selected @endif>{{ $platform['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group mb-3">
                        <span class="input-group-text">{{ __('FsLang::panel.table_type') }}</span>
                        <div class="form-control bg-white">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="1" id="fresns_key" checked>
                                <label class="form-check-label" for="fresns_key">{{ __('FsLang::panel.key_option_main_api') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="2" id="admin_key" disabled>
                                <label class="form-check-label" for="admin_key">{{ __('FsLang::panel.key_option_manage_api') }}</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="3" id="plugin_key" disabled>
                                <label class="form-check-label" for="plugin_key">{{ __('FsLang::panel.key_option_plugin_api') }}</label>
                            </div>
                        </div>
                    </div>
                    <!--api_type config-->
                    <!--api_local-->
                    <div class="collapse local_key_setting {{ ($params['webengine_api_type'] ?? 'local') == 'local' ? 'show' : '' }}" aria-labelledby="api_local" data-bs-parent="#accordionApiType">
                        <div class="input-group mb-2">
                            <label class="input-group-text">{{ __('WebEngine::fresns.webengine_key_id') }}</label>
                            <select class="form-select" name="webengine_key_id">
                                <option value="" {{ !($params['webengine_key_id'] ?? '') ? 'selected' : '' }}>{{ __('FsLang::panel.option_not_set') }}</option>
                                @foreach ($keys as $key)
                                    <option value="{{ $key->id }}" {{ ($params['webengine_key_id'] ?? '') == $key->id ? 'selected' : '' }}>{{ $key->app_id }} - {{ $key->name }}</option>
                                @endforeach
                            </select>
                            <a class="btn btn-outline-secondary" href="{{ route('panel.keys.index') }}" target="_blank" role="button">{{ __('FsLang::panel.button_view') }}</a>
                        </div>
                    </div>
                    <!--api_remote-->
                    <div class="collapse remote_key_setting {{ ($params['webengine_api_type'] ?? 'local') == 'remote' ? 'show' : '' }}" aria-labelledby="api_remote" data-bs-parent="#accordionApiType">
                        <div class="input-group mb-3">
                            <label class="input-group-text">API Host</label>
                            <input type="url" class="form-control" name="webengine_api_host" id="webengine_api_host" value="{{ $params['webengine_api_host'] ?? '' }}" placeholder="https://">
                        </div>
                        <div class="input-group mb-3">
                            <label class="input-group-text">API ID</label>
                            <input type="text" class="form-control" name="webengine_api_app_id" id="webengine_api_app_id" value="{{ $params['webengine_api_app_id'] ?? '' }}">
                        </div>
                        <div class="input-group mb-2">
                            <label class="input-group-text">API Secret</label>
                            <input type="text" class="form-control" name="webengine_api_app_secret" id="webengine_api_app_secret" value="{{ $params['webengine_api_app_secret'] ?? '' }}">
                        </div>
                    </div>
                    <!--api_type config end-->
                </div>
                <!-- api config end -->
            </div>
        </div>

        <!-- view config -->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">{{ __('WebEngine::fresns.webengine_view_config') }}:</label>
            <div class="col-lg-5">
                <div class="input-group mb-3">
                    <label class="input-group-text">{{ __('WebEngine::fresns.webengine_view_desktop') }}</label>
                    <select class="form-select" name="webengine_view_desktop">
                        <option value="" {{ !($params['webengine_view_desktop'] ?? '') ? 'selected' : '' }}>{{ __('FsLang::panel.option_not_set') }}</option>
                        @foreach ($plugins as $plugin)
                            <option value="{{ $plugin->fskey }}" {{ ($params['webengine_view_desktop'] ?? '') == $plugin->fskey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="input-group">
                    <label class="input-group-text">{{ __('WebEngine::fresns.webengine_view_mobile') }}</label>
                    <select class="form-select" name="webengine_view_mobile">
                        <option value="" {{ !($params['webengine_view_mobile'] ?? '') ? 'selected' : '' }}>{{ __('FsLang::panel.option_not_set') }}</option>
                        @foreach ($plugins as $plugin)
                            <option value="{{ $plugin->fskey }}" {{ ($params['webengine_view_mobile'] ?? '') == $plugin->fskey ? 'selected' : '' }}>{{ $plugin->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('WebEngine::fresns.webengine_view_intro') }}</div>
        </div>

        <!-- website interaction -->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">{{ __('WebEngine::fresns.website_status') }}:</label>
            <div class="col-lg-5 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="webengine_interaction_status" id="webengine_interaction_status_true" value=true data-bs-toggle="collapse" data-bs-target=".webengine_interaction_status_setting.show" aria-expanded="false" aria-controls="webengine_interaction_status_setting" {{ ($params['webengine_interaction_status'] ?? '') ? 'checked' : '' }}>
                    <label class="form-check-label" for="webengine_interaction_status_true">{{ __('FsLang::panel.option_open') }}</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="webengine_interaction_status" id="webengine_interaction_status_false" value=false data-bs-toggle="collapse" data-bs-target=".webengine_interaction_status_setting:not(.show)" aria-expanded="false" aria-controls="webengine_interaction_status_setting" {{ !($params['webengine_interaction_status'] ?? '') ? 'checked' : '' }}>
                    <label class="form-check-label" for="webengine_interaction_status_false">{{ __('FsLang::panel.option_close') }}</label>
                </div>
                <!-- Status Config -->
                <div class="collapse webengine_interaction_status_setting {{ !($params['webengine_interaction_status'] ?? '') ? 'show' : '' }}">
                    <div class="card mt-1">
                        <div class="card-header text-success">{{ __('WebEngine::fresns.website_status_config') }}</div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="webengine_interaction_number">{{ __('WebEngine::fresns.website_status_config_content_number') }}</label>
                                <input type="number" class="form-control" name="webengine_interaction_number" id="webengine_interaction_number" value="{{ $params['webengine_interaction_number'] ?? '' }}">
                                <span class="input-group-text">{{ __('FsLang::panel.unit_number') }}</span>
                            </div>
                            <div class="input-group">
                                <label class="input-group-text" for="webengine_interaction_percentage">{{ __('WebEngine::fresns.website_status_config_content_percentage') }}</label>
                                <input type="number" class="form-control" name="webengine_interaction_percentage" id="webengine_interaction_percentage" value="{{ $params['webengine_interaction_percentage'] ?? '' }}">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text"><i class="bi bi-info-circle"></i> {{ __('WebEngine::fresns.website_status_config_desc') }}</div>
                        </div>
                    </div>
                </div>
                <!-- Status Config end -->
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> {{ __('WebEngine::fresns.website_status_desc') }}</div>
        </div>

        <!-- save -->
        <div class="row my-3">
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <button type="submit" class="btn btn-primary">{{ __('FsLang::panel.button_save') }}</button>
            </div>
        </div>
    </form>
@endsection
