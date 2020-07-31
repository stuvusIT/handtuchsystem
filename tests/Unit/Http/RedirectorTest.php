<?php

namespace Handtuchsystem\Test\Unit\Http;

use Handtuchsystem\Http\Redirector;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Http\UrlGeneratorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RedirectorTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Redirector::__construct
     * @covers \Handtuchsystem\Http\Redirector::to
     */
    public function testTo()
    {
        $request = new Request();
        $response = new Response();
        $url = $this->getUrlGenerator();

        $redirector = new Redirector($request, $response, $url);

        $return = $redirector->to('/test');
        $this->assertEquals(['/test'], $return->getHeader('location'));
        $this->assertEquals(302, $return->getStatusCode());

        $return = $redirector->to('/foo', 303, ['test' => 'data']);
        $this->assertEquals(['/foo'], $return->getHeader('location'));
        $this->assertEquals(303, $return->getStatusCode());
        $this->assertEquals(['data'], $return->getHeader('test'));
    }

    /**
     * @covers \Handtuchsystem\Http\Redirector::back
     * @covers \Handtuchsystem\Http\Redirector::getPreviousUrl
     */
    public function testBack()
    {
        $request = new Request();
        $response = new Response();
        $url = $this->getUrlGenerator();

        $redirector = new Redirector($request, $response, $url);
        $return = $redirector->back();
        $this->assertEquals(['/'], $return->getHeader('location'));
        $this->assertEquals(302, $return->getStatusCode());

        $request = $request->withHeader('referer', '/old-page');
        $redirector = new Redirector($request, $response, $url);
        $return = $redirector->back(303, ['foo' => 'bar']);
        $this->assertEquals(303, $return->getStatusCode());
        $this->assertEquals(['/old-page'], $return->getHeader('location'));
        $this->assertEquals(['bar'], $return->getHeader('foo'));
    }

    /**
     * @return UrlGeneratorInterface|MockObject
     */
    protected function getUrlGenerator()
    {
        /** @var UrlGeneratorInterface|MockObject $url */
        $url = $this->getMockForAbstractClass(UrlGeneratorInterface::class);
        $url->expects($this->atLeastOnce())
            ->method('to')
            ->willReturnCallback([$this, 'returnPath']);

        return $url;
    }

    /**
     * Returns the provided path
     *
     * @param string $path
     * @return string
     */
    public function returnPath(string $path)
    {
        return $path;
    }
}
