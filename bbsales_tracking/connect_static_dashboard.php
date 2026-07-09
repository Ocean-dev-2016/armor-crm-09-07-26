<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Kolkata');
include("../include/define.php");
include("../include/function.class.php");
$db = new Admin();
$conn = $db->connect();
?>	