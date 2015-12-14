<?php

namespace BCC\EnumerableUtility;

use BCC\EnumerableUtility\Resolver\ExpressionResolver;
use BCC\EnumerableUtility\Resolver\NullResolver;
use BCC\EnumerableUtility\Resolver\PropertyPathResolver;
use BCC\EnumerableUtility\Resolver\ResolverInterface;
use InvalidArgumentException;
use LogicException;

abstract class Enumerable implements EnumerableInterface
{
    /**
     * @var array
     */
    protected $orderSequence   = [];

    /**
     * @var array
     */
    protected $orderAscending  = true;

    /**
     * @var array
     */
    protected $orderDescending = false;

    /**
     * @var ResolverInterface[]
     */
    protected static $resolvers = [];

    private static $initialized = false;

    public function __construct()
    {
        if (!self::$initialized) {
            self::resetResolvers();
            self::$initialized = true;
        }
    }

    public static function resetResolvers()
    {
        self::$resolvers = [
            new NullResolver(),
            new PropertyPathResolver(),
            new ExpressionResolver(),
        ];
    }

    /**
     * Append a resolver to the resolver chain
     *
     * @param ResolverInterface $resolver
     */
    public static function appendResolver(ResolverInterface $resolver)
    {
        self::$resolvers[] = $resolver;
    }

    /**
     * Prepend a resolver to the resolver chain
     *
     * @param ResolverInterface $resolver
     */
    public static function prependResolver(ResolverInterface $resolver)
    {
        array_unshift(self::$resolvers, $resolver);
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return (array) $this->getIterator();
    }

