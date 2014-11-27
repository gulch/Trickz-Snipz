<?php

$arr = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$t = true;
while ($t) {
    $t = false;
    for ($i = 0, $count = sizeof($arr) - 1; $i < $count; $i++) {
        if ($arr[$i] > $arr[$i + 1]) {
            $temp = $arr[$i + 1];
            $arr[$i + 1] = $arr[$i];
            $arr[$i] = $temp;
            $t = true;
        }
    }
}

print_r($arr);