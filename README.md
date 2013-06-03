# ArrayComparator

Array comparison helper.

## Usage

```php
$comparator = new ArrayComparator($array1, $array2);

$comparator->whenDifferent(function ($item1, $item2) {
    // Do your stuff !
})
->whenMissingRight(function ($item1) {
    // Do your stuff !
})
->whenMissingLeft(function ($item2) {
    // Do your stuff !
});

$comparator->compare();
```

Advanced example for Doctrine entities for example:

```php
$comparator = new ArrayComparator($array1, $array2);

// Set that items are considered the same if they have the same id
$comparator->setItemIdentityComparator(function ($item1, $item2) {
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

$comparator->compare();
```

## Documentation

* `whenDifferent` - Called when 2 items are found in both arrays, but are differents

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

* `setItemIdentityComparator` - Overrides the default identity comparator which determine if 2 items should be compared

Can be used for example to compare the `id` of the items.

**The default behavior compares the array keys using `===`.**

```php
$comparator->setItemIdentityComparator(function ($key1, $key2, $item1, $item2) {
    // return true or false
});
```

* `setItemComparator` - Overrides the default item comparator to determine if 2 items have differences

Can be used for example to compare specific attributes of the items. If the function returns true, the `whenDifferent`
callback will be called. If the function returns false, either `whenMissingRight` or `whenMissingLeft` will be called.

**The default behavior compares the items using `==`.**

```php
$comparator->setItemComparator(function ($item1, $item2) {
    // return true or false
});
```


## Installation

Edit your `composer.json` to add the dependency:

```json
{
	"require": {
		"myclabs/array-comparator": "*"
	}
}
```

## Contribute

Install dependencies with Composer:

    composer install

TODO:

* Optimize the array traversals
* Improve documentation ?
