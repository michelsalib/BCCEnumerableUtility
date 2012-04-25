<?php

namespace BCC\EnumerableUtility\Tests\Fixtures;

use \BCC\EnumerableUtility\Enumerable;

class EnumerableMock
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
        else if ($array instanceof EnumerableMock) {
            $this->array = $array->toArray();
        }
        else {
            throw new \LogicException('You must give an EnumerableMock');
        }
    }

    public function toArray() {
        return $this->array;
    }
}
