<?php

namespace Handtuchsystem\Test\Unit\Http\Exceptions;

use Handtuchsystem\Http\Exceptions\HttpAuthExpired;
use PHPUnit\Framework\TestCase;

class HttpAuthExpiredTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Exceptions\HttpAuthExpired::__construct
     */
    public function testConstruct()
    {
        $exception = new HttpAuthExpired();
        $this->assertEquals(419, $exception->getStatusCode());
        $this->assertEquals('Authentication Expired', $exception->getMessage());

        $exception = new HttpAuthExpired('Oops!');
        $this->assertEquals('Oops!', $exception->getMessage());
    }
}
