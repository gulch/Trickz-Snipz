<?php

$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$sortedArr = quickSort($arr);
print_r($sortedArr);

function quickSort($array)
{
    if (count($array) < 2) {
        return $array;
    }
    $left = $right = array();
    reset($array);
    $pivot_key = key($array);
    $pivot = array_shift($array);
    foreach ($array as $k => $v) {
        if ($v < $pivot)
            $left[$k] = $v;
        else
            $right[$k] = $v;
    }
    return array_merge(quickSort($left), array($pivot_key => $pivot), quickSort($right));
}