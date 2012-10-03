<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use Closure;
use BCC\EnumerableUtility\Dictionary;
use BCC\EnumerableUtility\Grouping;
use BCC\EnumerableUtility\KeyValuePair;

include_once('EnumerableTestBase.php');

class DictionaryTest extends EnumerableTestBase
{
    protected function newInstance($param = null)
    {
        return new Dictionary($param);
    }

    protected function preClosure(Closure $func)
    {
        return function() use ($func) {
            $args = \func_get_args();
            foreach ($args as $key => $arg) {
                if ($arg instanceof KeyValuePair) {
                    $args[$key] = $arg->getValue();
                }
            }

            return \call_user_func_array($func, $args);
        };
    }

    public static function assertEquals($expected, $actual, $message = '', $delta = 0, $maxDepth = 10, $canonicalize = FALSE, $ignoreCase = FALSE)
    {
        /** @var $actual Dictionary */
        if (\is_array($expected) && $actual instanceof Dictionary) {
            $actual = $actual->values();
        }

        if ($actual instanceof KeyValuePair) {
            $actual = $actual->getValue();
        }

        if (\is_array($expected) && isset($actual[0]) && $actual[0] instanceof Grouping) {
            foreach ($actual as $group) {
                $values = $group->toArray();
                $group->clear();
                $group->addRange(\array_map(function (KeyValuePair $item) { return $item->getValue(); }, $values));
            }
        }

        if ($expected instanceof Grouping) {
            $values = $actual->toArray();
            $actual->clear();
            $actual->addRange(\array_map(function (KeyValuePair $item) { return $item->getValue(); }, $values));
        }

        parent::assertEquals($expected, $actual, $message, $delta, $maxDepth, $canonicalize, $ignoreCase);
    }

    public function testConstructor()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $this->assertEquals(array('a', 'b', 'c'), $dictionary->keys());
        $this->assertEquals(array(1, 2, 3), $dictionary->values());

        $dictionary = new Dictionary(new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3)));
        $this->assertEquals(array('a', 'b', 'c'), $dictionary->keys());
        $this->assertEquals(array(1, 2, 3), $dictionary->values());

        $dictionary = new Dictionary();
        $this->assertEquals(array(), $dictionary);
    }

    /**
     * @expectedException \InvalidArgumentException
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
        $this->assertEquals(array('a', 'c', 'b'), $dictionary->keys());
        $this->assertEquals(array(1, 3, 2), $dictionary->values());
    }

    public function testClear()
    {
        $dictionary = new Dictionary(array('a' => 1, 'b' => 2, 'c' => 3));
        $dictionary->clear();
        $this->assertEquals(array(), $dictionary);
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
        $this->assertEquals(array('a', 'c'), $dictionary->keys());
        $this->assertEquals(array(1, 3), $dictionary->values());
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
