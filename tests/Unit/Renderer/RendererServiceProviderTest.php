<?php

namespace Handtuchsystem\Test\Unit\Renderer;

use Handtuchsystem\Renderer\EngineInterface;
use Handtuchsystem\Renderer\HtmlEngine;
use Handtuchsystem\Renderer\Renderer;
use Handtuchsystem\Renderer\RendererServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;

class RendererServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Renderer\RendererServiceProvider::register()
     * @covers \Handtuchsystem\Renderer\RendererServiceProvider::registerHtmlEngine()
     * @covers \Handtuchsystem\Renderer\RendererServiceProvider::registerRenderer()
     */
    public function testRegister()
    {
        /** @var Renderer|MockObject $renderer */
        $renderer = $this->getMockBuilder(Renderer::class)
            ->getMock();
        /** @var HtmlEngine|MockObject $htmlEngine */
        $htmlEngine = $this->getMockBuilder(HtmlEngine::class)
            ->getMock();

        $app = $this->getApp(['make', 'instance', 'tag']);

        $app->expects($this->exactly(2))
            ->method('make')
            ->withConsecutive(
                [Renderer::class],
                [HtmlEngine::class]
            )->willReturnOnConsecutiveCalls(
                $renderer,
                $htmlEngine
            );

        $app->expects($this->exactly(4))
            ->method('instance')
            ->withConsecutive(
                [Renderer::class, $renderer],
                ['renderer', $renderer],
                [HtmlEngine::class, $htmlEngine],
                ['renderer.htmlEngine', $htmlEngine]
            );

        $this->setExpects($app, 'tag', ['renderer.htmlEngine', ['renderer.engine']]);

        $serviceProvider = new RendererServiceProvider($app);
        $serviceProvider->register();
    }

    /**
     * @covers \Handtuchsystem\Renderer\RendererServiceProvider::boot()
     */
    public function testBoot()
    {
        /** @var Renderer|MockObject $renderer */
        $renderer = $this->getMockBuilder(Renderer::class)
            ->getMock();
        /** @var EngineInterface|MockObject $engine1 */
        $engine1 = $this->getMockForAbstractClass(EngineInterface::class);
        /** @var EngineInterface|MockObject $engine2 */
        $engine2 = $this->getMockForAbstractClass(EngineInterface::class);

        $app = $this->getApp(['get', 'tagged']);

        $this->setExpects($app, 'get', ['renderer'], $renderer);
        $this->setExpects($app, 'tagged', ['renderer.engine'], [$engine1, $engine2]);

        $renderer
            ->expects($this->exactly(2))
            ->method('addRenderer')
            ->withConsecutive([$engine1], [$engine2]);

        $serviceProvider = new RendererServiceProvider($app);
        $serviceProvider->boot();
    }
}
