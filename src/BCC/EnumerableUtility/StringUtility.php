<?php

namespace BCC\EnumerableUtility;

class StringUtility
{
    public static function __callStatic($name, $arguments)
    {
        $method = new \ReflectionMethod('\BCC\EnumerableUtility\String', $name);

        if ($method->isStatic()) {
            $result = $method->invokeArgs(null, $arguments);
        }
        else {
            $result = $method->invokeArgs(new String($arguments[0]), \array_slice($arguments, 1));
        }

        if ($result instanceof String) {
            return (string) $result;
        }

        return $result;
    }
}
