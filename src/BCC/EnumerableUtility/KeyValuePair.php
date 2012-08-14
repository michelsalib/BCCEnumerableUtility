<?php

namespace BCC\EnumerableUtility;

class KeyValuePair
{
    private $key;

    private $value;

    function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function getValue()
    {
        return $this->value;
    }
}