    /**
     * @inheritdoc
     */
    public function aggregate($func)
    {
        $result = null;
        foreach ($this as $item) {
            $result = $func($result, $item);
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function all($func)
    {
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            if (!$func($item)) {

                return false;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function any($func = null)
    {
        $func = $this->resolveFunction($func ?: function() { return true; });

        foreach ($this as $item) {
            if ($func($item)) {

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function average($func = null)
    {
        $func = $this->resolveFunction($func);

        $result = [];
        foreach ($this as $item) {
            $result[] = $func($item);
        }

        if (0 === count($result)) {
            throw new InvalidArgumentException('Enumerable has no element');
        }

        return array_sum($result) / count($result);
    }

    /**
     * @inheritdoc
     */
    public function contains($value)
    {
        foreach ($this as $item) {
            if ($value === $item) {

                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function count($func = null)
    {
        $func = $this->resolveFunction($func ?: function () { return true; });
        $result = 0;

        foreach ($this as $item) {
            if ($func($item)) {
                $result++;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function distinct($func = null)
    {
        $class = get_called_class();
        $distinct = [];
        $result = [];
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            $distinctKey = $func($item);
            if (!in_array($distinctKey, $distinct)) {
                $distinct[] = $distinctKey;
                $result[] = $item;
            }
        }

        return new $class($result);
    }

    /**
     * @inheritdoc
     */
    public function each($func)
    {
        $func = $this->resolveFunction($func);

        foreach ($this as $key => $item) {
            $func($item);
            $this[$key] = $item;
        }
    }

    /**
     * @inheritdoc
     */
    public function elementAt($index)
    {
        return $this[$index];
    }

    /**
     * @inheritdoc
     */
    public function first($func = null)
    {
        $func = $this->resolveFunction($func ?: function () { return true; });

        foreach ($this as $item) {
            if ($func($item)) {

                return $item;
            }
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function groupBy($func)
    {
        $compute = [];
        $result = [];
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            $group = $func($item);
            $key = is_object($group) ? spl_object_hash($group) : $group;
            if (!isset($compute[$key])) {
                $compute[$key] = array('group' => $group, 'items' => []);
            }
            $compute[$key]['items'][] = $item;
        }

        foreach ($compute as $item) {
            $result[] = new Grouping($item['group'], $item['items']);
        }

        return new Collection($result);
    }

    /**
     * @inheritdoc
     */
    public function join($innerItems, $outerSelector, $innerSelector, $resultFunc)
    {
        $result = [];

        $outerFunc = $this->resolveFunction($outerSelector);
        $innerFunc = $this->resolveFunction($innerSelector);

        foreach ($this as $outer) {
            $outerKey = $outerFunc($outer);
            foreach ($innerItems as $inner) {
                if ($outerKey == $innerFunc($inner)) {
                    $result[] = $resultFunc($outer, $inner);
                }
            }
        }

        return new Collection($result);
    }

    /**
     * @inheritdoc
     */
    public function last($func = null)
    {
        return $this->reverse()->first($func);
    }

    /**
     * @inheritdoc
     */
    public function max($func = null)
    {
        $result = null;
        $resultValue = null;
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            $newResultValue = $func($item);
            if ($newResultValue > $resultValue) {
                $resultValue = $newResultValue;
                $result = $item;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function min($func = null)
    {
        $result = null;
        $resultValue = PHP_INT_MAX;
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            $newResultValue = $func($item);
            if ($newResultValue < $resultValue) {
                $resultValue = $newResultValue;
                $result = $item;
            }
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function orderBy($func = null)
    {
        $func = $this->resolveFunction($func);

        return $this->order(array(array('order' => $this->orderAscending, 'func' => $func)));
    }

    /**
     * @inheritdoc
     */
    public function orderByDescending($func = null)
    {
        $func = $this->resolveFunction($func);

        return $this->order(array(array('order' => $this->orderDescending, 'func' => $func)));
    }

    /**
     * @inheritdoc
     */
    public function reverse()
    {
        $class = get_called_class();

        return new $class(array_reverse($this->toArray()));
    }

    /**
     * @inheritdoc
     */
    public function select($func)
    {
        $class = get_called_class();

        $func = $this->resolveFunction($func);

        return new $class(array_map($func, $this->toArray()));
    }

    /**
     * @inheritdoc
     */
    public function selectMany($func = null)
    {
        $class = get_called_class();
        $result = [];

        $func = $this->resolveFunction($func);

        foreach (array_map($func, $this->toArray()) as $subValue) {
            $result = array_merge($result, $subValue);
        }

        return new $class($result);
    }

    /**
     * @inheritdoc
     */
    public function skip($count)
    {
        $class = get_called_class();

        return new $class(array_slice($this->toArray(), $count));
    }

    /**
     * @inheritdoc
     */
    public function skipWhile($func)
    {
        $class = get_called_class();
        $result = [];
        $skipping = true;
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            if ($skipping && $func($item)) {
                continue;
            }
            else {
                $skipping = false;
            }
            $result[] = $item;
        }

        return new $class($result);
    }

    /**
     * @inheritdoc
     */
    public function sum($func = null)
    {
        $func = $this->resolveFunction($func);

        $result = 0;
        foreach ($this as $item) {
            $result += $func($item);
        }

        return $result;
    }


    /**
     * @inheritdoc
     */
    public function take($count)
    {
        $class = get_called_class();

        return new $class(array_slice($this->toArray(), 0, $count));
    }

    /**
     * @inheritdoc
     */
    public function takeWhile($func)
    {
        $class = get_called_class();
        $result = [];
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            if (!$func($item)) {
                break;
            }
            $result[] = $item;
        }

        return new $class($result);
    }

    /**
     * @inheritdoc
     */
    public function thenBy($func = null)
    {
        $func = $this->resolveFunction($func);
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderAscending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @inheritdoc
     */
    public function thenByDescending($func = null)
    {
        $func = $this->resolveFunction($func);
        $sequence = $this->orderSequence;
        $sequence[] = array('order' => $this->orderDescending, 'func' => $func);

        return $this->order($sequence);
    }

    /**
     * @inheritdoc
     */
    public function toDictionary($keySelector, $valueSelector = null)
    {
        $result = new Dictionary();
        $keyFunc = $this->resolveFunction($keySelector);
        $valueFunc = $this->resolveFunction($valueSelector);

        foreach ($this as $item) {
            $key = $keyFunc($item);

            if ($result->containsKey($key)) {
                throw new LogicException(sprintf('Key selection produces duplicated elements "%s".', $key));
            }

            $result->add($keyFunc($item), $valueFunc($item));
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function where($func)
    {
        $class = get_called_class();
        $result = [];
        $func = $this->resolveFunction($func);

        foreach ($this as $item) {
            if ($func($item)) {
                $result[] = $item;
            }
        }

        return new $class($result);
    }

    /**
     * @param array $sequence
     *
     * @return EnumerableInterface
     */
    protected function order(array $sequence)
    {
        $result = [];
        $class = get_called_class();

        foreach ($this as $item) {
            $result[] = $item;
        }

        usort($result, function ($a, $b) use ($sequence) {
            foreach ($sequence as $order) {
                $aValue = $order['func']($a);
                $bValue = $order['func']($b);
                if ($aValue > $bValue) {
                    return $order['order'];
                }
                elseif ($aValue < $bValue) {
                    return !$order['order'];
                }
            }

            return false;
        });

        $resultObject = new $class($result);
        $resultObject->orderSequence = $sequence;

        return $resultObject;
    }

    /**
     * @param mixed $function
     *
     * @return callable
     *
     * @throws LogicException
     */
    protected function resolveFunction($function)
    {
        // callable selector
        if (is_callable($function)) {
            return $function;
        }

        foreach (self::$resolvers as $resolver) {
            if (is_callable($result = $resolver->resolve($function))) {
                return $result;
            }
        }

        throw new LogicException(sprintf('Function "%s" cannot be resolved', $function));
    }
}
