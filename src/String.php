<?php

namespace BCC\EnumerableUtility;

use ArrayIterator;
use InvalidArgumentException;

/**
 * Class String
 *
 * @package BCC\EnumerableUtility
 *
 * @deprecated
 */
class String extends Stringer
{
    public function __construct($string = null)
    {
        @trigger_error('Class BCC\EnumerableUtility\String is deprecated. Please use BCC\EnumerableUtility\Stringer instead.', E_USER_DEPRECATED);

        parent::__construct($string);
    }

    public static function create($string = null)
    {
        @trigger_error('Class BCC\EnumerableUtility\String is deprecated. Please use BCC\EnumerableUtility\Stringer instead.', E_USER_DEPRECATED);

        return parent::create($string);
    }

}
