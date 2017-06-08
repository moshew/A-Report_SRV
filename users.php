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
	if (isset($_GET['phone'])) {
		$key = strrev(substr($_GET['phone'], 2));
		$state_code = substr($key,0,5);
		$hrs = substr($key,5,3);
		$cond = 'state_code="'.$state_code.'" and hrs="'.$hrs.'" and g_id='.$g_id;
		
		$nickname = $_GET['name'];
		if ($g_id=='4') {
			$name = $nickname;
			$first = explode(' ',$name)[0];
			$last = substr($name, strlen($first)+1);
			$nickname = $last;
			if ($conn->query('select id from users where nickname="'.$nickname.'" and g_id='.$g_id)->num_rows!=0) {
				$nickname.='.';
				foreach(preg_split('//u', $name, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
					$nickname .= $ch;
					if ($conn->query('select id from users where nickname="'.$nickname.'" and g_id='.$g_id)->num_rows==0) break;
				}
			}
		}
	
		$isMAnager = $_GET['manager'];
		$row = $conn->query('SELECT id FROM users WHERE '.$cond)->fetch_row();
		if ($row!=null) {
			$conn->query('update users set nickname="'.$nickname.'", manager='.$isMAnager.' where '.$cond);
			if ($isMAnager) $conn->query('update notifications set status=1 where monitor_id="'.$row[0].'"');
		} else {
			while(true) {
				$new_id = substr(rtrim(base64_encode(md5(microtime())),"="), 0, 16);
				if (null == $conn->query('select id from users where id="'.$new_id.'"')->fetch_row()) {
					$next_uid = intval($conn->query('select max(u_id) from users where g_id='.$g_id)->fetch_row()[0])+1;
					$conn->query('insert into users (id, u_id, g_id, state_code, nickname, hrs, manager) values("'.$new_id.'", '.$next_uid.', '.$g_id.', "'.$state_code.'", "'.$nickname.'", "'.$hrs.'", '.$isMAnager.')');
					break;
				}
			}
		}
	}

	if (isset($_GET['deleted'])) {
		foreach(explode(';', $_GET['deleted']) as $u_id) {
			$conn->query('delete from users where u_id='.$u_id.' and g_id='.$g_id);
		}
	}

	$del_candidates = array();
	$sql = 'select u_id, nickname FROM users where g_id='.$g_id.' and id not in (select u_id from reports where day>DATE_SUB(NOW(), INTERVAL 2 week))';
	$items = $conn->query($sql);
	while($item=$items->fetch_assoc()) array_push($del_candidates, array('name'=>$item['nickname'], 'u_id'=>$item['u_id'], 'phone'=>$item['u_id']));

	echo $_GET['callback'].'('.json_encode(array('id'=>$id, 'del_candidates'=>$del_candidates)).');';
}
$conn->close();
?>
