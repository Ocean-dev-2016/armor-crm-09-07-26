<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");

include("../include/no_to_word.php");
$dispatch_id=$_REQUEST['id'];
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("dispatch_detail","*",$ctable_where,"",0);
$d="";
$discount="";

?>
<div id="print_info">
<div class="row">
<div class="col-sm-12">
<table id="datatable_2"  style="border-collapse:collapse;width:700px;" class="table table-striped table-bordered table-hover">
	<thead>
	<tr><th colspan="6" class="bg-grey" style="text-align:center;">Dispatch Information</th></tr>
            <tr>
                <th><b>No.</b></th>
                <th><b>Customer Name</b></th>
                <th><b>Dispatch No.</b></th>
                <th style="text-align:right;"><b>Total Amount</b></th>
				<th style="text-align:right;"><b>Remaining Amount</b></th>
                <th style="text-align:right;"><b>Paid Amount</b></th>
				
                
            </tr>
        </thead>		
	<tbody>
	<?php
	
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            $total=0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
				$grand_total=CURR.$db->rp_num($ctable_d['grand_total']);
        ?>
			<tr>
				<td><?php echo $count;  ?></td>
				<td><?php echo $ctable_d['customer_name'];  ?></td>
				<td><?php echo $ctable_d['dispatch_no'];  ?></td>
				<td align="right"><?php echo $grand_total;  ?></td>
				<td align="right"><?php echo  CURR.$db->rp_num($ctable_d['remaining_amount']);  ?></td>
				<td align="right"><?php echo  CURR.$db->rp_num($ctable_d['paid_amount']); ?></td>
				
				
				
			</tr>
			
			<?php
			$total+=$ctable_d['paid_amount'];
				}
			}else{
				?>
				<td colspan="6" style="text-align:center;">No Data Available</td>
				<?php
			}
			?>
			<tr>
				<td colspan="5" style="text-align:right;"><b>Total Amount:</b></td>
				<td align="right" ><?php echo CURR.$db->rp_num($total); ?></td>
			</tr>
			
			
			</tbody>
			</table>
			</div>

			</div>
			<div class="row">
			<div class="col-sm-12">
			
			<table id="datatable_3"  style="border-collapse:collapse;width:700px;" class="table table-striped table-bordered table-hover">
				<thead>
				<tr><th colspan="6" style="text-align:center;" class="bg-grey">Payment Information</th></tr>
						<tr>
							<th><b>No.</b></th>
							<th><b>Customer Name</b></th>
							<th><b>Payment_date</b></th>
							<th><b>Payment Type</b></th>
							<th style="text-align:right;"><b>Paid Amount</b></th>
							
							
						</tr>
					</thead>		
				<tbody>
				<?php
				$ctable_where1	= "dispatch_id='".$_REQUEST['id']."' AND isDelete=0";
				$payment_r = $db->rp_getData("payment","*",$ctable_where1,"",0);
					if(mysqli_num_rows($payment_r)>0){
						$count = 0;
						$total=0;
						while($payment_d = mysqli_fetch_array($payment_r)){
							$count++;
							$customer_name=$db->rp_getValue("dispatch_detail","customer_name","id='".$_REQUEST['id']."' AND isDelete=0");
							//$grand_total=CURR.$db->rp_num($payment_d['grand_total']);
					?>
				<tr>
					<td><?php echo $count;  ?></td>
					<td><?php echo $customer_name;  ?></td>
					<td><?php echo  date('d-m-Y',strtotime($payment_d['payment_date']));  ?></td>
					<td><?php echo  $payment_d['payment_type'];  ?></td>
					<td align="right"><?php echo  CURR.$db->rp_num($payment_d['paid_amount']); ?></td>
				</tr>
				
				<?php
				$total+=$payment_d['paid_amount'];
					}
				}else{
					?>
					<td colspan="5" style="text-align:center;">No Data Available</td>
					<?php
				}
				?>
				<tr>
					<td colspan="4" style="text-align:right;"><b>Total Amount:</b></td>
					<td align="right" ><?php echo CURR.$db->rp_num($total); ?></td>
				</tr>
				
				
				</tbody>
				</table>
			</div>

			</div>
			
</div>
			<div class="row">
		<div class="col-md-2">
			<a onClick="paymentPrintPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="paymentGenReport('<?php echo $dispatch_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>