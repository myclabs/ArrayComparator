<?php

namespace ArrayComparator;

require_once __DIR__ . '/../../vendor/autoload.php';

class ArrayComparatorTest extends \PHPUnit_Framework_TestCase
{

    public function testNoError()
    {
        $comparator = new ArrayComparator(array(), array());
        $comparator->compare();
    }

    /**
     * Test that no comparison function is called with empty arrays
     */
    public function testCompareEmptyArrays()
    {
        $comparator = new ArrayComparator(array(), array());

        $comparator->whenDifferent(
            function () {
                throw new \Exception();
            }
        )
        ->whenMissingLeft(
            function () {
                throw new \Exception();
            }
        )
        ->whenMissingRight(
            function () {
                throw new \Exception();
            }
        )
        ->compare();
    }

    /**
     * Test when an item is missing from the right array
     */
    public function testWhenMissingRight()
    {
        $comparator = new ArrayComparator(array('foo'), array());

        $comparator->whenDifferent(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenMissingLeft(
            function () {
                throw new \Exception();
            }
        );

        $callCount = 0;
        $testCase = $this;
        $comparator->whenMissingRight(
            function ($item1) use (&$callCount, $testCase) {
                $testCase->assertEquals('foo', $item1);
                $callCount++;
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCount);
    }

    /**
     * Test when an item is missing from the left array
     */
    public function testWhenMissingLeft()
    {
        $comparator = new ArrayComparator(array(), array('foo'));

        $comparator->whenDifferent(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenMissingRight(
            function () {
                throw new \Exception();
            }
        );

        $callCount = 0;
        $testCase = $this;
        $comparator->whenMissingLeft(
            function ($item2) use (&$callCount, $testCase) {
                $testCase->assertEquals('foo', $item2);
                $callCount++;
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCount);
    }

    /**
     * Test when the same item is in both arrays, but has differences
     */
    public function testWhenDifferences()
    {
        $comparator = new ArrayComparator(array('foo'), array('bar'));

        $comparator->whenMissingRight(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenMissingLeft(
            function () {
                throw new \Exception();
            }
        );

        $callCount = 0;
        $testCase = $this;
        $comparator->whenDifferent(
            function ($item1, $item2) use (&$callCount, $testCase) {
                $testCase->assertEquals('foo', $item1);
                $testCase->assertEquals('bar', $item2);
                $callCount++;
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCount);
    }

    /**
     * Test when the same item is in both arrays, but has differences
     */
    public function testWhenDifferencesWithIndexedArray()
    {
        $comparator = new ArrayComparator(array('foo' => 'bar'), array('foo' => 'baz'));

        $comparator->whenMissingRight(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenMissingLeft(
            function () {
                throw new \Exception();
            }
        );

        $callCount = 0;
        $testCase = $this;
        $comparator->whenDifferent(
            function ($item1, $item2) use (&$callCount, $testCase) {
                $testCase->assertEquals('bar', $item1);
                $testCase->assertEquals('baz', $item2);
                $callCount++;
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCount);
    }

    /**
     * Test when the same item is in both arrays, but has differences
     */
    public function testWhenDifferencesWithCustomComparators()
    {
        $object1 = new \stdClass();
        $object1->id = 1;
        $object1->name = 'foo';

        $object2 = new \stdClass();
        $object2->id = 1;
        $object2->name = 'bar';

        $comparator = new ArrayComparator(array($object1), array($object2));

        $comparator->setItemIdentityComparator(
            function ($key1, $key2, $item1, $item2) {
                return $item1->id === $item2->id;
            }
        );

        // Compares the names of the objects
        $comparator->setItemComparator(
            function ($item1, $item2) {
                return $item1->name === $item2->name;
            }
        );

        $comparator->whenMissingRight(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenMissingLeft(
            function () {
                throw new \Exception();
            }
        );

        $callCount = 0;
        $testCase = $this;
        $comparator->whenDifferent(
            function ($item1, $item2) use (&$callCount, $testCase) {
                $testCase->assertEquals('foo', $item1->name);
                $testCase->assertEquals('bar', $item2->name);
                $callCount++;
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCount);
    }

    /**
     * Test when the same item is in both arrays and has no differences
     */
    public function testWhenNoDifferences()
    {
        $object1 = new \stdClass();
        $object1->id = 1;
        $object1->name = 'foo';

        $object2 = new \stdClass();
        $object2->id = 1;
        $object2->name = 'foo';

        $comparator = new ArrayComparator(array($object1), array($object2));

        $comparator->setItemIdentityComparator(
            function ($key1, $key2, $item1, $item2) {
                return $item1->id === $item2->id;
            }
        );

        // Compares the names of the objects
        $comparator->setItemComparator(
            function ($item1, $item2) {
                return $item1->name === $item2->name;
            }
        );

        $comparator->whenMissingRight(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenMissingLeft(
            function () {
                throw new \Exception();
            }
        );

        $comparator->whenDifferent(
            function () {
                throw new \Exception();
            }
        );

        $comparator->compare();
    }

    /**
     * Test missing items from both arrays, with an indexed array
     */
    public function testMissingWithIndexedArray()
    {
        $comparator = new ArrayComparator(array('foo' => 'bar'), array('bim' => 'baz'));

        $testCase = $this;

        $callCountRight = 0;
        $comparator->whenMissingRight(
            function ($item) use (&$callCountRight, $testCase) {
                $testCase->assertEquals('bar', $item);
                $callCountRight++;
            }
        );

        $callCountLeft = 0;
        $comparator->whenMissingLeft(
            function ($item) use (&$callCountLeft, $testCase) {
                $testCase->assertEquals('baz', $item);
                $callCountLeft++;
            }
        );

        $comparator->whenDifferent(
            function () {
                throw new \Exception();
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCountRight);
        $this->assertEquals(1, $callCountLeft);
    }

}
