<?php

namespace Handtuchsystem\Test\Unit\Http;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\SessionHandlers\DatabaseHandler;
use Handtuchsystem\Http\SessionServiceProvider;
use Handtuchsystem\Test\Unit\ServiceProviderTest;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface as StorageInterface;

class SessionServiceProviderTest extends ServiceProviderTest
{
    /**
     * @covers \Handtuchsystem\Http\SessionServiceProvider::getSessionStorage()
     * @covers \Handtuchsystem\Http\SessionServiceProvider::register()
     */
    public function testRegister()
    {
        $app = $this->getApp(['make', 'instance', 'bind', 'get']);

        $sessionStorage = $this->getMockForAbstractClass(StorageInterface::class);
        $sessionStorage2 = $this->getMockForAbstractClass(StorageInterface::class);
        $databaseHandler = $this->getMockBuilder(DatabaseHandler::class)
            ->disableOriginalConstructor()
            ->getMock();

        $session = $this->getSessionMock();
        $request = $this->getRequestMock();

        /** @var SessionServiceProvider|MockObject $serviceProvider */
        $serviceProvider = $this->getMockBuilder(SessionServiceProvider::class)
            ->setConstructorArgs([$app])
            ->onlyMethods(['isCli'])
            ->getMock();

        /** @var Config|MockObject $config */
        $config = $this->createMock(Config::class);

        $serviceProvider->expects($this->exactly(3))
            ->method('isCli')
            ->willReturnOnConsecutiveCalls(true, false, false);

        $app->expects($this->exactly(7))
            ->method('make')
            ->withConsecutive(
                [MockArraySessionStorage::class],
                [Session::class],
                [
                    NativeSessionStorage::class,
                    ['options' => ['cookie_httponly' => true, 'name' => 'session'], 'handler' => null],
                ],
                [Session::class],
                [DatabaseHandler::class],
                [
                    NativeSessionStorage::class,
                    ['options' => ['cookie_httponly' => true, 'name' => 'foobar'], 'handler' => $databaseHandler],
                ],
                [Session::class]
            )
            ->willReturnOnConsecutiveCalls(
                $sessionStorage,
                $session,
                $sessionStorage2,
                $session,
                $databaseHandler,
                $sessionStorage2,
                $session
            );
        $app->expects($this->atLeastOnce())
            ->method('instance')
            ->withConsecutive(
                ['session.storage', $sessionStorage],
                [Session::class, $session],
                ['session', $session]
            );

        $app->expects($this->exactly(5))
            ->method('get')
            ->withConsecutive(
                ['request'],
                ['config'],
                ['request'],
                ['config'],
                ['request']
            )
            ->willReturnOnConsecutiveCalls(
                $request,
                $config,
                $request,
                $config,
                $request
            );

        $config->expects($this->exactly(2))
            ->method('get')
            ->with('session')
            ->willReturnOnConsecutiveCalls(
                ['driver' => 'native', 'name' => 'session'],
                ['driver' => 'pdo', 'name' => 'foobar']
            );

        $app->expects($this->atLeastOnce())
            ->method('bind')
            ->withConsecutive(
                [StorageInterface::class, 'session.storage'],
                [SessionInterface::class, Session::class]
            );

        $this->setExpects($request, 'setSession', [$session], null, $this->atLeastOnce());
        $this->setExpects($session, 'has', ['_token'], false, $this->atLeastOnce());
        $this->setExpects($session, 'set', ['_token'], null, $this->atLeastOnce());
        $this->setExpects($session, 'start', null, null, $this->atLeastOnce());

        $serviceProvider->register();
        $serviceProvider->register();
        $serviceProvider->register();
    }

    /**
     * @covers \Handtuchsystem\Http\SessionServiceProvider::isCli()
     */
    public function testIsCli()
    {
        $app = $this->getApp(['make', 'instance', 'bind', 'get']);

        $sessionStorage = $this->getMockForAbstractClass(StorageInterface::class);

        $session = $this->getSessionMock();
        $request = $this->getRequestMock();

        $app->expects($this->exactly(2))
            ->method('make')
            ->withConsecutive(
                [MockArraySessionStorage::class],
                [Session::class]
            )
            ->willReturnOnConsecutiveCalls(
                $sessionStorage,
                $session
            );
        $app->expects($this->exactly(3))
            ->method('instance')
            ->withConsecutive(
                ['session.storage', $sessionStorage],
                [Session::class, $session],
                ['session', $session]
            );
        $app->expects($this->atLeastOnce())
            ->method('bind')
            ->withConsecutive(
                [StorageInterface::class, 'session.storage'],
                [SessionInterface::class, Session::class]
            );

        $this->setExpects($app, 'get', ['request'], $request);
        $this->setExpects($request, 'setSession', [$session]);
        $this->setExpects($session, 'has', ['_token'], true);
        $this->setExpects($session, 'start');

        $serviceProvider = new SessionServiceProvider($app);
        $serviceProvider->register();
    }

    /**
     * @return MockObject
     */
    private function getSessionMock()
    {
        $sessionStorage = $this->getMockForAbstractClass(StorageInterface::class);
        return $this->getMockBuilder(Session::class)
            ->setConstructorArgs([$sessionStorage])
            ->onlyMethods(['start', 'has', 'set'])
            ->getMock();
    }

    /**
     * @return MockObject
     */
    private function getRequestMock()
    {
        return $this->getMockBuilder(Request::class)
            ->onlyMethods(['setSession'])
            ->getMock();
    }
}
