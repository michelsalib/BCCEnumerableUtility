<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Dictionary;

class DictionaryTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), $dictionary->toArray());
        $collection = new Dictionary(new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3)));
        $this->assertEquals(array('a' => 1, 'b' => 2, 'c' => 3), $collection->toArray());
        $collection = new Dictionary();
        $this->assertEquals(array(), $collection->toArray());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorException()
    {
        new Dictionary(10);
    }

    public function testKeys()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->assertEquals(array('a', 'b', 'c'), $dictionary->keys());
    }

    public function testValues()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->assertEquals(array(1, 2, 3), $dictionary->values());
    }

    public function testAdd()
    {
        $dictionary = new Dictionary(array('a' => 1, 'c' => 3));
        $dictionary->add('b', 2);
        $this->assertEquals(array('a' => 1, 'c' => 3, 'b' => 2), $dictionary->toArray());
    }

    public function testClear()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $dictionary->clear();
        $this->assertEquals(array(), $dictionary->toArray());
    }

    public function testContainsKey()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->assertEquals(true, $dictionary->containsKey('b'));
        $this->assertEquals(false, $dictionary->containsKey('d'));
    }

    public function testContainsValue()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->assertEquals(true, $dictionary->containsValue(2));
        $this->assertEquals(false, $dictionary->containsValue(4));
    }

    public function testRemove()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $dictionary->remove('b');
        $this->assertEquals(array('a' => 1, 'c' => 3), $dictionary->toArray());
    }

    public function testTryGetValue()
    {
        $value = null;
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));

        $this->assertEquals(false, $dictionary->tryGetValue('d', $value));
        $this->assertEquals(null, $value);

        $this->assertEquals(true, $dictionary->tryGetValue('b', $value));
        $this->assertEquals(2, $value);
    }
}
