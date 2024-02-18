<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebEngine\Providers;

use App\Helpers\AppHelper;
use Browser;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Plugins\FresnsEngine\Exceptions\ErrorException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionServiceProvider extends ServiceProvider
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        ErrorException::class,
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
            // ErrorException
            if ($e->getPrevious() instanceof ErrorException) {
                return $e->getPrevious()->render();
            }

            // 404 page
            if ($e instanceof NotFoundHttpException) {
                if (! fs_theme('fskey')) {
                    $errorMessage = Browser::isMobile() ? '<p>'.__('WebEngine::tips.errorMobileFskey').'</p>' : '<p>'.__('WebEngine::tips.errorDesktopFskey').'</p>';

                    return Response::view('error', [
                        'message' => $errorMessage.'<p>'.__('WebEngine::tips.settingTip').'</p>',
                        'code' => 400,
                    ], 400);
                }

                return Response::view(404, [
                    'fresnsVersion' => AppHelper::VERSION_MD5_16BIT,
                ], 404);
            }
        };
    }
}
