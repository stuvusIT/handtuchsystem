<?php

namespace Handtuchsystem\Http\Validation\Rules;

use Respect\Validation\Rules\Max as RespectMax;

class Max extends RespectMax
{
    use StringInputLength;
}
