<?php

namespace Handtuchsystem\Test\Unit\Exceptions\Handlers;

use Handtuchsystem\Exceptions\Handlers\NullHandler;
use Handtuchsystem\Http\Request;
use ErrorException;
use PHPUnit\Framework\TestCase;

class NullHandlerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Exceptions\Handlers\NullHandler::render
     */
    public function testRender()
    {
        $handler = new NullHandler();
        $request = new Request();
        $exception = new ErrorException();

        $this->expectOutputString('');
        $handler->render($request, $exception);
    }
}
