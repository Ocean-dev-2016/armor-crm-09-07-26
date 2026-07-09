<?php
$page_id=400;$page_slug='dashboard';
include('connect.php');

$customer_id=$_REQUEST['customer_id'];
$type=$_REQUEST['type'];
$d=file_get_contents(SITEURL."service/service_visit.php?key=1226&s=213&user_id=&customer_id=".$customer_id."&type=".$type."&company_id=");
$result=json_decode($d,true);
echo json_encode($result);

// print_r($d);exit;
// print_r($result);exit;

// $reply=array("current_month_customer_cnt"=>$result['current_month_customer_cnt'],"total_customer_cnt"=>$result['total_customer_cnt']);
// echo json_encode($reply); 
?>                                                                                              