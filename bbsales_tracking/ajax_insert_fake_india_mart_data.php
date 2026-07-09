<?php
/*$page_id=605;$page_slug='customer_inquiry';
include("connect.php");
$custData = $db->rp_getData("customer_inquiry","*","isDelete=0");
while ($custDataD = mysqli_fetch_assoc($custData))
{
	$id = $custDataD['id'];
	unset($custDataD['id']);
	$db->rp_getQuery("INSERT INTO customer_inquiry (" .implode(",",array_keys($custDataD)). ") SELECT " .implode(",",array_keys($custDataD)). " FROM customer_inquiry WHERE id = '".$id."'",0);
}*/
?>