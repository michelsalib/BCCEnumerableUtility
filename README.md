#Readme

[![Build Status](https://secure.travis-ci.org/michelsalib/BCCEnumerableUtility.png?branch=master)](http://travis-ci.org/michelsalib/BCCEnumerableUtility)

##Enumerable Utility

The enumerable utility library provides an easy way to manipulate enumerables. This library contains:

- An `Enumerable` trait with many usefull methods
- A `String` class that uses `Enumerable` and adds more wonderful methods
- A `Collection` class that helps you rapidly leverage the `Enumerable` trait
- A `Dictionary` class that is an indexed `Collection`
- A `StringUtility` class if you don't like the idea of a `String` class

It takes inspiration from the Linq Enumerable class extension of the .NET framework.

##Installation

You will need to run under PHP 5.4.

You can simply download the source or use composer to get the package.

## A quick overview

Most of the time you will just use the `Collection` class to address most of your use cases:

``` php
<?php

use BCC\EnumerableUtility\Collection;

$values = new Collection(array(1, 2, 3));

// gets event/odd values
$values->where(function($item) { return $item % 2; });

// project the squared values
$values->select(function($item) { return $item*$item; });

// order
$values->orderByDescending();

// paginate
$values->skip(30)->take(10);

```

## `Enumerable` functions

Here is the list of the current implemented functions:

- aggregate: Applies an accumulator function over the elements
- all: Validates a closure against every elements, true if all matches
- any: Validates a closure against every elements, true if at least one matches
- average: Computes the average value of the elements, a selector might be used
- contains: Tells if the element is contains in the enumerable
- count: Counts the number of elements
- distinct: Reduces the set of elements to the distinct ones, a selector might be used
- each: Apply a closure to the set of element, you can safely get the items by reference
- elementAt: Gets the elements at a position
- first: Gets the first elements, a closure might be used
- groupBy: Groups the elements by Group, a selector that selects the group is needed
- join: Correlates the elements of two collections based on matching keys, selectors are needed for keys and a closure for result selection
- last: Gets the first elements, a closure might be used
- max: Gets the element with the maximum value, a selector might be used
- min: Gets the element with the minimum value, a selector might be used
- orderBy: Orders the elements, a v might be used
- orderByDescending: Orders the elements in a descending order, a selector might be used
- reverse: Reverses the order of the elements
- select: Projects every elements using a selector
- selectMany: Project every elements using a selector and flattens the result
- skip: Skips a number of elements
- skipWhile: Skips elements while a closure is satisfied
- sum: Computes the sum of the elements, a selector might be used
- take: Takes a number ok elements
- takeWhile: Takes elements while a closure is satisfied
- thenBy: Appends sub order, a selector might be used
- thenByDescending: Appends sub descending order, a selector might be used
- toDictionnary: Transform the enumerable to a dictionary, a key selector is required and a value selector might be used
- where: Reduces the set of elements using a closure

Note that the functions returns an instance of the calling class when applicable.

### Use of selectors

Sometimes, you just want to specify a single property and then a full closure:

``` php
<?php

$values->select(function($item) { return $item->address; });

```

Hopefully where the documentation says selector as argument name, you can simply give a string that tells the field you want to select:

``` php
<?php

$values->select('address');

```

Note that string selectors supports also:
- chaining: `address.city`
- array transversing: `phoneNumbers[2]`
- auto discover getter/haser/isser

*This is heavily based on the work done on the Symfony2 framework in the form component.*

## The `Collection` class

Behind being an enumerable, `Collection` has some useful functions:

- add: adds an item to the collection
- addRange: adds a collection of items to the collection
- clear: empty the collection
- indexOf: gets the element of an item
- insert: inserts an element at the given index
- remove: removes and item
- removeAt: removes and element at an index

## The `Dictionary` class

Behind being an enumerable, `Dictionary` has some useful functions:

- keys: gets the keys
- values: gets the values
- add: adds an item with the given key
- clear: empty the dictionary
- containsKey: validates if the key is used
- containsValue: validates if the value is contained
- remove: removes the item at the given key
- tryGetValue: tries to get the value at the given key

## The `String` class

The `String` class is also an `Enumerable` (understand enumerable of char), but it also adds some usefull methods.

These are inspired from the String class of the .NET framework:

- contains: test against a given string, can be insensitive
- endsWith: test against a given string, can be insensitive
- startsWith: test against a given string, can be insensitive
- equals: test against a given string, can be insensitive
- *static* format: maps to sprintf
- indexOf: gets the index of a given string, can be insensitive
- insert: inserts another string at the given position
- *static* isNullOrEmpty: tests if the given string is null or empty
- *static* isNullOrWhiteSpace: tests if the given string is null or whitespace
- *static* concatenate: Concats an array of strings using the given separator
- lastIndexOf: gets the last index of a given string, can be insensitive
- padLeft: pads the left of the string to match a given length, the padding char can be defined
- padRight: pads the right of the string to match a given length, the padding char can be defined
- remove: removes a portion of the string
- replace: replaces a given sequence in the string
- split: splits the string using a given separator
- subString: extracts a portion of the string
- toLower: lowers the string
- toUpper: uppers the string
- toCharArray: transforms to an array of char
- trim: trims the string, trim characters can be defined
- trimEnd: trims the end of the string, trim characters can be defined
- trimStart: trims the start of the string, trim characters can be defined

You can now do:

``` php
<?php

use BCC\EnumerableUtility\String;

$string = new String('Hello world!');

$string = $string->replace('world', 'pineapple')       // replace world by pineapple
       ->toUpper()                                     // to upper case
       ->skip(6)                                       // skip the 6 first letters
       ->takeWhile(function($char) { $char != '!'; }); // take the rest while the char is different from '!'

echo $string; // PINEAPPLE

```

##Use the `Enumerable` trait

First, implement the `IEnumerable` interface. A simple reference to the trait will auto implement the interface. You will just be forced to add some additional methods that comes from the `IteratorAggregate` and `ArrayAccess` interfaces.
Let's take as an example a Basket which is an enumerable of Product:

``` php
<?php

namespace \My;

use BCC\EnumerableUtility\Enumerable;
use BCC\EnumerableUtility\IEnumerable;

class Basket implements IEnumerable {

    use Enumerable;

    private $products;

    function __construct(array $products)
    {
        $this->products = $products;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->products);
    }

    public function offsetExists($offset)
    {
        return isset($this->products[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->products[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->products[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->products[$offset]);
    }
}

```

You are good to go, and you can now write some fancy code:

``` php
<?php

$basket = new Basket(...);

// all the products with a value over 10
$basket->where(function($product) { return $product->getValue() > 10; });

// order by name
$basket->orderBy(function($product) { return $product->getName(); });

// has any product with a value under 50
$basket->any(function($product) { return $product->getValue() < 50; });

// first element with a value of 30
$basket->first(function($product) { return $product->getValue() == 30; });

```

## The `StringUtility` class

If you don't like to use a `String` class, you can ave access to all the power using the `StringUtility` class.
All it does is internally mapping static calls to the `String` class:

``` php
<?php

use \BCC\EnumerableUtility\StringUtility;

$string = 'Hello world!';

$string = StringUtility::replace  ($string, 'world', 'pineapple');              // replace world by pineapple
$string = StringUtility::toUpper  ($string);                                    // to upper case
$string = StringUtility::skip     ($string, 6);                                 // skip the 6 first letters
$string = StringUtility::takeWhile($string, function($char) { $char != '!'; }); // take the rest while the char is different from '!'

echo $string; // PINEAPPLE

```
