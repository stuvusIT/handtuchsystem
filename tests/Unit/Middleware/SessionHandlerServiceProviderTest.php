<?php

namespace Handtuchsystem\Test\Unit\Middleware;

use Handtuchsystem\Middleware\SessionHandler;
use Handtuchsystem\Middleware\SessionHandlerServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use Illuminate\Contracts\Container\ContextualBindingBuilder;
use PHPUnit\Framework\MockObject\MockObject;

class SessionHandlerServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Middleware\SessionHandlerServiceProvider::register()
     */
    public function testRegister()
    {
        /** @var ContextualBindingBuilder|MockObject $bindingBuilder */
        $bindingBuilder = $this->createMock(ContextualBindingBuilder::class);
        $app = $this->getApp(['when']);

        $app->expects($this->once())
            ->method('when')
            ->with(SessionHandler::class)
            ->willReturn($bindingBuilder);

        $bindingBuilder->expects($this->once())
            ->method('needs')
            ->with('$paths')
            ->willReturn($bindingBuilder);

        $bindingBuilder->expects($this->once())
            ->method('give')
            ->willReturnCallback(function (callable $callable) {
                $paths = $callable();

                $this->assertIsArray($paths);
                $this->assertTrue(in_array('/metrics', $paths));
            });

        $serviceProvider = new SessionHandlerServiceProvider($app);
        $serviceProvider->register();
    }
}
