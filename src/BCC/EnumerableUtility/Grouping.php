<?php

namespace BCC\EnumerableUtility;

class Grouping extends Collection
{
    /**
     * @var mixed
     */
    private $key;

    function __construct($key, $array = null)
    {
        parent::__construct($array);
        $this->key = $key;
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }
}
