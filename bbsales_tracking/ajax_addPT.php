<?php
$page_id=504;$page_slug='page_price_list';
include("connect.php");
$ctable 	= "price_list_map_test";
$pid 		= $_POST["pid"];
$tid 		= $_POST["tid"];
$price 		= $db->rp_num($_POST["price"]);
$mode 		= $_POST["mode"];
if($mode=="edit"){
	if($_REQUEST['id']!="" && $_REQUEST['id']>0 && $price!=""){
		$rows 	= array(
				"price"				=> $price,
				
			);
			
		$where	= "id='".$_REQUEST['id']."'";
		$db->rp_update($ctable,$rows,$where);
		echo "1";die;
	}else{
		echo "0";die;
	}
}else{

	if($pid!="" && $price!="" && $tid!=""){
		$dup_where = "price_list_id = '".$pid."' AND test_id = '".$tid."' AND isDelete=0";
		$r = $db->rp_dupCheck($ctable,$dup_where);
		if($r){
			echo "2";die;
		}else{
			$display_order	= $db->rp_getDisplayOrder($ctable);
			$rows 	= array(
					"price_list_id",
					"test_id",
					"price",
					
				);
			$values = array(
					$pid,
					$tid,
					$price,
					
				);
			$db->rp_insert($ctable,$values,$rows,0);
			echo "1";die;
		}
	}else{
		echo "0";die;
	}
}
require_once 'disconnect.php'; 
?>