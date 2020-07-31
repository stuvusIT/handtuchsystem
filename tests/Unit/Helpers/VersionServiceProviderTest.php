<?php

namespace Handtuchsystem\Test\Unit\Helpers;

use Handtuchsystem\Application;
use Handtuchsystem\Helpers\Version;
use Handtuchsystem\Helpers\VersionServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;

class VersionServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Helpers\VersionServiceProvider::register
     */
    public function testRegister()
    {
        $app = new Application();
        $app->instance('path.storage.app', '/tmp');

        $serviceProvider = new VersionServiceProvider($app);
        $serviceProvider->register();

        $this->assertArrayHasKey(Version::class, $app->contextual);
    }
}
