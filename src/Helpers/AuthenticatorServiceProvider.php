<?php

namespace Handtuchsystem\Helpers;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Container\ServiceProvider;

class AuthenticatorServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var Config $config */
        $config = $this->app->get('config');
        /** @var Authenticator $authenticator */
        $authenticator = $this->app->make(Authenticator::class);
        $authenticator->setPasswordAlgorithm($config->get('password_algorithm'));

        $this->app->instance(Authenticator::class, $authenticator);
        $this->app->instance('authenticator', $authenticator);
        $this->app->instance('auth', $authenticator);
    }
}
