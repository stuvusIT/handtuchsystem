<?php

namespace Handtuchsystem\Test\Unit\Http\Stub;

use Handtuchsystem\Http\MessageTrait;
use Psr\Http\Message\MessageInterface;
use Symfony\Component\HttpFoundation\Request;

class MessageTraitRequestImplementation extends Request implements MessageInterface
{
    use MessageTrait;
}
