<?php
header('Content-Type: application/javascript; charset=utf-8');

if (!isset($_GET['id']) || !isset($_GET['day'])) exit();

$id = $_GET['id'];
$day= $_GET['day'];
$filename = $day.'_'.$id.'.jpg';
header('Location: http://online-files.co.il/2016/'.$filename);
?>
