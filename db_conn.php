<?php
   $conn = new mysqli('127.0.0.1', 'xxx', 'xxx', 'report_db');
   $conn->query("SET NAMES 'utf8'");
   $conn->query("SET CHARACTER SET utf8");

   return $conn;
?>
