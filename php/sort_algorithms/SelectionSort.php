<?php

$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$sortedArr = selectionSort($arr);
print_r($sortedArr);

/*function selectionSort(array $arr)
{
    $count = sizeof($arr);
    for ($i = 0; $i < $count; ++$i) {
        $min = null;
        $minKey = null;
        for($j = $i; $j < $count; ++$j) {
            if (null === $min || $arr[$j] < $min) {
                $minKey = $j;
                $min = $arr[$j];
            }
        }
        $arr[$minKey] = $arr[$i];
        $arr[$i] = $min;
    }
    return $arr;
}*/

function selectionSort(array $arr)
{
    $countJ = sizeof($arr);
    $countI = $countJ - 1;
    for ($i = 0; $i < $countI; ++$i)
    {
        $min = $arr[$i];
        $minKey = $i;
        for ($j = $i + 1; $j < $countJ; ++$j)
        {
            if($arr[$j] < $min)
            {
                $min = $arr[$j];
                $minKey = $j;
            }
        }
        if($minKey != $i)
        {
            $arr[$minKey] = $arr[$i];
            $arr[$i] = $min;
        }
    }
    return $arr;
}