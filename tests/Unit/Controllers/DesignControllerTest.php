<?php

namespace Handtuchsystem\Test\Unit\Controllers;

use Handtuchsystem\Controllers\DesignController;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Test\Unit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DesignControllerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Controllers\DesignController::__construct
     * @covers \Handtuchsystem\Controllers\DesignController::index
     */
    public function testIndex()
    {
        /** @var Response|MockObject $response */
        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('withView')
            ->with('pages/design')
            ->willReturn($response);

        $controller = new DesignController($response);
        $return = $controller->index();

        $this->assertEquals($response, $return);
    }
}
