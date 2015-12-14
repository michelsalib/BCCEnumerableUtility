<?php

namespace BCC\EnumerableUtility\Resolver;

interface ResolverInterface
{
    /**
     * Transforms something that represents a function into a callable
     *
     * @param $func mixed Something that represents a function
     * @return callable The resolved callable, or null if the function cannot be resolved
     */
    public function resolve($func);
}
