<?php

namespace BCC\EnumerableUtility;

class Collection implements IEnumerable
{
    use Enumerable;

    private $array;

    function __construct($array = null)
    {
        if ($array === null) {
            $this->array = array();
        }
        else if (is_array($array)) {
            $this->array = \array_values($array);
        }
        else if ($array instanceof \Traversable) {
            $this->array = \iterator_to_array($array, false);
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

    public function add($item)
    {
        $this->array[] = $item;
    }

    public function addRange($items)
    {
        if ($items instanceof \Traversable) {
            $items = \iterator_to_array($items);
        }

        if (!is_array($items))  {
            throw new \InvalidArgumentException('You must give an array or a Traversable');
        }

        $this->array = \array_merge($this->array, $items);
    }

    public function clear()
    {
        $this->array = array();
    }

    public function indexOf($item)
    {
        $result = \array_search($item, $this->array, true);

        return $result !== false ? $result : -1;
    }

    public function insert($index, $item)
    {
        $result = $this->take($index);
        $result->add($item);
        $result->addRange($this->skip($index)->toArray());

        $this->array = $result->toArray();
    }

    public function remove($item)
    {
        $index = $this->indexOf($item);

        if ($index !== -1) {
            $this->removeAt($index);
        }
    }

    public function removeAt($index)
    {
        unset($this->array[$index]);

        $this->array = \array_values($this->array);
    }
}
