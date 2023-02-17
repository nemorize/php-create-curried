<?php

namespace Nemorize\Curried;

class Memoized
{
    public function __construct (
        public array $args = [],
        public array $restArgs = [],
    ) {}

    public function applyArg (int|string $position, mixed $arg): self
    {
        $new = clone $this;
        if ($position === 'rest') {
            $new->restArgs = $arg;
        } else {
            $new->args[$position] = $arg;
        }

        return $new;
    }
}