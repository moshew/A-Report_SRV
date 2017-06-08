<?php
if (!isset($_GET['id'])) exit();
$id = $_GET['id'];

if ($conn==null)
{
   header('Content-Type: application/javascript; charset=utf-8');
   $conn = require('db_conn.php');
   $report_id = $id;
   $title = 'דיווח עתידי';
   $future_manage = true;
}

$day = Date('Y-m-d');

$info = str_replace('"', '\"', $_GET['info']);
if ($info=='undefined') $info='';

$status = array(code=>'success');
if (isset($_GET['oper'])) {
  $oper = intval($_GET['oper']);
  if ($oper==-1) {
      $conn->query('update reports, future_reports set reports.active=0, future_reports.active=0 where reports.u_id="'.$report_id.'" and day>"'.$day.'" and day>=start_day and day<=end_day and future_reports.id='.$_GET['task_id']);
  } else {
     $start_time = strtotime($_GET['start_day']);
     $end_time = strtotime($_GET['end_day']);
     
     $start_day = date('Y-m-d', $start_time);
     $end_day = date('Y-m-d', $end_time);

     if ($start_day>$day && $end_day>=$start_day) {
        if ($oper==0 && ($end_time-$start_time)/86400>=14) $status = array(code=>'error', info=>'לא ניתן להזין דיווח "נוכח/ת" עתידי, עבור למעלה מ-14 יום');
        elseif ($conn->query('select id from future_reports where u_id="'.$report_id.'" and active=1 and id not in (select id from future_reports where start_day>"'.$end_day.'" or end_day<"'.$start_day.'")')->num_rows == 0) {
           $conn->query('insert into future_reports (u_id, start_day, end_day, status, info) values ("'.$report_id.'", "'.$start_day.'", "'.$end_day.'", '.$oper.', "'.$info.'")');
           for ($i=$start_time; $i<=$end_time; $i=$i+86400) {
              $current_day = date('Y-m-d', $i);
              $conn->query('insert into reports (u_id, day, status, info) values ("'.$report_id.'", "'.$current_day.'", '.$oper.', "'.$info.'")');
           }
        } else $status = $status = array(code=>'error', info=>'לא ניתן להזין תאריכים חופפים');
     } else $status = array(code=>'error', info=>'תקלה בהזנת פרמטרי הדיווח');
  }
}

$future_tasks = array();
$items = $conn->query('select id, status, start_day, end_day, info from future_reports where active=1 and u_id="'.$report_id.'" and end_day>"'.$day.'"');
while($item=$items->fetch_assoc()) {
  $date_str = date('j/n', strtotime($item[end_day]));
  if ($item[start_day] != $item[end_day]) $date_str.=' - '.date('j/n', strtotime($item[start_day]));
  array_push($future_tasks, array(id=>$item[id], date_str=>$date_str, status=>$item[status], info=>$item[info]));
}

$result = array(id=>$id, title=>$title, status=>$status, future_tasks=>$future_tasks);

if ($future_manage)
{
  $conn->close();
  echo $_GET['callback'].'('.json_encode($result).');';
}

return $result;
?>