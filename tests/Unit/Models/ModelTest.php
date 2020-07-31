<?php

namespace Handtuchsystem\Test\Unit\Models;

use Handtuchsystem\Test\Unit\HasDatabase;
use Handtuchsystem\Test\Unit\TestCase;

abstract class ModelTest extends TestCase
{
    use HasDatabase;

    /**
     * Prepare test
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->initDatabase();
    }
}
