<?php

namespace Handtuchsystem\Test\Unit\Http\Validation\Stub;

use Handtuchsystem\Controllers\BaseController;
use Handtuchsystem\Http\Request;

class ValidatesRequestImplementation extends BaseController
{
    /**
     * @param Request $request
     * @param array   $rules
     * @return array
     */
    public function validateData(Request $request, array $rules)
    {
        return $this->validate($request, $rules);
    }

    /**
     * @return bool
     */
    public function hasValidator()
    {
        return !is_null($this->validator);
    }
}
