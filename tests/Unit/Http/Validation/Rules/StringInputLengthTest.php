<?php

namespace Handtuchsystem\Test\Unit\Http\Validation\Rules;

use Handtuchsystem\Test\Unit\Http\Validation\Rules\Stub\UsesStringInputLength;
use Handtuchsystem\Test\Unit\TestCase;

class StringInputLengthTest extends TestCase
{
    /**
     * @covers       \Handtuchsystem\Http\Validation\Rules\StringInputLength::validate
     * @covers       \Handtuchsystem\Http\Validation\Rules\StringInputLength::isDateTime
     * @dataProvider validateProvider
     * @param mixed $input
     * @param mixed $expectedInput
     */
    public function testValidate($input, $expectedInput)
    {
        $rule = new UsesStringInputLength();
        $rule->validate($input);

        $this->assertEquals($expectedInput, $rule->lastInput);
    }

    /**
     * @return array[]
     */
    public function validateProvider()
    {
        return [
            ['TEST', 4],
            ['?', 1],
            ['2042-01-01 00:00', '2042-01-01 00:00'],
            ['3', '3'],
        ];
    }
}
