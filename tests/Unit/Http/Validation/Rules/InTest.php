<?php

namespace Handtuchsystem\Test\Unit\Http\Validation\Rules;

use Handtuchsystem\Http\Validation\Rules\In;
use Handtuchsystem\Test\Unit\TestCase;

class InTest extends TestCase
{
    /**
     * @covers \Handtuchsystem\Http\Validation\Rules\In::__construct
     */
    public function testConstruct()
    {
        $rule = new In('foo,bar');

        $this->assertEquals(['foo', 'bar'], $rule->haystack);
    }
}
