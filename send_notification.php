<?PHP
function sendNotification($id, $message){

  $fields = array(
    'app_id' => "b329644d-2d8d-44cf-98cb-3dbe7a788610",
    'included_segments' => array('All'),
    'data' => array("foo" => "bar"),
    'contents' => array("en" => $message),
    'tags' => array(array("key"=>"id", "relation"=> "=", "value"=> $id))
  );
    
  $fields = json_encode($fields);

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json',
                         'Authorization: Basic M2U0ZDBlMTItOTA2Yy00YjZlLWI5MjgtZWI1YWE4YjJjODUy'));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_POST, TRUE);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

  $response = curl_exec($ch);
  curl_close($ch);
}
?>