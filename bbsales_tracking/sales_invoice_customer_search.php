<?php
$page_id=435;$page_slug='page_sales_invoice';
include('connect.php');
$ctable='customer';
$customer=array();
///get Customer details
$where = "id='".$_REQUEST['customer_id']."' AND isDelete=0"; 
 $customer_id_r=$db->rp_getData("customer","*",$where,"",0);
 
if($customer_id_r){
	while($row_customer = mysqli_fetch_assoc($customer_id_r) ){
		$data=array();		
		$row= array("customer_id"=>$row_customer['id'],"customer_name"=>$row_customer['customer_name'], "customer_city"=>$row_customer['customer_city'],"customer_phone"=>$row_customer['customer_phone'],"customer_email"=>$row_customer['customer_email'],"customer_address"=>$row_customer['customer_address']);
		$customer=$row;
		}
}
echo json_encode(array("result"=>array("customer"=>$customer)),true);
?>