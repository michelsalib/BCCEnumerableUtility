<?php

namespace BCC\EnumerableUtility;

use BCC\EnumerableUtility\Util\PropertyPath;

trait Enumerable
{
    protected $orderSequence   = array();
    protected $orderAscending  = true;
    protected $orderDescending = false;

    /**
     * @abstract
     * @return \Iterator
     */
    public abstract function getIterator();

    public abstract function offsetExists($offset);

    public abstract function offsetGet($offset);

    public abstract function offsetSet($offset, $value);

    public abstract function offsetUnset($offset);

    public function toArray()
    {
        return (array) $this->getIterator();
    }

    public function aggregate($func)
    {
        $result = null;
        foreach ($this as $item) {
            $result = $func($result, $item);
        }

        return $result;
    }

    public function all($func)
    {
        foreach ($this as $item) {
            if (!$func($item)) {

                return false;
            }
        }

        return true;
    }

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

    public function average($selector)
    {
        $func = $this->resolveSelector($selector);

        $result = array();
        foreach ($this as $item) {
            $result[] = $func($item);
        }

        return \array_sum($result) / \count($result);
    }

    public function contains($value)
    {
        foreach ($this as $item) {
            if ($value === $item) {

                return true;
            }
        }

        return false;
    }

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

    public function distinct($selector = null)
    {
        $class = __CLASS__;
        $distinct = array();
        $result = array();
        $func = $this->resolveSelector($selector);

        foreach ($this as $item) {
            $distinctKey = $func($item);
            if (!\in_array($distinctKey, $distinct)) {
                $distinct[] = $distinctKey;
                $result[] = $item;
            }
        }

        return new $class($result);
    }

    public function elementAt($index)
    {
        return $this[$index];
    }

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
     * @param null $func
     * @return Enumerable
     */
    public function groupBy($selector)
    {
        $compute = array();
        $result = array();
        $func = $this->resolveSelector($selector);

        foreach ($this as $item) {
            $group = $func($item);
            $key = is_object($group) ? \spl_object_hash($group) : $group;
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

    public function last($func = null)
    {
        return $this->reverse()->first($func);
    }

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
     * @param null $selector
     * @return Enumerable
     */
    public function orderBy($selector = null)
    {
        $func = $this->resolveSelector($selector);

        return $this->order(array(array('order' => $this->orderAscending, 'func' => $func)));
    }

    /**
     * @param null $selector
     * @return Enumerable
     */
    public function orderByDescending($selector = null)
    {
        $func = $this->resolveSelector($selector);

        return $this->order(array(array('order' => $this->orderDescending, 'func' => $func)));
    }

    /**
     * @param $string
     * @return Enumerable
     */
    public function reverse()
    {
        $class = __CLASS__;

        return new $class(\array_reverse($this->toArray()));
    }

    /**
     * @param $selector
     * @return Enumerable
     */
    public function select($selector)
    {
        $class = __CLASS__;

        $func = $this->resolveSelector($selector);

        return new $class(\array_map($func, $this->toArray()));
    }

    /**
     * @param $count
     * @return Enumerable
     */
    public function skip($count)
    {
        $class = __CLASS__;

        return new $class(\array_slice($this->toArray(), $count));
    }

    /**
     * @param $count
     * @return Enumerable
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
     * @param $count
     * @return Enumerable
     */
    public function take($count)
    {
        $class = __CLASS__;
        return new $class(\array_slice($this->toArray(), 0, $count));
    }

    /**
     * @param $count
     * @return Enumerable
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
     * @param null $func
     * @return Enumerable
     */
    public function thenBy($selector = null)
    {
        $func = $this->resolveSelector($selector);
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderAscending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @param null $func
     * @return Enumerable
     */
    public function thenByDescending($selector = null)
    {
        $func = $this->resolveSelector($selector);
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderDescending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @param $count
     * @return Enumerable
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

    protected function order(array $sequence)
    {
        $result = array();
        $class = __CLASS__;

        foreach ($this as $item) {
            $result[] = $item;
        }

        \usort($result, function ($a, $b) use ($sequence) {
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

    protected function resolveSelector($selector = null)
    {
        if ($selector === null) {
            return function ($item) { return $item; };
        }
        if (\is_callable($selector)) {
            return $selector;
        }
        if (\is_string($selector)) {
            $propertyPath = new PropertyPath($selector);

            return function($item) use ($propertyPath) { return $propertyPath->getValue($item); };
        }

        throw new \LogicException('Selector cannot be resolved');
    }
}
