<?php
header('Content-Type: application/javascript; charset=utf-8');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

$u_id=-1;
if (isset($_GET['u_id'])) $u_id = $_GET['u_id'];
elseif (isset($_GET['phone'])) $u_id = $_GET['phone'];
else exit();

$conn = require('db_conn.php');
$row = $conn->query('select g_id from users where id="'.$_GET['id'].'" and (manager=true or admin=true)')->fetch_row();
if ($row != null) {
	$g_id = $row[0];
	$result = require 'login.php';
	$user = $conn->query('select nickname from users where u_id='.$u_id.' and g_id='.$g_id)->fetch_row();
	if ($user != null) {
		$result['date_str'] = 'דיווח עבור '.$user[0];
		$result['phone'] = $u_id;
		$result['u_id'] = $u_id;
		echo $_GET['callback'].'('.json_encode($result).');';
	}
}

$conn->close();
?>