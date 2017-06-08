<?php
header('Content-Type: application/javascript; charset=utf-8');
date_default_timezone_set ('Asia/Jerusalem');

$id = $_GET['id'];
$day = Date('Y-m-d');

$conn = require('db_conn.php');
if ($_GET['op']=='get') {
  $result=array();
  if ($conn->query('select id from users where id="'.$id.'" and admin=true')->num_rows == 0) exit();
  $items = $conn->query('select distinct(nickname) from users, reports where users.id=reports.approved_by and reports.active=1 and reports.day="'.$day.'"');
  while($item=$items->fetch_assoc()) array_push($result, $item[nickname]);
}
else {
  $result = array(id=>$id);
  if ($conn->query('select id from users where id="'.$id.'" and manager=true')->num_rows == 0) $result['status'] = array(code=>'error', info=>'לא ניתנו הרשאות מתאימות');
  else {
    $sql = 'update reports r, notifications n, users u set r.approved_by="'.$id.'" where r.active=true and n.status=1 and r.day="'.$day.'" and u.id=r.u_id and u.id=n.reporter_id and n.monitor_id="'.$id.'"';
    $conn->query($sql);
    $result['status'] = array(code=>'success');
  }
}
echo $_GET['callback'].'('.json_encode($result).');';
$conn->close();
?>