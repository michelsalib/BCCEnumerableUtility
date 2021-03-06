<?php

namespace BCC\EnumerableUtilityUtility\Tests\Enumerable;

use BCC\EnumerableUtility\StringUtility;

class StringUtilityTest extends \PHPUnit_Framework_TestCase
{
    public function testNonStaticFallback()
    {
        $this->assertTrue(StringUtility::contains('Hello world!', 'H'));
    }

    public function testStaticFallback()
    {
        $this->assertEquals('Hello world!', StringUtility::concatenate(' ', array('Hello', 'world!')));
    }

    public function testNonStringReturn()
    {
        $this->assertNotInstanceOf('\BCC\EnumerableUtility\String', StringUtility::trim(' Hello world! '));
    }
}
