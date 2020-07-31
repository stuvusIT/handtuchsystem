<?php

namespace Handtuchsystem\Test\Unit;

use Handtuchsystem\Application;
use PHPUnit\Framework\MockObject\MockObject;

abstract class ServiceProviderTest extends TestCase
{
    /**
     * @param array $methods
     * @return Application|MockObject
     */
    protected function getApp($methods = ['make', 'instance'])
    {
        return $this->getMockBuilder(Application::class)
            ->onlyMethods($methods)
            ->getMock();
    }
}
