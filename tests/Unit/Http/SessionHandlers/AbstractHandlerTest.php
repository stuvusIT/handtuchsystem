<?php

namespace Handtuchsystem\Test\Unit\Http\SessionHandlers;

use Handtuchsystem\Test\Unit\Http\SessionHandlers\Stub\ArrayHandler;
use PHPUnit\Framework\TestCase;

class AbstractHandlerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\SessionHandlers\AbstractHandler::open
     */
    public function testOpen()
    {
        $handler = new ArrayHandler();
        $return = $handler->open('/foo/bar', '1337asd098hkl7654');

        $this->assertTrue($return);
        $this->assertEquals('1337asd098hkl7654', $handler->getName());
        $this->assertEquals('/foo/bar', $handler->getSessionPath());
    }

    /**
     * @covers \Handtuchsystem\Http\SessionHandlers\AbstractHandler::close
     */
    public function testClose()
    {
        $handler = new ArrayHandler();
        $return = $handler->close();

        $this->assertTrue($return);
    }

    /**
     * @covers \Handtuchsystem\Http\SessionHandlers\AbstractHandler::gc
     */
    public function testGc()
    {
        $handler = new ArrayHandler();
        $return = $handler->gc(60 * 60 * 24);

        $this->assertTrue($return);
    }
}
