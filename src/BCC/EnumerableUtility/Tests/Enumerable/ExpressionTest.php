<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Tests\Fixtures\ResolverEnumerableMock;

class ExpressionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * HACK: the way to test the call of resolveExpression is ugly. Sadly phpunit mock ignores traits.
     *
     * @dataProvider expressionFunctions
     * @expectedException \BCC\EnumerableUtility\Tests\Fixtures\ResolverCalledException
     */
    public function testExpressionResolver($function)
    {
        $enumerable = new ResolverEnumerableMock();

         \call_user_func_array(array($enumerable, $function), array('test'));
    }

    /**
     * HACK: the way to test the call of resolveExpression is ugly. Sadly phpunit mock ignores traits.
     *
     * @expectedException \BCC\EnumerableUtility\Tests\Fixtures\ResolverCalledException
     */
    public function testExpressionResolverOnJoin()
    {
        $enumerable = new ResolverEnumerableMock();

        $enumerable->join(array(), 'test', 'test', function() {});
    }

    public function expressionFunctions()
    {
        return array(
            array('average'),
            array('distinct'),
            array('groupBy'),
            array('max'),
            array('min'),
            array('orderBy'),
            array('orderByDescending'),
            array('select'),
            array('sum'),
            array('thenBy'),
            array('thenByDescending'),
        );
    }
}
