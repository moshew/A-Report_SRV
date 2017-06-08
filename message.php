<?php
header('Content-Type: application/javascript; charset=utf-8');
if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');

$row = $conn->query('SELECT g_id FROM users WHERE id="'.$id.'"')->fetch_row();
if ($row != null) {
	$g_id = $row[0];
	if ($_GET['op']=='confirm') {
		$conn->query('update users set message_status=1 where message_status>0 and id="'.$id.'"');
		$result = require 'login.php';
	} else {
		if (isset($_GET['op']) && $conn->query('SELECT id FROM users WHERE admin=true and id="'.$id.'"')->num_rows>0) {
			if ($_GET['op']=='new') {
				$conn->query('update users set message_status=2 where g_id='.$g_id);
				$msg = str_replace('"','\"',$_GET['msg']);
				$conn->query('update message set info="'.$msg.'" where id='.$g_id);
			} elseif ($_GET['op']=='reset') {
				$conn->query('update users set message_status=0 where g_id='.$g_id);
			}
		}
		$item = $conn->query('select info from message where id='.$g_id)->fetch_row();
		if ($item!=null) {
			$result = array(id=>$id, message=>$item[0]);
		}
	}
	echo $_GET['callback'].'('.json_encode($result).');';
}

$conn->close();

?>