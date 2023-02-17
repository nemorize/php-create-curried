# php-create-curried

A utility to create curried functions that its parameters are reordered by user-defined order.<br />
Inspired by https://github.com/andjsrk/create-curried.

## Requirements
- PHP 8.1 or later

## Installation
```bash
composer require nemorize/create-curried
```

## Usage

### `Curried`

#### `Curried::from(Closure $fn): Context`
Returns a `Context` for a given closure.
```php
use Nemorize\Curried\Curried;
$context = Curried::from(function ($foo, $bar) {
    return $foo . $bar;
});
```

### `Context`

#### `Context::takes(int $position): Context`
Takes a parameter at the given position.
```php
use Nemorize\Curried\Curried;
$context = Curried::from(function ($foo, $bar) {
    return $foo . $bar;
})->takes(0)->takes(1);
```

#### `Context::takesRest(): Context`
Takes the rest of parameters.
```php
use Nemorize\Curried\Curried;
$context = Curried::from(function ($foo, $bar, $baz) {
    return $foo . $bar . $baz;
})->takes(0)->takesRest();
```

#### `Context::withStatic(int $position): Closure`
Binds a parameter at the given position to the closure.<br />
Returned closure returns a `Context` for the next parameter.
```php
use Nemorize\Curried\Curried;
$context = Curried::from(function ($foo, $bar) {
    return $foo . $bar;
})->takes(0)->withStatic(1)('bar');
```

#### `Context::generate(): Closure`
Generates a curried function.
```php
use Nemorize\Curried\Curried;
$curried = Curried::from(function ($foo, $bar) {
    return $foo . $bar;
})->takes(0)->takes(1)->generate();
$curried('foo')('bar'); // 'foobar'
```

## Examples

### Plus one to each element of an array
```php
use Nemorize\Curried\Curried;
$map = Curried::from(array_map(...))
    ->takes(0)
    ->takes(1)
    ->generate();
$plusOneEach = $map(fn (int $x) => $x + 1);
$plusOneEach([1, 2, 3]); // [2, 3, 4]
```

### Sum of an array
```php
use Nemorize\Curried\Curried;
$sum = Curried::from(array_reduce(...))
    ->takes(1)
    ->withStatic(0)(fn (int $x, int $y) => $x + $y)
    ->generate();
$sum([1, 2, 3]); // 6
```