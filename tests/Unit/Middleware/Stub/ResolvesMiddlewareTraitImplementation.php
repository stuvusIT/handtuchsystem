<?php

namespace Handtuchsystem\Test\Unit\Middleware\Stub;

use Handtuchsystem\Application;
use Handtuchsystem\Middleware\ResolvesMiddlewareTrait;
use InvalidArgumentException;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResolvesMiddlewareTraitImplementation
{
    use ResolvesMiddlewareTrait;

    /** @var Application */
    protected $container;

    /**
     * @param Application $container
     */
    public function __construct(Application $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param string|callable|MiddlewareInterface|RequestHandlerInterface $middleware
     * @return MiddlewareInterface|RequestHandlerInterface
     * @throws InvalidArgumentException
     */
    public function callResolveMiddleware($middleware)
    {
        return $this->resolveMiddleware($middleware);
    }
}
