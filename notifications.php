<?php
header('Content-Type: application/javascript; charset=utf-8');
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

require 'send_notification.php';

$conn = require('db_conn.php');
date_default_timezone_set ('Asia/Jerusalem');

$row = $conn->query('SELECT g_id FROM users WHERE id="'.$id.'"')->fetch_row();
if ($row != null) {
	$g_id = $row[0];

	if (isset($_GET['op'])) {
		
		if (isset($_GET['u_id'])) $cond = 'u_id='.$_GET['u_id'];
		else $cond = 'nickname="'.$_GET['user'].'"';
		
		if ($_GET['op']=='del') {
			$conn->query('UPDATE users, notifications SET status=status+2 WHERE users.id=notifications.monitor_id and users.id="'.$id.'" and status<2 and notifications.reporter_id = (SELECT id FROM users WHERE g_id='.$g_id.' and '.$cond.')');
		} elseif ($_GET['op']=='req') {
			$conn->query('UPDATE users, notifications SET status=status-2 WHERE users.id=notifications.monitor_id and users.id="'.$id.'" and status>1 and notifications.reporter_id = (SELECT id FROM users WHERE g_id='.$g_id.' and '.$cond.')');
			if ($conn->affected_rows==0) {
				$sender = $conn->query('SELECT id FROM users WHERE g_id='.$g_id.' and '.$cond.' and id not in (select notifications.reporter_id from users, notifications where users.id="'.$id.'" and users.id=notifications.monitor_id)')->fetch_row();
				if ($sender != null) {
					$informed = $conn->query('SELECT manager FROM users WHERE id="'.$id.'"')->fetch_row();
					$conn->query('INSERT INTO notifications (reporter_id, monitor_id, status) VALUES ("'.$sender[0].'", "'.$id.'", '.$informed[0].')');
					if (!$informed[0]) {
						$conn->query('update users set permission_request=1 where id="'.$sender[0].'"');
						$message = 'קיימת בקשת הרשאה לדיווחים שלך. לניהול הרשאה "בחר מעקב דיווח->הרשאות"';
						sendNotification($sender[0], $message);
					}
				}
			}
		}
	}

	$reported = $waiting = array();
	$sql = 'select users2.nickname as nickname, users2.u_id as u_id, ifnull(reports.status,99) as status, reports.info as info from notifications left join reports on reports.u_id=notifications.reporter_id and reports.day="'.Date('Y-m-d').'" and reports.active=1 inner join users on notifications.monitor_id=users.id inner join users as users2 on notifications.reporter_id=users2.id where notifications.status=1 and users.id="'.$id.'"';
	$items = $conn->query($sql);
	while($item=$items->fetch_assoc()) array_push($reported, array('name'=>$item['nickname'], 'u_id'=>$item['u_id'], 'phone'=>$item['u_id'], 'status'=>$item['status'], 'info'=>$item['info']));

	$items = $conn->query('select users.nickname from users, notifications where users.id=notifications.reporter_id and notifications.status=0 and notifications.monitor_id="'.$id.'"');
	while($item=$items->fetch_assoc()) array_push($waiting, $item['nickname']);

	echo $_GET['callback'].'('.json_encode(array('id'=>$id, 'reported'=>$reported, 'waiting'=>$waiting)).');';
}
$conn->close();

/*
0- waiting
1- in
2- deleted while waiting
3- deleted
*/


?>