<?php

namespace Handtuchsystem\Test\Unit\Http\Exceptions;

use Handtuchsystem\Http\Exceptions\ValidationException;
use Handtuchsystem\Http\Validation\Validator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidationExceptionTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Exceptions\ValidationException::__construct
     * @covers \Handtuchsystem\Http\Exceptions\ValidationException::getValidator
     */
    public function testConstruct()
    {
        /** @var Validator|MockObject $validator */
        $validator = $this->createMock(Validator::class);

        $exception = new ValidationException($validator);

        $this->assertEquals($validator, $exception->getValidator());
    }
}
