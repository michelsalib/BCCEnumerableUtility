<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Collection;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $collection = new Collection(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $collection->toArray());
        $collection = new Collection(new Collection(array(1, 2, 3)));
        $this->assertEquals(array(1, 2, 3), $collection->toArray());
        $collection = new Collection();
        $this->assertEquals(array(), $collection->toArray());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorException()
    {
        new Collection(10);
    }

    public function testToArray()
    {
        $collection = new Collection(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $collection->toArray());
    }
}
