<?php

namespace ArrayComparator\Tests;

use ArrayComparator\ArrayComparator;
use PHPUnit\Framework\TestCase;

class ArrayComparatorTest extends TestCase
{

    public function testNoError()
    {
        $comparator = new ArrayComparator();
        $comparator->compare(array(), array());
    }

    /**
     * Test that no comparison function is called with empty arrays
     */
    public function testCompareEmptyArrays()
    {
        $comparator = new ArrayComparator();

        $comparator->whenEqual(
            function () {
                throw new \Exception();
            }
        )
            ->whenDifferent(
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
            ->compare(array(), array());
    }

    /**
     * Test when an item is missing from the right array
     */
    public function testWhenMissingRight()
    {
        $comparator = new ArrayComparator();

        $comparator->whenEqual(
            function () {
                throw new \Exception();
            }
        );
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
                $testCase->assertSame('foo', $item1);
                $callCount++;
            }
        );

        $comparator->compare(array('foo'), array());

        $this->assertSame(1, $callCount);
    }

    /**
     * Test when an item is missing from the left array
     */
    public function testWhenMissingLeft()
    {
        $comparator = new ArrayComparator();

        $comparator->whenEqual(
            function () {
                throw new \Exception();
            }
        );
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
                $testCase->assertSame('foo', $item2);
                $callCount++;
            }
        );

        $comparator->compare(array(), array('foo'));

        $this->assertSame(1, $callCount);
    }

    /**
     * Test when the same item is in both arrays, but has differences
     */
    public function testWhenDifferences()
    {
        $comparator = new ArrayComparator();

        $comparator->whenEqual(
            function () {
                throw new \Exception();
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
                $testCase->assertSame('foo', $item1);
                $testCase->assertSame('bar', $item2);
                $callCount++;
            }
        );

        $comparator->compare(array('foo'), array('bar'));

        $this->assertSame(1, $callCount);
    }

    /**
     * Test when the same item is in both arrays, but has differences
     */
    public function testWhenDifferencesWithIndexedArray()
    {
        $comparator = new ArrayComparator();

        $comparator->whenEqual(
            function () {
                throw new \Exception();
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
                $testCase->assertSame('bar', $item1);
                $testCase->assertSame('baz', $item2);
                $callCount++;
            }
        );

        $comparator->compare(array('foo' => 'bar'), array('foo' => 'baz'));

        $this->assertSame(1, $callCount);
    }

    /**
     * Test with overridden comparator behaviors
     */
    public function testWhenDifferencesWithCustomComparators()
    {
        $object1 = new \stdClass();
        $object1->id = 1;
        $object1->name = 'foo';

        $object2 = new \stdClass();
        $object2->id = 1;
        $object2->name = 'bar';

        $comparator = new ArrayComparator();

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

        $comparator->whenEqual(
            function () {
                throw new \Exception();
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
                $testCase->assertSame('foo', $item1->name);
                $testCase->assertSame('bar', $item2->name);
                $callCount++;
            }
        );

        $comparator->compare(array(1 => $object1), array(2 => $object2));

        $this->assertSame(1, $callCount);
    }

    /**
     * Test with overridden comparator class
     */
    public function testWhenDifferencesWithCustomComparatorClass()
    {
        $object1 = new \stdClass();
        $object1->id = 1;
        $object1->name = 'foo';

        $object2 = new \stdClass();
        $object2->id = 1;
        $object2->name = 'bar';

        $comparator = new CustomComparator();

        $comparator->whenEqual(
            function () {
                throw new \Exception();
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
                $testCase->assertSame('foo', $item1->name);
                $testCase->assertSame('bar', $item2->name);
                $callCount++;
            }
        );

        $comparator->compare(array(1 => $object1), array(2 => $object2));

        $this->assertSame(1, $callCount);
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

        $comparator = new ArrayComparator();

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

        $testCase = $this;

        $callCountEqual = 0;
        $comparator->whenEqual(
            function () use (&$callCountEqual, $testCase) {
                $callCountEqual++;
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

        $comparator->compare(array($object1), array($object2));

        $this->assertSame(1, $callCountEqual);
    }

    /**
     * Test missing items from both arrays, with an indexed array
     */
    public function testMissingWithIndexedArray()
    {
        $comparator = new ArrayComparator();

        $testCase = $this;

        $callCountRight = 0;
        $comparator->whenMissingRight(
            function ($item) use (&$callCountRight, $testCase) {
                $testCase->assertSame('bar', $item);
                $callCountRight++;
            }
        );

        $callCountLeft = 0;
        $comparator->whenMissingLeft(
            function ($item) use (&$callCountLeft, $testCase) {
                $testCase->assertSame('baz', $item);
                $callCountLeft++;
            }
        );

        $comparator->whenEqual(
            function () {
                throw new \Exception();
            }
        );
        $comparator->whenDifferent(
            function () {
                throw new \Exception();
            }
        );

        $comparator->compare(array('foo' => 'bar'), array('bim' => 'baz'));

        $this->assertSame(1, $callCountRight);
        $this->assertSame(1, $callCountLeft);
    }

    /**
     * Test missing items from both arrays, with an indexed array
     */
    public function testComplexArray()
    {
        $comparator = new ArrayComparator();

        $testCase = $this;

        $callCountEqual = 0;
        $comparator->whenEqual(
            function ($item) use (&$callCountEqual, $testCase) {
                $callCountEqual++;
            }
        );

        $callCountDifferent = 0;
        $comparator->whenDifferent(
            function ($item) use (&$callCountDifferent, $testCase) {
                $callCountDifferent++;
            }
        );

        $callCountRight = 0;
        $comparator->whenMissingRight(
            function ($item) use (&$callCountRight, $testCase) {
                $callCountRight++;
            }
        );

        $callCountLeft = 0;
        $comparator->whenMissingLeft(
            function ($item) use (&$callCountLeft, $testCase) {
                $callCountLeft++;
            }
        );

        $comparator->compare(
            array('foo' => '1', 'fuu' => '2', 'fii' => '3', 'bar' => '4'),
            array('bim' => 'baz', 'foo' => '1', 'fuu' => '21', 'bar' => '51')
        );

        $this->assertSame(1, $callCountEqual);
        $this->assertSame(2, $callCountDifferent);
        $this->assertSame(1, $callCountRight);
        $this->assertSame(1, $callCountLeft);
    }

    /**
     * Test missing items from both arrays, with an indexed array
     */
    public function testNoCallableWorking()
    {
        $comparator = new ArrayComparator();

        $comparator->compare(
            array('foo' => '1', 'fuu' => '2', 'fii' => '3', 'bar' => '4'),
            array('bim' => 'baz', 'foo' => '1', 'fuu' => '21', 'bar' => '51')
        );

        $this->assertTrue(true);
    }

}

/**
 * Custom comparator by extending the Comparator class
 * @package ArrayComparator
 */
class CustomComparator extends ArrayComparator
{
    /**
     * {@inheritdoc}
     */
    protected function areSame($key1, $key2, $item1, $item2)
    {
        return $item1->id === $item2->id;
    }

    /**
     * {@inheritdoc}
     */
    protected function areEqual($item1, $item2)
    {
        return $item1->name === $item2->name;
    }
}
