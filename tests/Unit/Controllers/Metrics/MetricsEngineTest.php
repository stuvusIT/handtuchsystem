<?php

namespace Handtuchsystem\Test\Unit\Controllers\Metrics;

use Handtuchsystem\Controllers\Metrics\MetricsEngine;
use Handtuchsystem\Test\Unit\TestCase;

class MetricsEngineTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::escape
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::formatData
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::formatValue
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::get
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::renderLabels
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::renderValue
     */
    public function testGet()
    {
        $engine = new MetricsEngine();

        $this->assertEquals('', $engine->get('/metrics'));

        $this->assertEquals('engelsystem_users 13', $engine->get('/metrics', ['users' => 13]));

        $this->assertEquals('engelsystem_bool_val 0', $engine->get('/metrics', ['bool_val' => false]));

        $this->assertEquals('# Lorem \n Ipsum', $engine->get('/metrics', ["Lorem \n Ipsum"]));

        $this->assertEquals(
            'engelsystem_foo{lorem="ip\\\\sum"} \\"lorem\\n\\\\ipsum\\"',
            $engine->get('/metrics', [
                'foo' => ['labels' => ['lorem' => 'ip\\sum'], 'value' => "\"lorem\n\\ipsum\""],
            ])
        );

        $this->assertEquals(
            'engelsystem_foo_count{bar="14"} 42',
            $engine->get('/metrics', ['foo_count' => ['labels' => ['bar' => 14], 'value' => 42]])
        );

        $this->assertEquals(
            'engelsystem_lorem{test="123"} NaN' . "\n" . 'engelsystem_lorem{test="456"} 999.99',
            $engine->get('/metrics', [
                'lorem' => [
                    ['labels' => ['test' => 123], 'value' => 'NaN'],
                    ['labels' => ['test' => 456], 'value' => 999.99],
                ],
            ])
        );

        $this->assertEquals(
            "# HELP engelsystem_test Some help\\n  text\n# TYPE engelsystem_test counter\nengelsystem_test 99",
            $engine->get('/metrics', ['test' => ['help' => "Some help\n  text", 'type' => 'counter', 'value' => 99]])
        );
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::canRender
     */
    public function testCanRender()
    {
        $engine = new MetricsEngine();

        $this->assertFalse($engine->canRender('/'));
        $this->assertFalse($engine->canRender('/metrics.foo'));
        $this->assertTrue($engine->canRender('/metrics'));
    }

    /**
     * @covers \Handtuchsystem\Controllers\Metrics\MetricsEngine::share
     */
    public function testShare()
    {
        $engine = new MetricsEngine();

        $engine->share('foo', 42);
        $this->assertEquals('', $engine->get('/metrics'));
    }
}
