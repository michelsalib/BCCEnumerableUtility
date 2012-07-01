<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Tests\Fixtures\EnumerableMock;
use BCC\EnumerableUtility\Grouping;
use BCC\EnumerableUtility\Tests\Fixtures\Object;

class EnumerableTest extends \PHPUnit_Framework_TestCase
{
    public function testContains()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertTrue($enumerable->contains(1));
        $this->assertFalse($enumerable->contains(4));
    }

    public function testAggregate()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals('321', $enumerable->aggregate(function ($workingSequence, $next) { return $next.$workingSequence; }));
    }

    public function testAll()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertTrue($enumerable->all(function ($item) { return $item <= 3; }));
        $this->assertFalse($enumerable->all(function ($item) { return $item != 2; }));
    }

    public function testAny()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertTrue($enumerable->any(function ($item) { return $item == 1; }));
        $this->assertFalse($enumerable->any(function ($item) { return $item == 4; }));
    }

    public function testAverage()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(2, $enumerable->average(function ($item) { return $item; }));
        $this->assertEquals(3, $enumerable->average(function ($item) { return $item + 1; }));
    }

    public function testCount()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(3, $enumerable->count());
        $this->assertEquals(2, $enumerable->count(function ($item) { return $item <= 2;}));
    }

    public function testDistinct()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3, 3));
        $this->assertEquals(array(1, 2, 3), $enumerable->distinct()->toArray());
        $this->assertEquals(array(1, 2), $enumerable->distinct(function ($item) { return $item%2;})->toArray());

        $obj1 = new Object(1, 2);
        $obj2 = new Object(1, 1);
        $obj3 = new Object(3, 1);
        $obj4 = new Object(1, 1);
        $enumerable = new EnumerableMock(array($obj1, $obj2, $obj3, $obj4));
        $this->assertEquals(array($obj1, $obj3), $enumerable->distinct(function ($item) { return $item->a;})->toArray());
        $this->assertEquals(array($obj1, $obj2, $obj3), $enumerable->distinct(function ($item) { return array($item->a, $item->b);})->toArray());
    }

    public function testElementAt()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(2, $enumerable->elementAt(1));
    }

    public function testFirst()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(1, $enumerable->first());
        $this->assertEquals(2, $enumerable->first(function ($item) { return $item > 1; }));
        $this->assertEquals(null, $enumerable->first(function ($item) { return $item > 3; }));
    }

    public function testGroupBy()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(2, 1);
        $obj3 = new Object(3, 2);

        $enumerable = new EnumerableMock(array($obj1, $obj2, $obj3));
        $expect = array(
            2 => new Grouping(2, array($obj1, $obj3)),
            1 => new Grouping(1, array($obj2)),
        );
        $this->assertEquals($expect, $enumerable->groupBy(function (Object $obj) { return $obj->b; })->toArray());
    }

    public function testJoin()
    {
        $obj1 = new Object('Michel', 1);
        $obj2 = new Object('Sam', 2);
        $obj3 = new Object('Julien', 3);

        $obj4 = new Object(1, 'Salib');
        $obj5 = new Object(2, 'Michaud');
        $obj6 = new Object(3, 'Crochet');

        $enumerable1 = new EnumerableMock(array($obj1, $obj2, $obj3));
        $enumerable2 = new EnumerableMock(array($obj4, $obj5, $obj6));
        $this->assertEquals(array('Michel Salib', 'Sam Michaud', 'Julien Crochet'),
            $enumerable1->join(
                $enumerable2,
                function (Object $obj) { return $obj->b; },
                function (Object $obj) { return $obj->a; },
                function (Object $obj1, Object $obj2) { return $obj1->a.' '.$obj2->b; }
            )->toArray()
        );
    }

    public function testLast()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(3, $enumerable->last());
        $this->assertEquals(2, $enumerable->last(function ($item) { return $item < 3; }));
        $this->assertEquals(null, $enumerable->last(function ($item) { return $item < 1; }));
    }

    public function testMax()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(3, $enumerable->max());
        $this->assertEquals(2, $enumerable->max(function ($item) { return $item === 3 ? 2 : $item; }));
    }

    public function testMin()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(1, $enumerable->min());
        $this->assertEquals(2, $enumerable->min(function ($item) { return $item === 1 ? 3 : $item; }));
    }

    public function testOrderBy()
    {
        $enumerable = new EnumerableMock(array(1, 3, 2));
        $this->assertEquals(array(1, 2, 3), $enumerable->orderBy()->toArray());
        $this->assertEquals(array(3, 1, 2), $enumerable->orderBy(function ($item) { return $item === 3 ? 0 : $item; })->toArray());
    }

    public function testOrderByDescending()
    {
        $enumerable = new EnumerableMock(array(1, 3, 2));
        $this->assertEquals(array(3, 2, 1), $enumerable->orderByDescending()->toArray());
        $this->assertEquals(array(2, 1, 3), $enumerable->orderByDescending(function ($item) { return $item === 3 ? 0 : $item; })->toArray());
    }

    public function testReverse()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(3, 2, 1), $enumerable->reverse()->toArray());
    }

    public function testSelect()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(1, 4, 9), $enumerable->select(function ($item) { return $item*$item;})->toArray());
    }

    public function testSkip()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(3), $enumerable->skip(2)->toArray());
    }

    public function testSkipWhile()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(2, 3), $enumerable->skipWhile(function ($item) { return $item < 2; })->toArray());
    }

    public function testSum()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(6, $enumerable->sum());
        $this->assertEquals(14, $enumerable->sum(function ($item) { return $item*$item; }));
    }

    public function testTake()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(1), $enumerable->take(1)->toArray());
    }

    public function testTakeWhile()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(1, 2), $enumerable->takeWhile(function ($item) { return $item !== 3; })->toArray());
    }

    public function testThenBy()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(1, 1);
        $obj3 = new Object(3, 1);

        $enumerable = new EnumerableMock(array($obj1, $obj2, $obj3));
        $this->assertEquals(array($obj2, $obj1, $obj3), $enumerable
            ->orderBy(function (Object $obj) { return $obj->a; })
            ->thenBy(function (Object $obj) { return $obj->b; })->toArray(),
            'Order with then should double order');
    }

    public function testThenByDescending()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(1, 1);
        $obj3 = new Object(3, 1);

        $enumerable = new EnumerableMock(array($obj1, $obj2, $obj3));
        $this->assertEquals(array($obj1, $obj2, $obj3), $enumerable
                ->orderBy(function (Object $obj) { return $obj->a; })
                ->thenByDescending(function (Object $obj) { return $obj->b; })->toArray(),
            'Order with then should double order');
    }

    public function testToArray()
    {
        $collection = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $collection->toArray());
    }

    public function testWhere()
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));
        $this->assertEquals(array(1, 3), $enumerable->where(function ($item) { return $item !== 2; })->toArray());
    }

    /**
     * Tests that the given function does not change the calling string
     *
     * @dataProvider functions
     * @param $function string The function to test
     */
    public function testUnchangedValue($function)
    {
        $enumerable = new EnumerableMock(array(1, 2, 3));

        $result = \call_user_func_array(array($enumerable, $function), \array_slice(\func_get_args(), 1));

        $this->assertEquals(array(1, 2, 3), $enumerable->toArray());
        $this->assertNotSame($enumerable, $result);
    }

    /**
     * Tests that the given function returns an instance of String
     *
     * @dataProvider enumerableReturnFunctions
     * @param $function string The function to test
     */
    public function testReturnEnumerable($function)
    {
        $enumerable = new EnumerableMock();

        $result = \call_user_func_array(array($enumerable, $function), \array_slice(\func_get_args(), 1));

        $this->assertInstanceOf('\BCC\EnumerableUtility\Tests\Fixtures\EnumerableMock', $result);
    }

    public function enumerableReturnFunctions()
    {
        return array(
            array('distinct'),
            array('join', array(), $this->func(), $this->func(), $this->func()),
            array('groupBy', $this->func()),
            array('orderBy', $this->func()),
            array('orderByDescending', $this->func()),
            array('reverse'),
            array('select', $this->func()),
            array('skip', 1),
            array('skipWhile', $this->func()),
            array('take', 1),
            array('takeWhile', $this->func()),
            array('thenBy', $this->func()),
            array('thenByDescending', $this->func()),
            array('where', $this->func()),
        );
    }

    public function functions()
    {
        return array(
            array('contains', 'a'),
            array('aggregate', $this->func()),
            array('all', $this->func()),
            array('any'),
            array('average', $this->func()),
            array('count'),
            array('distinct'),
            array('elementAt', 1),
            array('first'),
            array('groupBy', $this->func()),
            array('join', array(), $this->func(), $this->func(), $this->func()),
            array('last'),
            array('max'),
            array('min'),
            array('orderBy', $this->func()),
            array('orderByDescending', $this->func()),
            array('reverse'),
            array('select', $this->func()),
            array('skip', 1),
            array('skipWhile', $this->func()),
            array('sum', $this->func()),
            array('take', 1),
            array('takeWhile', $this->func()),
            array('thenBy', $this->func()),
            array('thenByDescending', $this->func()),
            array('toArray'),
            array('where', $this->func()),
        );
    }

    private function func(){
        return function () { return 1; };
    }
}
