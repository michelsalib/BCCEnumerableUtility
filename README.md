#Readme

[![Build Status](https://secure.travis-ci.org/michelsalib/BCCEnumerableUtility.png?branch=master)](http://travis-ci.org/michelsalib/BCCEnumerableUtility)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/michelsalib/BCCEnumerableUtility/badges/quality-score.png?s=423e5edf7fa4db0a299285498102c7bcadab87e3)](https://scrutinizer-ci.com/g/michelsalib/BCCEnumerableUtility/)

##Enumerable Utility

The enumerable utility library provides an easy way to manipulate collections. This library contains:

- A `Stringer` class that uses `Enumerable` and adds more wonderful methods
- A `Collection` class that helps you rapidly leverage the `Enumerable`
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
- average: Computes the average value of the elements
- contains: Tells if the element is contains in the enumerable
- count: Counts the number of elements
- distinct: Reduces the set of elements to the distinct ones
- each: Apply a closure to the set of element, you can safely get the items by reference
- elementAt: Gets the elements at a position
- first: Gets the first elements, a closure might be used
- groupBy: Groups the elements by Group
- join: Correlates the elements of two collections based on matching keys
- last: Gets the first elements, a closure might be used
- max: Gets the element with the maximum value
- min: Gets the element with the minimum value
- orderBy: Orders the elements, a v might be used
- orderByDescending: Orders the elements in a descending order
- reverse: Reverses the order of the elements
- select: Projects every elements
- selectMany: Project every elements and flattens the result
- skip: Skips a number of elements
- skipWhile: Skips elements while a closure is satisfied
- sum: Computes the sum of the elements
- take: Takes a number ok elements
- takeWhile: Takes elements while a closure is satisfied
- thenBy: Appends sub order
- thenByDescending: Appends sub descending order
- toDictionnary: Transform the enumerable to a dictionary
- where: Reduces the set of elements using a closure

Note that the functions returns an instance of the calling class when applicable.

### Use of functions

Sometimes, you just want to specify a single property and then a full closure:

``` php
<?php

$values->select(function($item) { return $item->address; });

```

Hopefully there is a function resolver mechanism which allows you to shorten your syntax.

You can simply give a property path to the field you want to select:

``` php
<?php

$values->select('address');

```

Note that string property supports also:
- chaining: `address.city`
- array transversing: `phoneNumbers[2]`
- auto discover getter/haser/isser

*Property path comes from the symfony/property-access library.*

You can also give an array that represents an expression:

``` php
<?php

// select a path
$values->select(['i' => 'i.address');

// select the square
$values->average(['i' => 'i * i');

// select a multiplication by a variable
$values->select([
    'i' => 'i * m',
    'm' => 2,
]);

```

*Expression comes from the symfony/expression-language library.*

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

## The `Stringer` class

The `Stringer` class is also an `Enumerable` (understand enumerable of char), but it also adds some usefull methods.

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

use BCC\EnumerableUtility\Stringer;

$string = new Stringer('Hello world!');

$string = $string->replace('world', 'pineapple')       // replace world by pineapple
       ->toUpper()                                     // to upper case
       ->skip(6)                                       // skip the 6 first letters
       ->takeWhile(function($char) { $char != '!'; }); // take the rest while the char is different from '!'

echo $string; // PINEAPPLE

```

Or use static methods:

``` php
<?php

use BCC\EnumerableUtility\Stringer;

$string = Stringer::create('Hello world!')
       ->replace('world', 'pineapple')                 // replace world by pineapple
       ->toUpper()                                     // to upper case
       ->skip(6)                                       // skip the 6 first letters
       ->takeWhile(function($char) { $char != '!'; }); // take the rest while the char is different from '!'

echo $string; // PINEAPPLE

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
