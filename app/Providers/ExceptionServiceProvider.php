<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Plugins\FresnsEngine\Providers;

use App\Helpers\AppHelper;
use App\Helpers\PluginHelper;
use Browser;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        \Plugins\FresnsEngine\Exceptions\ErrorException::class,
    ];

    /**
     * Register any services.
     */
    public function boot(): void
    {
        $handler = resolve(ExceptionHandler::class);

        if (method_exists($handler, 'reportable')) {
            $handler->reportable($this->reportable());
        }

        if (method_exists($handler, 'renderable')) {
            $handler->renderable($this->renderable());
        }

        if (method_exists($handler, 'ignore') && $this->dontReport) {
            foreach ($this->dontReport as $exceptionClass) {
                $handler->ignore($exceptionClass);
            }
        }
    }

    /**
     * Register a reportable callback.
     *
     * @param  callable  $reportUsing
     * @return \Illuminate\Foundation\Exceptions\ReportableHandler
     */
    public function reportable()
    {
        return function (\Throwable $e) {
            //
        };
    }

    /**
     * Register a renderable callback.
     *
     * @param  callable  $renderUsing
     * @return $this
     */
    public function renderable()
    {
        return function (\Throwable $e) {
            // 404 page
            if ($e instanceof NotFoundHttpException) {
                $themeFskey = Browser::isMobile() ? fs_db_config('FresnsEngine_Mobile') : fs_db_config('FresnsEngine_Desktop');

                $finder = app('view')->getFinder();
                $finder->prependLocation(base_path("extensions/themes/{$themeFskey}"));

                $engineVersion = PluginHelper::fresnsPluginVersionByFskey('FresnsEngine') ?? 'null';
                $themeVersion = PluginHelper::fresnsPluginVersionByFskey($themeFskey) ?? 'null';

                return Response::view(404, [
                    'fresnsVersion' => AppHelper::VERSION_MD5_16BIT,
                    'engineFskey' => 'FresnsEngine',
                    'engineVersion' => $engineVersion,
                    'themeFskey' => $themeFskey,
                    'themeVersion' => $themeVersion,
                ], 404);
            }
        };
    }
}
