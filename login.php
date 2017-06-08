<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

if (!isset($conn) || $conn==null) {
   header('Content-Type: application/javascript; charset=utf-8');
   $conn = require('db_conn.php');
   $login_manage = true;
}

date_default_timezone_set ('Asia/Jerusalem');
$days_db = array('שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת', 'ראשון');
$month_db = array('ינואר', 'פברואר', 'מרץ', 'אפריל', 'מאי', 'יוני', 'יולי', 'אוגוסט', 'סמפטמבר', 'אוקטובר', 'נובמבר', 'דצמבר');
$date_str = date('יום '.$days_db[intval(Date('N'))-1].', j ב'.$month_db[intval(Date('n'))-1].' Y');
$day = Date('Y-m-d');

$id=-1;
$status = -1;
$lock = 0;

if (isset($_GET['delete'])) setcookie('id', null, -1, '/');
elseif (isset($_GET['id'])) {
  $id = $_GET['id'];
  if (strlen($id)==5) {
	 //echo 'select u.id from users u inner join temp_login l on u.id=l.u_id where l.reg_time>DATE_SUB(NOW(), INTERVAL 10 minute) and l.temp_id='.$_GET['id'];
     $result = $conn->query('select u.id from users u inner join temp_login l on u.id=l.u_id where l.reg_time>DATE_SUB(NOW(), INTERVAL 10 minute) and l.temp_id='.$_GET['id'])->fetch_row();
     if ($result!=null) $id = $result[0];
  }
}
elseif (isset($_COOKIE['id'])) $id = $_COOKIE['id']; 

$result = $conn->query('select g_id, permission_request, manager, admin, message_status, forms_request from users where id="'.$id.'"')->fetch_row();
if ($result!=null)
{
	$g_id = $result[0];
    $settings = array('permission_request'=>$result[1]==1, 'manager'=>$result[2]==1, 'admin'=>$result[3]==1, 'message_status'=>$result[4], 'forms_request'=>$result[5]==1);
    setcookie('id', $id, time() + (86400 * 30), '/'); // 86400 = 1 day

    $result = $conn->query('select status from reports where u_id="'.$id.'" and day="'.$day.'" and active=1')->fetch_row();
    if ($result!=null) $status=intval($result[0]);
    
    if ($conn->query('select day from locked where day="'.$day.'" and g_id='.$g_id)->num_rows == 1) $lock = 1;
    
} else {
    $id=-1;
    setcookie('id', null, -1, '/');
}

$result = array('id'=>$id, 'date_str'=>$date_str, 'day'=>$day, 'status'=>$status, 'lock'=>$lock, 'settings'=>$settings, 'ver'=>3.2);

if ($login_manage)
{
  $conn->close();
  echo $_GET['callback'].'('.json_encode($result).');';
}

return $result;
?>