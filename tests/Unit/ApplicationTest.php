<?php

namespace Handtuchsystem\Test\Unit;

use Handtuchsystem\Application;
use Handtuchsystem\Config\Config;
use Handtuchsystem\Container\Container;
use Handtuchsystem\Container\ServiceProvider;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use ReflectionClass;

class ApplicationTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Application::__construct
     * @covers \Handtuchsystem\Application::registerBaseBindings
     */
    public function testConstructor()
    {
        $app = new Application('.');

        $this->assertInstanceOf(Container::class, $app);
        $this->assertInstanceOf(ContainerInterface::class, $app);
        $this->assertSame($app, $app->get('app'));
        $this->assertSame($app, $app->get('container'));
        $this->assertSame($app, $app->get(Container::class));
        $this->assertSame($app, $app->get(Application::class));
        $this->assertSame($app, $app->get(ContainerInterface::class));
        $this->assertSame($app, Application::getInstance());
        $this->assertSame($app, Container::getInstance());
    }

    /**
     * @covers \Handtuchsystem\Application::path
     * @covers \Handtuchsystem\Application::registerPaths
     * @covers \Handtuchsystem\Application::setAppPath
     */
    public function testAppPath()
    {
        $app = new Application();

        $this->assertFalse($app->has('path'));

        $app->setAppPath('.');
        $this->assertTrue($app->has('path'));
        $this->assertTrue($app->has('path.assets'));
        $this->assertTrue($app->has('path.config'));
        $this->assertTrue($app->has('path.lang'));
        $this->assertTrue($app->has('path.resources'));
        $this->assertTrue($app->has('path.views'));
        $this->assertTrue($app->has('path.storage'));
        $this->assertTrue($app->has('path.cache'));
        $this->assertTrue($app->has('path.cache.routes'));
        $this->assertTrue($app->has('path.cache.views'));

        $this->assertEquals(realpath('.'), $app->path());
        $this->assertEquals(realpath('.') . '/config', $app->get('path.config'));

        $app->setAppPath('./../');
        $this->assertEquals(realpath('../') . '/config', $app->get('path.config'));
    }

    /**
     * @covers \Handtuchsystem\Application::register
     */
    public function testRegister()
    {
        $app = new Application();

        $serviceProvider = $this->mockServiceProvider($app, ['register']);
        $serviceProvider->expects($this->once())
            ->method('register');

        $app->register($serviceProvider);

        $anotherServiceProvider = $this->mockServiceProvider($app, ['register', 'boot']);
        $anotherServiceProvider->expects($this->once())
            ->method('register');
        $anotherServiceProvider->expects($this->once())
            ->method('boot');

        $app->bootstrap();
        $app->register($anotherServiceProvider);
    }

    /**
     * @covers \Handtuchsystem\Application::register
     */
    public function testRegisterBoot()
    {
        $app = new Application();
        $app->bootstrap();

        $serviceProvider = $this->mockServiceProvider($app, ['register', 'boot']);
        $serviceProvider->expects($this->once())
            ->method('register');
        $serviceProvider->expects($this->once())
            ->method('boot');

        $app->register($serviceProvider);
    }

    /**
     * @covers \Handtuchsystem\Application::register
     */
    public function testRegisterClassName()
    {
        $app = new Application();

        $mockClassName = $this->getMockClass(ServiceProvider::class);
        $serviceProvider = $this->getMockBuilder($mockClassName)
            ->setConstructorArgs([$app])
            ->onlyMethods(['register'])
            ->getMock();

        $serviceProvider->expects($this->once())
            ->method('register');

        $app->instance($mockClassName, $serviceProvider);
        $app->register($mockClassName);
    }

    /**
     * @covers \Handtuchsystem\Application::bootstrap
     * @covers \Handtuchsystem\Application::getMiddleware
     * @covers \Handtuchsystem\Application::isBooted
     */
    public function testBootstrap()
    {
        /** @var Application|MockObject $app */
        $app = $this->getMockBuilder(Application::class)
            ->onlyMethods(['register'])
            ->getMock();

        $serviceProvider = $this->mockServiceProvider($app, ['boot']);
        $serviceProvider->expects($this->once())
            ->method('boot');

        $app->expects($this->once())
            ->method('register')
            ->with($serviceProvider);

        /** @var Config|MockObject $config */
        $config = $this->getMockBuilder(Config::class)
            ->getMock();

        $middleware = [MiddlewareInterface::class];
        $config->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(['providers'], ['middleware'])
            ->willReturnOnConsecutiveCalls([$serviceProvider], $middleware);

        $property = (new ReflectionClass($app))->getProperty('serviceProviders');
        $property->setAccessible(true);
        $property->setValue($app, [$serviceProvider]);

        $app->bootstrap($config);

        $this->assertTrue($app->isBooted());
        $this->assertEquals($middleware, $app->getMiddleware());

        // Run bootstrap another time to ensure that providers are registered only once
        $app->bootstrap($config);
    }

    /**
     * @param Application $app
     * @param array       $methods
     * @return ServiceProvider|MockObject
     */
    protected function mockServiceProvider(Application $app, $methods = [])
    {
        return $this->getMockBuilder(ServiceProvider::class)
            ->setConstructorArgs([$app])
            ->onlyMethods($methods)
            ->getMockForAbstractClass();
    }
}
