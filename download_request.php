<?php
header('Content-Type: application/javascript; charset=utf-8');

require_once 'send_sms.php';
$conn = require('db_conn.php');

$items = $conn->query('select p_id from users where id not in (select u_id from reports)');
while($item=$items->fetch_assoc())
{
   echo $item[p_id].';';
   $msg = 'שלום, טרם דווחת באפליקצית הנוכחות. חפש "דוח סגולה" ב-google play.';
   //sendSms($item[p_id], $msg);
}
$conn->close();
?>