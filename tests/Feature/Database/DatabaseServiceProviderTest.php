<?php

namespace Handtuchsystem\Test\Feature\Database;

use Handtuchsystem\Config\Config;
use Handtuchsystem\Database\Database;
use Handtuchsystem\Database\DatabaseServiceProvider;

class DatabaseServiceProviderTest extends DatabaseTest
{
    /**
     * @covers \Handtuchsystem\Database\DatabaseServiceProvider::register()
     */
    public function testRegister()
    {
        $this->app->instance('config', new Config([
            'database' => $this->getDbConfig(),
            'timezone' => 'UTC',
        ]));

        $serviceProvider = new DatabaseServiceProvider($this->app);
        $serviceProvider->register();
        $this->assertTrue($this->app->has(Database::class));
    }
}
