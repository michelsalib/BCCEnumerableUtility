<?php

namespace BCC\EnumerableUtility\Resolver;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyAccess\PropertyPath;

class PropertyPathResolver implements ResolverInterface
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @inheritdoc
     */
    public function resolve($func)
    {
        if (!is_string($func)) {
            return null;
        }

        $propertyPath = new PropertyPath($func);
        return function($item) use ($propertyPath) { return $this->propertyAccessor->getValue($item, $propertyPath); };
    }
}
