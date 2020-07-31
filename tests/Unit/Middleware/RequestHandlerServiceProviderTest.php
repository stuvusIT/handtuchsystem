<?php

namespace Handtuchsystem\Test\Unit\Middleware;

use Handtuchsystem\Middleware\RequestHandler;
use Handtuchsystem\Middleware\RequestHandlerServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;

class RequestHandlerServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Middleware\RequestHandlerServiceProvider::register()
     */
    public function testRegister()
    {
        /** @var RequestHandler|MockObject $requestHandler */
        $requestHandler = $this->createMock(RequestHandler::class);

        $app = $this->getApp(['make', 'instance', 'bind']);

        $app->expects($this->once())
            ->method('make')
            ->with(RequestHandler::class)
            ->willReturn($requestHandler);
        $app->expects($this->once())
            ->method('instance')
            ->with('request.handler', $requestHandler);
        $app->expects($this->once())
            ->method('bind')
            ->with(RequestHandler::class, 'request.handler');

        $serviceProvider = new RequestHandlerServiceProvider($app);
        $serviceProvider->register();
    }
}
