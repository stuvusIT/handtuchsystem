<?php

namespace Handtuchsystem\Test\Unit\Http\Exceptions;

use Handtuchsystem\Http\Exceptions\HttpRedirect;
use Handtuchsystem\Http\Exceptions\HttpTemporaryRedirect;
use PHPUnit\Framework\TestCase;

class HttpTemporaryRedirectTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Exceptions\HttpTemporaryRedirect::__construct
     */
    public function testConstruct()
    {
        $exception = new HttpTemporaryRedirect('https://lorem.ipsum/foo/bar');
        $this->assertInstanceOf(HttpRedirect::class, $exception);
        $this->assertEquals(302, $exception->getStatusCode());
    }
}
