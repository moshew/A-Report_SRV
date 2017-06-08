<?php
header('Content-Type: application/javascript; charset=utf-8');

if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');
$row = $conn->query('SELECT g_id FROM users WHERE admin=true and id="'.$id.'"')->fetch_row();
if ($row!=null)
{
	$g_id = $row[0];
	date_default_timezone_set ('Asia/Jerusalem');
	
	$day = $_GET['day'];
	if (DateTime::createFromFormat('Y-m-d', $day) == FALSE) {
		$day = Date('Y-m-d');
	}
	
	$sql = 'SELECT count(r.id) FROM reports r inner join users u on u.id=r.u_id WHERE r.active=1 and r.day="'.$day.'" and u.g_id='.$g_id;
	$row = $conn->query($sql)->fetch_row();
	$qrows = ceil($row[0]/200);
	$sql = 'SELECT count(r.id) FROM reports r inner join users u on u.id=r.u_id WHERE r.active=1 and r.day="'.$day.'" and r.info!="" and u.g_id='.$g_id;
	$row = $conn->query($sql)->fetch_row();
	$qrlen = $qrows + ceil($row[0]/45);
	if ($_GET['op']=='qrlen') {
		echo $_GET['callback'].'('.json_encode(array('len'=>$qrlen)).');';
	} else {
		require_once "phpqrcode/phpqrcode.php";
		$op = intval($_GET['op']);
		$str = str_replace('-','',$day);
		$str .= sprintf("%02d%02d", $qrlen, $op);
		if ($op>=0 && $op<$qrows) {
			$start_with = 190*$op;
			$sql = 'SELECT users.state_code, users.hrs, reports.status, reports.approved_by!="" as is_approved FROM users, reports WHERE reports.active=1 and reports.day="'.$day.'" and users.id=reports.u_id and users.g_id='.$g_id.' order by reports.id limit '.$start_with.', 210';
			$reports = $conn->query($sql);
			while($report=$reports->fetch_assoc()) {
				$key = strrev($report['state_code'].$report['hrs']);
				$str .= $key.$report['is_approved'].sprintf("%02d", $report['status']);
			}
		} else if ($op<$qrlen) {
			$start_with = 40*($op-$qrows);
			$sql = 'SELECT users.state_code, users.hrs, reports.info FROM users, reports WHERE reports.active=1 and reports.day="'.$day.'" and users.id=reports.u_id and users.g_id='.$g_id.' and reports.info!="" order by reports.id limit '.$start_with.', 50';
			$reports = $conn->query($sql);
			while($report=$reports->fetch_assoc()) {
				$key = strrev($report['state_code'].$report['hrs']);
				$str .= $key.$report['info'].';';
			}
		} 
		QRcode::png($str);
		//echo $str;
	}
}
$conn->close();
?>