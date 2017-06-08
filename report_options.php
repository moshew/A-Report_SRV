<?php
header('Content-Type: application/javascript; charset=utf-8');

$options = array();
$options[12] = array(id=>12, title=>'בסיפוח', info=>true);
$options[13] = array(id=>13, title=>'חופשת אבל', info=>false);
$options[14] = array(id=>14, title=>'טיפול פוריות', info=>false);
$options[15] = array(id=>15, title=>'מחלת הורה', info=>false);
$options[16] = array(id=>16, title=>'מחלת בן/בת זוג', info=>false);
$options[17] = array(id=>17, title=>'הריון או לידת בת זוג', info=>false);
$options[18] = array(id=>18, title=>'מחלה חריגה', info=>false);
$options[19] = array(id=>19, title=>'מחלת הורים חריגה', info=>false);
$options[20] = array(id=>20, title=>'מחלת בן זוג חריגה', info=>false);
$options[21] = array(id=>21, title=>'מחלת ילד חריגה', info=>false);
$options[22] = array(id=>22, title=>'חופשה בריאותית', info=>false);
$options[23] = array(id=>23, title=>'נעדר משירות שלא ברשות', info=>false);
$options[24] = array(id=>24, title=>'כלוא', info=>false);
$options[25] = array(id=>25, title=>'מאושפז', info=>false);
$options[26] = array(id=>26, title=>'נעדר', info=>false);
$options[27] = array(id=>27, title=>'תרומת מח עצם', info=>false);
$options[28] = array(id=>28, title=>'מיוחדת ללא תשלום', info=>false);
$options[29] = array(id=>29, title=>'חופשת נישואים', info=>false);
$options[30] = array(id=>30, title=>'חופשה משפחתית חובה', info=>false);
$options[31] = array(id=>31, title=>'חופשת חובה בקבע', info=>false);
$options[32] = array(id=>32, title=>'חל"ד ללא תשלום', info=>false);
$options[33] = array(id=>33, title=>'נהגים מבצעיים', info=>false);
$options[34] = array(id=>34, title=>'חו"ל בתפקיד', info=>false);
$options[35] = array(id=>35, title=>'חו"ל דח"ש', info=>false);
$options[36] = array(id=>36, title=>'חו"ל בריאותי', info=>false);
$options[37] = array(id=>37, title=>'חו"ל במיוחדת', info=>false);
$options[38] = array(id=>38, title=>'חו"ל חל"ת', info=>false);
$options[39] = array(id=>39, title=>'חו"ל ע"ח חובה', info=>false);
$options[40] = array(id=>40, title=>'נגרע ממתין לקליטה', info=>false);

echo $_GET['callback'].'('.json_encode($options).');';
?>