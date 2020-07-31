<?php

namespace Handtuchsystem\Test\Unit\Helpers;

use Handtuchsystem\Application;
use Handtuchsystem\Config\Config;
use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Helpers\AuthenticatorServiceProvider;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticatorServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Helpers\AuthenticatorServiceProvider::register()
     */
    public function testRegister()
    {
        $app = new Application();
        $app->bind(ServerRequestInterface::class, Request::class);

        $config = new Config();
        $config->set('password_algorithm', PASSWORD_DEFAULT);
        $app->instance('config', $config);

        $serviceProvider = new AuthenticatorServiceProvider($app);
        $serviceProvider->register();

        $this->assertInstanceOf(Authenticator::class, $app->get(Authenticator::class));
        $this->assertInstanceOf(Authenticator::class, $app->get('authenticator'));
        $this->assertInstanceOf(Authenticator::class, $app->get('auth'));

        /** @var Authenticator $auth */
        $auth = $app->get(Authenticator::class);
        $this->assertEquals(PASSWORD_DEFAULT, $auth->getPasswordAlgorithm());
    }
}
