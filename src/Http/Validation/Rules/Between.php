<?php

namespace Handtuchsystem\Http\Validation\Rules;

use Respect\Validation\Rules\Between as RespectBetween;

class Between extends RespectBetween
{
    use StringInputLength;
}
