<?php

namespace Handtuchsystem\Exceptions\Handlers;

use Handtuchsystem\Http\Request;
use Throwable;

interface HandlerInterface
{
    /**
     * @param Request   $request
     * @param Throwable $e
     */
    public function render($request, Throwable $e);

    /**
     * @param Throwable $e
     */
    public function report(Throwable $e);
}
