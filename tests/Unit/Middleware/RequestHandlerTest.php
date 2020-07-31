<?php

namespace Handtuchsystem\Test\Unit\Middleware;

use Handtuchsystem\Application;
use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Http\Exceptions\HttpForbidden;
use Handtuchsystem\Middleware\CallableHandler;
use Handtuchsystem\Middleware\RequestHandler;
use Handtuchsystem\Test\Unit\Middleware\Stub\ControllerImplementation;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use ReflectionClass as Reflection;

class RequestHandlerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Middleware\RequestHandler::__construct
     */
    public function testInit()
    {
        /** @var Application|MockObject $container */
        $container = $this->createMock(Application::class);

        $handler = new RequestHandler($container);

        $reflection = new Reflection(get_class($handler));
        $property = $reflection->getProperty('container');
        $property->setAccessible(true);

        $this->assertEquals($container, $property->getValue($handler));
    }

    /**
     * @covers \Handtuchsystem\Middleware\RequestHandler::process
     */
    public function testProcess()
    {
        /** @var Application|MockObject $container */
        /** @var ServerRequestInterface|MockObject $request */
        /** @var RequestHandlerInterface|MockObject $handler */
        /** @var ResponseInterface|MockObject $response */
        /** @var MiddlewareInterface|MockObject $middlewareInterface */
        list($container, $request, $handler, $response, $middlewareInterface) = $this->getMocks();

        $requestHandlerInterface = $this->getMockForAbstractClass(RequestHandlerInterface::class);

        $request->expects($this->exactly(3))
            ->method('getAttribute')
            ->with('route-request-handler')
            ->willReturn('FooBarClass');

        /** @var RequestHandler|MockObject $middleware */
        $middleware = $this->getMockBuilder(RequestHandler::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(['resolveRequestHandler'])
            ->getMock();
        $middleware->expects($this->exactly(3))
            ->method('resolveRequestHandler')
            ->with('FooBarClass')
            ->willReturnOnConsecutiveCalls(
                $middlewareInterface,
                $requestHandlerInterface,
                null
            );

        $middlewareInterface->expects($this->once())
            ->method('process')
            ->with($request, $handler)
            ->willReturn($response);
        $requestHandlerInterface->expects($this->once())
            ->method('handle')
            ->with($request)
            ->willReturn($response);

        $return = $middleware->process($request, $handler);
        $this->assertEquals($return, $response);

        $middleware->process($request, $handler);
        $this->assertEquals($return, $response);

        $this->expectException(InvalidArgumentException::class);
        $middleware->process($request, $handler);
    }

    /**
     * @covers \Handtuchsystem\Middleware\RequestHandler::resolveRequestHandler
     */
    public function testResolveRequestHandler()
    {
        /** @var Application|MockObject $container */
        /** @var ServerRequestInterface|MockObject $request */
        /** @var RequestHandlerInterface|MockObject $handler */
        /** @var ResponseInterface|MockObject $response */
        /** @var MiddlewareInterface|MockObject $middlewareInterface */
        list($container, $request, $handler, $response, $middlewareInterface) = $this->getMocks();

        $className = 'Handtuchsystem\\Controllers\\FooBarTestController';

        $request->expects($this->exactly(1))
            ->method('getAttribute')
            ->with('route-request-handler')
            ->willReturn('FooBarTestController@showStuff');

        /** @var RequestHandler|MockObject $middleware */
        $middleware = $this->getMockBuilder(RequestHandler::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(['resolveMiddleware'])
            ->getMock();
        $middleware->expects($this->once())
            ->method('resolveMiddleware')
            ->with([$middlewareInterface, 'showStuff'])
            ->willReturn($middlewareInterface);

        $middlewareInterface->expects($this->once())
            ->method('process')
            ->with($request, $handler)
            ->willReturn($response);

        $container->expects($this->exactly(2))
            ->method('has')
            ->withConsecutive(['FooBarTestController'], [$className])
            ->willReturnOnConsecutiveCalls(false, true);
        $container->expects($this->once())
            ->method('make')
            ->with($className)
            ->willReturn($middlewareInterface);

        $return = $middleware->process($request, $handler);
        $this->assertEquals($return, $response);
    }

    /**
     * @covers \Handtuchsystem\Middleware\RequestHandler::checkPermissions
     * @covers \Handtuchsystem\Middleware\RequestHandler::process
     */
    public function testCheckPermissions()
    {
        /** @var Application|MockObject $container */
        /** @var ServerRequestInterface|MockObject $request */
        /** @var RequestHandlerInterface|MockObject $handler */
        /** @var ResponseInterface|MockObject $response */
        list($container, $request, $handler, $response) = $this->getMocks();

        /** @var Authenticator|MockObject $auth */
        $auth = $this->createMock(Authenticator::class);

        $class = new ControllerImplementation();
        /** @var CallableHandler|MockObject $callable */
        $callable = $this->getMockBuilder(CallableHandler::class)
            ->setConstructorArgs([[$class, 'actionStub']])
            ->getMock();

        $callable->expects($this->exactly(2))
            ->method('getCallable')
            ->willReturn([$class, 'actionStub']);

        $callable->expects($this->exactly(1))
            ->method('process')
            ->with($request, $handler)
            ->willReturn($response);

        $request->expects($this->exactly(2))
            ->method('getAttribute')
            ->with('route-request-handler')
            ->willReturn($callable);


        /** @var RequestHandler|MockObject $middleware */
        $middleware = $this->getMockBuilder(RequestHandler::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(['resolveRequestHandler'])
            ->getMock();

        $middleware->expects($this->exactly(2))
            ->method('resolveRequestHandler')
            ->with($callable)
            ->willReturn($callable);

        $container->expects($this->exactly(2))
            ->method('get')
            ->with('auth')
            ->willReturn($auth);

        $hasPermissions = [];
        $auth->expects($this->atLeastOnce())
            ->method('can')
            ->willReturnCallback(function ($permission) use (&$hasPermissions) {
                return in_array($permission, $hasPermissions);
            });

        $hasPermissions = ['foo', 'test', 'user'];
        $class->setPermissions([
            'foo',
            'loremIpsumAction' => ['dolor', 'sit'],
            'actionStub'       => ['test'],
            'user',
        ]);
        $middleware->process($request, $handler);

        $class->setPermissions(array_merge(['not.existing.permission'], $hasPermissions));
        $this->expectException(HttpForbidden::class);
        $middleware->process($request, $handler);
    }

    /**
     * @return array
     */
    protected function getMocks(): array
    {
        /** @var Application|MockObject $container */
        $container = $this->createMock(Application::class);
        /** @var ServerRequestInterface|MockObject $request */
        $request = $this->getMockForAbstractClass(ServerRequestInterface::class);
        /** @var RequestHandlerInterface|MockObject $handler */
        $handler = $this->getMockForAbstractClass(RequestHandlerInterface::class);
        /** @var ResponseInterface|MockObject $response */
        $response = $this->getMockForAbstractClass(ResponseInterface::class);
        /** @var MiddlewareInterface $middlewareInterface */
        $middlewareInterface = $this->getMockForAbstractClass(MiddlewareInterface::class);

        return [$container, $request, $handler, $response, $middlewareInterface];
    }
}
