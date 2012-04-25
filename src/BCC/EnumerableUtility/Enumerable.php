<?php

namespace BCC\EnumerableUtility;

trait Enumerable
{
    public abstract function toArray();

    public function aggregate($func)
    {
        $result = null;
        foreach ($this->toArray() as $item) {
            $result = $func($result, $item);
        }

        return $result;
    }

    public function all($func)
    {
        foreach ($this->toArray() as $item) {
            if (!$func($item)) {

                return false;
            }
        }

        return true;
    }

    public function any($func = null)
    {
        $func = $func ?: function() { return true; };

        foreach ($this->toArray() as $item) {
            if ($func($item)) {

                return true;
            }
        }

        return false;
    }

    public function average($func)
    {
        $result = array();
        foreach ($this->toArray() as $item) {
            $result[] = $func($item);
        }

        return \array_sum($result) / \count($result);
    }

    public function contains($value)
    {
        foreach ($this->toArray() as $item) {
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

        foreach ($this->toArray() as $item) {
            if ($func($item)) {
                $result++;
            }
        }

        return $result;
    }

    public function distinct()
    {
        $class = __CLASS__;
        $result = array();

        foreach ($this->toArray() as $item) {
            if (!\in_array($item, $result)) {
                $result[] = $item;
            }
        }

        return new $class($result);
    }

    public function elementAt($index)
    {
        return $this->toArray()[$index];
    }

    public function first($func = null)
    {
        $func = $func ?: function () { return true; };

        foreach ($this->toArray() as $item) {
            if ($func($item)) {

                return $item;
            }
        }

        return null;
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

        foreach ($this->toArray() as $item) {
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

        foreach ($this->toArray() as $item) {
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
        $sort = array();
        $result = array();
        $class = __CLASS__;
        $func = $func ?: function ($item) { return $item; };

        foreach ($this->toArray() as $item) {
            $sort[$func($item)][] = $item;
        }

        \ksort($sort);

        foreach ($sort as $item) {
            $result = \array_merge($result, $item);
        }

        return new $class($result);
    }

    public function orderByDescending($func = null)
    {
        $sort = array();
        $result = array();
        $class = __CLASS__;
        $func = $func ?: function ($item) { return $item; };

        foreach ($this->toArray() as $item) {
            $sort[$func($item)][] = $item;
        }

        \krsort($sort);

        foreach ($sort as $item) {
            $result = \array_merge($result, $item);
        }

        return new $class($result);
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

        foreach ($this->toArray() as $item) {
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
        foreach ($this->toArray() as $item) {
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

        foreach ($this->toArray() as $item) {
            if (!$func($item)) {
                break;
            }
            $result[] = $item;
        }

        return new $class($result);
    }

    /**
     * @param $count
     * @return Enumerable
     */
    public function where($func)
    {
        $class = __CLASS__;
        $result = array();

        foreach ($this->toArray() as $item) {
            if ($func($item)) {
                $result[] = $item;
            }
        }

        return new $class($result);
    }
}
