<?php
//echo substr(chr( mt_rand( 97 ,122 ) ) .substr( md5( time( ) ) ,1 ), 0, 16);
//exit();
$pass = array();
$conn = require('db_conn.php');
$items = $conn->query('select id from users');
while($item=$items->fetch_assoc()) {
  $pass[$item[id]] = substr(rtrim(base64_encode(md5(microtime())),"="), 0, 16);
  //substr(chr( mt_rand( 97 ,122 ) ) .substr( md5( time( ) ) ,1 ), 0, 16);
}

foreach(array_keys($pass) as $key) {
  $sql = 'update users set id="'.$pass[$key].'" where id='.$key;
  $conn->query($sql);
  $sql ='update reports set u_id="'.$pass[$key].'" where u_id='.$key;
  $conn->query($sql);
  $sql ='update future_reports set u_id="'.$pass[$key].'" where u_id='.$key;
  $conn->query($sql);
  $sql ='update notifications set sender_id="'.$pass[$key].'" where sender_id='.$key;
  $conn->query($sql);
  
}
echo json_encode($pass);
$conn->close();
?>