<?php

namespace BCC\EnumerableUtility;

use BCC\EnumerableUtility\Util\PropertyPath;
use LogicException;
use Iterator;
use InvalidArgumentException;

trait Enumerable
{
    /**
     * @var array
     */
    protected $orderSequence   = array();

    /**
     * @var array
     */
    protected $orderAscending  = true;

    /**
     * @var array
     */
    protected $orderDescending = false;

    /**
     * @abstract
     * @return Iterator
     */
    public abstract function getIterator();

    /**
     * @param $offset
     *
     * @return bool
     */
    public abstract function offsetExists($offset);

    /**
     * @param $offset
     *
     * @return mixed
     */
    public abstract function offsetGet($offset);

    /**
     * @param $offset
     * @param $value
     *
     * @return mixed
     */
    public abstract function offsetSet($offset, $value);

    /**
     * @param $offset
     *
     * @return mixed
     */
    public abstract function offsetUnset($offset);

    /**
     * @return array
     */
    public function toArray()
    {
        return (array) $this->getIterator();
    }

    /**
     * @param callable $func
     *
     * @return mixed
     */
    public function aggregate($func)
    {
        $result = null;
        foreach ($this as $item) {
            $result = $func($result, $item);
        }

        return $result;
    }

    /**
     * @param callable $func
     *
     * @return bool
     */
    public function all($func)
    {
        foreach ($this as $item) {
            if (!$func($item)) {

                return false;
            }
        }

        return true;
    }

