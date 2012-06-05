<?php

namespace BCC\EnumerableUtility\Tests\Fixtures;

class Object
{
    public $a;

    public $b;

    function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }
}
