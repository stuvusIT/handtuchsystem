<?php

namespace Handtuchsystem\Test\Unit\Controllers;

use Handtuchsystem\Test\Unit\Controllers\Stub\ControllerImplementation;
use PHPUnit\Framework\TestCase;

class BaseControllerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Controllers\BaseController::getPermissions
     */
    public function testGetPermissions()
    {
        $controller = new ControllerImplementation();

        $this->assertEquals([
            'foo',
            'lorem' => [
                'ipsum',
                'dolor',
            ],
        ], $controller->getPermissions());

        $this->assertTrue(method_exists($controller, 'setValidator'));
    }
}
