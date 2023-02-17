<?php

namespace Nemorize\Curried;

use Closure;
use stdClass;

class Context
{
    /**
     * @var array<int|string> $resultParameters Registered parameter position.
     */
    private array $resultParameters = [];

    private array $boundArgs = [];

    /**
     * Constructor.
     *
     * @param Closure $fn
     */
    public function __construct (
        private readonly Closure $fn
    ) {}

    public function takes (int $position): self
    {
        if ($position < 0) {
            throw new \InvalidArgumentException('Argument 1 passed to ' . __METHOD__ . ' must be greater than or equal to 0');
        }

        if (in_array($position, $this->resultParameters)) {
            throw new \InvalidArgumentException('Argument 1 passed to ' . __METHOD__ . ' must be unique');
        }

        $new = clone $this;
        $new->resultParameters[] = $position;
        return $new;
    }

    public function takesRest (): self
    {
        if (in_array('rest', $this->resultParameters)) {
            throw new \InvalidArgumentException(__METHOD__ . ' must be called only once');
        }

        $new = clone $this;
        $new->resultParameters[] = 'rest';
        return $new;
    }

    public function withStatic (int $position): Closure
    {
        $new = clone $this;
        return function (mixed $arg) use ($new, $position): self {
            $new->boundArgs[$position] = $arg;
            return $new;
        };
    }

    public function generate (): Closure
    {
        if (count($this->resultParameters) < 1) {
            return function () {
                $this->fn->call(new stdClass());
            };
        }

        return array_reduce(
            array_reverse($this->resultParameters),
            static function (Closure $carry, int|string $item) {
                return static function (Memoized $memoized) use ($carry, $item) {
                    return static function (mixed $arg) use ($carry, $memoized, $item) {
                        return $carry($memoized->applyArg($item, $arg));
                    };
                };
            },
            function (Memoized $memoized) {
                $args = $this->boundArgs + $memoized->args;
                ksort($args);

                return $this->fn->__invoke(...$args, ...$memoized->restArgs);
            }
        )(new Memoized());
    }
}