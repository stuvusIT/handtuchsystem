<?php

namespace Handtuchsystem\Test\Unit\Http\Validation\Rules;

use Handtuchsystem\Http\Validation\Rules\Min;
use Handtuchsystem\Test\Unit\TestCase;

class MinTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Validation\Rules\Min
     */
    public function testValidate()
    {
        $rule = new Min(3);
        $this->assertFalse($rule->validate(1));
        $this->assertFalse($rule->validate('2'));
        $this->assertTrue($rule->validate(3));
        $this->assertFalse($rule->validate('AS'));
        $this->assertTrue($rule->validate('TEST'));

        $rule = new Min('2042-01-01');
        $this->assertFalse($rule->validate('2000-01-01'));
        $this->assertTrue($rule->validate('2345-01-01'));
    }
}
