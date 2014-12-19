<?php

$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$sortedArr = cocktailSorting($arr);
print_r($sortedArr);

function cocktailSorting($arr)
{
    $left = 0;
    $right = sizeof($arr) - 1;
    do {
        for ($i = $left; $i < $right; $i++) {
            if ($arr[$i] > $arr[$i + 1]) {
                list($arr[$i], $arr[$i + 1]) = array($arr[$i + 1], $arr[$i]);
            }
        }
        --$right;
        for ($i = $right; $i > $left; $i--) {
            if ($arr[$i] < $arr[$i - 1]) {
                list($arr[$i], $arr[$i - 1]) = array($arr[$i - 1], $arr[$i]);
            }
        }
        ++$left;
    } while ($left <= $right);

    return $arr;
}