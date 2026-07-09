<?php

$page_id=400;$page_slug='dashboard';
include("connect.php");
$date = date("d-m-Y");
extract($_REQUEST);

$applied_date = isset($applied_date)?date("d-m-Y",strtotime($applied_date)):"";
$dup_check = $db->rp_dupCheck("pricelist_replace_record","old_pricelist_id='".$old_price_list."' AND change_flag=0",0);

if($old_price_list != "" && $new_price_list !="")
{
	if ($applied_date != "" && !empty($applied_date )) 
	{
		if ($applied_date === $date) 
		{
			$check_customer=$db->rp_getTotalRecord("executive","isDelete=0 AND price_list_id='".$old_price_list."'",0);
			if($check_customer > 0)
			{
				if ($dup_check) 
				{
					$ack=array("ack"=>0,"ack_msg"=>"This PriceList Is Already Assign For Change Of Another PriceList");
					echo json_encode($ack);
				}
				else
				{
					$pr_record_rows = array("old_pricelist_id","new_price_list_id","applied_date","change_flag");
					$pr_record_values = array($old_price_list,$new_price_list,date("Y-m-d",strtotime($applied_date)),"1");

					$isInsert = $db->rp_insert("pricelist_replace_record",$pr_record_values,$pr_record_rows,0);

					if ($isInsert) 
					{
						$rows=array("price_list_id"=>$new_price_list);
						$is_update=$db->rp_update("executive",$rows,"isDelete=0 AND price_list_id='".$old_price_list."'",0);
						if($is_update)
						{
							$ack=array("ack"=>1,"ack_msg"=>"PriceList Successfully Changed");
							echo json_encode($ack);
						}
						else
						{
							$ack=array("ack"=>0,"ack_msg"=>"Something Went Wrong");
							echo json_encode($ack);
						}
					}
					else
					{
						$ack=array("ack"=>0,"ack_msg"=>"Something Went Wrong");
						echo json_encode($ack);
					}
				}
			}
			else
			{
				$ack=array("ack"=>0,"ack_msg"=>"Customers Not Available");
				echo json_encode($ack);
			}	
		}
		else
		{
			if ($dup_check) 
			{
				$ack=array("ack"=>0,"ack_msg"=>"This PriceList Is Already Assign For Change Of Another PriceList");
				echo json_encode($ack);
			}
			else
			{
				$pr_record_rows = array("old_pricelist_id","new_price_list_id","applied_date","change_flag");
				$pr_record_values = array($old_price_list,$new_price_list,date("Y-m-d",strtotime($applied_date)),"0");

				$isInsert = $db->rp_insert("pricelist_replace_record",$pr_record_values,$pr_record_rows);

				if($isInsert)
				{
					$ack=array("ack"=>1,"ack_msg"=>"PriceList Successfully Applied");
					echo json_encode($ack);
				}
				else
				{
					$ack=array("ack"=>0,"ack_msg"=>"Something Went Wrong");
					echo json_encode($ack);
				}
			}
		}
	}
	else
	{
		$ack=array("ack"=>0,"ack_msg"=>"Please Select Required Fileds");
		echo json_encode($ack);
	}
}
else
{
	$ack=array("ack"=>0,"ack_msg"=>"Please Select Required Fileds");
	echo json_encode($ack);
}
require_once "disconnect.php";

?>