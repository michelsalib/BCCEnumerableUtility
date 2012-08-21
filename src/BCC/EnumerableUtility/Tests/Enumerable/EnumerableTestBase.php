<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use Closure;
use BCC\EnumerableUtility\Enumerable;
use BCC\EnumerableUtility\IEnumerable;
use BCC\EnumerableUtility\Collection;
use BCC\EnumerableUtility\Grouping;
use BCC\EnumerableUtility\Tests\Fixtures\Object;

abstract class EnumerableTestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * @abstract
     * @param null $param
     * @return Enumerable
     */
    protected abstract function newInstance($param = null);

    protected function preClosure(Closure $func)
    {
        return $func;
    }

    public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = FALSE, $ignoreCase = FALSE)
    {
        /** @var $actual String */
        if (\is_array($expected) && $actual instanceof IEnumerable) {
            $actual = $actual->toArray();
        }

        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    public function testContains()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertTrue($enumerable->contains(1));
        $this->assertFalse($enumerable->contains(4));
    }

    public function testAggregate()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals('321', $enumerable->aggregate($this->preClosure(function ($workingSequence, $next) { return $next.$workingSequence; })));
    }

    public function testAll()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertTrue($enumerable->all($this->preClosure(function ($item) { return $item <= 3; })));
        $this->assertFalse($enumerable->all($this->preClosure(function ($item) { return $item != 2; })));
    }

    public function testAny()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertTrue($enumerable->any($this->preClosure(function ($item) { return $item == 1; })));
        $this->assertFalse($enumerable->any($this->preClosure(function ($item) { return $item == 4; })));
    }

    public function testAverage()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(2, $enumerable->average());
        $this->assertEquals(3, $enumerable->average($this->preClosure(function ($item) { return $item + 1; })));
    }

    public function testCount()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(3, $enumerable->count());
        $this->assertEquals(2, $enumerable->count($this->preClosure(function ($item) { return $item <= 2;})));
    }

    public function testDistinct()
    {
        $enumerable = $this->newInstance(array(1, 2, 3, 3));
        $this->assertEquals(array(1, 2, 3), $enumerable->distinct());
        $this->assertEquals(array(1, 2), $enumerable->distinct($this->preClosure(function ($item) { return $item%2;})));
    }

    public function testDistinctWithObjects()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(1, 1);
        $obj3 = new Object(3, 1);
        $obj4 = new Object(1, 1);
        $enumerable = $this->newInstance(array($obj1, $obj2, $obj3, $obj4));
        $this->assertEquals(array($obj1, $obj3), $enumerable->distinct($this->preClosure(function ($item) { return $item->a;})));
        $this->assertEquals(array($obj1, $obj2, $obj3), $enumerable->distinct($this->preClosure(function ($item) { return array($item->a, $item->b);})));
    }

    public function testElementAt()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(2, $enumerable->elementAt(1));
    }

    public function testFirst()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(1, $enumerable->first());
        $this->assertEquals(2, $enumerable->first($this->preClosure(function ($item) { return $item > 1; })));
        $this->assertEquals(null, $enumerable->first($this->preClosure(function ($item) { return $item > 3; })));
    }

    public function testGroupBy()
    {
        $enumerable = $this->newInstance(array('e', 'E', 'a', 'A'));
        $expect = array(
            new Grouping('E', array('e', 'E')),
            new Grouping('A', array('a', 'A')),
        );
        $this->assertEquals($expect, $enumerable->groupBy($this->preClosure(function ($item) { return \strtoupper($item); })));
    }

    public function testGroupByWithObjects()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(2, 1);
        $obj3 = new Object(3, 2);

        $enumerable = $this->newInstance(array($obj1, $obj2, $obj3));
        $expect = array(
            new Grouping(2, array($obj1, $obj3)),
            new Grouping(1, array($obj2)),
        );
        $this->assertEquals($expect, $enumerable->groupBy($this->preClosure(function (Object $obj) { return $obj->b; })));
    }

    public function testGroupByWithSubObjects()
    {
        $key1 = new Object(1, 1);
        $key2 = new Object(1, 2);

        $obj1 = new Object(1, $key1);
        $obj2 = new Object(2, $key2);
        $obj3 = new Object(3, $key1);

        $enumerable = $this->newInstance(array($obj1, $obj2, $obj3));
        $result = $enumerable->groupBy($this->preClosure(function (Object $obj) { return $obj->b; }));

        $this->assertCount(2, $result);
        $this->assertEquals(new Grouping($key1, array($obj1, $obj3)), $result->first());
        $this->assertEquals(new Grouping($key2, array($obj2)), $result->elementAt(1));
    }

    public function testJoin()
    {
        $enumerable1 = $this->newInstance(array('a', 'b'));
        $enumerable2 = $this->newInstance(array('A', 'B'));
        $this->assertEquals(array('aA', 'bB'),
            $enumerable1->join(
                $enumerable2,
                $this->preClosure(function ($item) { return \strtoupper($item); }),
                $this->preClosure(function ($item) { return \strtoupper($item); }),
                $this->preClosure(function ($item1, $item2) { return $item1.$item2; })
            )
        );
    }

    public function testJoinWithObjects()
    {
        $obj1 = new Object('Michel', 1);
        $obj2 = new Object('Sam', 2);
        $obj3 = new Object('Julien', 3);

        $obj4 = new Object(1, 'Salib');
        $obj5 = new Object(2, 'Michaud');
        $obj6 = new Object(3, 'Crochet');

        $enumerable1 = $this->newInstance(array($obj1, $obj2, $obj3));
        $enumerable2 = $this->newInstance(array($obj4, $obj5, $obj6));
        $this->assertEquals(array('Michel Salib', 'Sam Michaud', 'Julien Crochet'),
            $enumerable1->join(
                $enumerable2,
                $this->preClosure(function (Object $obj) { return $obj->b; }),
                $this->preClosure(function (Object $obj) { return $obj->a; }),
                $this->preClosure(function (Object $obj1, Object $obj2) { return $obj1->a.' '.$obj2->b; })
            )
        );
    }

    public function testLast()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(3, $enumerable->last());
        $this->assertEquals(2, $enumerable->last($this->preClosure(function ($item) { return $item < 3; })));
        $this->assertEquals(null, $enumerable->last($this->preClosure(function ($item) { return $item < 1; })));
    }

    public function testMax()
    {
        $enumerable = $this->newInstance(array(1, 3, 2));
        $this->assertEquals(3, $enumerable->max());
        $this->assertEquals(2, $enumerable->max($this->preClosure(function ($item) { return $item == 3 ? 1 : $item; })));
    }

    public function testMin()
    {
        $enumerable = $this->newInstance(array(3, 1, 2));
        $this->assertEquals(1, $enumerable->min());
        $this->assertEquals(2, $enumerable->min($this->preClosure(function ($item) { return $item == 1 ? 3 : $item; })));
    }

    public function testOrderBy()
    {
        $enumerable = $this->newInstance(array(1, 3, 2));
        $this->assertEquals(array(1, 2, 3), $enumerable->orderBy());
        $this->assertEquals(array(3, 1, 2), $enumerable->orderBy($this->preClosure(function ($item) { return $item == 3 ? 0 : $item; })));
    }

    public function testOrderByDescending()
    {
        $enumerable = $this->newInstance(array(1, 3, 2));
        $this->assertEquals(array(3, 2, 1), $enumerable->orderByDescending());
        $this->assertEquals(array(2, 1, 3), $enumerable->orderByDescending($this->preClosure(function ($item) { return $item == 3 ? 0 : $item; })));
    }

    public function testReverse()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(3, 2, 1), $enumerable->reverse());
    }

    public function testSelect()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(1, 4, 9), $enumerable->select($this->preClosure(function ($item) { return $item*$item;})));
    }

    public function testSkip()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(3), $enumerable->skip(2));
    }

    public function testSkipWhile()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(2, 3), $enumerable->skipWhile($this->preClosure(function ($item) { return $item < 2; })));
    }

    public function testSum()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(6, $enumerable->sum());
        $this->assertEquals(14, $enumerable->sum($this->preClosure(function ($item) { return $item*$item; })));
    }

    public function testTake()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(1), $enumerable->take(1));
    }

    public function testTakeWhile()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(1, 2), $enumerable->takeWhile($this->preClosure(function ($item) { return $item != 3; })));
    }

    public function testThenBy()
    {
        $enumerable = $this->newInstance(array(1, 2, 3, 4, 5));
        $this->assertEquals(array(2, 4, 1, 3, 5), $enumerable
                ->orderBy($this->preClosure(function ($item) { return $item%2; }))
                ->thenBy(),
            'Order with then should double order');
    }

    public function testThenByWithObject()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(1, 1);
        $obj3 = new Object(3, 1);

        $enumerable = $this->newInstance(array($obj1, $obj2, $obj3));
        $this->assertEquals(array($obj2, $obj1, $obj3), $enumerable
                ->orderBy($this->preClosure(function (Object $obj) { return $obj->a; }))
                ->thenBy($this->preClosure(function (Object $obj) { return $obj->b; })),
            'Order with then should double order');
    }

    public function testThenByDescending()
    {
        $enumerable = $this->newInstance(array(1, 2, 3, 4, 5));
        $this->assertEquals(array(4, 2, 5, 3, 1), $enumerable
                ->orderBy($this->preClosure(function ($item) { return $item%2; }))
                ->thenByDescending(),
            'Order with then should double order');
    }

    public function testThenByDescendingWithObject()
    {
        $obj1 = new Object(1, 2);
        $obj2 = new Object(1, 1);
        $obj3 = new Object(3, 1);

        $enumerable = $this->newInstance(array($obj1, $obj2, $obj3));
        $this->assertEquals(array($obj1, $obj2, $obj3), $enumerable
                ->orderBy($this->preClosure(function (Object $obj) { return $obj->a; }))
                ->thenByDescending($this->preClosure(function (Object $obj) { return $obj->b; })),
            'Order with then should double order');
    }

    public function testToArray()
    {
        $collection = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $collection);
    }

    public function testWhere()
    {
        $enumerable = $this->newInstance(array(1, 2, 3));
        $this->assertEquals(array(1, 3), $enumerable->where($this->preClosure(function ($item) { return $item != 2; })));
    }

    /**
     * Tests that the given function does not change the calling string
     *
     * @dataProvider functions
     * @param $function string The function to test
     */
    public function testUnchangedValue($function)
    {
        $enumerable = $this->newInstance(array(1, 2, 3));

        $result = \call_user_func_array(array($enumerable, $function), \array_slice(\func_get_args(), 1));

        $this->assertEquals(array(1, 2, 3), $enumerable);
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
        $enumerable = $this->newInstance();

        $result = \call_user_func_array(array($enumerable, $function), \array_slice(\func_get_args(), 1));

        $this->assertInstanceOf('\BCC\EnumerableUtility\IEnumerable', $result);
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
        return $this->preClosure(function () { return 1; });
    }
}
