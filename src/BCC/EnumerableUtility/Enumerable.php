<?php

namespace BCC\EnumerableUtility;

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

    public function average($func)
    {
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

    public function distinct($func = null)
    {
        $class = __CLASS__;
        $distinct = array();
        $result = array();
        $func = $func ?: function ($item) { return $item; };

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
    public function groupBy($func)
    {
        $class = __CLASS__;
        $result = array();

        foreach ($this as $item) {
            $key = $func($item);
            if (!isset($result[$key])) {
                $result[$key] = array();
            }
            $result[$key][] = $item;
        }

        foreach ($result as $key => $items) {
            $result[$key] = new Grouping($key, $items);
        }

        return new $class($result);
    }

    public function last($func = null)
    {
        return $this->reverse()->first($func);
    }

    public function max($func = null)
    {
        if ($func === null) {
            return \max($this->toArray());
        }

        $result = null;
        $resultValue = null;

        foreach ($this as $item) {
            $newResultValue = $func($item);
            if ($newResultValue > $resultValue) {
                $resultValue = $newResultValue;
                $result = $item;
            }
        }

        return $result;
    }

    public function min($func = null)
    {
        if ($func === null) {
            return \min($this->toArray());
        }

        $result = null;
        $resultValue = PHP_INT_MAX;

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
     * @param null $func
     * @return Enumerable
     */
    public function orderBy($func = null)
    {
        $func = $func ?: function ($item) { return $item; };

        return $this->order(array(array('order' => $this->orderAscending, 'func' => $func)));
    }

    /**
     * @param null $func
     * @return Enumerable
     */
    public function orderByDescending($func = null)
    {
        $func = $func ?: function ($item) { return $item; };

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

    public function select($func)
    {
        $class = __CLASS__;

        return new $class(\array_map(function ($item) use ($func) { return $func($item); }, $this->toArray()));
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

    public function sum($func = null)
    {
        $func = $func ?: function ($item) { return $item; };

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
    public function thenBy($func = null)
    {
        $func = $func ?: function ($item) { return $item; };
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderAscending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @param null $func
     * @return Enumerable
     */
    public function thenByDescending($func = null)
    {
        $func = $func ?: function ($item) { return $item; };
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
}
