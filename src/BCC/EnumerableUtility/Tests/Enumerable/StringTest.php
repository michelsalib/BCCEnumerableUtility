<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\String;
use BCC\EnumerableUtility\Grouping;

include_once('EnumerableTestBase.php');

class StringTest extends EnumerableTestBase
{
    protected function newInstance($param = null)
    {
        return new String($param);
    }

    public function testConstructor()
    {
        $this->assertEquals('Hello', new String('Hello'));
        $this->assertEquals('Hello', new String(new String('Hello')));
        $this->assertEquals('Hello', new String(array('H', 'e', 'l', 'l', 'o')));
        $this->assertEquals('', new String());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorException()
    {
        new String(10);
    }

    public function testDistinctWithObjects()
    {
        return; // String does not support Objects
    }

    public function testGroupByWithObjects()
    {
        return; // String does not support Objects
    }

    public function testGroupByWithSubObjects()
    {
        return; // String does not support Objects
    }

    public function testJoinWithObjects()
    {
        return; // String does not support Objects
    }

    public function testThenBy()
    {
        $this->markTestSkipped('Must be ported');
    }

    public function testThenByDescending()
    {
        $this->markTestSkipped('Must be ported');
    }

    public function testContains()
    {
        $string = new String('Hello world!');
        $this->assertTrue($string->contains('world'));
        $this->assertFalse($string->contains('WORLD'));
        $this->assertTrue($string->contains('WORLD', true));
    }

    public function testStartsWith()
    {
        $string = new String('Hello world!');
        $this->assertTrue($string->startsWith('Hello'));
        $this->assertFalse($string->startsWith('HELLO'));
        $this->assertTrue($string->startsWith('HELLO', true));
    }

    public function testEndsWith()
    {
        $string = new String('Hello world!');
        $this->assertTrue($string->endsWith('world!'));
        $this->assertFalse($string->endsWith('WORLD!'));
        $this->assertTrue($string->endsWith('WORLD!', true));

        $string = new String('Hello to myself');
        $this->assertTrue($string->endsWith('myself'));
    }

    public function testEquals()
    {
        $string = new String('Hello world!');
        $this->assertTrue($string->equals('Hello world!'));
        $this->assertFalse($string->equals('HELLO WORLD!'));
        $this->assertTrue($string->equals('HELLO WORLD!', true));
    }

    public function testFormat()
    {
        $this->assertEquals('Hello world!', String::format('%s %s!', 'Hello', 'world'));
    }

    public function testIndexOf()
    {
        $string = new String('Hello world!');
        $this->assertEquals(0, $string->indexOf('Hello'));
        $this->assertEquals(6, $string->indexOf('world'));
        $this->assertEquals(-1, $string->indexOf('pineapple'));
        $this->assertEquals(-1, $string->indexOf('WORLD'));
        $this->assertEquals(6, $string->indexOf('WORLD', true));
    }

    public function testInsert()
    {
        $string = new String('Hello world!');
        $this->assertEquals('Hello beautiful world!', $string->insert(6, 'beautiful '));
    }

    public function testIsNullOrEmpty()
    {
        $this->assertTrue(String::isNullOrEmpty(''));
        $this->assertTrue(String::isNullOrEmpty(null));
        $this->assertFalse(String::isNullOrEmpty(' '));
    }

    public function testIsNullOrWhiteSpace()
    {
        $this->assertTrue(String::isNullOrWhiteSpace(''));
        $this->assertTrue(String::isNullOrWhiteSpace(null));
        $this->assertTrue(String::isNullOrWhiteSpace(' '));
    }

    public function testConcatenate()
    {
        $this->assertEquals('Hello world!', String::concatenate(' ', array('Hello', 'world!')));
    }

    public function testLastIndexOf()
    {
        $string = new String('Hello world!');
        $this->assertEquals(7, $string->lastIndexOf('o'));
        $this->assertEquals(6, $string->lastIndexOf('world'));
        $this->assertEquals(-1, $string->lastIndexOf('pineapple'));
        $this->assertEquals(-1, $string->lastIndexOf('O'));
        $this->assertEquals(7, $string->lastIndexOf('O', true));
    }

    public function testPadLeft()
    {
        $string = new String('Hello world!');
        $this->assertEquals('   Hello world!', $string->padLeft(15));
        $this->assertEquals('...Hello world!', $string->padLeft(15, '.'));
    }

    public function testPadRight()
    {
        $string = new String('Hello world!');
        $this->assertEquals('Hello world!   ', $string->padRight(15));
        $this->assertEquals('Hello world!...', $string->padRight(15, '.'));
    }

    public function testRemove()
    {
        $string = new String('Hello world!');
        $this->assertEquals('Hello', $string->remove(5));
        $this->assertEquals('Hello!', $string->remove(5, 6));
    }

    public function testReplace()
    {
        $string = new String('Hello world!');
        $this->assertEquals('Hello pineapple!', $string->replace('world', 'pineapple'));
    }

    public function testSplit()
    {
        $string = new String('Hello world!');
        $result = $string->split(' ');
        $this->assertCount(2, $result);
        $this->assertEquals('Hello', $result[0]);
        $this->assertEquals('world!', $result[1]);
    }

    public function testSubString()
    {
        $string = new String('Hello world!');
        $this->assertEquals('world!', $string->subString(6));
        $this->assertEquals('world', $string->subString(6, 5));
    }

    public function testToLower()
    {
        $string = new String('Hello world!');
        $this->assertEquals('hello world!', $string->toLower());
    }

    public function testToUpper()
    {
        $string = new String('Hello world!');
        $this->assertEquals('HELLO WORLD!', $string->toUpper());
    }

    public function testToCharArray()
    {
        $string = new String('Hello');
        $result = $string->toCharArray();
        $this->assertCount(5, $result);
        $this->assertEquals('H', $result[0]);
        $this->assertEquals('e', $result[1]);
        $this->assertEquals('l', $result[2]);
        $this->assertEquals('l', $result[3]);
        $this->assertEquals('o', $result[4]);
    }

    public function testTrim()
    {
        $string = new String(' Hello world! ');
        $this->assertEquals('Hello world!', $string->trim());


        $string = new String('.Hello world!-');
        $this->assertEquals('Hello world!', $string->trim('.-'));
    }

    public function testTrimEnd()
    {
        $string = new String(' Hello world! ');
        $this->assertEquals(' Hello world!', $string->trimEnd());


        $string = new String('.Hello world!-');
        $this->assertEquals('.Hello world!', $string->trimEnd('.-'));
    }

    public function testTrimStart()
    {
        $string = new String(' Hello world! ');
        $this->assertEquals('Hello world! ', $string->trimStart());


        $string = new String('.Hello world!-');
        $this->assertEquals('Hello world!-', $string->trimStart('.-'));
    }

    public function testToArray()
    {
        $string = new String('Hello');
        $this->assertEquals(array('H', 'e', 'l', 'l', 'o'), $string->toArray());

        $string = new String();
        $this->assertEquals(array(), $string->toArray());
    }

    /**
     * Tests that the given function does not change the calling string
     *
     * @dataProvider functions
     * @param $function string The function to test
     */
    public function testUnchangedString($function)
    {
        $string = new String('original');

        $result = \call_user_func_array(array($string, $function), \array_slice(\func_get_args(), 1));

        $this->assertEquals('original', $string);
        $this->assertNotSame($string, $result);
    }

    /**
     * Tests that the given function returns an instance of String
     *
     * @dataProvider stringReturnFunctions
     * @param $function string The function to test
     */
    public function testReturnString($function)
    {
        $string = new String('original');

        $result = \call_user_func_array(array($string, $function), \array_slice(\func_get_args(), 1));

        $this->assertInstanceOf('\BCC\EnumerableUtility\String', $result);
    }

    public function stringReturnFunctions()
    {
        return array(
            array('padLeft', 1),
            array('padRight', 1),
            array('remove', 10),
            array('replace', 'a', 'a'),
            array('subString', 1),
            array('toLower'),
            array('toUpper'),
            array('trim'),
            array('trimEnd'),
            array('trimStart'),
        );
    }

    public function functions()
    {
        return array(
            array('contains', 'a'),
            array('endsWith', 'a'),
            array('startsWith', 'a'),
            array('equals', 'a'),
            array('indexOf', 'a'),
            array('insert', 0, 'a'),
            array('lastIndexOf', 'a'),
            array('padLeft', 1),
            array('padRight', 1),
            array('remove', 10),
            array('replace', 'a', 'a'),
            array('split', 'a'),
            array('subString', 1),
            array('toCharArray'),
            array('toLower'),
            array('toUpper'),
            array('trim'),
            array('trimEnd'),
            array('trimStart'),
        );
    }

    private function func(){
        return function () { return 1; };
    }
}
