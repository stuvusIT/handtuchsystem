<?php

namespace Handtuchsystem\Test\Unit\Exceptions\Handlers;

use Handtuchsystem\Exceptions\Handlers\LegacyDevelopment;
use Handtuchsystem\Http\Request;
use ErrorException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class LegacyDevelopmentTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Exceptions\Handlers\LegacyDevelopment::formatStackTrace()
     * @covers \Handtuchsystem\Exceptions\Handlers\LegacyDevelopment::render()
     */
    public function testRender()
    {
        $handler = new LegacyDevelopment();
        /** @var Request|MockObject $request */
        $request = $this->createMock(Request::class);
        $exception = new ErrorException('Lorem <b>Ipsum</b>', 4242, 1, 'foo.php', 9999);

        $regex = sprintf(
            '%%<pre.*>.*ErrorException.*4242.*Lorem &lt;b&gt;Ipsum&lt;/b&gt;.*%s.*%s.*%s.*</pre>%%is',
            'foo.php',
            9999,
            __FUNCTION__
        );
        $this->expectOutputRegex($regex);

        $handler->render($request, $exception);
    }
}
