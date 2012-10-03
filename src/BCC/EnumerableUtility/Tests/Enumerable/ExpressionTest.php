<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Tests\Fixtures\ResolverEnumerableMock;
use BCC\EnumerableUtility\Util\PropertyPath;
use BCC\EnumerableUtility\Tests\Fixtures\Object;

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

    public function testPropertyPath()
    {
        $propertyPath = new PropertyPath('b');
        $this->assertEquals(2, $propertyPath->getValue(new Object(1, 2)));

        $propertyPath = new PropertyPath('[0].b');
        $this->assertEquals(2, $propertyPath->getValue(array(new Object(1, 2))));

        $propertyPath = new PropertyPath('b[0]');
        $this->assertEquals(2, $propertyPath->getValue(new Object(1, array(2))));

        $propertyPath = new PropertyPath('b.a');
        $this->assertEquals(2, $propertyPath->getValue(new Object(1, new Object(2, 3))));

        $propertyPath = new PropertyPath('[a][b]');
        $this->assertEquals(2, $propertyPath->getValue(array('a' => array('b' => 2))));
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
