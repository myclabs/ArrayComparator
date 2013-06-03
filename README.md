# ArrayComparator

Array comparison helper.

## Usage

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
});

$comparator->compare();
```

## Documentation

* `setItemIdentityComparator`

```php
$comparator->setItemIdentityComparator(function ($item1, $item2) {
});
```

* `setItemComparator`

```php
$comparator->setItemComparator(function ($item1, $item2) {
});
```

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


## Installation

Edit your `composer.json` to add the dependency:

```json
{
	"require": {
		"myclabs/array-comparator": "*"
	}
}
```
