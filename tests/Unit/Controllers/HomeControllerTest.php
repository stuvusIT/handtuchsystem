<?php

namespace Handtuchsystem\Test\Unit\Controllers;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Controllers\HomeController;
use Handtuchsystem\Helpers\Authenticator;
use Handtuchsystem\Http\Redirector;
use Handtuchsystem\Http\Response;
use Handtuchsystem\Test\Unit\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class HomeControllerTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Controllers\HomeController::__construct
     * @covers \Handtuchsystem\Controllers\HomeController::index
     */
    public function testIndex()
    {
        $config = new Config(['home_site' => '/foo']);
        /** @var Authenticator|MockObject $auth */
        $auth = $this->createMock(Authenticator::class);
        $this->setExpects($auth, 'user', null, true);
        /** @var Redirector|MockObject $redirect */
        $redirect = $this->createMock(Redirector::class);
        $this->setExpects($redirect, 'to', ['/foo'], new Response());

        $controller = new HomeController($auth, $config, $redirect);
        $controller->index();
    }
}
