<?php

namespace BCC\EnumerableUtility;

class Dictionary implements IEnumerable
{
    use Enumerable {
        Enumerable::resolveSelector as originalResolveSelector;
    }

    private $array;

    function __construct($array = null)
    {
        if ($array === null) {
            $this->array = array();
            return;
        }

        if ($array instanceof Dictionary) {
            $this->array = $array->array;
            return;
        }

        if ($array instanceof \Traversable) {
            $array = \iterator_to_array($array, true);
        }

        if (\is_array($array)) {
            $this->array = array();
            foreach ($array as $key => $value) {
                if ($value instanceof KeyValuePair) {
                    $this->array[is_object($value->getKey()) ? \spl_object_hash($value->getKey()) : $value->getKey()] = $value;
                }
                else {
                    $this->array[is_object($key) ? \spl_object_hash($key) : $key] = new KeyValuePair($key, $value);
                }
            }
            return;
        }

        throw new \InvalidArgumentException('You must give an array or a Traversable');
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->array);
    }

    public function offsetExists($offset)
    {
        $key = is_object($offset) ? \spl_object_hash($offset) : $offset;

        return isset($this->array[$key]);
    }

    public function offsetGet($offset)
    {
        $key = is_object($offset) ? \spl_object_hash($offset) : $offset;

        return $this->array[$key];
    }

    public function offsetSet($offset, $value)
    {
        $key = is_object($offset) ? \spl_object_hash($offset) : $offset;

        $this->array[$key] = $value;
    }

    public function offsetUnset($offset)
    {
        $key = is_object($offset) ? \spl_object_hash($offset) : $offset;

        unset($this->array[$key]);
    }

    public function keys()
    {
        return \array_values(\array_map(function (KeyValuePair $keyValuePair) {
            return $keyValuePair->getKey();
        }, $this->array));
    }

    public function values()
    {
        return \array_values(\array_map(function (KeyValuePair $keyValuePair) {
            return $keyValuePair->getValue();
        }, $this->array));
    }

    public function add($key, $item)
    {
        $this->array[is_object($key) ? \spl_object_hash($key) : $key] = new KeyValuePair($key, $item);
    }

    public function clear()
    {
        $this->array = array();
    }

    public function containsKey($key)
    {
        return $this->offsetExists($key);
    }

    public function containsValue($item)
    {
        return $this->contains($item);
    }

    public function remove($key)
    {
        $this->offsetUnset($key);
    }

    public function tryGetValue($key, &$value)
    {
        $key = is_object($key) ? \spl_object_hash($key) : $key;

        if (isset($this->array[$key])) {
            $value = $this->array[$key]->getValue();

            return true;
        }

        return false;
    }

    public function contains($value)
    {
        foreach ($this->values() as $item) {
            if ($value === $item) {

                return true;
            }
        }

        return false;
    }

    protected function resolveSelector($selector = null)
    {
        if ($selector == null) {
            $selector = function (KeyValuePair $item) { return $item->getValue(); } ;
        }

        return $this->originalResolveSelector($selector);
    }
}
