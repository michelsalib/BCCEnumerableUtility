<?php

namespace BCC\EnumerableUtility;

class Dictionary implements IEnumerable
{
    use Enumerable;

    private $array;

    function __construct($array = null)
    {
        if ($array === null) {
            $this->array = array();
        }
        else if (is_array($array)) {
            $this->array = $array;
        }
        else if ($array instanceof \Traversable) {
            $this->array = \iterator_to_array($array, true);
        }
        else {
            throw new \InvalidArgumentException('You must give an array or a Traversable');
        }
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->array[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);
    }

    public function keys()
    {
        return \array_keys($this->array);
    }

    public function values()
    {
        return \array_values($this->array);
    }

    public function add($key, $item)
    {
        $this->array[$key] = $item;
    }

    public function clear()
    {
        $this->array = array();
    }

    public function containsKey($key)
    {
        return isset($this->array[$key]);
    }

    public function containsValue($item)
    {
        return $this->contains($item);
    }

    public function remove($key)
    {
        unset($this->array[$key]);
    }

    public function tryGetValue($key, &$value)
    {
        if (isset($this->array[$key])) {
            $value = $this->array[$key];

            return true;
        }

        return false;
    }
}
