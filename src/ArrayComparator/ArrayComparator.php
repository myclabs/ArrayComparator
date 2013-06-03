<?php

namespace ArrayComparator;

/**
 * Compares 2 arrays
 *
 * @author Matthieu Napoli <matthieu@mnapoli.fr>
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
    }

    /**
     * Run the comparison over the arrays
     */
    public function compare()
    {
        $whenDifferent = $this->whenDifferent;
        $whenMissingLeft = $this->whenMissingLeft;
        $whenMissingRight = $this->whenMissingRight;

        foreach ($this->array1 as $item1) {
            $item2 = $this->searchItem($item1, $this->array2);

            if ($item2 !== null) {
                // Items are different
                $whenDifferent($item1, $item2);
            } else {
                // Item from left array is missing from right array
                $whenMissingRight($item1);
            }
        }

        foreach ($this->array2 as $item2) {
            $item1 = $this->searchItem($item2, $this->array1);

            if ($item1 === null) {
                // Item from right array is missing from left array
                $whenMissingLeft($item2);
            }
        }
    }

    /**
     * Closure executed when items are different
     * @param callable $callback function($item1, $item2)
     * @return $this
     */
    public function whenDifferent(Closure $callback)
    {
        $this->whenDifferent = $callback;

        return $this;
    }

    /**
     * Closure executed when an item is missing from the left array (first array)
     * @param callable $callback function($item2)
     * @return $this
     */
    public function whenMissingLeft(Closure $callback)
    {
        $this->whenMissingLeft = $callback;

        return $this;
    }

    /**
     * Closure executed when an item is missing from the right array (second array)
     * @var callable $callback function($item1)
     * @return $this
     */
    public function whenMissingRight(Closure $callback)
    {
        $this->whenMissingRight = $callback;

        return $this;
    }

    private function searchItem($item, array $array)
    {
        foreach ($array as $arrayItem) {
            if ($this->areItemsEqual($item, $arrayItem)) {
                return $arrayItem;
            }
        }

        return null;
    }

    private function areItemsEqual($item1, $item2)
    {
        return ($item1 === $item2);
    }
}
