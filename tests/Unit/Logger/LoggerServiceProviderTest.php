<?php

namespace Handtuchsystem\Test\Unit\Logger;

use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Logger\Logger;
use Handtuchsystem\Logger\LoggerServiceProvider;
use Handtuchsystem\Logger\UserAwareLogger;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class LoggerServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Logger\LoggerServiceProvider::register
     */
    public function testRegister()
    {
        $serviceProvider = new LoggerServiceProvider($this->app);
        $serviceProvider->register();

        $this->assertInstanceOf(UserAwareLogger::class, $this->app->get('logger'));
        $this->assertInstanceOf(UserAwareLogger::class, $this->app->get(LoggerInterface::class));
        $this->assertInstanceOf(UserAwareLogger::class, $this->app->get(Logger::class));
        $this->assertInstanceOf(UserAwareLogger::class, $this->app->get(UserAwareLogger::class));
    }

    /**
     * @covers \Handtuchsystem\Logger\LoggerServiceProvider::boot
     */
    public function testBoot()
    {
        /** @var Authenticator|MockObject $auth */
        $auth = $this->getMockBuilder(Authenticator::class)
            ->disableOriginalConstructor()
            ->getMock();
        /** @var UserAwareLogger|MockObject $log */
        $log = $this->getMockBuilder(UserAwareLogger::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->app->instance(Authenticator::class, $auth);
        $this->app->instance(UserAwareLogger::class, $log);

        $log->expects($this->once())
            ->method('setAuth')
            ->with($auth);

        $serviceProvider = new LoggerServiceProvider($this->app);
        $serviceProvider->boot();
    }
}
