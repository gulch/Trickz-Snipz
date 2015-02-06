<?php
$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$sortedArr = radixSort($arr);
print_r($sortedArr);


/**
 * This is LSD radix sort using queues. Assuming the longest element has
 * 3 digits length.
 *
 * @param reference &$elements Reference to array elements.
 * @return void $elements already sorted in place.
 */
function radixSort($elements)
{
    // Array for 10 queues.
    $queues = array(
        array(), array(), array(), array(), array(), array(), array(), array(), array(), array()
    );
    // Queues are allocated dynamically. In first iteration longest digits
    // element also determined.
    $longest = 0;
    foreach ($elements as $el) {
        if ($el > $longest) {
            $longest = $el;
        }
        array_push($queues[$el % 10], $el);
    }
    // Queues are dequeued back into original elements.
    $i = 0;
    foreach ($queues as $key => $q) {
        while (!empty($queues[$key])) {
            $elements[$i++] = array_shift($queues[$key]);
        }
    }
    // Remaining iterations are determined based on longest digits element.
    $it = strlen($longest) - 1;
    $d = 10;
    while ($it--) {
        foreach ($elements as $el) {
            array_push($queues[floor($el / $d) % 10], $el);
        }
        $i = 0;
        foreach ($queues as $key => $q) {
            while (!empty($queues[$key])) {
                $elements[$i++] = array_shift($queues[$key]);
            }
        }
        $d *= 10;
    }

    return $elements;
}