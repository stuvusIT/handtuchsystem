<?php

namespace Handtuchsystem\Test\Unit\Helpers;

use Carbon\CarbonTimeZone;
use Handtuchsystem\Config\Config;
use Handtuchsystem\Exceptions\Handler;
use Handtuchsystem\Helpers\ConfigureEnvironmentServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;

class ConfigureEnvironmentServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Helpers\ConfigureEnvironmentServiceProvider::register
     * @covers \Handtuchsystem\Helpers\ConfigureEnvironmentServiceProvider::setupDevErrorHandler
     */
    public function testRegister()
    {
        $config = new Config(['timezone' => 'Australia/Eucla', 'environment' => 'production']);
        $this->app->instance('config', $config);

        $handler = new Handler();
        $this->app->instance('error.handler', $handler);

        /** @var ConfigureEnvironmentServiceProvider|MockObject $serviceProvider */
        $serviceProvider = $this->getMockBuilder(ConfigureEnvironmentServiceProvider::class)
            ->setConstructorArgs([$this->app])
            ->onlyMethods(['setTimeZone', 'displayErrors', 'errorReporting'])
            ->getMock();

        $serviceProvider->expects($this->exactly(2))
            ->method('setTimeZone')
            ->willReturnCallback(function (CarbonTimeZone $timeZone) {
                $this->assertEquals('Australia/Eucla', $timeZone->getName());
            });
        $serviceProvider->expects($this->exactly(3))
            ->method('displayErrors')
            ->withConsecutive([false], [false], [true]);
        $serviceProvider->expects($this->exactly(1))
            ->method('errorReporting')
            ->with(E_ALL);

        $serviceProvider->register();
        $this->assertNotEquals(Handler::ENV_DEVELOPMENT, $handler->getEnvironment());

        $config->set('environment', 'development');
        $serviceProvider->register();
        $this->assertEquals(Handler::ENV_DEVELOPMENT, $handler->getEnvironment());
    }
}
