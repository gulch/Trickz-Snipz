<?php

$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$sortedArr = insertionSorting($arr);
print_r($sortedArr);

function insertionSorting($array)
{
    $length = count($array);
    for ($i = 1; $i < $length; $i++) {
        $element = $array[$i];
        $j = $i;
        while ($j > 0 && $array[$j - 1] > $element) {
            //move value to right and key to previous smaller index
            $array[$j] = $array[$j - 1];
            $j = $j - 1;
        }
        //put the element at index $j
        $array[$j] = $element;
    }

    return $array;
}