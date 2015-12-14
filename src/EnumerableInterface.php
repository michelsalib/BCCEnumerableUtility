<?php

namespace BCC\EnumerableUtility;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use IteratorAggregate;
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
     * @param mixed $func
     *
     * @return bool
     */
    public function all($func);

    /**
     * @param mixed $func
     *
     * @return bool
     */
    public function any($func = null);

    /**
     * @param mixed $func
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function average($func = null);

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value);

    /**
     * @param mixed $func
     *
     * @return int
     */
    public function count($func = null);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function distinct($func = null);

    /**
     * @param mixed $func
     */
    public function each($func);

    /**
     * @param mixed $index
     *
     * @return mixed
     */
    public function elementAt($index);

    /**
     * @param mixed $func
     *
     * @return mixed
     */
    public function first($func = null);

    /**
     * @param string|mixed $func
     *
     * @return EnumerableInterface
     */
    public function groupBy($func);

    /**
     * @param Iterator|array $innerItems
     * @param mixed $outerSelector
     * @param mixed $innerSelector
     * @param callable $resultFunc
     *
     * @return EnumerableInterface
     */
    public function join($innerItems, $outerSelector, $innerSelector, $resultFunc);

    /**
     * @param mixed $func
     *
     * @return mixed
     */
    public function last($func = null);

    /**
     * @param mixed $func
     *
     * @return mixed
     */
    public function max($func = null);

    /**
     * @param mixed $func
     *
     * @return mixed
     */
    public function min($func = null);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function orderBy($func = null);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function orderByDescending($func = null);

    /**
     * @return EnumerableInterface
     */
    public function reverse();

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function select($func);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function selectMany($func = null);

    /**
     * @param int $count
     *
     * @return EnumerableInterface
     */
    public function skip($count);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function skipWhile($func);

    /**
     * @param mixed $func
     *
     * @return float
     */
    public function sum($func = null);

    /**
     * @param int $count
     *
     * @return EnumerableInterface
     */
    public function take($count);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function takeWhile($func);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function thenBy($func = null);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function thenByDescending($func = null);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param mixed $keySelector
     * @param mixed $valueSelector
     *
     * @throws LogicException
     *
     * @return Dictionary
     */
    public function toDictionary($keySelector, $valueSelector = null);

    /**
     * @param mixed $func
     *
     * @return EnumerableInterface
     */
    public function where($func);
}
