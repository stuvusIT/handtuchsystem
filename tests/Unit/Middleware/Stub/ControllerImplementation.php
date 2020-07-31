<?php

namespace Handtuchsystem\Test\Unit\Middleware\Stub;

use Handtuchsystem\Controllers\BaseController;

class ControllerImplementation extends BaseController
{
    /**
     * @param array $permissions
     */
    public function setPermissions(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @return string
     */
    public function actionStub()
    {
        return '';
    }
}
