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

    public function testMissingRight()
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
        $comparator->whenMissingRight(
            function () use (&$callCount) {
                $callCount++;
            }
        );

        $comparator->compare();

        $this->assertEquals(1, $callCount);
    }

}
