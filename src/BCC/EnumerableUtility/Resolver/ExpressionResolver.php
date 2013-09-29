<?php

namespace BCC\EnumerableUtility\Resolver;

use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ExpressionResolver implements ResolverInterface
{
    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    function __construct()
    {
        $this->expressionLanguage = new ExpressionLanguage();
    }

    /**
     * @inheritdoc
     */
    public function resolve($func)
    {
        if (!is_array($func)) {
            return null;
        }

        $key = key($func);
        $expression = array_shift($func);

        return function($item) use ($key, $expression, $func) {
            return $this->expressionLanguage->evaluate($expression, array_merge($func ,[
                $key => $item,
            ]));
        };
    }
}
