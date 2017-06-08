<?php
header('Content-Type: text/html; charset=utf-8');
require_once "phpqrcode/phpqrcode.php";

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

	$str = str_replace('-','',$day);
	if ($_GET['op']=='1' || $_GET['op']=='2') {
		$str .= $_GET['op'];
		$start_with = 180*(intval($_GET['op'])-1);
		$sql = 'SELECT users.state_code, users.hrs, reports.status, reports.approved_by!="" as is_approved FROM users, reports WHERE reports.active=1 and reports.day="'.$day.'" and users.id=reports.u_id and users.g_id='.$g_id.' order by reports.id limit '.$start_with.', 200';
		$reports = $conn->query($sql);
		while($report=$reports->fetch_assoc()) {
			$key = strrev($report['state_code'].$report['hrs']);
			$str .= $key.$report['is_approved'].str_pad($report['status'], 2, '0', STR_PAD_LEFT);
		}
	} else {
		$str .= '0';
		$sql = 'SELECT users.state_code, users.hrs, reports.info FROM users, reports WHERE reports.active=1 and reports.day="'.$day.'" and users.id=reports.u_id and users.g_id='.$g_id.' and reports.info!=""';
		$reports = $conn->query($sql);
		while($report=$reports->fetch_assoc()) {
			$key = strrev($report['state_code'].$report['hrs']);
			$str .= $key.$report['info'].';';
		}
	} 
	QRcode::png($str);
	//echo $str;
}
$conn->close();
?>