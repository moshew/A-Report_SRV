<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = require('db_conn.php');
/*
$conn->query('update users set nickname1="", state_code="", hrs=""');
$items = $conn->query('select * from users');
while($item=$items->fetch_assoc()) {
	$name = $item[nickname];
	$names = explode(' ',$name);
	$first = $names[0];
	$last = substr($name, strlen($first)+1);
	
	$cand = $last;
	if ($conn->query('select id from users where nickname1="'.$cand.'"')->num_rows!=0) {
		$cand.='.';
		foreach(preg_split('//u', $name, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
			$cand .= $ch;
			if ($conn->query('select id from users where nickname1="'.$cand.'"')->num_rows==0) break;
		}
	}

	$pid = strrev($item[p_id]);
	$sql = 'update users set nickname1="'.$cand.'", state_code="'.substr($pid,0,5).'", hrs="'.substr($pid,5,3).'"where id="'.$item[id].'"';
	$conn->query($sql);
}  
*/
$sql = "SELECT u.id as uid, n.id as nid FROM users u INNER JOIN notifications n ON u.state_code = SUBSTRING( REVERSE( n.informed_id ) , 1, 5 ) AND hrs = SUBSTRING( REVERSE( n.informed_id ) , 6, 3 )";
$items = $conn->query($sql);

while($item=$items->fetch_assoc()) {
	$sql2 = 'update notifications set n_id="'.$item[uid].'" where id="'.$item[nid].'"';
	echo $sql2;
	$conn->query($sql2);
}  

$conn->close();
?>