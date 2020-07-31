<?php

namespace Handtuchsystem\Test\Unit\Http;

use Handtuchsystem\Http\UrlGenerator;
use Handtuchsystem\Http\UrlGeneratorInterface;
use Handtuchsystem\Http\UrlGeneratorServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;

class UrlGeneratorServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Http\UrlGeneratorServiceProvider::register()
     */
    public function testRegister()
    {
        /** @var UrlGenerator|MockObject $urlGenerator */
        $urlGenerator = $this->getMockBuilder(UrlGenerator::class)
            ->getMock();

        $app = $this->getApp(['make', 'instance', 'bind']);

        $this->setExpects($app, 'make', [UrlGenerator::class], $urlGenerator);
        $app->expects($this->exactly(2))
            ->method('instance')
            ->withConsecutive(
                [UrlGenerator::class, $urlGenerator],
                ['http.urlGenerator', $urlGenerator],
                [UrlGeneratorInterface::class, $urlGenerator]
            );
        $app->expects($this->once())
            ->method('bind')
            ->with(UrlGeneratorInterface::class, UrlGenerator::class);

        $serviceProvider = new UrlGeneratorServiceProvider($app);
        $serviceProvider->register();
    }
}
