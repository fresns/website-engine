<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Providers;

use Browser;
use App\Helpers\AppHelper;
use App\Helpers\PluginHelper;
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
        \Fresns\WebEngine\Exceptions\ErrorException::class,
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
                $viewNamespace = Browser::isMobile() ? fs_db_config('engine_view_mobile') : fs_db_config('engine_view_desktop');
                $viewVersion = PluginHelper::fresnsPluginVersionByFskey($viewNamespace);

                return Response::view(404, [
                    'fresnsVersion' => AppHelper::VERSION_MD5_16BIT,
                    'viewFskey' => $viewNamespace,
                    'viewVersion' => $viewVersion,
                ], 404);
            }
        };
    }
}
