<?php

namespace BCC\EnumerableUtility\Resolver;

class NullResolver implements ResolverInterface
{
    /**
     * @inheritdoc
     */
    public function resolve($func)
    {
        if ($func !== null) {
            return null;
        }

        return function($item) { return $item; };
    }
}
