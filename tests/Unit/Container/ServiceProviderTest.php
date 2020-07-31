<?php

namespace Handtuchsystem\Test\Unit\Container;

use Handtuchsystem\Container\ServiceProvider;
use Handtuchsystem\Test\Unit\Container\Stub\ServiceProviderImplementation;
use Handtuchsystem\Test\Unit\ServiceProviderTest as ServiceProviderTestCase;

class ServiceProviderTest extends ServiceProviderTestCase
{
    /**
     * @covers \Handtuchsystem\Container\ServiceProvider::__construct
     * @covers \Handtuchsystem\Container\ServiceProvider::register
     * @covers \Handtuchsystem\Container\ServiceProvider::boot
     */
    public function testRegister()
    {
        $app = $this->getApp();

        $serviceProvider = new ServiceProviderImplementation($app);

        $this->assertInstanceOf(ServiceProvider::class, $serviceProvider);

        $serviceProvider->register();
        $serviceProvider->boot();
    }
}
