<?php

$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$sortedArr = gnomeSorting($arr);
print_r($sortedArr);

function gnomeSorting($arr)
{
    $i = 1;
    $j = 2;
    $count = sizeof($arr);

    while ($i < $count) {
        if ($arr[$i - 1] < $arr[$i]) {
            $i = $j;
            ++$j;
        } else {
            list($arr[$i - 1], $arr[$i]) = array($arr[$i], $arr[$i - 1]);
            --$i;
            if ($i === 0) {
                $i = $j;
                ++$j;
            }
        }
    }

    return $arr;
}