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
     * @var array
     */
    private $array1;

    /**
     * @var array
     */
    private $array2;

    /**
     * Closure comparing 2 items and returning if they have the same identity
     * @var callable function($item1, $item2) returns true or false
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
     * @param array $array1 Left array
     * @param array $array2 Right array
     */
    public function __construct(array $array1, array $array2)
    {
        $this->array1 = $array1;
        $this->array2 = $array2;

        // Default behaviors
        $this->itemIdentityComparator = function($item1, $item2) {
            return $item1 === $item2;
        };
        $this->itemComparator = function($item1, $item2) {
            return $item1 == $item2;
        };
    }

    /**
     * Run the comparison over the arrays
     */
    public function compare()
    {
        $compareItems = $this->itemComparator;
        $whenDifferent = $this->whenDifferent;
        $whenMissingLeft = $this->whenMissingLeft;
        $whenMissingRight = $this->whenMissingRight;

        foreach ($this->array1 as $item1) {
            $item2 = $this->searchItem($item1, $this->array2);

            if ($item2 !== null) {
                // Compare 2 items
                $itemsAreEqual = $compareItems($item1, $item2);
                if (!$itemsAreEqual) {
                    // Items are different
                    $whenDifferent($item1, $item2);
                }
            } elseif ($whenMissingRight) {
                // Item from left array is missing from right array
                $whenMissingRight($item1);
            }
        }

        foreach ($this->array2 as $item2) {
            $item1 = $this->searchItem($item2, $this->array1);

            if ($item1 === null && $whenMissingLeft) {
                // Item from right array is missing from left array
                $whenMissingLeft($item2);
            }
        }
    }

    /**
     * Closure comparing 2 items and returning if they have the same identity
     * @var callable $callback function($item1, $item2) returns true or false
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

    private function searchItem($item, array $array)
    {
        $areSameItem = $this->itemIdentityComparator;

        foreach ($array as $arrayItem) {
            if ($areSameItem($item, $arrayItem)) {
                return $arrayItem;
            }
        }

        return null;
    }
}
