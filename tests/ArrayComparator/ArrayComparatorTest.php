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

    public function testCompareEmptyArrays()
    {
        $comparator = new ArrayComparator(array(), array());

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
        $comparator->whenMissingRight(
            function () {
                throw new \Exception();
            }
        );

        $comparator->compare();
    }

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

    public function testWhenDifferent()
    {
        $object1 = new \stdClass();
        $object1->id = 1;
        $object1->name = 'foo';

        $object2 = new \stdClass();
        $object2->id = 1;
        $object2->name = 'bar';

        $comparator = new ArrayComparator(array($object1), array($object2));

        $comparator->setItemIdentityComparator(
            function ($item1, $item2) {
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

}
