<?php

namespace Handtuchsystem\Http;

use Handtuchsystem\Container\ServiceProvider;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ResponseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $response = $this->app->make(Response::class);
        $this->app->instance(Response::class, $response);
        $this->app->instance(SymfonyResponse::class, $response);
        $this->app->instance('response', $response);
    }
}
