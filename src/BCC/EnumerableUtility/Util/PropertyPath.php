<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Copied from https://github.com/symfony/symfony/blob/master/src/Symfony/Component/Form/Util/PropertyPath.php
 */

namespace BCC\EnumerableUtility\Util;

use Traversable;
use ReflectionClass;

/**
 * Allows easy traversing of a property path
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class PropertyPath
{
    /**
     * Character used for separating between plural and singular of an element.
     * @var string
     */
    const SINGULAR_SEPARATOR = '|';

    const VALUE = 0;
    const IS_REF = 1;

    /**
     * The elements of the property path
     * @var array
     */
    private $elements = array();

    /**
     * The number of elements in the property path
     * @var integer
     */
    private $length;

    /**
     * Contains a Boolean for each property in $elements denoting whether this
     * element is an index. It is a property otherwise.
     * @var array
     */
    private $isIndex = array();

    /**
     * Constructs a property path from a string.
     *
     * @param PropertyPath|string $propertyPath The property path as string or instance.
     *
     * @throws \LogicException      If the given path is not a string.
     * @throws \LogicException If the syntax of the property path is not valid.
     */
    public function __construct($propertyPath)
    {
        // Can be used as copy constructor
        if ($propertyPath instanceof PropertyPath) {
            /* @var PropertyPath $propertyPath */
            $this->elements = $propertyPath->elements;
            $this->length = $propertyPath->length;
            $this->isIndex = $propertyPath->isIndex;

            return;
        }
        if (!is_string($propertyPath)) {
            throw new \LogicException($propertyPath, 'string or BCC\EnumerableUtility\Util\PropertyPath');
        }

        if ('' === $propertyPath) {
            throw new \LogicException('The property path should not be empty.');
        }

        $position = 0;
        $remaining = $propertyPath;

        // first element is evaluated differently - no leading dot for properties
        $pattern = '/^(([^\.\[]+)|\[([^\]]+)\])(.*)/';

        while (preg_match($pattern, $remaining, $matches)) {

            if ('' !== $matches[2]) {
                $element = $matches[2];
                $this->isIndex[] = false;
            } else {
                $element = $matches[3];
                $this->isIndex[] = true;
            }

            $pos = strpos($element, self::SINGULAR_SEPARATOR);
            $singular = null;

            if (false !== $pos) {
                $element = substr($element, 0, $pos);
            }

            $this->elements[] = $element;

            $position += strlen($matches[1]);
            $remaining = $matches[4];
            $pattern = '/^(\.(\w+)|\[([^\]]+)\])(.*)/';
        }

        if (!empty($remaining)) {
            throw new \LogicException(sprintf(
                'Could not parse property path "%s". Unexpected token "%s" at position %d',
                $propertyPath,
                $remaining{0},
                $position
            ));
        }

        $this->length = count($this->elements);
    }

    /**
     * Returns the value at the end of the property path of the object
     *
     * Example:
     * <code>
     * $path = new PropertyPath('child.name');
     *
     * echo $path->getValue($object);
     * // equals echo $object->getChild()->getName();
     * </code>
     *
     * This method first tries to find a public getter for each property in the
     * path. The name of the getter must be the camel-cased property name
     * prefixed with "get", "is", or "has".
     *
     * If the getter does not exist, this method tries to find a public
     * property. The value of the property is then returned.
     *
     * If none of them are found, an exception is thrown.
     *
     * @param object|array $objectOrArray The object or array to traverse
     *
     * @return mixed The value at the end of the property path
     *
     * @throws \LogicException      If the property/getter does not exist
     * @throws \LogicException If the property/getter exists but is not public
     */
    public function getValue($objectOrArray)
    {
        $propertyValues =& $this->readPropertiesUntil($objectOrArray, $this->length - 1);

        return $propertyValues[count($propertyValues) - 1][self::VALUE];
    }

    /**
     * Reads the path from an object up to a given path index.
     *
     * @param object|array $objectOrArray The object or array to read from.
     * @param integer      $lastIndex     The integer up to which should be read.
     *
     * @return array The values read in the path.
     *
     * @throws \LogicException If a value within the path is neither object nor array.
     */
    private function &readPropertiesUntil(&$objectOrArray, $lastIndex)
    {
        $propertyValues = array();

        for ($i = 0; $i <= $lastIndex; ++$i) {
            if (!is_object($objectOrArray) && !is_array($objectOrArray)) {
                throw new \LogicException($objectOrArray, 'object or array');
            }

            $property = $this->elements[$i];
            $isIndex = $this->isIndex[$i];
            $isArrayAccess = is_array($objectOrArray) || $objectOrArray instanceof \ArrayAccess;

            // Create missing nested arrays on demand
            if ($isIndex && $isArrayAccess && !isset($objectOrArray[$property])) {
                $objectOrArray[$property] = $i + 1 < $this->length ? array() : null;
            }

            $propertyValue =& $this->readProperty($objectOrArray, $property, $isIndex);
            $objectOrArray =& $propertyValue[self::VALUE];

            $propertyValues[] =& $propertyValue;
        }

        return $propertyValues;
    }

    /**
     * Reads the a property from an object or array.
     *
     * @param object|array $objectOrArray The object or array to read from.
     * @param string       $property      The property to read.
     * @param Boolean      $isIndex       Whether to interpret the property as index.
     *
     * @return mixed The value of the read property
     *
     * @throws \LogicException      If the property does not exist.
     * @throws \LogicException If the property cannot be accessed due to
     *                                       access restrictions (private or protected).
     */
    private function &readProperty(&$objectOrArray, $property, $isIndex)
    {
        // Use an array instead of an object since performance is
        // very crucial here
        $result = array(
            self::VALUE => null,
            self::IS_REF => false
        );

        if ($isIndex) {
            if (!$objectOrArray instanceof \ArrayAccess && !is_array($objectOrArray)) {
                throw new \LogicException(sprintf('Index "%s" cannot be read from object of type "%s" because it doesn\'t implement \ArrayAccess', $property, get_class($objectOrArray)));
            }

            if (isset($objectOrArray[$property])) {
                if (is_array($objectOrArray)) {
                    $result[self::VALUE] =& $objectOrArray[$property];
                    $result[self::IS_REF] = true;
                } else {
                    $result[self::VALUE] = $objectOrArray[$property];
                }
            }
        } elseif (is_object($objectOrArray)) {
            $camelProp = $this->camelize($property);
            $reflClass = new ReflectionClass($objectOrArray);
            $getter = 'get'.$camelProp;
            $isser = 'is'.$camelProp;
            $hasser = 'has'.$camelProp;

            if ($reflClass->hasMethod($getter)) {
                if (!$reflClass->getMethod($getter)->isPublic()) {
                    throw new \LogicException(sprintf('Method "%s()" is not public in class "%s"', $getter, $reflClass->name));
                }

                $result[self::VALUE] = $objectOrArray->$getter();
            } elseif ($reflClass->hasMethod($isser)) {
                if (!$reflClass->getMethod($isser)->isPublic()) {
                    throw new \LogicException(sprintf('Method "%s()" is not public in class "%s"', $isser, $reflClass->name));
                }

                $result[self::VALUE] = $objectOrArray->$isser();
            } elseif ($reflClass->hasMethod($hasser)) {
                if (!$reflClass->getMethod($hasser)->isPublic()) {
                    throw new \LogicException(sprintf('Method "%s()" is not public in class "%s"', $hasser, $reflClass->name));
                }

                $result[self::VALUE] = $objectOrArray->$hasser();
            } elseif ($reflClass->hasMethod('__get')) {
                // needed to support magic method __get
                $result[self::VALUE] = $objectOrArray->$property;
            } elseif ($reflClass->hasProperty($property)) {
                if (!$reflClass->getProperty($property)->isPublic()) {
                    throw new \LogicException(sprintf('Property "%s" is not public in class "%s". Maybe you should create the method "%s()" or "%s()"?', $property, $reflClass->name, $getter, $isser));
                }

                $result[self::VALUE] =& $objectOrArray->$property;
                $result[self::IS_REF] = true;
            } elseif (property_exists($objectOrArray, $property)) {
                // needed to support \stdClass instances
                $result[self::VALUE] =& $objectOrArray->$property;
                $result[self::IS_REF] = true;
            } else {
                throw new \LogicException(sprintf('Neither property "%s" nor method "%s()" nor method "%s()" exists in class "%s"', $property, $getter, $isser, $reflClass->name));
            }
        } else {
            throw new \LogicException(sprintf('Cannot read property "%s" from an array. Maybe you should write the property path as "[%s]" instead?', $property, $property));
        }

        // Objects are always passed around by reference
        if (is_object($result[self::VALUE])) {
            $result[self::IS_REF] = true;
        }

        return $result;
    }

    /**
     * Camelizes a given string.
     *
     * @param  string $string Some string.
     *
     * @return string The camelized version of the string.
     */
    private function camelize($string)
    {
        return preg_replace_callback('/(^|_|\.)+(.)/', function ($match) { return ('.' === $match[1] ? '_' : '').strtoupper($match[2]); }, $string);
    }

    /**
     * Returns whether a method is public and has a specific number of required parameters.
     *
     * @param  \ReflectionClass $class      The class of the method.
     * @param  string           $methodName The method name.
     * @param  integer          $parameters The number of parameters.
     *
     * @return Boolean Whether the method is public and has $parameters
     *                                      required parameters.
     */
    private function isAccessible(ReflectionClass $class, $methodName, $parameters)
    {
        if ($class->hasMethod($methodName)) {
            $method = $class->getMethod($methodName);

            if ($method->isPublic() && $method->getNumberOfRequiredParameters() === $parameters) {
                return true;
            }
        }

        return false;
    }
}
