<?php

namespace BCC\EnumerableUtility;

interface IEnumerable extends \IteratorAggregate, \ArrayAccess, \Countable
{
    public function aggregate($func);

    public function all($func);

    public function any($func = null);

    public function average($selector);

    public function contains($value);

    public function count($func = null);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function distinct($selector = null);

    public function elementAt($index);

    public function first($func = null);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function groupBy($selector);

    /**
     * @param $innerItems
     * @param $outerSelector
     * @param $innerSelector
     * @param $resultFunc
     * @return IEnumerable
     */
    public function join($innerItems, $outerSelector, $innerSelector, $resultFunc);

    public function last($func = null);

    public function max($selector = null);

    public function min($selector = null);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function orderBy($selector = null);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function orderByDescending($selector = null);

    /**
     * @param $string
     * @return IEnumerable
     */
    public function reverse();

    /**
     * @param $selector
     * @return IEnumerable
     */
    public function select($selector);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function selectMany($selector = null);

    /**
     * @param $count
     * @return IEnumerable
     */
    public function skip($count);

    /**
     * @param $func
     * @return IEnumerable
     */
    public function skipWhile($func);

    public function sum($selector = null);

    /**
     * @param $count
     * @return IEnumerable
     */
    public function take($count);

    /**
     * @param $func
     * @return IEnumerable
     */
    public function takeWhile($func);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function thenBy($selector = null);

    /**
     * @param null $selector
     * @return IEnumerable
     */
    public function thenByDescending($selector = null);

    /**
     * @abstract
     * @return array
     */
    public function toArray();

    /**
     * @param $func
     * @return IEnumerable
     */
    public function where($func);
}
