<?php

namespace BCC\EnumerableUtility\Tests\Fixtures;

use BCC\EnumerableUtility\Enumerable;
use BCC\EnumerableUtility\IEnumerable;

class ResolverEnumerableMock implements IEnumerable
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
            $this->array = \iterator_to_array($array);
        }
        else {
            throw new \LogicException('You must give an EnumerableMock');
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

    protected function resolveSelector($selector = null)
    {
        throw new ResolverCalledException();
    }
}
