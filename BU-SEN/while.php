<?php
$count = 0;
$sum = 0;

do {
    $sum += $count;
    $count++;
} while ($count <= 20000);
echo "<h1>Sum: {$sum}</h1>";

?>