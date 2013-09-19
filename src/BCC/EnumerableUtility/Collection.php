<?php

namespace BCC\EnumerableUtility;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;

class Collection extends Enumerable
{
    /**
     * @var array
     */
    private $array;

    /**
     * @param array|Traversable $array
     *
     * @throws \InvalidArgumentException
     */
    function __construct($array = null)
    {
        if ($array === null) {
            $this->array = array();
        }
        else if (is_array($array)) {
            $this->array = array_values($array);
        }
        else if ($array instanceof Traversable) {
            $this->array = iterator_to_array($array, false);
        }
        else {
            throw new InvalidArgumentException('You must give an array or a Traversable');
        }
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    /**
     * @param int $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->array[$offset]);
    }

    /**
     * @param int $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->array[$offset];
    }

    /**
     * @param int $offset
     *
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->array[$offset] = $value;

        $this->array = array_values($this->array);
    }

    /**
     * @param int $offset
     */
    public function offsetUnset($offset)
    {
        unset($this->array[$offset]);

        $this->array = array_values($this->array);
    }

    /**
     * @param mixed $item
     */
    public function add($item)
    {
        $this->array[] = $item;
    }

    /**
     * @param mixed[] $items
     *
     * @throws InvalidArgumentException
     */
    public function addRange($items)
    {
        if ($items instanceof Traversable) {
            $items = iterator_to_array($items);
        }

        if (!is_array($items))  {
            throw new InvalidArgumentException('You must give an array or a Traversable');
        }

        $this->array = array_merge($this->array, $items);
    }

    public function clear()
    {
        $this->array = array();
    }

    /**
     * @param mixed $item
     *
     * @return int
     */
    public function indexOf($item)
    {
        $result = array_search($item, $this->array, true);

        return $result !== false ? $result : -1;
    }

    /**
     * @param int $index
     * @param mixed $item
     */
    public function insert($index, $item)
    {
        $result = $this->take($index);
        $result->add($item);
        $result->addRange($this->skip($index)->toArray());

        $this->array = $result->toArray();
    }

    /**
     * @param mixed $item
     */
    public function remove($item)
    {
        $index = $this->indexOf($item);

        if ($index !== -1) {
            $this->offsetUnset($index);
        }
    }

    /**
     * @param int $index
     */
    public function removeAt($index)
    {
        $this->offsetUnset($index);
    }
}
