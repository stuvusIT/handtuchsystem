<?php

namespace Handtuchsystem\Database\Migration;

use Handtuchsystem\Container\ServiceProvider;
use Handtuchsystem\Database\Database;
use Illuminate\Database\Schema\Builder as SchemaBuilder;

class MigrationServiceProvider extends ServiceProvider
{
    public function register()
    {
        /** @var Database $database */
        $database = $this->app->get(Database::class);
        $schema = $database->getConnection()->getSchemaBuilder();

        $this->app->instance('db.schema', $schema);
        $this->app->bind(SchemaBuilder::class, 'db.schema');

        $migration = $this->app->make(Migrate::class);
        $this->app->instance('db.migration', $migration);
    }
}
