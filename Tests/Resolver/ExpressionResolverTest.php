<?php

namespace BCC\EnumerableUtility\Tests\Resolver;

use BCC\EnumerableUtility\Resolver\ExpressionResolver;

class ExpressionResolverTest extends \PHPUnit_Framework_TestCase
{
    public function testResolvePath()
    {
        // ARRANGE
        $resolver = new ExpressionResolver();

        // ACT
        $result = $resolver->resolve(['i' => 'i["city"]']);

        // ASSERT
        $this->assertSame('Paris', $result(['city' => 'Paris']));
    }

    public function testResolveSquare()
    {
        // ARRANGE
        $resolver = new ExpressionResolver();

        // ACT
        $result = $resolver->resolve(['i' => 'i * i']);

        // ASSERT
        $this->assertSame(4, $result(2));
    }

    public function testResolveWithVariables()
    {
        // ARRANGE
        $resolver = new ExpressionResolver();

        // ACT
        $result = $resolver->resolve([
            'i' => 'i * m',
            'm' => 2,
        ]);

        // ASSERT
        $this->assertSame(4, $result(2));
    }
}
