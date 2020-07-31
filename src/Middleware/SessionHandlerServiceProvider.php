<?php

namespace Handtuchsystem\Middleware;

use Handtuchsystem\Container\ServiceProvider;

class SessionHandlerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app
            ->when(SessionHandler::class)
            ->needs('$paths')
            ->give(function () {
                return [
                    '/api',
                    '/atom',
                    '/ical',
                    '/metrics',
                    '/shifts-json-export',
                    '/stats',
                ];
            });
    }
}
