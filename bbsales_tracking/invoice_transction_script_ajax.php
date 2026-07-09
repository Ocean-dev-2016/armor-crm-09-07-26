<?php 
include('connect.php');
require_once("../include/class.executive.php");
$objClass= new Executive();
$ctable_r = $db->rp_getData("invoice_new","*","isDelete=0 AND status=1","",0);
if($ctable_r)
{
	while($invoice_data_d = mysqli_fetch_assoc($ctable_r))
	{
		$invoice_id = $invoice_data_d['id'];
		require_once('../include/class.system.php');
		$system = new System();

		$AccountInfo=$db->rp_getData("account","*","cid='".$invoice_data_d['customer_id']."'","",0);
		$AccountInfo=mysqli_fetch_assoc($AccountInfo);
		// $AccountInfo=$system->GetAccountInfo($invoice_data_d['customer_id']);
		if($AccountInfo)
		{
			$AccountID=$AccountInfo['id'];
			$AccountNo=$AccountInfo['acc_no'];
			$Columns=array("cid","account_id","account_no","type","debit","amount","reference_id","reference_table","description","payment_date");
			$debit="-".$db->rp_getValue("invoice_new","grand_total","id='".$invoice_id."'",0);
			$grand_total=$db->rp_getValue("invoice_new","grand_total","id='".$invoice_id."'",0);
			// $payment_date=date('Y-m-d');
			$payment_type = 0;
			$remark = "Invoice Entry Of Invoice No. <a target='_blank' href='invoice_viewer.php?invoice_id=".$invoice_id."'>". $db->rp_getValue("invoice_new","invoice_no","id='".$invoice_id."'",0)."</a>";
			$Values=array($invoice_data_d['customer_id'],$AccountID,$AccountNo,$payment_type,$debit,$grand_total,$invoice_id,"invoice",$remark,$invoice_data_d['invoice_date']);
			/*entry account transaction*/
			$TransctionID=$db->rp_insert("account_transaction",$Values,$Columns,0);
		}
	}
}

?>
<?php require_once 'disconnect.php';  ?>