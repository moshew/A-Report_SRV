<?php
if ($conn==null) {
	header('Content-Type: application/javascript; charset=utf-8');
	$conn = require('db_conn.php');
	$report_manage = true;
}
date_default_timezone_set ('Asia/Jerusalem');
if (!isset($_GET['id']) || !isset($_GET['oper'])) exit();

$id = $_GET['id'];
$day = Date('Y-m-d');
$oper = intval($_GET['oper']);
$info = str_replace('"', '\"', $_GET['info']);
if ($info=='undefined') $info='';

if (isset($_GET['attach'])) {
	$conn->query('update reports set attach='.$oper.' where u_id="'.$id.'" and day="'.$_GET['day'].'" and active=1');
} else {
	$result = $conn->query('select g_id, manager, admin from users where id="'.$id.'"')->fetch_row();
	if ($result!=null) {
		if ($conn->query('select day from locked where day="'.$day.'" and g_id='.$result[0])->num_rows == 0) {
			$report_id = $id;
			if (isset($_GET['u_id'])) $u_id = $_GET['u_id'];
			elseif (isset($_GET['phone'])) $u_id = $_GET['phone'];
			if (isset($u_id) && ($result[1]==1 || $result[2]==1)) {
				$row = $conn->query('select id from users where u_id='.$u_id.' and g_id='.$result[0])->fetch_row();
				if ($row!=null) $report_id = $row[0];
			}
    
			if ($report_id!=-1) {
				$conn->query('update reports set active=0 where u_id="'.$report_id.'" and day="'.$day.'"');
				if ($oper>=0) $conn->query('insert into reports (u_id, day, status, info) values ("'.$report_id.'", "'.$day.'", '.$oper.', "'.$info.'")');
			}
		}
	}
}

$result = require 'login.php';
$result['report'] = true;

if ($report_manage) {
  $conn->close();
  echo $_GET['callback'].'('.json_encode($result).');';
}

return $result;
?>