<?php

namespace BCC\EnumerableUtility;

use ArrayIterator;
use InvalidArgumentException;
use Traversable;

class Dictionary extends Enumerable
{
    /**
     * @var KeyValuePair[]
     */
    private $array;

    /**
     * @param array|Dictionary|Traversable $array
     *
     * @throws \InvalidArgumentException
     */
    function __construct($array = null)
    {
        parent::__construct();

        if ($array === null) {
            $this->array = [];
            return;
        }

        if ($array instanceof Dictionary) {
            $this->array = $array->array;
            return;
        }

        if ($array instanceof Traversable) {
            $array = iterator_to_array($array, true);
        }

        if (is_array($array)) {
            $this->array = array();
            foreach ($array as $key => $value) {
                if ($value instanceof KeyValuePair) {
                    $this->array[is_object($value->getKey()) ? spl_object_hash($value->getKey()) : $value->getKey()] = $value;
                }
                else {
                    $this->array[is_object($key) ? spl_object_hash($key) : $key] = new KeyValuePair($key, $value);
                }
            }
            return;
        }

        throw new InvalidArgumentException('You must give an array or a Traversable');
    }

    /**
     * @inheritdoc
     */
    public function getIterator()
    {
        return new ArrayIterator($this->array);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        $key = is_object($offset) ? spl_object_hash($offset) : $offset;

        return isset($this->array[$key]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        $key = is_object($offset) ? spl_object_hash($offset) : $offset;

        return $this->array[$key];
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $key = is_object($offset) ? spl_object_hash($offset) : $offset;

        $this->array[$key] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $key = is_object($offset) ? spl_object_hash($offset) : $offset;

        unset($this->array[$key]);
    }

    /**
     * @inheritdoc
     */
    public function keys()
    {
        return array_values(array_map(function (KeyValuePair $keyValuePair) {
            return $keyValuePair->getKey();
        }, $this->array));
    }

    /**
     * @return array
     */
    public function values()
    {
        return array_values(array_map(function (KeyValuePair $keyValuePair) {
            return $keyValuePair->getValue();
        }, $this->array));
    }

    /**
     * @inheritdoc
     */
    public function add($key, $item)
    {
        $this->array[is_object($key) ? spl_object_hash($key) : $key] = new KeyValuePair($key, $item);
    }

    public function clear()
    {
        $this->array = array();
    }

    /**
     * @param mixed $key
     *
     * @return bool
     */
    public function containsKey($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    public function containsValue($item)
    {
        return $this->contains($item);
    }

    /**
     * @param mixed $key
     */
    public function remove($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * @param mixed $key
     * @param mixed $value
     *
     * @return bool
     */
    public function tryGetValue($key, &$value)
    {
        $key = is_object($key) ? spl_object_hash($key) : $key;

        if (isset($this->array[$key])) {
            $value = $this->array[$key]->getValue();

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function contains($value)
    {
        foreach ($this->values() as $item) {
            if ($value === $item) {

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function resolveFunction($function)
    {
        if ($function === null) {
            return function (KeyValuePair $item) { return $item->getValue(); } ;
        }

        return parent::resolveFunction($function);
    }
}
