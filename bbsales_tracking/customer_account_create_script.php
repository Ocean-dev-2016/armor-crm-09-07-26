<?php
include('connect.php');
require_once("../include/class.executive.php");
$objClass= new Executive();
$ctable_r = $db->rp_getData("executive","*","isDelete=0","",0);
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

			$date = "01";
			$month = "04";
    		$year  = date('Y');
    		// $time = date('h:i:s');
			$created_date = $year."-".$month."-".$date;
			$transaction_date = $year."-".$month."-".$date;
			// get financial year date
// echo $ctable_d['openinig_balance'];exit;
			$debit=$ctable_d['openinig_balance']*-1;
			$description = $created_date." Opening Balance RS ".$ctable_d['openinig_balance'];

			$acc_id = $db->rp_getValue("account","id","cid='".$ctable_d['id']."'");
			$acc_no = $db->rp_getValue("account","acc_no","cid='".$ctable_d['id']."'");

			if($ctable_d['credit_debit_type']==1)
			{
				$rows = array(
					"reference_table",
					"reference_id",
					"cid",
					"account_id",
					"account_no",
					"opening",
					"amount",
					"credit",
					"debit",
					"type",
					"description",
					"payment_date",
				);
				$value =  array(
					"customer",
					$ctable_d['id'],
					$ctable_d['id'],
					$acc_id,
					$acc_no,
					1,
					$ctable_d['openinig_balance'],
					$ctable_d['openinig_balance'],
					"",
					1,
					$description,
					$transaction_date,
				);
				
				$insert = $db->rp_insert("account_transaction",$value,$rows,0);
			}
			else
			{
				$rows = array(
					"reference_table",
					"reference_id",
					"cid",
					"account_id",
					"account_no",
					"opening",
					"amount",
					"credit",
					"debit",
					"type",
					"description",
					"payment_date",
				);
				$value =  array(
					"customer",
					$ctable_d['id'],
					$ctable_d['id'],
					$acc_id,
					$acc_no,
					1,
					$ctable_d['openinig_balance'],
					"",
					$debit,
					2,
					$description,
					$transaction_date,
				);

				$insert = $db->rp_insert("account_transaction",$value,$rows,0);
			}
		}
	}
}
?>