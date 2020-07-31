<?php

namespace Handtuchsystem\Test\Feature\Database;

use Handtuchsystem\Test\Unit\TestCase;

abstract class DatabaseTest extends TestCase
{
    /**
     * Returns the database config
     *
     * @return string[]
     */
    protected function getDbConfig()
    {
        $configValues = require __DIR__ . '/../../../config/config.default.php';
        $configFile = __DIR__ . '/../../../config/config.php';

        if (file_exists($configFile)) {
            $configValues = array_replace_recursive($configValues, require $configFile);
        }

        return $configValues['database'];
    }
}
