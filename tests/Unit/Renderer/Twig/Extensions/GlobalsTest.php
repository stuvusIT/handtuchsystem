<?php

namespace Handtuchsystem\Test\Unit\Renderer\Twig\Extensions;

use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Models\User\User;
use Handtuchsystem\Renderer\Twig\Extensions\Globals;
use PHPUnit\Framework\MockObject\MockObject;

class GlobalsTest extends ExtensionTest
{
    /**
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Globals::__construct
     * @covers \Handtuchsystem\Renderer\Twig\Extensions\Globals::getGlobals
     */
    public function testGetGlobals()
    {
        /** @var Authenticator|MockObject $auth */
        $auth = $this->createMock(Authenticator::class);
        $user = new User();

        $auth->expects($this->exactly(2))
            ->method('user')
            ->willReturnOnConsecutiveCalls(
                null,
                $user
            );

        $extension = new Globals($auth);

        $globals = $extension->getGlobals();
        $this->assertGlobalsExists('user', [], $globals);

        $globals = $extension->getGlobals();
        $this->assertGlobalsExists('user', $user, $globals);
    }
}
