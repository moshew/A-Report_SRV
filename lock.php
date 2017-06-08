<?php
header('Content-Type: application/javascript; charset=utf-8');
if (!isset($_GET['id']) || !isset($_GET['op'])) exit();
$id = $_GET['id'];
$op = $_GET['op'];

$conn = require('db_conn.php');

$row = $conn->query('SELECT g_id FROM users WHERE admin=true and id="'.$id.'"')->fetch_row();
if ($row != null) {
	date_default_timezone_set ('Asia/Jerusalem');
	$day = Date('Y-m-d');
	$g_id = $row[0];

	if ($op=='true') {
		if ($conn->query('select day from locked where day="'.$day.'" and g_id='.$g_id)->num_rows == 0) {
			$conn->query('insert into locked (day, g_id) values ("'.$day.'", '.$g_id.')');
			$conn->query('update users u inner join reports r on u.id=r.u_id set forms_request=1 where status in(2,6,7,8,9) and day="'.$day.'" and g_id='.$g_id);
		}
	} else $conn->query('delete from locked where day="'.$day.'" and g_id='.$g_id);
	$result = require 'login.php';
	echo $_GET['callback'].'('.json_encode($result).');';
}
$conn->close();
?>