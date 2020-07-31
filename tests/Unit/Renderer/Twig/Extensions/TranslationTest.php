<?php

namespace Handtuchsystem\Test\Unit\Renderer\Twig\Extensions;

use Handtuchsystem\Helpers\Translation\Translator;
use Handtuchsystem\Renderer\Twig\Extensions\Translation;
use PHPUnit\Framework\MockObject\MockObject;

class TranslationTest extends ExtensionTest
{
    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Translation::__construct
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Translation::getFilters
     */
    public function testGeFilters()
    {
        /** @var Translator|MockObject $translator */
        $translator = $this->createMock(Translator::class);

        $extension = new Translation($translator);
        $filters = $extension->getFilters();

        $this->assertExtensionExists('trans', [$translator, 'translate'], $filters);
    }

    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Translation::getFunctions
     */
    public function testGetFunctions()
    {
        /** @var Translator|MockObject $translator */
        $translator = $this->createMock(Translator::class);

        $extension = new Translation($translator);
        $functions = $extension->getFunctions();

        $this->assertExtensionExists('__', [$translator, 'translate'], $functions);
        $this->assertExtensionExists('_e', [$translator, 'translatePlural'], $functions);
    }
}
