<?php

namespace Handtuchsystem\Renderer\Twig\Extensions;

use Handtuchsystem\Config\Config as HandtuchsystemConfig;
use Twig\Extension\AbstractExtension as TwigExtension;
use Twig\TwigFunction;

class Config extends TwigExtension
{
    /** @var HandtuchsystemConfig */
    protected $config;

    /**
     * @param HandtuchsystemConfig $config
     */
    public function __construct(HandtuchsystemConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('config', [$this->config, 'get']),
        ];
    }
}
