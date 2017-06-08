<?php
header('Content-Type: application/javascript; charset=utf-8');

if (isset($_GET['u_id'])) $u_id = $_GET['u_id'];
elseif (isset($_GET['phone'])) $u_id = $_GET['phone'];

if (!isset($_GET['id']) || !isset($u_id)) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');
$row = $conn->query('select g_id from users where id="'.$id.'" and admin=true')->fetch_row();
if ($row != null) {
	$g_id = $row[0];
	$user = $conn->query('select id, nickname from users where u_id='.$u_id.' and g_id='.$g_id)->fetch_row();
	if ($user != null) {
		$report_id = $user[0];
		$title =  $user[1];

		$result = require 'future_tasks.php';
		$result['phone'] = $u_id;
		$result['u_id'] = $u_id;
		echo $_GET['callback'].'('.json_encode($result).');';
	}
}

$conn->close();
?>