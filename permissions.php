<?php
header('Content-Type: application/javascript; charset=utf-8');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');
date_default_timezone_set ('Asia/Jerusalem');

if (null != $conn->query('SELECT g_id FROM users WHERE id="'.$id.'"')->fetch_row()) {
	
	$conn->query('update users set permission_request=0 where id="'.$id.'"');

	if (isset($_GET['op'])) {
		if ($_GET['op']=='del') {
			$conn->query('delete from notifications where reporter_id="'.$id.'" and id='.$_GET['nId']);
		} elseif ($_GET['op']=='change') {
			if ($_GET['status']=='true') {
				$conn->query('update notifications set status=status+1 where status in (0,2) and reporter_id="'.$id.'" and id='.$_GET['nId']);
			} else {
				$conn->query('update notifications set status=status-1 where status in (1,3) and reporter_id="'.$id.'" and id='.$_GET['nId']);
			}
		}
	}

	$permissions = array();
	$sql = 'select notifications.id as nId, nickname, status FROM notifications, users where monitor_id = users.id and manager=false and reporter_id ="'.$id.'"';
	$items = $conn->query($sql);
	while($item=$items->fetch_assoc()) {
		$status = intval($item['status']);
		if ($status>1) $status -= 2;
		array_push($permissions, array('nId'=>$item['nId'], 'name'=>$item['nickname'], 'status'=>$status));
	}

	echo $_GET['callback'].'('.json_encode(array('id'=>$id, 'permissions'=>$permissions)).');';
}

$conn->close();
?>