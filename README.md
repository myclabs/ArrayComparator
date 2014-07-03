# ArrayComparator

Array comparison helper.

[![Latest Stable Version](https://poser.pugx.org/myclabs/array-comparator/v/stable.png)](https://packagist.org/packages/myclabs/array-comparator)
[![Build Status](https://travis-ci.org/myclabs/ArrayComparator.png)](https://travis-ci.org/myclabs/ArrayComparator)
[![Coverage Status](https://coveralls.io/repos/myclabs/ArrayComparator/badge.png?branch=master)](https://coveralls.io/r/myclabs/ArrayComparator?branch=master)

## Principle

Here is the default behavior:

Array 1    | Array 2    | Method called
-----------|------------|--------------
foo => Foo | foo => Foo |
bar => Bar | bar => Foo | whenDifferent
baz => Baz |            | whenMissingRight
           | bam => Bam | whenMissingLeft

By default, array keys are compared. This behavior can be customized.

## Usage

```php
$comparator = new ArrayComparator();

$comparator->whenEqual(function ($item1, $item2) {
    // Do your stuff !
})
->whenDifferent(function ($item1, $item2) {
    // Do your stuff !
})
->whenMissingRight(function ($item1) {
    // Do your stuff !
})
->whenMissingLeft(function ($item2) {
    // Do your stuff !
});

$comparator->compare($array1, $array2);
```

Advanced example for Doctrine entities for example:

```php
$comparator = new ArrayComparator();

// Set that items are considered the same if they have the same id
// Array keys are ignored in this example
$comparator->setItemIdentityComparator(function ($key1, $key2, $item1, $item2) {
    return $item1->id === $item2->id;
});

// Items have differences if their name differ
$comparator->setItemComparator(function ($item1, $item2) {
    return $item1->name === $item2->name;
});

$comparator->whenDifferent(function ($item1, $item2) {
    // Do your stuff !
})
->whenMissingRight(function ($item1) {
    // Do your stuff !
})
->whenMissingLeft(function ($item2) {
    // Do your stuff !
});

$comparator->compare($array1, $array2);
```

Note that you can also use any PHP callable format instead of inline functions, for example:

```php
$comparator->whenDifferent(array($this, 'whenDifferent'));
```

## Documentation

* `whenEqual` - Called when 2 items are found in both arrays, and are equal

```php
$comparator->whenEqual(function ($item1, $item2) {
});
```

* `whenDifferent` - Called when 2 items are found in both arrays, but are different

```php
$comparator->whenDifferent(function ($item1, $item2) {
});
```

* `whenMissingRight` - Called when an item is in the first array (left array) but not in the second (right array)

```php
$comparator->whenMissingRight(function ($item1) {
});
```

* `whenMissingLeft` - Called when an item is in the second array (right array) but not in the first (left array)

```php
$comparator->whenMissingLeft(function ($item2) {
});
```

* `setItemIdentityComparator` - Overrides the default identity comparator which determine if 2 items represent the same thing

Can be used for example to compare the `id` of the items.

**The default behavior compares the array keys using `===`.**

```php
$comparator->setItemIdentityComparator(function ($key1, $key2, $item1, $item2) {
    // return true or false
});
```

* `setItemComparator` - Overrides the default item comparator to determine if 2 items (representing the same thing) have differences

Can be used for example to compare specific attributes of the items. The function should return "is equal", i.e. `true` if items have no differences (then nothing is done because all is good), or `false` if they have differences (then `whenDifferent` is called).

**The default behavior compares the items using `==`.**

```php
$comparator->setItemComparator(function ($item1, $item2) {
    // return true or false
});
```

## Custom comparator

There is an alternative to using `setItemIdentityComparator` and `setItemComparator` by writing your own comparator class:

```php
class CustomComparator extends ArrayComparator
{
    protected function areSame($key1, $key2, $item1, $item2)
    {
        // Your stuff
        return $item1->id === $item2->id;
    }

    protected function areEqual($item1, $item2)
    {
        // Your stuff
        return $item1->name === $item2->name;
    }
}
```

## Installation

Edit your `composer.json` to add the dependency:

```json
{
	"require": {
		"myclabs/array-comparator": "1.0.*"
	}
}
```

ArrayComparator is tested with PHP 5.3 and greater.

## Changelog

- 1.0: Stable version after testing and use in production (no change from 0.3)
- 0.3: PHP 5.3 compatibility and support all PHP callable types
- 0.2: Allowed to extend the `ArrayComparator` class and write custom comparators
- 0.1: First version

## Contribute

Install dependencies with Composer:

    composer install

TODO:

- Optimize the array traversals
- Improve documentation ?
