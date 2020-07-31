<?php

namespace Handtuchsystem\Http;

use Handtuchsystem\Container\ServiceProvider;

class RedirectServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('redirect', Redirector::class);
    }
}
