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

## Installation

Edit your `composer.json` to add the dependency:

```json
{
	"require": {
		"myclabs/array-comparator": "*"
	}
}
```
