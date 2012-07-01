<?php

namespace BCC\EnumerableUtility;

interface IEnumerable extends \IteratorAggregate, \ArrayAccess
{
    public function aggregate($func);

    public function all($func);

    public function any($func = null);

    public function average($func);

    public function contains($value);

    public function count($func = null);

    /**
     * @param null $func
     * @return IEnumerable
     */
    public function distinct($func = null);

    public function elementAt($index);

    public function first($func = null);

    /**
     * @param null $func
     * @return IEnumerable
     */
    public function groupBy($func);

    /**
     * @param $innerItems
     * @param $outerSelector
     * @param $innerSelector
     * @param $resultSelector
     * @return IEnumerable
     */
    public function join($innerItems, $outerSelector, $innerSelector, $resultSelector);

    public function last($func = null);

    public function max($func = null);

    public function min($func = null);

    /**
     * @param null $func
     * @return IEnumerable
     */
    public function orderBy($func = null);

    /**
     * @param null $func
     * @return IEnumerable
     */
    public function orderByDescending($func = null);

    /**
     * @param $string
     * @return IEnumerable
     */
    public function reverse();

    /**
     * @param $func
     * @return IEnumerable
     */
    public function select($func);

    /**
     * @param $count
     * @return IEnumerable
     */
    public function skip($count);

    /**
     * @param $count
     * @return IEnumerable
     */
    public function skipWhile($func);

    public function sum($func = null);

    /**
     * @param $count
     * @return IEnumerable
     */
    public function take($count);

    /**
     * @param $count
     * @return IEnumerable
     */
    public function takeWhile($func);

    /**
     * @param null $func
     * @return IEnumerable
     */
    public function thenBy($func = null);

    /**
     * @param null $func
     * @return IEnumerable
     */
    public function thenByDescending($func = null);

    /**
     * @abstract
     * @return array
     */
    public function toArray();

    /**
     * @param $count
     * @return IEnumerable
     */
    public function where($func);
}