    /**
     * @param callable $func
     *
     * @return bool
     */
    public function any($func = null)
    {
        $func = $func ?: function() { return true; };

        foreach ($this as $item) {
            if ($func($item)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param string|callable $selector
     *
     * @return float
     *
     * @throws InvalidArgumentException
     */
    public function average($selector = null)
    {
        $func = $this->resolveSelector($selector);

        $result = array();
        foreach ($this as $item) {
            $result[] = $func($item);
        }

        if (0 === count($result)) {
            throw new InvalidArgumentException('Enumerable has no element');
        }

        return array_sum($result) / count($result);
    }

    /**
     * @param mixed $value
     *
     * @return bool
     */
    public function contains($value)
    {
        foreach ($this as $item) {
            if ($value === $item) {

                return true;
            }
        }

        return false;
    }

    /**
     * @param callable $func
     *
     * @return int
     */
    public function count($func = null)
    {
        $func = $func ?: function () { return true; };
        $result = 0;

        foreach ($this as $item) {
            if ($func($item)) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @param string|callable $selector
     *
     * @return IEnumerable
     */
    public function distinct($selector = null)
    {
        $class = __CLASS__;
        $distinct = array();
        $result = array();
        $func = $this->resolveSelector($selector);

        foreach ($this as $item) {
            $distinctKey = $func($item);
            if (!in_array($distinctKey, $distinct)) {
                $distinct[] = $distinctKey;
                $result[] = $item;
            }
        }

        return new $class($result);
    }

    /**
     * @param callable $func
     *
     * @return IEnumerable
     */
    public function each($func)
    {
        foreach ($this as $key => $item) {
            $func($item);
            $this[$key] = $item;
        }
    }

    /**
     * @param mixed $index
     *
     * @return mixed
     */
    public function elementAt($index)
    {
        return $this[$index];
    }

    /**
     * @param callable $func
     *
     * @return mixed
     */
    public function first($func = null)
    {
        $func = $func ?: function () { return true; };

        foreach ($this as $item) {
            if ($func($item)) {

                return $item;
            }
        }

        return null;
    }

    /**
     * @param callable|string $selector
     *
     * @return IEnumerable
     */
    public function groupBy($selector)
    {
        $compute = array();
        $result = array();
        $func = $this->resolveSelector($selector);

        foreach ($this as $item) {
            $group = $func($item);
            $key = is_object($group) ? spl_object_hash($group) : $group;
            if (!isset($compute[$key])) {
                $compute[$key] = array('group' => $group, 'items' => array());
            }
            $compute[$key]['items'][] = $item;
        }

        foreach ($compute as $item) {
            $result[] = new Grouping($item['group'], $item['items']);
        }

        return new Collection($result);
    }

    /**
     * @param Iterator $innerItems
     * @param callable $outerSelector
     * @param callable $innerSelector
     * @param callable $resultFunc
     *
     * @return Collection
     */
    public function join($innerItems, $outerSelector, $innerSelector, $resultFunc)
    {
        $result = array();

        $outerFunc = $this->resolveSelector($outerSelector);
        $innerFunc = $this->resolveSelector($innerSelector);

        foreach ($this as $outer) {
            $outerKey = $outerFunc($outer);
            foreach ($innerItems as $inner) {
                if ($outerKey == $innerFunc($inner)) {
                    $result[] = $resultFunc($outer, $inner);
                }
            }
        }

        return new Collection($result);
    }

    /**
     * @param callable $func
     *
     * @return mixed
     */
    public function last($func = null)
    {
        return $this->reverse()->first($func);
    }

    /**
     * @param callable|string $selector
     *
     * @return mixed
     */
    public function max($selector = null)
    {
        $result = null;
        $resultValue = null;
        $func = $this->resolveSelector($selector);

        foreach ($this as $item) {
            $newResultValue = $func($item);
            if ($newResultValue > $resultValue) {
                $resultValue = $newResultValue;
                $result = $item;
            }
        }

        return $result;
    }

    /**
     * @param callable|string $selector
     *
     * @return mixed
     */
    public function min($selector = null)
    {
        $result = null;
        $resultValue = PHP_INT_MAX;
        $func = $this->resolveSelector($selector);

        foreach ($this as $item) {
            $newResultValue = $func($item);
            if ($newResultValue < $resultValue) {
                $resultValue = $newResultValue;
                $result = $item;
            }
        }

        return $result;
    }

    /**
     * @param callable|string $selector
     *
     * @return IEnumerable
     */
    public function orderBy($selector = null)
    {
        $func = $this->resolveSelector($selector);

        return $this->order(array(array('order' => $this->orderAscending, 'func' => $func)));
    }

    /**
     * @param callable|string $selector
     *
     * @return IEnumerable
     */
    public function orderByDescending($selector = null)
    {
        $func = $this->resolveSelector($selector);

        return $this->order(array(array('order' => $this->orderDescending, 'func' => $func)));
    }

    /**
     * @return IEnumerable
     */
    public function reverse()
    {
        $class = __CLASS__;

        return new $class(array_reverse($this->toArray()));
    }

    /**
     * @param callable $selector
     *
     * @return IEnumerable
     */
    public function select($selector)
    {
        $class = __CLASS__;

        $func = $this->resolveSelector($selector);

        return new $class(array_map($func, $this->toArray()));
    }

    /**
     * @param callable $selector
     *
     * @return IEnumerable
     */
    public function selectMany($selector = null)
    {
        $class = __CLASS__;
        $result = array();

        $func = $this->resolveSelector($selector);

        foreach (array_map($func, $this->toArray()) as $subValue) {
            $result = array_merge($result, $subValue);
        }

        return new $class($result);
    }

    /**
     * @param int $count
     *
     * @return IEnumerable
     */
    public function skip($count)
    {
        $class = __CLASS__;

        return new $class(array_slice($this->toArray(), $count));
    }

    /**
     * @param callable $func
     *
     * @return IEnumerable
     */
    public function skipWhile($func)
    {
        $class = __CLASS__;
        $result = array();
        $skipping = true;

        foreach ($this as $item) {
            if ($skipping && $func($item)) {
                continue;
            }
            else {
                $skipping = false;
            }
            $result[] = $item;
        }

        return new $class($result);
    }

    /**
     * @param callable|string $selector
     *
     * @return float
     */
    public function sum($selector = null)
    {
        $func = $this->resolveSelector($selector);

        $result = 0;
        foreach ($this as $item) {
            $result += $func($item);
        }

        return $result;
    }


    /**
     * @param int $count
     *
     * @return IEnumerable
     */
    public function take($count)
    {
        $class = __CLASS__;

        return new $class(array_slice($this->toArray(), 0, $count));
    }

    /**
     * @param callable $func
     *
     * @return IEnumerable
     */
    public function takeWhile($func)
    {
        $class = __CLASS__;
        $result = array();

        foreach ($this as $item) {
            if (!$func($item)) {
                break;
            }
            $result[] = $item;
        }

        return new $class($result);
    }

    /**
     * @param callable|string $selector
     *
     * @return IEnumerable
     */
    public function thenBy($selector = null)
    {
        $func = $this->resolveSelector($selector);
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderAscending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @param callable|string $selector
     *
     * @return IEnumerable
     */
    public function thenByDescending($selector = null)
    {
        $func = $this->resolveSelector($selector);
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderDescending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @param callable|string $keySelector
     * @param callable|string $valueSelector
     *
     * @throws LogicException
     *
     * @return Dictionary
     */
    public function toDictionary($keySelector, $valueSelector = null)
    {
        $result = new Dictionary();
        $keyFunc = $this->resolveSelector($keySelector);
        $valueFunc = $this->resolveSelector($valueSelector);

        foreach ($this as $item) {
            $key = $keyFunc($item);

            if ($result->containsKey($key)) {
                throw new LogicException(sprintf('Key selection produces duplicated elements "%s".', $key));
            }

            $result->add($keyFunc($item), $valueFunc($item));
        }

        return $result;
    }

    /**
     * @param callable $func
     *
     * @return IEnumerable
     */
    public function where($func)
    {
        $class = __CLASS__;
        $result = array();

        foreach ($this as $item) {
            if ($func($item)) {
                $result[] = $item;
            }
        }

        return new $class($result);
    }

    /**
     * @param array $sequence
     *
     * @return IEnumerable
     */
    protected function order(array $sequence)
    {
        $result = array();
        $class = __CLASS__;

        foreach ($this as $item) {
            $result[] = $item;
        }

        usort($result, function ($a, $b) use ($sequence) {
            foreach ($sequence as $order) {
                $aValue = $order['func']($a);
                $bValue = $order['func']($b);
                if ($aValue > $bValue) {
                    return $order['order'];
                }
                elseif ($aValue < $bValue) {
                    return !$order['order'];
                }
            }

            return false;
        });

        $resultObject = new $class($result);
        $resultObject->orderSequence = $sequence;

        return $resultObject;
    }

    /**
     * @param callable|string $selector
     *
     * @return callable
     *
     * @throws LogicException
     */
    protected function resolveSelector($selector = null)
    {
        if ($selector === null) {
            return function ($item) { return $item; };
        }
        if (is_callable($selector)) {
            return $selector;
        }
        if (is_string($selector)) {
            $propertyPath = new PropertyPath($selector);

            return function($item) use ($propertyPath) { return $propertyPath->getValue($item); };
        }

        throw new LogicException('Selector cannot be resolved');
    }
}
