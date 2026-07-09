<?php
require_once('connect_in.php');
$ctable_where = $_REQUEST['where'];
$mode = $_REQUEST['mode'];

if ($mode == 'quotation') 
{
	$t_quotation = $db->rp_getTotalRecord("quotation_detail",$ctable_where,0);
	$t_quotation_val = $db->rp_getValue("quotation_detail","SUM(grand_total)",$ctable_where,0); 
	$t_quotation_val_approve = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=1 AND ".$ctable_where,0); 
	$t_quotation_val_pending = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=0 AND ".$ctable_where,0); 
	$t_quotation_val_cancel = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=3 AND ".$ctable_where,0); 
	$t_quotation_val_lost = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=5 AND ".$ctable_where,0); 
	$t_quotation_val_disapprove = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=-2 AND ".$ctable_where,0); 
	$t_quotation_val_order = $db->rp_getValue("quotation_detail","SUM(grand_total)","isDelete=0 AND status=4 AND ".$ctable_where,0);
	?>
	<table class="table_amount table-striped table-bordered table-hover">
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Total Quotation </strong></td>
			<td style="padding-right: 10px;" id="total-quotation"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Approve Quotation Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value-approve"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Pendind Quotation Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value-pending"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Cancel Quotation Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value-cancel"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Lost Quotation Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value-lost"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Disapprove Quotation Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value-disapprove"></td>
		</tr>
		<tr>
			<td style="text-align: right;padding-right: 10px;"><strong>Order Generated Quotation Total Amount </strong></td>
			<td style="padding-right: 10px;" id="total-quotation-value-order"></td>
		</tr>
	</table>
				
	<script type="text/javascript">
		$("#total-quotation").html("<strong><?php echo $t_quotation; ?></strong>");

		$("#total-quotation-value").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val),2); ?></strong>");

		$("#total-quotation-value-approve").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val_approve),2); ?></strong>");

		$("#total-quotation-value-pending").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val_pending),2); ?></strong>");

		$("#total-quotation-value-cancel").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val_cancel),2); ?></strong>");

		$("#total-quotation-value-lost").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val_lost),2); ?></strong>");

		$("#total-quotation-value-disapprove").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val_disapprove),2); ?></strong>");

		$("#total-quotation-value-order").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_quotation_val_order),2); ?></strong>");

	</script>

	<?php
}
else if($mode == 'order')
{
	//$t_order_val += $ctable_d['grand_total']; 
	$t_orders = $db->rp_getTotalRecord("orders",$ctable_where,0);
	$t_order_val = $db->rp_getValue("orders","SUM(grand_total)",$ctable_where,0); 
	$t_order_val_approve = $db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND status=1 AND".$ctable_where,0); 
	$t_order_val_pending = $db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND status=0 AND".$ctable_where,0); 
	$t_order_val_cancel = $db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND status=3 AND".$ctable_where,0);
	$t_order_val_disapprove = $db->rp_getValue("orders","SUM(grand_total)","isDelete=0 AND status=-2 AND".$ctable_where,0);
	?>
		<table class="table_amount table-striped table-bordered table-hover">
			
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Total Order </strong></td>
				<td style="padding-right: 10px;" id="total-order"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Approve order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-approve"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Pending order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-pending"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Cancel order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-cancel"></td>
			</tr>
			<tr>
				<td style="text-align: right;padding-right: 10px;"><strong>Disapprove order Total Amount </strong></td>
				<td style="padding-right: 10px;" id="total-order-value-disapprove"></td>
			</tr>
		</table>
		<script type="text/javascript">
			$("#total-order").html("<strong><?php echo $t_orders; ?></strong>");

			$("#total-order-value").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_order_val),2); ?></strong>");
			$("#total-order-value-approve").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_order_val_approve),2); ?></strong>");
			$("#total-order-value-pending").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_order_val_pending),2); ?></strong>");
			$("#total-order-value-cancel").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_order_val_cancel),2); ?></strong>");
			$("#total-order-value-disapprove").html("<strong><?php echo CURR.$db->rp_number_format(stripslashes($t_order_val_disapprove),2); ?></strong>");
		</script>
	<?php
}

else if($mode == 'customer')
{
	//echo "jrtr44";exit();

	?>
	<table class="table_amount table-striped table-bordered table-hover">
	  <tr>
		<td style="text-align: left;padding-right: 10px;"><button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-lg blue dropdown-toggle" style="background-color: #FFB6C1; margin-bottom: 5px;"></button> <b>INQUIRY </b></td>		
	   </tr>
	    <tr>
		<td style="text-align: left;padding-right: 10px;"><button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-lg  dropdown-toggle" style="background-color: #FF9377; margin-bottom: 5px;"></button><b> ORDERS PENDING FOR PAYMENT STATUS</b></td>		
	   </tr>
	    <tr>
		<td style="text-align: left;padding-right: 10px;"><button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-lg blue dropdown-toggle" style="background-color: #ADD8E6; margin-bottom: 5px;"></button><b> UNTOCHED DATA &nbsp;</b></td>		
	   </tr>
	    <tr>
		<td style="text-align: left;padding-right: 10px;"><button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-lg dropdown-toggle" style="background-color: #AEDCAE; margin-bottom: 5px;"></button> <b>RUNNING CUSTOMERS  OR CONFIRM ORDER</b></td>		
	   </tr>
		<!-- <div class="col-md-5 col-xs-5 col-sm-5">
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<br>

		<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-lg blue dropdown-toggle" style="background-color: #ADD8E6; margin-bottom: 5px;"></button><b> UNTOCHED DATA &nbsp;</b>
		<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-lg dropdown-toggle" style="background-color: #AEDCAE; margin-bottom: 5px;"></button> <b>RUNNING CUSTOMERS  OR CONFIRM ORDER</b> -->
	 <!--  </div> -->
	</table>
	<?php

}
?>
