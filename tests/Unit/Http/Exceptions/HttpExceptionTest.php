<?php

namespace Handtuchsystem\Test\Unit\Http\Exceptions;

use Handtuchsystem\Http\Exceptions\HttpException;
use PHPUnit\Framework\TestCase;

class HttpExceptionTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Exceptions\HttpException::__construct
     * @covers \Handtuchsystem\Http\Exceptions\HttpException::getHeaders
     * @covers \Handtuchsystem\Http\Exceptions\HttpException::getStatusCode
     */
    public function testConstruct()
    {
        $exception = new HttpException(123);
        $this->assertEquals(123, $exception->getStatusCode());
        $this->assertEquals('', $exception->getMessage());
        $this->assertEquals([], $exception->getHeaders());

        $exception = new HttpException(404, 'Nothing found', ['page' => '/test']);
        $this->assertEquals('Nothing found', $exception->getMessage());
        $this->assertEquals(['page' => '/test'], $exception->getHeaders());
    }
}
