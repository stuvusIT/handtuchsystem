<?php

namespace Handtuchsystem\Test\Unit\Http\Exceptions;

use Handtuchsystem\Http\Exceptions\HttpForbidden;
use PHPUnit\Framework\TestCase;

class HttpForbiddenTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Exceptions\HttpForbidden::__construct
     */
    public function testConstruct()
    {
        $exception = new HttpForbidden();
        $this->assertEquals(403, $exception->getStatusCode());
        $this->assertEquals('', $exception->getMessage());

        $exception = new HttpForbidden('Go away!');
        $this->assertEquals('Go away!', $exception->getMessage());
    }
}
