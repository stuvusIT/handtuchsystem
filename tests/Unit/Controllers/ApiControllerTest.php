<?php

namespace Handtuchsystem\Test\Unit\Controllers;

use Handtuchsystem\Controllers\ApiController;
use Handtuchsystem\Http\Response;
use PHPUnit\Framework\TestCase;

class ApiControllerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Controllers\ApiController::__construct
     * @covers \Handtuchsystem\Controllers\ApiController::index
     */
    public function testIndex()
    {
        $controller = new ApiController(new Response());

        $response = $controller->index();

        $this->assertEquals(501, $response->getStatusCode());
        $this->assertEquals(['application/json'], $response->getHeader('content-type'));
        $this->assertJson($response->getContent());
    }
}
