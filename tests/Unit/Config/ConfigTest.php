<?php

namespace Handtuchsystem\Test\Unit\Config;

use Handtuchsystem\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Config\Config::get
     */
    public function testGet()
    {
        $config = new Config();

        $config->set('test', 'FooBar');
        $this->assertEquals(['test' => 'FooBar'], $config->get(null));
        $this->assertEquals('FooBar', $config->get('test'));

        $this->assertEquals('defaultValue', $config->get('notExisting', 'defaultValue'));

        $this->assertNull($config->get('notExisting'));
    }

    /**
     * @covers \Handtuchsystem\Config\Config::set
     */
    public function testSet()
    {
        $config = new Config();

        $config->set('test', 'FooBar');
        $this->assertEquals('FooBar', $config->get('test'));

        $config->set([
            'name' => 'Handtuchsystem',
            'mail' => ['user' => 'test'],
        ]);
        $this->assertEquals('Handtuchsystem', $config->get('name'));
        $this->assertEquals(['user' => 'test'], $config->get('mail'));
    }

    /**
     * @covers \Handtuchsystem\Config\Config::has
     */
    public function testHas()
    {
        $config = new Config();

        $this->assertFalse($config->has('test'));

        $config->set('test', 'FooBar');
        $this->assertTrue($config->has('test'));
    }

    /**
     * @covers \Handtuchsystem\Config\Config::remove
     */
    public function testRemove()
    {
        $config = new Config();
        $config->set(['foo' => 'bar', 'test' => '123']);

        $config->remove('foo');
        $this->assertEquals(['test' => '123'], $config->get(null));
    }

    /**
     * @covers \Handtuchsystem\Config\Config::__get
     */
    public function testMagicGet()
    {
        $config = new Config();

        $config->set('test', 'FooBar');
        $this->assertEquals('FooBar', $config->test);
    }

    /**
     * @covers \Handtuchsystem\Config\Config::__set
     */
    public function testMagicSet()
    {
        $config = new Config();

        $config->test = 'FooBar';
        $this->assertEquals('FooBar', $config->get('test'));
    }

    /**
     * @covers \Handtuchsystem\Config\Config::__isset
     */
    public function testMagicIsset()
    {
        $config = new Config();

        $this->assertFalse(isset($config->test));

        $config->set('test', 'FooBar');
        $this->assertTrue(isset($config->test));
    }

    /**
     * @covers \Handtuchsystem\Config\Config::__unset
     */
    public function testMagicUnset()
    {
        $config = new Config();
        $config->set(['foo' => 'bar', 'test' => '123']);

        unset($config->foo);
        $this->assertEquals(['test' => '123'], $config->get(null));
    }
}
