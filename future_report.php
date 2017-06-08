<?php
header('Content-Type: application/javascript; charset=utf-8');
if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

$conn = require('db_conn.php');
require 'login.php';

$status = -1;
$start_day = null;
$end_day = null;
$date_str = null;
$info = str_replace('"', '\"', $_GET['info']);
if ($info=='undefined') $info='';

if (isset($_GET['oper'])) 
{
  $oper = intval($_GET['oper']);
  $conn->query('update future_reports set active=0 where u_id="'.$id.'"');
  $conn->query('update reports set active=0 where u_id="'.$id.'" and day>"'.$day.'"');
  if ($oper>=0 && isset($_GET['start_day']) && isset($_GET['end_day']))
  {
     $start_time = strtotime($_GET['start_day']);
     $end_time = strtotime($_GET['end_day']);
     
     $start_day = date('Y-m-d', $start_time);
     $end_day = date('Y-m-d', $end_time);
     
     if ($start_day>$day && $end_day>=$start_day)
     {
        $status = $oper;
        $conn->query('insert into future_reports (u_id, start_day, end_day, status, info) values ("'.$id.'", "'.$start_day.'", "'.$end_day.'", '.$oper.', "'.$info.'")');
        for ($i=$start_time; $i<=$end_time; $i=$i+86400) 
        {
           $current_day = date('Y-m-d', $i);
           $conn->query('insert into reports (u_id, day, status, info) values ("'.$id.'", "'.$current_day.'", '.$oper.', "'.$info.'")');
        }
     }
  }
}
else
{
   $result = $conn->query('select status, start_day, end_day from future_reports where active=1 and u_id="'.$id.'" and end_day>"'.$day.'"')->fetch_row();
   if ($result!=null)
   {
      $status = $result[0];
      $start_day = $result[1];
      $end_day = $result[2];
   }
}

if ($status>0)
{
   $date_str = 'דיווח עתידי  '.date('j/n', strtotime($end_day));
   if ($start_day != $end_day) $date_str .= ' - '.date('j/n', strtotime($start_day));
}

echo $_GET['callback'].'('.json_encode(array(id=>$id, date_str=>$date_str, day=>$day, status=>$status, futurePage=>true )).');';
$conn->close();
?>