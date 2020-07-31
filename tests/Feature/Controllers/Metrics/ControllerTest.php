<?php

namespace Handtuchsystem\Test\Feature\Controllers\Metrics;

use Handtuchsystem\Controllers\Metrics\Controller;
use Handtuchsystem\Test\Feature\ApplicationFeatureTest;

class ControllerTest extends ApplicationFeatureTest
{
    /**
     * @covers \Handtuchsystem\Controllers\Metrics\Controller::metrics
     */
    public function testMetrics()
    {
        config(['api_key' => null]);

        /** @var Controller $controller */
        $controller = app()->make(Controller::class);
        $response = $controller->metrics();

        $this->assertEquals(200, $response->getStatusCode());
    }
}
