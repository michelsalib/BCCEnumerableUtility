<?php

namespace BCC\EnumerableUtility;

class Collection
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
        else if ($array instanceof Collection) {
            $this->array = $array->toArray();
        }
        else {
            throw new \InvalidArgumentException('You must give an array or a Collection');
        }
    }

    public function toArray() {
        return $this->array;
    }
}
