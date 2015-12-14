<?php

namespace BCC\EnumerableUtility\Tests\Enumerable;

use BCC\EnumerableUtility\Collection;

class ResolverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider expressionFunctions
     */
    public function testResolversAreCalled($function)
    {
        $enumerable = new Collection([1, 2, 3]);

        $resolver = $this->getMock('\BCC\EnumerableUtility\Resolver\ResolverInterface', ['resolve']);
        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with('test')
            ->will($this->returnValue(function($item) { return $item; }));

        Collection::prependResolver($resolver);

         \call_user_func_array(array($enumerable, $function), ['test']);

        Collection::resetResolvers();
    }

    public function testResolversAreCalledOnSelectMany()
    {
        $enumerable = new Collection([[1, 2], [3, 4]]);

        $resolver = $this->getMockForAbstractClass('\BCC\EnumerableUtility\Resolver\ResolverInterface', ['resolve']);
        $resolver
            ->expects($this->once())
            ->method('resolve')
            ->with('test')
            ->will($this->returnValue(function($item) { return $item; }));

        Collection::prependResolver($resolver);

        $enumerable->selectMany('test');

        Collection::resetResolvers();
    }

    public function testResolversAreCalledOnJoin()
    {
        $enumerable = new Collection();

        $resolver = $this->getMockForAbstractClass('\BCC\EnumerableUtility\Resolver\ResolverInterface', ['resolve']);
        $resolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->with('test')
            ->will($this->returnValue(function($item) { return $item; }));

        Collection::prependResolver($resolver);

        $enumerable->join([], 'test', 'test', function() {});

        Collection::resetResolvers();
    }

    public function testResolversAreCalledOnToDictionary()
    {
        $enumerable = new Collection();

        $resolver = $this->getMockForAbstractClass('\BCC\EnumerableUtility\Resolver\ResolverInterface', ['resolve']);
        $resolver
            ->expects($this->exactly(2))
            ->method('resolve')
            ->with('test')
            ->will($this->returnValue(function($item) { return $item; }));

        Collection::prependResolver($resolver);

        $enumerable->toDictionary('test', 'test');

        Collection::resetResolvers();
    }

    public function expressionFunctions()
    {
        return [
            ['all'],
            ['any'],
            ['average'],
            ['count'],
            ['distinct'],
            ['each'],
            ['first'],
            ['groupBy'],
            ['last'],
            ['max'],
            ['min'],
            ['orderBy'],
            ['orderByDescending'],
            ['select'],
            ['skipWhile'],
            ['sum'],
            ['takeWhile'],
            ['thenBy'],
            ['thenByDescending'],
            ['where'],
        ];
    }
}
