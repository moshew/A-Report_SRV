<?php
header('Content-Type: application/javascript; charset=utf-8');

//require_once 'send_whatsapp.php';
require_once "phpqrcode/phpqrcode.php";

$conn = require('db_conn.php');
date_default_timezone_set ('Asia/Jerusalem');
$day = Date('Y-m-d');
$file_time = microtime();
$absence_reason = array('לא דווח', 'חופש', 'מחלה', 'חו"ל', 'מחוץ ליחידה', 'קורס', 'מיוחדת', 'מחלה בהצהרה', 'יום ד', 'מחלת ילד', 'חופשת לידה', 'אחר');

$conn->query('update reports set notification="'.$file_time.'" where notification is null');
$items = $conn->query('SELECT notifications.informed_id, users.nickname, reports.status from reports, notifications, users where users.id=notifications.sender_id and users.id=reports.u_id and reports.active=1 and reports.status>0 and notifications.active=1 and (select get_changes from users where p_id=notifications.informed_id)=1 and reports.day="'.$day.'" and reports.notification="'.$file_time.'"');
foreach($items as $item) {
  //sendWhatsapp($item[informed_id], 'דיווח '.$absence_reason[$item[status]].' הוזן עבור '.$item[nickname]);
}

$conn->query('update future_reports set notification="'.$file_time.'" where notification is null');
$items = $conn->query('SELECT notifications.informed_id, users.nickname, future_reports.status, future_reports.start_day, future_reports.end_day from future_reports, notifications, users where users.id=notifications.sender_id and users.id=future_reports.u_id and future_reports.active=1 and notifications.active=1 and (select get_changes from users where p_id=notifications.informed_id)=1 and future_reports.notification="'.$file_time.'"');
foreach($items as $item) {
   $date_str = ' עתידי ל- '.date('j/n', strtotime($item[end_day]));
   if ($item[start_day] != $item[end_day]) $date_str .= '-'.date('j/n', strtotime($item[start_day]));
   //sendWhatsapp($item[informed_id], 'דיווח '.$absence_reason[$item[status]].$date_str.' הוזן עבור '.$item[nickname]);
}

$items = $conn->query('SELECT users.p_id, reports.status from users, reports where users.id=reports.u_id and reports.active=1 and reports.status>0 and users.get_approval=1 and day="'.$day.'" and notification="'.$file_time.'"');
foreach($items as $item) {
  //sendWhatsapp($item[p_id], 'דיווח '.$absence_reason[$item[status]].' הוזן עבורך בהצלחה');
}

$items = $conn->query('SELECT users.p_id, future_reports.status, future_reports.start_day, future_reports.end_day from users, future_reports where users.id=future_reports.u_id and future_reports.active=1 and users.get_approval=1 and notification="'.$file_time.'"');
foreach($items as $item) {
   $date_str = ' עתידי ל- '.date('j/n', strtotime($item[end_day]));
   if ($item[start_day] != $item[end_day]) $date_str .= '-'.date('j/n', strtotime($item[start_day]));
   //sendWhatsapp($item[p_id], 'דיווח '.$absence_reason[$item[status]].$date_str.' הוזן עבורך בהצלחה');
}

$conn->close();
?>