<?php

namespace Handtuchsystem\Test\Unit\Renderer\Twig\Extensions;

use Handtuchsystem\Http\Request;
use Handtuchsystem\Renderer\Twig\Extensions\Legacy;
use PHPUnit\Framework\MockObject\MockObject;

class LegacyTest extends ExtensionTest
{
    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Legacy::getFunctions
     */
    public function testGetFunctions()
    {
        $isSafeHtml = ['is_safe' => ['html']];
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $extension = new Legacy($request);
        $functions = $extension->getFunctions();

        $this->assertExtensionExists('menu', 'make_navigation', $functions, $isSafeHtml);
        $this->assertExtensionExists('menuUserShiftState', 'User_shift_state_render', $functions, $isSafeHtml);
        $this->assertExtensionExists('menuUserMessages', 'user_unread_messages', $functions, $isSafeHtml);
        $this->assertExtensionExists('menuUserHints', 'header_render_hints', $functions, $isSafeHtml);
        $this->assertExtensionExists('menuUserSubmenu', 'make_user_submenu', $functions, $isSafeHtml);
        $this->assertExtensionExists('page', [$extension, 'getPage'], $functions);
        $this->assertExtensionExists('msg', 'msg', $functions, $isSafeHtml);
    }

    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Legacy::__construct
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Legacy::getPage
     */
    public function testIsAuthenticated()
    {
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);

        $extension = new Legacy($request);

        $request->expects($this->exactly(2))
            ->method('has')
            ->with('p')
            ->willReturnOnConsecutiveCalls(true, false);

        $request->expects($this->once())
            ->method('get')
            ->with('p')
            ->willReturn('foo-bar');

        $request->expects($this->once())
            ->method('path')
            ->willReturn('batz');

        $this->assertEquals('foo-bar', $extension->getPage());
        $this->assertEquals('batz', $extension->getPage());
    }
}
