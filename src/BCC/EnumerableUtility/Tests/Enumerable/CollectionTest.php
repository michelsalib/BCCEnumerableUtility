<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Collection;

include_once('EnumerableTestBase.php');

class CollectionTest extends EnumerableTestBase
{
    protected function newInstance($param = null)
    {
        return new Collection($param);
    }

    public function testConstructor()
    {
        $collection = new Collection(array(1, 2, 3));
        $this->assertEquals(array(1, 2, 3), $collection);
        $collection = new Collection(new Collection(array(1, 2, 3)));
        $this->assertEquals(array(1, 2, 3), $collection);
        $collection = new Collection();
        $this->assertEquals(array(), $collection);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructorException()
    {
        new Collection(10);
    }

    public function testAdd()
    {
        $collection = new Collection(array(1, 2));
        $collection->add(3);
        $this->assertEquals(array(1, 2, 3), $collection);
    }

    public function testAddRange()
    {
        $collection = new Collection(array(1, 2));
        $collection->addRange(array(3, 4));
        $this->assertEquals(array(1, 2, 3, 4), $collection);

        $collection = new Collection(array(1, 2));
        $collection->addRange(new Collection(array(3, 4)));
        $this->assertEquals(array(1, 2, 3, 4), $collection);
    }

    public function testClear()
    {
        $collection = new Collection(array(1, 2));
        $collection->clear();
        $this->assertEquals(array(), $collection);
    }

    public function testIndexOf()
    {
        $collection = new Collection(array(1, 2));
        $this->assertEquals(1, $collection->indexOf(2));
        $this->assertEquals(0, $collection->indexOf(1));
        $this->assertEquals(-1, $collection->indexOf(3));
    }

    public function testInsert()
    {
        $collection = new Collection(array(1, 3));
        $collection->insert(1, 2);
        $this->assertEquals(array(1, 2, 3), $collection);
    }

    public function testRemove()
    {
        $collection = new Collection(array(1, 3));
        $collection->remove(1);
        $this->assertEquals(array(3), $collection);
    }

    public function testRemoveAt()
    {
        $collection = new Collection(array(1, 3));
        $collection->removeAt(0);
        $this->assertEquals(array(3), $collection);
    }
}
