<?php

namespace Nemorize\Curried;

use Closure;

class Curried
{
    /**
     * Create a new curried context.
     *
     * @param Closure $fn
     * @return Context
     */
    public static function from (Closure $fn): Context
    {
        return new Context($fn);
    }
}