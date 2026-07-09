<?php
include('connect.php');
require_once("../include/class.executive.php");
$objClass= new Executive();
$ctable_r = $db->rp_getData("executive","id","isDelete=0","",0);
if($ctable_r)
{
	while($ctable_d = mysqli_fetch_assoc($ctable_r))
	{
		$totalrecord = $db->rp_getTotalRecord("account","cid='".$ctable_d['id']."'");
		if($totalrecord>0)
		{

		}
		else
		{
			$objClass->CreateCustomerAccount($ctable_d['id']); 
		}
	}
}
?>