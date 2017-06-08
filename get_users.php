<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/javascript; charset=utf-8');

if (!isset($_GET['id']) && !isset($_GET['s'])) exit();

$conn = require('db_conn.php');
date_default_timezone_set ('Asia/Jerusalem');

$id = $_GET['id'];
$search = $_GET['s'];
$row = $conn->query('SELECT g_id FROM users WHERE id="'.$id.'"')->fetch_row();
if ($row != null) {
	$g_id = $row[0];
	$users = array();
	if (strlen($search)>=3) {
		$items = $conn->query('select nickname, u_id from users where nickname like "%'.$search.'%" and g_id='.$g_id.' and id not in (select notifications.reporter_id from notifications, users where notifications.status not in (2,3) and notifications.monitor_id = users.id and users.id="'.$id.'")');
		while($item=$items->fetch_assoc()) array_push($users, array('name'=>$item[nickname], 'u_id'=>$item[u_id]));
	}
	echo json_encode(array(id=>$id, users=>$users));
}

$conn->close();
?>