<?php

namespace Handtuchsystem\Renderer;

abstract class Engine implements EngineInterface
{
    /** @var array */
    protected $sharedData = [];

    /**
     * @param mixed[]|string $key
     * @param null           $value
     */
    public function share($key, $value = null): void
    {
        if (!is_array($key)) {
            $key = [$key => $value];
        }

        $this->sharedData = array_replace_recursive($this->sharedData, $key);
    }
}
