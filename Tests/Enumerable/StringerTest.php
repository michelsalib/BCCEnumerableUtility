<?php

namespace BCC\EnumerableUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Stringer;
use InvalidArgumentException;

class StringerTest extends EnumerableTestBase
{
    public static function setUpBeforeClass()
    {
        mb_internal_encoding( 'UTF-8');
        mb_regex_encoding( 'UTF-8');
    }

    protected function newInstance($param = null)
    {
        return new Stringer($param);
    }

    public function testConstructor()
    {
        $this->assertEquals('Hello', new Stringer('Hello'));
        $this->assertEquals('Hello', new Stringer(new Stringer('Hello')));
        $this->assertEquals('Hello', new Stringer(array('H', 'e', 'l', 'l', 'o')));
        $this->assertEquals('', new Stringer());

        $this->assertEquals('Café', new Stringer('Café'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testConstructorException()
    {
        new Stringer(10);
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

    public function testSelectManyWithObject()
    {
        return; // String does not support Objects
    }

    public function testThenByWithObject()
    {
        return; // String does not support Objects
    }

    public function testThenByDescendingWithObject()
    {
        return; // String does not support Objects
    }

    public function testToDictionaryWithObject()
    {
        return; // String does not support Objects
    }

    public function testEachWithObject()
    {
        return; // String does not support Objects
    }

    public function testContains()
    {
        $string = new Stringer('Hello world!');
        $this->assertTrue($string->contains('world'));
        $this->assertFalse($string->contains('WORLD'));
        $this->assertTrue($string->contains('WORLD', true));

        $string = new Stringer('Café');
        $this->assertTrue($string->contains('Café'));
        $this->assertFalse($string->contains('café'));
        $this->assertTrue($string->contains('CAFé', true));
    }

    public function testCount()
    {
        $string = new Stringer('café');
        $this->assertEquals(4, $string->count());

        parent::testCount();
    }

    public function testStartsWith()
    {
        $string = new Stringer('Hello world!');
        $this->assertTrue($string->startsWith('Hello'));
        $this->assertFalse($string->startsWith('HELLO'));
        $this->assertTrue($string->startsWith('HELLO', true));

        $string = new Stringer('Café moulu');
        $this->assertTrue($string->startsWith('Café'));
        $this->assertFalse($string->startsWith('café'));
        $this->assertTrue($string->startsWith('CAFé', true));
    }

    public function testEndsWith()
    {
        $string = new Stringer('Hello world!');
        $this->assertTrue($string->endsWith('world!'));
        $this->assertFalse($string->endsWith('WORLD!'));
        $this->assertTrue($string->endsWith('WORLD!', true));

        $string = new Stringer('Hello to myself');
        $this->assertTrue($string->endsWith('myself'));

        $string = new Stringer('Thé et café');
        $this->assertTrue($string->endsWith('café'));
        $this->assertFalse($string->endsWith('Café'));
        $this->assertTrue($string->endsWith('CAFé', true));
    }

    public function testEquals()
    {
        $string = new Stringer('Hello world!');
        $this->assertTrue($string->equals('Hello world!'));
        $this->assertFalse($string->equals('HELLO WORLD!'));
        $this->assertTrue($string->equals('HELLO WORLD!', true));

        $string = new Stringer('Café');
        $this->assertTrue($string->equals('Café'));
        $this->assertFalse($string->equals('café'));
        $this->assertTrue($string->equals('CAFé', true));
    }

    public function testFormat()
    {
        $this->assertEquals('Hello world!', Stringer::format('%s %s!', 'Hello', 'world'));

        $this->assertEquals('Thé et café', Stringer::format('%s et %s', 'Thé', 'café'));
    }

    public function testIndexOf()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals(0, $string->indexOf('Hello'));
        $this->assertEquals(6, $string->indexOf('world'));
        $this->assertEquals(-1, $string->indexOf('pineapple'));
        $this->assertEquals(-1, $string->indexOf('WORLD'));
        $this->assertEquals(6, $string->indexOf('WORLD', true));

        $string = new Stringer('être ou ne pas être');
        $this->assertEquals(0, $string->indexOf('être'));
        $this->assertEquals(-1, $string->indexOf('ETRE'));
        $this->assertEquals(0, $string->indexOf('êTRE', true));
    }

    public function testInsert()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('Hello beautiful world!', $string->insert(6, 'beautiful '));

        $string = new Stringer('Thé sans sucre');
        $this->assertEquals('Thé et café sans sucre', $string->insert(5, 'et café '));
    }

    public function testIsNullOrEmpty()
    {
        $this->assertTrue(Stringer::isNullOrEmpty(''));
        $this->assertTrue(Stringer::isNullOrEmpty(null));
        $this->assertFalse(Stringer::isNullOrEmpty(' '));
    }

    public function testIsNullOrWhiteSpace()
    {
        $this->assertTrue(Stringer::isNullOrWhiteSpace(''));
        $this->assertTrue(Stringer::isNullOrWhiteSpace(null));
        $this->assertTrue(Stringer::isNullOrWhiteSpace(' '));
    }

    public function testConcatenate()
    {
        $this->assertEquals('Hello world!', Stringer::concatenate(' ', array('Hello', 'world!')));

        $this->assertEquals('Thé café', Stringer::concatenate(' ', array('Thé', 'café')));
    }

    public function testLastIndexOf()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals(7, $string->lastIndexOf('o'));
        $this->assertEquals(6, $string->lastIndexOf('world'));
        $this->assertEquals(-1, $string->lastIndexOf('pineapple'));
        $this->assertEquals(-1, $string->lastIndexOf('O'));
        $this->assertEquals(7, $string->lastIndexOf('O', true));

        $string = new Stringer('être ou ne pas être');
        $this->assertEquals(16, $string->lastIndexOf('être'));
        $this->assertEquals(-1, $string->lastIndexOf('ETRE'));
        $this->assertEquals(16, $string->lastIndexOf('êTRE', true));
    }

    public function testPadLeft()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('   Hello world!', $string->padLeft(15));
        $this->assertEquals('...Hello world!', $string->padLeft(15, '.'));

        $string = new Stringer('café');
        $this->assertEquals('   café', $string->padLeft(7));
    }

