<?php

$ar = array(71, 98, 13, 96, 76, 44, 90, 9, 2, 24, 50, 52, 74, 31, 81, 67, 70, 91, 40, 21);

$count = array();
$min = 99999;
$max = -99999;
foreach ($ar as $v) {
    $count[$v] = isset($count[$v]) ? $count[$v] + 1 : 1;
    if ($v < $min) {
        $min = $v;
    }
    if ($v > $max) {
        $max = $v;
    }
}
$sorted = array();
for ($i = $min; $i <= $max; $i++) {
    if (!isset($count[$i])) {
        continue;
    }
    for ($j = 0; $j < $count[$i]; $j++) {
        $sorted[] = $i;
    }
}

var_dump($sorted);