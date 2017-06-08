<?PHP
date_default_timezone_set ('Asia/Jerusalem');

$day = Date('N');
if ($day==5 || $day==6) exit();

function sendMessage($key1, $val1, $msg){

    $fields = array(
      'app_id' => "9e0291cd-9d82-4a5e-a5c7-a2ad63a89e27",
      'included_segments' => array('All'),
      'data' => array("foo" => "bar"),
      'contents' => array("en" => $msg),
      'tags' => array(array("key"=>$key1, "relation"=> "=", "value"=> $val1))
    );
    
    $fields = json_encode($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                           'Authorization: Basic ZTEyODc2NzctODI4Ni00NTNmLWIyZjUtZGJiNGVkMGY3YTZi'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}

$conn = require('db_conn.php');

$sql = 'select id from users where g_id=4 and id not in (select u_id from reports where day="'.Date('Y-m-d').'" and active=1)';
$items = $conn->query($sql);
while($item=$items->fetch_assoc()) {
    sendMessage('id', $item[id], 'בוקר טוב, תשומת ליבך שטרם הזנת סטאטוס נוכחות הבוקר');
}

$sql = 'select id from users where g_id=4 and forms_request=1';
$items = $conn->query($sql);
while($item=$items->fetch_assoc()) {
    sendMessage('id', $item[id], 'תשומת ליבך שטרם העברת את כל הטפסים לאישור ימי ההעדרות שהזנת');
}

$conn->close();

?>