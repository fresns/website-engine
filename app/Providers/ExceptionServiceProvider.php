<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jevan Tang
 * Released under the Apache-2.0 License.
 */

namespace Fresns\WebsiteEngine\Providers;

use Browser;
use Fresns\WebsiteEngine\Exceptions\ErrorException;
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
        ErrorException::class,
        \App\Fresns\Api\Exceptions\ResponseException::class,
        \Fresns\DTO\Exceptions\ResponseException::class,
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
                    $errorMessage = Browser::isMobile() ? '<p>'.__('WebsiteEngine::tips.errorMobileFskey').'</p>' : '<p>'.__('WebsiteEngine::tips.errorDesktopFskey').'</p>';

                    return Response::view('error', [
                        'message' => $errorMessage.'<p>'.__('WebsiteEngine::tips.settingTip').'</p>',
                        'code' => 400,
                    ], 400);
                }

                return Response::view(404, [], 404);
            }
        };
    }
}
