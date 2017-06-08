<?php
header('Content-Type: application/javascript; charset=utf-8');

function sendSms($target, $message)
{
   if (strlen($target)==8) $target = "05".$target;

   $ini = ini_set("soap.wsdl_cache_enabled","0");
   $client = new SoapClient("http://api.itnewsletter.co.il/webServices/WebServiceSMS.asmx?wsdl");

   $params = array();
   $params["un"] = "moshe.waisman@gmail.com";
   $params["pw"] = "xxx";
   $params["accid"] = "1110";
   $params["sysPW"] = "itnewslettrSMS";
   $params["t"] = date("Y-m-d H:i:s");
   
   $params["txtUserCellular"] = "0532243523";
   $params["destination"] = $target;
   $params["txtSMSmessage"] = $message;
   $params["dteToDeliver"] = "";
	
   $client->sendSMSrecipients($params)->sendSMSrecipientsResult;
}
?>
