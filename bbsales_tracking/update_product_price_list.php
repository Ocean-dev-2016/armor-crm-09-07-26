<?php
$page_id=580;$page_slug='price_list_master';
require_once("connect.php");
$price_list_id=$_REQUEST['price_list_id'];
$tcid=$_REQUEST['tcid'];
$cid=$_REQUEST['cid'];
$discount=$_REQUEST['discount'];
$mode=$_REQUEST['mode'];

/*$tcids_r=$db->rp_getData("product","id","tcid='".$tcid."' AND cid='".$cid."' AND isDelete=0");
$PID_TCID=array();
if($tcids_r)
{
	while($k=mysqli_fetch_assoc($tcids_r))
	{
		$PID_TCID[]=$k['id'];
	}
	$PID_TCID=implode(",",$PID_TCID);
}
$where ="isDelete=0 AND product_id IN (".$PID_TCID.")";*/
$dt=date("Y-m-d H:i:s");
/*if($mode=="percentage")
{
	$mrp_price=$_REQUEST['mrp'];	
	
    $check_record1=$db->rp_getTotalRecord("product_price_list","price_list_id='".$price_list_id."' AND pid='".$pid."' AND weight_id='".$weight_id."' AND isDelete=0",0);
	if($check_record1==0)
	{
		// insert
		$discounted_price=$discount;
		$discounted_amount=$mrp_price-$discounted_price;
		$discount=($discounted_amount*100)/$mrp_price;
		$row=array(
			"tcid",
			"cid",
			"pid",
			"weight_id",
			"price",
			"price_list_id",
			"discounted_price",
			"discounted_amount",
			"discount_type",
			"discount",
		);
		$values=array(
			$tcid,
			$cid,
			$pid,
			$weight_id,
			$mrp_price,
			$price_list_id,
			$discounted_price,
			$discounted_amount,
			"1",
			round($discount,2),
		);

		$insert_id=$db->rp_insert("product_price_list",$values,$row,0);
	}
	else
	{
		// update
		// $discounted_amount=($mrp_price*$discount)/100;
		// $discounted_price=$mrp_price-$discounted_amount;
		$discounted_price=$discount;
		$discounted_amount=$mrp_price-$discounted_price;
		$discount=($discounted_amount*100)/$mrp_price;
		$row_value=array(
			"tcid"=>$tcid,
			"cid"=>$cid,
			"price"=>$mrp_price,
			"discounted_price"=>$discounted_price,
			"discounted_amount"=>$discounted_amount,
			"discount"=>round($discount,2),
			"discount_type"=>"1",
		);			

		$updated_id=$db->rp_update("product_price_list",$row_value,"price_list_id='".$price_list_id."' AND pid='".$pid."' AND weight_id='".$weight_id."'",0);
	}
}
else*/ if($mode=="net")
{
	$pid=$_REQUEST['pid'];
	$weight_id=$_REQUEST['weight_id'];
	$mrp_price=$_REQUEST['mrp_price'];
	$discount_type=$_REQUEST['discount_type'];
	$check_record1=$db->rp_getTotalRecord("product_price_list","price_list_id='".$price_list_id."' AND pid='".$pid."' AND weight_id='".$weight_id."' AND isDelete=0",0);
	// $cid=$db->rp_getValue("product","cid","id='".$pid."' AND isDelete=0");
	if($check_record1==0)
	{
		// insert
		$discounted_price=$discount;
		$discounted_amount=round(($mrp_price-$discounted_price),4);
		$discount=($discounted_amount*100)/$mrp_price;
		$row=array(
			"tcid",
			"cid",
			"pid",
			"weight_id",
			"price",
			"price_list_id",
			"discounted_price",
			"discounted_amount",
			"discount_type",
			"discount",
		);
		$values=array(
			$tcid,
			$cid,
			$pid,
			$weight_id,
			$mrp_price,
			$price_list_id,
			$discounted_price,
			$discounted_amount,
			$_REQUEST['discount_type'],
			round($discount,2),
		);

		$insert_id=$db->rp_insert("product_price_list",$values,$row,0);
	}
	else
	{
		// update
		$discounted_price=$discount;
		$discounted_amount=round(($mrp_price-$discounted_price),4);
		$discount=($discounted_amount*100)/$mrp_price;
		$row_value=array(
			"tcid"=>$tcid,
			"cid"=>$cid,
			"price"=>$mrp_price,
			"discounted_price"=>$discounted_price,
			"discounted_amount"=>$discounted_amount,
			"discount"=>round($discount,2),
			"discount_type"=>$_REQUEST['discount_type'],
			"modified_date"=>$dt,
		);			

		$updated_id=$db->rp_update("product_price_list",$row_value,"price_list_id='".$price_list_id."' AND pid='".$pid."' AND weight_id='".$weight_id."'",0);
	}

	// check in order if item in added to cart
	$user_r=$db->rp_getData("customer","id","price_list_id='".$price_list_id."' AND isDelete=0");
	if($user_r)
	{
		$USERIDS=array();
		while($user_d=mysqli_fetch_assoc($user_r))
		{
			$USERIDS[]=$user_d['id'];
		}
		$USERIDS=implode(",",$USERIDS);

		// check order 
		$order_r=$db->rp_getData("orders","id","customer_id IN (".$USERIDS.") AND status=-1 AND isDelete=0");
		if($order_r)
		{
			$ORDERIDS=array();
			while($order_d=mysqli_fetch_assoc($order_r))
			{
				$ORDERIDS[]=$order_d['id'];
			}
			$ORDERIDS=implode(",",$ORDERIDS);

			$order_item_r=$db->rp_getData("order_product_item","*","pro_id='".$pid."' AND weight_id='".$weight_id."' AND order_id IN (".$ORDERIDS.") AND isDelete=0");
			if($order_item_r)
			{
				while($order_item_d=mysqli_fetch_assoc($order_item_r))
				{
					// print_r($order_item_d);
					$discounted_price=$_REQUEST['discount'];
					$discounted_amount=$mrp_price-$discounted_price;
					$discount=($discounted_amount*100)/$mrp_price;

					$totalprice=$order_item_d['pro_qty']*$discounted_price;
					$totalprice=$db->rp_num($totalprice);

					$row_value=array(
					"original_price"=>$mrp_price,
					"unitprice"=>$discounted_price,
					"totalprice"=>$totalprice,
					"discount_amount"=>$discounted_amount,
					"discount"=>round($discount,2),
					"price_list_id"=>$price_list_id,
					"price_list_price"=>$mrp_price,
					"price_list_discounted_price"=>$discounted_price,
					"price_list_discounted_amount"=>$discounted_amount,
					"price_list_discount_type"=>$discount_type,
					"price_list_discount"=>round($discount,2),
				);			

				$updated_id=$db->rp_update("order_product_item",$row_value,"id='".$order_item_d['id']."'",0);
				}
			}
		}
		// check order 
	}
	// check in order if item in added to cart
}
else if($mode=="delete")
{
	$pid=$_REQUEST['pid'];
	$weight_id=$_REQUEST['weight_id'];
	// $delete_id=$db->rp_update("product_price_list",array("isDelete"=>1),"price_list_id='".$price_list_id."' AND pid='".$pid."' AND weight_id='".$weight_id."'",0);
	$delete_id=$db->rp_delete("product_price_list","price_list_id='".$price_list_id."' AND pid='".$pid."' AND weight_id='".$weight_id."'",0);
}
else if($mode=="delete_all_price_list")
{
	$pid=$_REQUEST['pid'];

	$delete_id=$db->rp_delete("product_price_list","price_list_id='".$pid."'",0);
}
?>