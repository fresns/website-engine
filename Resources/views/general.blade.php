@extends('FresnsEngine::layout')

@section('settings')
    <form class="mt-4" action="{{ route('fresnsengine.setting.general') }}" method="post">
        @csrf
        <!--web_stat_code-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEngine::fresns.webStatCodeTitle'):</label>
            <div class="col-lg-5"><textarea class="form-control" name="web_stat_code" rows="3">{{ old('web_stat_code', $content['web_stat_code']) }}</textarea></div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.webStatCodeDesc')</div>
        </div>
        <!--web_stat_position-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEngine::fresns.webStatPositionTitle'):</label>
            <div class="col-lg-5 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="web_stat_position" id="web_stat_position_head" value="head" @if(old('web_stat_position', $content['web_stat_position']) === "head") checked @endif>
                    <label class="form-check-label" for="web_stat_position_head">&lt;head&gt;</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="web_stat_position" id="web_stat_position_body" value="body" @if(old('web_stat_position', $content['web_stat_position']) === "body") checked @endif>
                    <label class="form-check-label" for="web_stat_position_body">&lt;body&gt;</label>
                </div>
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.webStatPositionDesc')</div>
        </div>
        <!--web_status-->
        <div class="row mb-4">
            <label class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEngine::fresns.webStatusTitle'):</label>
            <div class="col-lg-5 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="web_status" id="web_status_true" value=true data-bs-toggle="collapse" data-bs-target="#web_status_setting.show" aria-expanded="false" aria-controls="web_status_setting" @if( old('web_status', $content['web_status']) == 'true') checked @endif>
                    <label class="form-check-label" for="web_status_true">@lang('FresnsEngine::fresns.webStatusTrue')</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="web_status" id="web_status_false" value=false data-bs-toggle="collapse" data-bs-target="#web_status_setting:not(.show)" aria-expanded="false" aria-controls="web_status_setting" @if( old('web_status', $content['web_status']) == 'false') checked @endif>
                    <label class="form-check-label" for="web_status_false">@lang('FresnsEngine::fresns.webStatusFalse')</label>
                </div>
                <!--Web Status Config-->
                <div class="collapse @if( old('web_status', $content['web_status']) == 'false' ) show @endif" id="web_status_setting">
                    <div class="card mt-2">
                        <div class="card-header text-success">@lang('FresnsEngine::fresns.webStatusConfigTitle')</div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="web_number">@lang('FresnsEngine::fresns.webStatusConfigContentNumber')</label>
                                <input type="number" class="form-control" name="web_number" id="web_number" value="{{ old('web_number', $content['web_number']) }}">
                                <span class="input-group-text">@lang('FresnsEngine::fresns.webStatusConfigContentNumberUnit')</span>
                            </div>
                            <div class="input-group">
                                <label class="input-group-text" for="web_proportion">@lang('FresnsEngine::fresns.webStatusConfigContentProportion')</label>
                                <input type="number" class="form-control" name="web_proportion" id="web_proportion" value="{{ old('web_proportion', $content['web_proportion']) }}">
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.webStatusConfigDesc')</div>
                        </div>
                    </div>
                </div>
                <!--Web Status Config end-->
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.webStatusDesc')</div>
        </div>
        <!--site_china_mode-->
        <div class="row mb-4">
            <label for="site_copyright" class="col-lg-2 col-form-label text-lg-end">@lang('FresnsEngine::fresns.siteChinaModeTitle'):</label>
            <div class="col-lg-5 pt-2">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="site_china_mode" id="china_server_false" value=false data-bs-toggle="collapse" data-bs-target="#china_server_setting.show" aria-expanded="false" aria-controls="china_server_setting" @if( old('site_china_mode', $content['site_china_mode']) == 'false') checked @endif>
                    <label class="form-check-label" for="china_server_false">@lang('FresnsEngine::fresns.siteChinaModeFalse')</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="site_china_mode" id="china_server_true" value=true data-bs-toggle="collapse" data-bs-target="#china_server_setting:not(.show)" aria-expanded="false" aria-controls="china_server_setting" @if( old('site_china_mode', $content['site_china_mode']) == 'true') checked @endif>
                    <label class="form-check-label" for="china_server_true">@lang('FresnsEngine::fresns.siteChinaModeTrue')</label>
                </div>
                <!--China Mode Config-->
                <div class="collapse @if( old('site_china_mode', $content['site_china_mode']) == 'true' ) show @endif" id="china_server_setting">
                    <div class="card mt-2">
                        <div class="card-header">@lang('FresnsEngine::fresns.siteChinaModeConfigTitle')</div>
                        <div class="card-body">
                            <!--Config-->
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="site_miit_beian">@lang('FresnsEngine::fresns.siteMiitBeianTitle')</label>
                                <input type="text" class="form-control" id="site_miit_beian" name="site_miit_beian" value="{{ old('site_miit_beian', $content['site_miit_beian']) }}">
                            </div>
                            <div class="input-group mb-3">
                                <label class="input-group-text" for="site_miit_tsm">@lang('FresnsEngine::fresns.siteMiitTsmTitle')</label>
                                <input type="text" class="form-control" id="site_miit_tsm" name="site_miit_tsm" value="{{ old('site_miit_tsm', $content['site_miit_tsm']) }}">
                            </div>
                            <div class="input-group mb-1">
                                <label class="input-group-text" for="site_miit_gongan">@lang('FresnsEngine::fresns.siteMiitGonganTitle')</label>
                                <input type="text" class="form-control" id="site_miit_gongan" name="site_miit_gongan" value="{{ old('site_miit_gongan', $content['site_miit_gongan']) }}">
                            </div>
                            <!--Config end-->
                        </div>
                    </div>
                </div>
                <!--China Mode Config end-->
            </div>
            <div class="col-lg-5 form-text pt-1"><i class="bi bi-info-circle"></i> @lang('FresnsEngine::fresns.siteChinaModeTitle')</div>
        </div>
        <div class="row mb-4">
            <div class="col-lg-2"></div>
            <div class="col-lg-10"><button type="submit" class="btn btn-primary">@lang('FresnsEngine::fresns.save')</button></div>
        </div>
    </form>
@endsection
