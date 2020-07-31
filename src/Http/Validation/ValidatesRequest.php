<?php

namespace Handtuchsystem\Http\Validation;

use Handtuchsystem\Http\Exceptions\ValidationException;
use Handtuchsystem\Http\Request;

trait ValidatesRequest
{
    /** @var Validator */
    protected $validator;

    /**
     * @param Request $request
     * @param array   $rules
     * @return array
     */
    protected function validate(Request $request, array $rules)
    {
        $isValid = $this->validator->validate(
            (array)$request->getParsedBody(),
            $rules
        );

        if (!$isValid) {
            throw new ValidationException($this->validator);
        }

        return $this->validator->getData();
    }

    /**
     * @param Validator $validator
     */
    public function setValidator(Validator $validator)
    {
        $this->validator = $validator;
    }
}
