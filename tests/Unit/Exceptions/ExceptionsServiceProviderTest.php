<?php

namespace Handtuchsystem\Test\Unit\Exceptions;

use Handtuchsystem\Exceptions\ExceptionsServiceProvider;
use Handtuchsystem\Exceptions\Handler;
use Handtuchsystem\Exceptions\Handlers\HandlerInterface;
use Handtuchsystem\Exceptions\Handlers\Legacy;
use Handtuchsystem\Exceptions\Handlers\LegacyDevelopment;
use Handtuchsystem\Exceptions\Handlers\Whoops;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class ExceptionsServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Exceptions\ExceptionsServiceProvider::addDevelopmentHandler
     * @covers \Handtuchsystem\Exceptions\ExceptionsServiceProvider::addProductionHandler
     * @covers \Handtuchsystem\Exceptions\ExceptionsServiceProvider::register
     */
    public function testRegister()
    {
        $app = $this->getApp(['make', 'instance', 'bind']);

        /** @var Handler|MockObject $handler */
        $handler = $this->createMock(Handler::class);
        $this->setExpects($handler, 'register');
        /** @var Legacy|MockObject $legacyHandler */
        $legacyHandler = $this->createMock(Legacy::class);
        /** @var LegacyDevelopment|MockObject $developmentHandler */
        $developmentHandler = $this->createMock(LegacyDevelopment::class);

        $whoopsHandler = $this->getMockBuilder(Whoops::class)
            ->setConstructorArgs([$app])
            ->getMock();

        $app->expects($this->exactly(3))
            ->method('instance')
            ->withConsecutive(
                ['error.handler.production', $legacyHandler],
                ['error.handler.development', $whoopsHandler],
                ['error.handler', $handler]
            );

        $app->expects($this->exactly(4))
            ->method('make')
            ->withConsecutive(
                [Handler::class],
                [Legacy::class],
                [LegacyDevelopment::class],
                [Whoops::class]
            )
            ->willReturnOnConsecutiveCalls(
                $handler,
                $legacyHandler,
                $developmentHandler,
                $whoopsHandler
            );

        $app->expects($this->exactly(2))
            ->method('bind')
            ->withConsecutive(
                [HandlerInterface::class, 'error.handler.production'],
                [Handler::class, 'error.handler']
            );

        $handler->expects($this->exactly(2))
            ->method('setHandler')
            ->withConsecutive(
                [Handler::ENV_PRODUCTION, $legacyHandler],
                [Handler::ENV_DEVELOPMENT, $whoopsHandler]
            );

        $serviceProvider = new ExceptionsServiceProvider($app);
        $serviceProvider->register();
    }

    /**
     * @covers \Handtuchsystem\Exceptions\ExceptionsServiceProvider::boot
     * @covers \Handtuchsystem\Exceptions\ExceptionsServiceProvider::addLogger
     */
    public function testBoot()
    {
        /** @var HandlerInterface|MockObject $handlerImpl */
        $handlerImpl = $this->getMockForAbstractClass(HandlerInterface::class);

        /** @var Legacy|MockObject $loggingHandler */
        $loggingHandler = $this->createMock(Legacy::class);

        /** @var Handler|MockObject $handler */
        $handler = $this->createMock(Handler::class);

        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        /** @var LoggerInterface|MockObject $log */
        $log = $this->getMockForAbstractClass(LoggerInterface::class);

        $handler->expects($this->exactly(2))
            ->method('setRequest')
            ->with($request);
        $handler->expects($this->exactly(2))
            ->method('getHandler')
            ->willReturnOnConsecutiveCalls([$handlerImpl], [$loggingHandler]);

        $loggingHandler->expects($this->once())
            ->method('setLogger')
            ->with($log);

        $app = $this->getApp(['get']);
        $app->expects($this->exactly(5))
            ->method('get')
            ->withConsecutive(
                ['error.handler'],
                ['request'],
                ['error.handler'],
                ['request'],
                [LoggerInterface::class]
            )
            ->willReturnOnConsecutiveCalls(
                $handler,
                $request,
                $handler,
                $request,
                $log
            );

        $provider = new ExceptionsServiceProvider($app);
        $provider->boot();
        $provider->boot();
    }
}
