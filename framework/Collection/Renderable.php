<?php

namespace MyUCP\Collection;

interface Renderable
{
    /**
     * Render.
     *
     * @param callable|null $callback
     *
     * @return string
     */
    public function render(callable $callback = null);
}
