<?php

namespace Handtuchsystem\Test\Unit\Http\Stub;

use Handtuchsystem\Http\MessageTrait;
use Psr\Http\Message\MessageInterface;
use Symfony\Component\HttpFoundation\Response;

class MessageTraitResponseImplementation extends Response implements MessageInterface
{
    use MessageTrait;
}
