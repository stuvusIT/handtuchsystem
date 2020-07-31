<?php

namespace Handtuchsystem\Test\Unit\Controllers\Stub;

use Handtuchsystem\Controllers\BaseController;

class ControllerImplementation extends BaseController
{
    /** @var array */
    protected $permissions = [
        'foo',
        'lorem' => [
            'ipsum',
            'dolor',
        ],
    ];
}
