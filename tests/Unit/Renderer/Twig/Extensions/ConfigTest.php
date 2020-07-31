<?php

namespace Handtuchsystem\Test\Unit\Renderer\Twig\Extensions;

use Handtuchsystem\Config\Config as HandtuchsystemConfig;
use Handtuchsystem\Renderer\Twig\Extensions\Config;
use PHPUnit\Framework\MockObject\MockObject;

class ConfigTest extends ExtensionTest
{
    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Config::__construct
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Config::getFunctions
     */
    public function testGetFunctions()
    {
        /** @var HandtuchsystemConfig|MockObject $config */
        $config = $this->createMock(HandtuchsystemConfig::class);

        $extension = new Config($config);
        $functions = $extension->getFunctions();

        $this->assertExtensionExists('config', [$config, 'get'], $functions);
    }
}
