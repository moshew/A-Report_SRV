<?php
header('Content-Type: application/javascript; charset=utf-8');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');
$row = $conn->query('SELECT g_id FROM users WHERE admin=true and id="'.$id.'"')->fetch_row();
if ($row != null) {
	$g_id = $row[0];

	if (isset($_GET['deleted'])) {
		foreach(explode(';', $_GET['deleted']) as $u_id) {
			$conn->query('update users set forms_request=0 where u_id='.$u_id.' and g_id='.$g_id);
		}
	}

	$form_requests = array();
	$sql = 'select u_id, nickname FROM users where forms_request=1 and g_id='.$g_id;
	$items = $conn->query($sql);
	while($item=$items->fetch_assoc()) array_push($form_requests, array('name'=>$item['nickname'], 'u_id'=>$item['u_id'], 'phone'=>$item['u_id']));

	echo $_GET['callback'].'('.json_encode(array('id'=>$id, 'form_requests'=>$form_requests)).');';
}
$conn->close();
?>
