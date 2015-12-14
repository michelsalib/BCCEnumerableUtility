<?php

namespace BCC\EnumerableUtility\Resolver;

class CallbackResolver implements ResolverInterface
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * @param $callback callable
     */
    function __construct(callable $callback)
    {
        $this->callback = $callback;
    }


    /**
     * @inheritdoc
     */
    public function resolve($func)
    {
        $callback = $this->callback;

        return $callback($func);
    }
}
