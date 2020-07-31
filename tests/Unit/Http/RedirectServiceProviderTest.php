<?php

namespace Handtuchsystem\Test\Unit\Http;

use Handtuchsystem\Application;
use Handtuchsystem\Http\RedirectServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;

class RedirectServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Http\RedirectServiceProvider::register
     */
    public function testRegister()
    {
        $app = new Application();

        $serviceProvider = new RedirectServiceProvider($app);
        $serviceProvider->register();

        $this->assertTrue($app->has('redirect'));
    }
}
