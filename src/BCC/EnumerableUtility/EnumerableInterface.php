<?php

namespace BCC\EnumerableUtility;

use InvalidArgumentException;
use IteratorAggregate;
use ArrayAccess;
use Countable;
use LogicException;

interface EnumerableInterface extends IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @param callable $func
     *
     * @return mixed
     */
    public function aggregate($func);

    /**
     * @param callable $func
     *
     * @return bool
     */
    public function all($func);

    /**
     * @param callable|string $func
     *
     * @return bool
     */
    public function any($func = null);

    /**
     * @param callable|string $selector
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function average($selector = null);

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value);

    /**
     * @param callable $func
     *
     * @return int
     */
    public function count($func = null);

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function distinct($selector = null);

    /**
     * @param callable $func
     */
    public function each($func);

    /**
     * @param mixed $index
     *
     * @return mixed
     */
    public function elementAt($index);

    /**
     * @param callable $func
     *
     * @return mixed
     */
    public function first($func = null);

    /**
     * @param string|callable $selector
     *
     * @return EnumerableInterface
     */
    public function groupBy($selector);

    /**
     * @param Iterator $innerItems
     * @param callable $outerSelector
     * @param callable $innerSelector
     * @param callable $resultFunc
     *
     * @return EnumerableInterface
     */
    public function join($innerItems, $outerSelector, $innerSelector, $resultFunc);

    /**
     * @param callable $func
     *
     * @return mixed
     */
    public function last($func = null);

    /**
     * @param callable|string $selector
     *
     * @return mixed
     */
    public function max($selector = null);

    /**
     * @param callable|string $selector
     *
     * @return mixed
     */
    public function min($selector = null);

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function orderBy($selector = null);

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function orderByDescending($selector = null);

    /**
     * @return EnumerableInterface
     */
    public function reverse();

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function select($selector);

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function selectMany($selector = null);

    /**
     * @param int $count
     *
     * @return EnumerableInterface
     */
    public function skip($count);

    /**
     * @param callable $func
     *
     * @return EnumerableInterface
     */
    public function skipWhile($func);

    /**
     * @param callable|string $selector
     *
     * @return float
     */
    public function sum($selector = null);

    /**
     * @param int $count
     *
     * @return EnumerableInterface
     */
    public function take($count);

    /**
     * @param callable $func
     *
     * @return EnumerableInterface
     */
    public function takeWhile($func);

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function thenBy($selector = null);

    /**
     * @param callable|string $selector
     *
     * @return EnumerableInterface
     */
    public function thenByDescending($selector = null);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param callable|string $keySelector
     * @param callable|string $valueSelector
     *
     * @throws LogicException
     *
     * @return Dictionary
     */
    public function toDictionary($keySelector, $valueSelector = null);

    /**
     * @param callable $func
     *
     * @return EnumerableInterface
     */
    public function where($func);
}
