<?php

namespace BCC\EnumerableUtility;

class Grouping extends Collection
{
    private $key;

    function __construct($key, $array = null)
    {
        parent::__construct($array);
        $this->key = $key;
    }

    public function getKey()
    {
        return $this->key;
    }
}
