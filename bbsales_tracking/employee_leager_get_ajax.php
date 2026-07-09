<?php
$page_id=634;$page_slug='customer_leager';
include("connect.php");
$ctable = "employee_account_transaction";
$ctable1 = "customer_leager";

$ctable_where="";
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{  
  $todate= $_REQUEST['ToDate'];
  // $ctable_where .= " AND transaction_date <='".date('Y-m-d',strtotime($_REQUEST['ToDate']))."' ";
  $ctable_where .= " AND payment_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
} 
if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{ 
  $fromdate= $_REQUEST['FromDate'];
  $ctable_where .= " AND payment_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}


if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!=""){
	// $account_id=$db->rp_getValue("sales_executive","id","id='".$_REQUEST['account_id']."'",0);
	$ctable_where .= " AND sales_id='".$_REQUEST['sales_id']."' ";
}  

// if(isset($_REQUEST['account_id']) && $_REQUEST['account_id']!=""){
// 	$account_id=$db->rp_getValue("sales_executive","id","id='".$_REQUEST['account_id']."'",0);
// 	$ctable_where .= " AND account_id='".$account_id."' ";
// }  
// if(isset($_REQUEST['account_id']) && $_REQUEST['account_id']!=""){
// 	$account_id=$db->rp_getValue("account","id","cid='".$_REQUEST['account_id']."'",0);
// 	$ctable_where .= " AND account_id='".$account_id."' ";
// }  
  
$ctable_r = $db->rp_getData($ctable,"*","isDelete=0 AND isActive=1 AND payment_date!=0000-00-00".$ctable_where,"payment_date ASC",0);  
// $AccountInfo=$system->GetEmployeeAccountInfo($account_id);
$AccountInfo=$system->GetEmployeeAccountInfo($_REQUEST['sales_id']);
if($_REQUEST['sales_id']!="")
{ 
	$sales_executive_id = $db->rp_getValue("executive","sales_executive_id","id=".$AccountInfo['id']."",0);
	$responsibleperson = $db->rp_getValue("sales_executive","name","id=".$sales_executive_id.""); 
	?> 
	<form action="" name="frm"  method="post">
		<table id="datatable_1" class="table table-striped table-bordered table-hover" width="100%">
			<tr>
				<td colspan="8" align="center"><h4><strong>Employee Account Report</strong></h4></td>
			</tr>
			<tr>
				<!-- <td colspan="2"><b> Account No. -    </b><?php echo " :<b> ".$AccountInfo['acc_no']."</b><br/>"; ?></td> -->
				<td colspan="4"><b> Employee Name - </b><?php echo " :<b> ".$AccountInfo['username']."</b>"; ?></td>
				<td colspan="2"><b>From: </b><?php echo $fromdate; ?></td>
				<td align="right" colspan="2"><b>To: </b><?php echo $todate; ?></td>
			</tr>
		</table>

		<table id="datatable_1" class="table table-striped table-bordered table-hover" width="100%">
			<thead>
				<tr>
			        <?php
			        // for today opening  
			        $fDate = date('d-m-Y',strtotime($_REQUEST['FromDate']));  
			        $today_opening_date = date('Y-m-d', strtotime($fDate. ' - 1 days')); 
			        $today_opening_debit1 = $db->rp_getValue("employee_account_transaction","SUM(debit)","sales_id='".$_REQUEST['sales_id']."' AND payment_date<='".$today_opening_date."' AND isDelete=0",0); 
			        $today_opening_credit1 = $db->rp_getValue("employee_account_transaction","SUM(credit)","sales_id='".$_REQUEST['sales_id']."' AND payment_date<='".$today_opening_date."' AND isDelete=0",0);
			        
			        $closing_amount1 = $today_opening_credit1 - (-0-$today_opening_debit1);
			       
			        if($closing_amount1>0 && $closing_amount1!=0)
			        {  
			          $today_opening_credit = $closing_amount1;
			          $today_opening_debit = 0;
			        }
			        else if($closing_amount1!=0 && $closing_amount1<0)
			        { 
			          $today_opening_credit = 0;
			          $today_opening_debit = -0-$closing_amount1; 
			        }
			        else
			        { 
			          $today_opening_credit = 0;
			          $today_opening_debit = 0;
			        }
			        ?>
			        <td></td>
			        <td></td> 
			        <td class="text-right"><strong>Opening Balance</strong></td>
			        <td class="text-right"><strong><?= $today_opening_debit ?></strong></td><td class="text-right"><strong><?= $today_opening_credit ?></strong></td>
			        <!-- <th class="text-center">Balance</th>-->
		      	</tr>
	            <tr>
	                <th>Sr No.</th>
					<th>Transaction Date</th>
					<th>Description</th>
					<th>Debit<p style="font-size: 12px;">(Company's <br>Paid/Given Expense)</p></th>
					<th>Credit<p style="font-size: 12px;">(Company's <br>Pass Expense)</p></th>			
				</tr>
	        </thead>
	        <tbody> 
		        <?php 
	            $count = 0;
	            $grand_total=0;
				$total_credit=0;
				$total_debit=0;
				$total_debit1=0;
				$closing_amount=0;
				if($ctable_r)
				{  
		            while($ctable_d = mysqli_fetch_array($ctable_r))
		            {
		            	?>
			            <tr>
			            	<td><?php echo ++$count;  ?></td>
							<td><?php echo date('d-m-Y',strtotime($ctable_d['payment_date'])); ?></td>
							<td> <?= $ctable_d['description'];  ?></td>
							<?php
							if($ctable_d['debit']<0)
							{ 
								$total_debit+=$ctable_d['debit'];
								$total_debit1 = -0-$total_debit;
								?> 
								<td align="right"><?php echo 0-$ctable_d['debit']; ?></td>
								<td> </td>
								<?php
							}
							if($ctable_d['credit']>0)
							{ 
								$total_credit+=$ctable_d['credit'];
								?> 
								<td> </td>
								<td align="right"><?php echo stripslashes($ctable_d['credit']); ?></td> 
								<?php
							}
							?>
			            </tr> 
			       		<?php
	       			} 
					?>
					<tr>
						<td colspan="3" align="right"><b>Total</b></td>
						<td align="right"><b><?php echo round(-0-$total_debit,2);?></b></td>
						<td align="right"><b><?php echo $total_credit;?></b></td>
					</tr> 
					<tr>
						<td colspan=3 align="right"><b>Closing Balance</b></td>
					 	<?php 
					 	$closing_amount = ($total_credit + $today_opening_credit) - ($total_debit1 + $today_opening_debit); 
				        ?>
				        <td style="text-align: right!important;"><b><?= ($closing_amount<0)?-0-$closing_amount:""; ?></b></td>
				        <td style="text-align: right!important;"><b><?= ($closing_amount>0)?$closing_amount:""; ?></b></td>
					</tr>
					<?php
				}
				else
				{
					?>
					<tr><td colspan="5" class="text-center">No data found for this date!!</td></tr>
					<?php
				}
				?>  
	        </tbody> 
	    </table> 
	</form>
	<?php
}
else
{ 
	?> 
	<h3 class="text-center" style="margin: 0;padding: 10px;">Please Select Customer Too See Ledger</h3>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>