<?php

namespace Handtuchsystem\Test\Unit\Http\Validation\Rules;

use Handtuchsystem\Http\Validation\Rules\NotIn;
use Handtuchsystem\Test\Unit\TestCase;

class NotInTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Validation\Rules\NotIn::validate
     */
    public function testConstruct()
    {
        $rule = new NotIn('foo,bar');

        $this->assertTrue($rule->validate('lorem'));
        $this->assertFalse($rule->validate('foo'));
    }
}
