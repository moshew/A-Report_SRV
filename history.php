<?php
header('Content-Type: application/javascript; charset=utf-8');
if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');
date_default_timezone_set ('Asia/Jerusalem');

if (isset($_GET['day'])) {
    $day = $_GET['day'];
    $sql = 'select status, info, attach from reports where u_id="'.$id.'" and active=1 and day="'.$day.'"';
    $item = $conn->query($sql)->fetch_row();
    if ($item!=null) {
        $result = array(date_str=>date('j/n', strtotime($day)), status=>$item[0], info=>$item[1], attach=>$item[2]==1);
    } else {
        $result = array(date_str=>date('j/n', strtotime($day)), status=>12, info=>'אין דיווח', attach=>false);
    }
} else {
    $month = (isset($_GET['month']))?$_GET['month']:Date('Y-m');
    $sql = 'select day, status from reports where active=1 and day like "'.$month.'%" and u_id="'.$id.'"';
    $items = $conn->query($sql);

    $reported = array();
    while($item=$items->fetch_assoc()) array_push($reported, array(day=>$item[day], status=>$item[status]));

    $result = array(id=>$id, reported=>$reported);
}

echo $_GET['callback'].'('.json_encode($result).');';
$conn->close();