    public function testPadRight()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('Hello world!   ', $string->padRight(15));
        $this->assertEquals('Hello world!...', $string->padRight(15, '.'));

        $string = new Stringer('café');
        $this->assertEquals('café   ', $string->padRight(7));
    }

    public function testRemove()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('Hello', $string->remove(5));
        $this->assertEquals('Hello!', $string->remove(5, 6));

        $string = new Stringer('Thé et café');
        $this->assertEquals('Thé', $string->remove(3));
        $this->assertEquals('Thé café', $string->remove(3, 3));
    }

    public function testReplace()
    {
        $string = new Stringer('Hello world!');

        $this->assertEquals('Hello pineapple!', $string->replace('world', 'pineapple'));
        $this->assertEquals('Hello world!', $string->replace('World', 'pineapple'));
        $this->assertEquals('Hello pineapple!', $string->replace('World', 'pineapple', true));

        $string = new Stringer('Thé et chocolat');

        $this->assertEquals('Thé et café', $string->replace('chocolat', 'café'));
        $this->assertEquals('Thé et chocolat', $string->replace('thé', 'Café'));
        $this->assertEquals('Café et chocolat', $string->replace('thé', 'Café', true));
    }

    public function testSplit()
    {
        $string = new Stringer('Hello world!');
        $result = $string->split(' ');
        $this->assertCount(2, $result);
        $this->assertEquals('Hello', $result[0]);
        $this->assertEquals('world!', $result[1]);

        $string = new Stringer('Thé et café');
        $result = $string->split(' ');
        $this->assertCount(3, $result);
        $this->assertEquals('Thé', $result[0]);
        $this->assertEquals('café', $result[2]);
    }

    public function testSubString()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('world!', $string->subString(6));
        $this->assertEquals('world', $string->subString(6, 5));

        $string = new Stringer('Thé et café');
        $this->assertEquals('café', $string->subString(7));
        $this->assertEquals('Thé', $string->subString(0, 3));
    }

    public function testToLower()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('hello world!', $string->toLower());

        $string = new Stringer('Thé et Café');
        $this->assertEquals('thé et café', (string)$string->toLower());
    }

    public function testToUpper()
    {
        $string = new Stringer('Hello world!');
        $this->assertEquals('HELLO WORLD!', $string->toUpper());

        $string = new Stringer('Thé et Café');
        $this->assertEquals('THÉ ET CAFÉ', $string->toUpper());
    }

    public function testToCharArray()
    {
        $string = new Stringer('Hello');
        $result = $string->toCharArray();
        $this->assertCount(5, $result);
        $this->assertEquals('H', $result[0]);
        $this->assertEquals('e', $result[1]);
        $this->assertEquals('l', $result[2]);
        $this->assertEquals('l', $result[3]);
        $this->assertEquals('o', $result[4]);

        $string = new Stringer('Café');
        $result = $string->toCharArray();
        $this->assertCount(4, $result);
        $this->assertEquals('C', $result[0]);
        $this->assertEquals('a', $result[1]);
        $this->assertEquals('f', $result[2]);
        $this->assertEquals('é', $result[3]);
    }

    public function testTrim()
    {
        $string = new Stringer(' Hello world! ');
        $this->assertEquals('Hello world!', $string->trim());

        $string = new Stringer('.Hello world!-');
        $this->assertEquals('Hello world!', $string->trim('.-'));

        $string = new Stringer(' café ');
        $this->assertEquals('café', $string->trim());

        $string = new Stringer('.café-');
        $this->assertEquals('café', $string->trim('.-'));

        $string = new Stringer('.café-');
        $this->assertEquals('caf', $string->trim('.-é'));
    }

    public function testTrimEnd()
    {
        $string = new Stringer(' Hello world! ');
        $this->assertEquals(' Hello world!', $string->trimEnd());

        $string = new Stringer('.Hello world!-');
        $this->assertEquals('.Hello world!', $string->trimEnd('.-'));

        $string = new Stringer(' café ');
        $this->assertEquals(' café', $string->trimEnd());

        $string = new Stringer('.café-');
        $this->assertEquals('.café', $string->trimEnd('.-'));

        $string = new Stringer('.café-');
        $this->assertEquals('.caf', $string->trimEnd('.-é'));
    }

    public function testTrimStart()
    {
        $string = new Stringer(' Hello world! ');
        $this->assertEquals('Hello world! ', $string->trimStart());


        $string = new Stringer('.Hello world!-');
        $this->assertEquals('Hello world!-', $string->trimStart('.-'));

        $string = new Stringer(' café ');
        $this->assertEquals('café ', $string->trimStart());

        $string = new Stringer('.café-');
        $this->assertEquals('café-', $string->trimStart('.-'));
    }

    public function testToArray()
    {
        $string = new Stringer('Hello');
        $this->assertEquals(array('H', 'e', 'l', 'l', 'o'), $string->toArray());

        $string = new Stringer('café');
        $this->assertEquals(array('c', 'a', 'f', 'é'), $string->toArray());

        $string = new Stringer();
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
        $string = new Stringer('original');

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
        $string = new Stringer('original');

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

    protected function func(){
        return function () { return 1; };
    }
}
