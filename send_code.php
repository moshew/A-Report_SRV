<?php
header('Content-Type: application/javascript; charset=utf-8');

if (!isset($_GET['p_id'])) exit();
$p_id = substr($_GET['p_id'], 2);
$pid_rev = strrev($p_id);
$cond = 'state_code="'.substr($pid_rev,0,5).'" and hrs="'.substr($pid_rev,5,3).'"';

require_once 'send_sms.php';
$conn = require('db_conn.php');

$row = $conn->query('select id from users where '.$cond)->fetch_row();
if ($row!=null) {

   while(true) {
      $id = rand(10000,99999);
      if (null == $conn->query('select temp_id from temp_login where temp_id='.$id)->fetch_row()) {
         $conn->query('insert into temp_login (temp_id, u_id) values ('.$id.', "'.$row[0].'")');
         break;
      }
   }
 
   sendSms($p_id, 'שלום, הקוד שלך לדוח סגולה הוא '.$id.'. יום טוב.');
   $status = array(status=>true, msg=>'קוד המשתמש נשלח בהצלחה למספר שהזנת');
   
} else {
   $status = array(status=>false, msg=>'המספר שהזנת לא נמצא במערכת. אנא פנה לגורמי המשאן');
}

echo $_GET['callback'].'('.json_encode($status).');';
?>