<?php
/**
 * @author  Matthieu Napoli <matthieu@mnapoli.fr>
 * @license http://www.opensource.org/licenses/mit-license.php MIT (see the LICENSE file)
 */

namespace ArrayComparator;

/**
 * Compares 2 arrays
 */
class ArrayComparator
{
    /**
     * Closure comparing 2 items and returning if they have the same identity
     * @var callable function($key1, $key2, $item1, $item2) returns true or false
     */
    private $itemIdentityComparator;

    /**
     * Closure comparing 2 items and returning if there are differences
     * @var callable function($item1, $item2) returns true or false
     */
    private $itemComparator;

    /**
     * Closure executed when items are different
     * @var callable function($item1, $item2)
     */
    private $whenDifferent;

    /**
     * Closure executed when an item is missing from the left array (first array)
     * @var callable function($item2)
     */
    private $whenMissingLeft;

    /**
     * Closure executed when an item is missing from the right array (second array)
     * @var callable function($item1)
     */
    private $whenMissingRight;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Default behaviors
        $this->itemIdentityComparator = array($this, 'areSame');
        $this->itemComparator = array($this, 'areEqual');
    }

    /**
     * Run the comparison over the arrays
     */
    public function compare(array $array1, array $array2)
    {
        $compareItems = $this->itemComparator;
        $whenDifferent = $this->whenDifferent;
        $whenMissingLeft = $this->whenMissingLeft;
        $whenMissingRight = $this->whenMissingRight;

        foreach ($array1 as $key1 => $item1) {
            $item2 = $this->searchItem($key1, $item1, $array2);

            if ($item2 !== null) {
                // Compare 2 items
                $itemsAreEqual = call_user_func($compareItems, $item1, $item2);
                if (!$itemsAreEqual) {
                    // Items are different
                    call_user_func($whenDifferent, $item1, $item2);
                }
            } elseif ($whenMissingRight) {
                // Item from left array is missing from right array
                call_user_func($whenMissingRight, $item1);
            }
        }

        foreach ($array2 as $key2 => $item2) {
            $item1 = $this->searchItem($key2, $item2, $array1);

            if ($item1 === null && $whenMissingLeft) {
                // Item from right array is missing from left array
                call_user_func($whenMissingLeft, $item2);
            }
        }
    }

    /**
     * Closure comparing 2 items and returning if they have the same identity
     * @var callable $callback function($key1, $key2, $item1, $item2) returns true or false
     * @return $this
     */
    public function setItemIdentityComparator($callback)
    {
        $this->itemIdentityComparator = $callback;

        return $this;
    }

    /**
     * Closure comparing 2 items and returning if there are differences
     * @var callable $callback function($item1, $item2) returning true or false
     * @return $this
     */
    public function setItemComparator($callback)
    {
        $this->itemComparator = $callback;

        return $this;
    }

    /**
     * Closure executed when items are different
     * @param callable $callback function($item1, $item2)
     * @return $this
     */
    public function whenDifferent($callback)
    {
        $this->whenDifferent = $callback;

        return $this;
    }

    /**
     * Closure executed when an item is missing from the left array (first array)
     * @param callable $callback function($item2)
     * @return $this
     */
    public function whenMissingLeft($callback)
    {
        $this->whenMissingLeft = $callback;

        return $this;
    }

    /**
     * Closure executed when an item is missing from the right array (second array)
     * @var callable $callback function($item1)
     * @return $this
     */
    public function whenMissingRight($callback)
    {
        $this->whenMissingRight = $callback;

        return $this;
    }

    /**
     * Compares 2 items and returns if they have the same identity (if they represent the same item)
     * @param mixed $key1 Array key of the first item
     * @param mixed $key2 Array key of the second item
     * @param mixed $item1
     * @param mixed $item2
     * @return boolean
     */
    protected function areSame($key1, $key2, $item1, $item2)
    {
        // Default behavior
        return $key1 === $key2;
    }

    /**
     * Compares 2 items and returns if there are differences
     * @param mixed $item1
     * @param mixed $item2
     * @return boolean
     */
    protected function areEqual($item1, $item2)
    {
        // Default behavior
        return $item1 == $item2;
    }

    private function searchItem($key1, $item1, array $array)
    {
        $areSameItem = $this->itemIdentityComparator;

        foreach ($array as $key2 => $item2) {
            if (call_user_func($areSameItem, $key1, $key2, $item1, $item2)) {
                return $item2;
            }
        }

        return null;
    }
}
