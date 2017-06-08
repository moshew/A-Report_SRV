<?php
header('Content-Type: text/html; charset=utf-8');
$str = "The time is " . date("h:i:sa");
file_put_contents('test.txt', $str);
echo $str;
?>