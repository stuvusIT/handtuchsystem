<?php

namespace Handtuchsystem\Test\Unit\Renderer\Twig\Extensions;

use Handtuchsystem\Renderer\Twig\Extensions\Csrf;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CsrfTest extends ExtensionTest
{
    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Csrf::getFunctions
     */
    public function testGetGlobals()
    {
        /** @var SessionInterface|MockObject $session */
        $session = $this->createMock(SessionInterface::class);

        $extension = new Csrf($session);
        $functions = $extension->getFunctions();

        $this->assertExtensionExists('csrf', [$extension, 'getCsrfField'], $functions, ['is_safe' => ['html']]);
        $this->assertExtensionExists('csrf_token', [$extension, 'getCsrfToken'], $functions);
    }

    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Csrf::getCsrfField
     */
    public function testGetCsrfField()
    {
        /** @var Csrf|MockObject $extension */
        $extension = $this->getMockBuilder(Csrf::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getCsrfToken'])
            ->getMock();

        $extension->expects($this->once())
            ->method('getCsrfToken')
            ->willReturn('SomeRandomCsrfToken');

        $this->assertEquals(
            '<input type="hidden" name="_token" value="SomeRandomCsrfToken">',
            $extension->getCsrfField()
        );
    }

    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Csrf::__construct
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Csrf::getCsrfToken
     */
    public function testGetCsrfToken()
    {
        /** @var SessionInterface|MockObject $session */
        $session = $this->createMock(SessionInterface::class);
        $session->expects($this->once())
            ->method('get')
            ->with('_token')
            ->willReturn('SomeOtherCsrfToken');

        $extension = new Csrf($session);
        $this->assertEquals('SomeOtherCsrfToken', $extension->getCsrfToken());
    }
}
