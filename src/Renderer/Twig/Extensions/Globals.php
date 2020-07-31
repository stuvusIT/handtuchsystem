<?php

namespace Handtuchsystem\Renderer\Twig\Extensions;

use Handtuchsystem\Helpers\Authenticator;
use Twig\Extension\AbstractExtension as TwigExtension;
use Twig\Extension\GlobalsInterface as GlobalsInterface;

class Globals extends TwigExtension implements GlobalsInterface
{
    /** @var Authenticator */
    protected $auth;

    /**
     * @param Authenticator $auth
     */
    public function __construct(Authenticator $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals(): array
    {
        $user = $this->auth->user();

        return [
            'user' => $user ? $user : [],
        ];
    }
}
