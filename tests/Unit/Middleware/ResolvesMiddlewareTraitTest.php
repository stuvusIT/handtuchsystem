<?php

namespace Handtuchsystem\Test\Unit\Middleware;

use Handtuchsystem\Application;
use Handtuchsystem\Middleware\CallableHandler;
use Handtuchsystem\Test\Unit\Middleware\Stub\HasStaticMethod;
use Handtuchsystem\Test\Unit\Middleware\Stub\ResolvesMiddlewareTraitImplementation;
use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\MiddlewareInterface;

class ResolvesMiddlewareTraitTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Middleware\ResolvesMiddlewareTrait::isMiddleware
     * @covers \Handtuchsystem\Middleware\ResolvesMiddlewareTrait::resolveMiddleware
     */
    public function testResolveMiddleware()
    {
        /** @var Application|MockObject $container */
        $container = $this->createMock(Application::class);
        $middlewareInterface = $this->getMockForAbstractClass(MiddlewareInterface::class);
        $callable = [HasStaticMethod::class, 'foo'];

        $container->expects($this->exactly(3))
            ->method('make')
            ->withConsecutive(
                ['FooBarClass'],
                [CallableHandler::class, ['callable' => $callable]],
                ['UnresolvableClass']
            )
            ->willReturnOnConsecutiveCalls(
                $middlewareInterface,
                $middlewareInterface,
                null
            );

        $middleware = new ResolvesMiddlewareTraitImplementation($container);

        $return = $middleware->callResolveMiddleware('FooBarClass');
        $this->assertEquals($middlewareInterface, $return);

        $return = $middleware->callResolveMiddleware($callable);
        $this->assertEquals($middlewareInterface, $return);

        $this->expectException(InvalidArgumentException::class);
        $middleware->callResolveMiddleware('UnresolvableClass');
    }

    /**
     * @covers \Handtuchsystem\Middleware\ResolvesMiddlewareTrait::resolveMiddleware
     */
    public function testResolveMiddlewareNoContainer()
    {
        $middlewareInterface = $this->getMockForAbstractClass(MiddlewareInterface::class);

        $middleware = new ResolvesMiddlewareTraitImplementation();
        $return = $middleware->callResolveMiddleware($middlewareInterface);

        $this->assertEquals($middlewareInterface, $return);

        $this->expectException(InvalidArgumentException::class);
        $middleware->callResolveMiddleware('FooBarClass');
    }
}
