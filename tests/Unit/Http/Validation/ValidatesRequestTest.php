<?php

namespace Handtuchsystem\Test\Unit\Http\Validation;

use Handtuchsystem\Http\Exceptions\ValidationException;
use Handtuchsystem\Http\Request;
use Handtuchsystem\Http\Validation\Validator;
use Handtuchsystem\Test\Unit\Http\Validation\Stub\ValidatesRequestImplementation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ValidatesRequestTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Validation\ValidatesRequest::validate
     * @covers \Handtuchsystem\Http\Validation\ValidatesRequest::setValidator
     */
    public function testValidate()
    {
        /** @var Validator|MockObject $validator */
        $validator = $this->createMock(Validator::class);
        $validator->expects($this->exactly(2))
            ->method('validate')
            ->withConsecutive(
                [['foo' => 'bar'], ['foo' => 'required']],
                [[], ['foo' => 'required']]
            )
            ->willReturnOnConsecutiveCalls(
                true,
                false
            );
        $validator->expects($this->once())
            ->method('getData')
            ->willReturn(['foo' => 'bar']);

        $implementation = new ValidatesRequestImplementation();
        $implementation->setValidator($validator);

        $return = $implementation->validateData(new Request([], ['foo' => 'bar']), ['foo' => 'required']);

        $this->assertEquals(['foo' => 'bar'], $return);

        $this->expectException(ValidationException::class);
        $implementation->validateData(new Request([], []), ['foo' => 'required']);
    }
}
