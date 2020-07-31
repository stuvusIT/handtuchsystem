<?php

namespace Handtuchsystem\Test\Unit\Controllers;

use Handtuchsystem\Test\Unit\Controllers\Stub\HasUserNotificationsImplementation;
use Handtuchsystem\Test\Unit\TestCase;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class HasUserNotificationsTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Controllers\HasUserNotifications::getNotifications
     * @covers \Handtuchsystem\Controllers\HasUserNotifications::addNotification
     */
    public function testNotifications()
    {
        $session = new Session(new MockArraySessionStorage());
        $this->app->instance('session', $session);

        $notify = new HasUserNotificationsImplementation();
        $notify->add('Foo', 'errors');
        $notify->add('Bar', 'warnings');
        $notify->add(['Baz', 'Lorem'], 'information');
        $notify->add(['Hm', ['Uff', 'sum']], 'messages');

        $this->assertEquals([
            'errors'      => new Collection(['Foo']),
            'warnings'    => new Collection(['Bar']),
            'information' => new Collection(['Baz', 'Lorem']),
            'messages'    => new Collection(['Hm', 'Uff', 'sum']),
        ], $notify->get());
    }
}
