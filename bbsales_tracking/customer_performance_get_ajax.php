<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
if($_REQUEST['mode']=="prospect")
{
	$ProspectR = $db->rp_getData("no_order_inquiry","*","dealer_id='".$_REQUEST['customer_id']."' AND isDelete=0 AND inquiry_lead_flag='-1'","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Raw Data No</th>
					<th>Source Of Medium</th>
					<th>Raw Data Date</th>
					<th>Raw Data Taken By</th>
					<th>Raw Data Assign To</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($ProspectR)
				{
					while($prospectD = mysqli_fetch_assoc($ProspectR))
					{
						$status_array = array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","5"=>"Cold","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"11");
						?>
							<tr>
								<td>#INQ/<?= $prospectD['id']; ?></td>
								<td><?= $db->rp_getValue("source_of_inquiry","name","id='".$prospectD['source_of_inquiry']."'"); ?></td>
								<td><?php if($prospectD['created_date']!="1970-01-01" && $prospectD['created_date']!="0000-00-00" ){ echo date('d-m-Y',strtotime($prospectD['created_date']));}else{echo ""; }?>
								</td>
								<td><?= $db->rp_getValue("sales_executive","name","id='".$prospectD['inquiry_created_by']."'"); ?></td> 	
								<td><?= $db->rp_getValue("sales_executive","name","id='".$prospectD['inquiry_assign_to']."'"); ?></td>
								<td><?= $status_array[$prospectD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="inquiry")
{
	$InquiryR = $db->rp_getData("no_order_inquiry","*","dealer_id='".$_REQUEST['customer_id']."' AND isDelete=0 AND inquiry_lead_flag='0'","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Inquiry No</th>
					<th>Source Of Medium</th>
					<th>Inquiry Date</th>
					<th>Inquiry Taken By</th>
					<th>Inquiry Assign To</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($InquiryR)
				{
					while($InquiryD = mysqli_fetch_assoc($InquiryR))
					{
						$status_array = array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","5"=>"Cold","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"11");
						?>
							<tr>
								<td>#INQ/<?= $InquiryD['id']; ?></td>
								<td><?= $db->rp_getValue("source_of_inquiry","name","id='".$InquiryD['source_of_inquiry']."'"); ?></td>
								<td><?php if($InquiryD['inquiry_date']!="1970-01-01" && $InquiryD['inquiry_date']!="0000-00-00" ){ echo date('d-m-Y',strtotime($InquiryD['inquiry_date']));}else{echo ""; }?>
								</td>
								<td><?= $db->rp_getValue("sales_executive","name","id='".$InquiryD['inquiry_created_by']."'"); ?></td> 	
								<td><?= $db->rp_getValue("sales_executive","name","id='".$InquiryD['inquiry_assign_to']."'"); ?></td>
								<td><?= $status_array[$InquiryD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="lead")
{
	$LeadR = $db->rp_getData("no_order_inquiry","*","dealer_id='".$_REQUEST['customer_id']."' AND isDelete=0 AND inquiry_lead_flag='1'","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Lead No</th>
					<th>Source Of Medium</th>
					<th>Lead Date</th>
					<th>Lead Taken By</th>
					<th>Lead Assign To</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($LeadR)
				{
					while($LeadD = mysqli_fetch_assoc($LeadR))
					{
						$status_array = array("0"=>"Generate","1"=>"In Followup","-1"=>"Not Interested","3"=>"Working","-2"=>"Non Relavent Inquiry","5"=>"Cold","8"=>"Will Interested","9"=>"Not Working","10"=>"Not Doing Business","11"=>"11");
						?>
							<tr>
								<td>#INQ/<?= $LeadD['id']; ?></td>
								<td><?= $db->rp_getValue("source_of_inquiry","name","id='".$LeadD['source_of_inquiry']."'"); ?></td>
								<td><?php if($LeadD['inquiry_date']!="1970-01-01" && $LeadD['inquiry_date']!="0000-00-00" ){ echo date('d-m-Y',strtotime($LeadD['inquiry_date']));}else{echo ""; }?>
								</td>
								<td><?= $db->rp_getValue("sales_executive","name","id='".$LeadD['inquiry_created_by']."'"); ?></td> 	
								<td><?= $db->rp_getValue("sales_executive","name","id='".$LeadD['inquiry_assign_to']."'"); ?></td>
								<td><?= $status_array[$LeadD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="followup")
{
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<?php
		$refrence_id = $db->rp_getValue("no_order_inquiry","id","dealer_id='".$_REQUEST['customer_id']."' AND isDelete=0",0);
		if($refrence_id != "")
		{
			$FollowupR = $db->rp_getData("followup","*","reference_id='".$refrence_id."' AND isDelete=0","id DESC",0);
			//$FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 ","id DESC",0);
			?>
			<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
				<thead>
					<tr>
						<th>Date and Time</th>
						<th>Description</th>
						<th>Through</th>
						<th>Response Date</th>
						<th>Response</th>
						<th>status</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($FollowupR)
					{
						while($FollowupD = mysqli_fetch_assoc($FollowupR))
						{
							$Followup_status_array = array("0"=>"Cancel","1"=>"Responded");
							$followupthrough_array = array("1"=>"Call","2"=>"Sms","3"=>"Email");

						if($FollowupD['reference_table'] == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$FollowupD['reference_id']."' AND inquiry_lead_flag = '0'",0))
	                    {
	                        $slag = "Inquiry";
	                    }

	                    else if ($FollowupD['reference_table']  == "no_order_inquiry" && $db->rp_getValue("no_order_inquiry","id","id='".$FollowupD['reference_id']."' AND inquiry_lead_flag = '-1'",0)) {
	                        $slag= "Prospects";
	                    }

	                    else if ( $FollowupD['reference_table']  == "no_order_inquiry" &&  $db->rp_getValue("no_order_inquiry","id","id='".$FollowupD['reference_id']."' AND inquiry_lead_flag = '1'",0)) {
	                        $slag= "Leads";
	                    }

						else if ($FollowupD['reference_table'] == "sales_executive") {
							$slag = "Sales Officer";
						}
						else if ($FollowupD['reference_table'] == "customer_inquiry") {
							
							$slag = "Customer Inquiry";
						}
						else if ($FollowupD['reference_table'] == "quotation_followup") {
							
							$slag = "Quotation";
						}
						else if ($FollowupD['reference_table'] == "executive") {
							
							$slag = "Executive";
						}
						else if ($FollowupD['reference_table'] == "customer_inquiry") {
							
							$slag = "Executive";
						}
						else if ($FollowupD['reference_table'] == "quotation_detail") {
							
							$slag = "Quotation";
						}

							?>
								<tr>
									<td><?php if($FollowupD['followup_date']!="1970-01-01" && $FollowupD['followup_date']!="0000-00-00" ){ echo date('d-m-Y',strtotime($FollowupD['followup_date']));}else{echo ""; }?>
									</td>
									<td><?php echo $FollowupD['description']; ?></td>
									<td><?php echo $followupthrough_array[$FollowupD['through']]?></td>
									<td><?php if($FollowupD['response_date']!="1970-01-01" && $FollowupD['response_date']!="0000-00-00 00:00:00" ){ echo date('d-m-Y',strtotime($FollowupD['response_date']));}else{echo ""; }?>
									<td><?php echo $FollowupD['response']; ?></td>
									<td><?php echo $Followup_status_array[$FollowupD['status']]; ?></td>
									<td><?php echo $slag; ?></td>
								</tr>
							<?php
						}
					}
					?>
				</tbody>
			</table>
			<?php
		}
		else
		{
			?>
			<tr><td colspan="6" class="text-center">No Followup found!!</td></tr>
			<?php
		}
		?>
	</div>
	<?php
}
else if($_REQUEST['mode']=="quotation")
{
	$QuotationR = $db->rp_getData("quotation_detail","*","customer_id='".$_REQUEST['customer_id']."' AND isDelete=0
		","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Inquiry No.</th>
					<th>Quotation No</th>
					<th>Quotation Date</th>
					<th>Quotation Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($QuotationR)
				{
					while($QuotationD = mysqli_fetch_assoc($QuotationR))
					{
						$quotationstatus_array = array("0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated","-2"=>"Disapproved");
						?>
							<tr>
								<td><?= "#INQ/" . $QuotationD['inquiry_id']; ?></td>
								<td><a href="quotation_viewer.php?quotation_id=<?= $QuotationD['id'] ?>" target="_blank" title="View Order"><?php echo stripslashes($QuotationD['quotation_no'])."  ".$repeated_str; ?></a></td>
								<td><?php echo date('d-m-Y', strtotime($QuotationD['quotation_date'])); ?></td>
								<td align="right"><?php echo stripslashes(CURR . $db->rp_num(round($QuotationD['grand_total']))); ?></td>
								<td><?= $quotationstatus_array[$QuotationD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="order")
{
	$OrderR = $db->rp_getData("orders","*","customer_id='".$_REQUEST['customer_id']."' AND isDelete=0
		","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Order No</th>
					<th>Quotation No</th>
					<th>Order Date</th>
					<th>Order Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($OrderR)
				{
					while($OrderD = mysqli_fetch_assoc($OrderR))
					{
						$orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Ready For Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Ready For Partially Dispatched");
						?>
							<tr>
								<td><a href="order_viewer.php?order_id=<?= $OrderD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $OrderD['order_no']; ?></span></a></td>
								<td><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$OrderD['quotation_id']."'",0) ?></td>
								<td><?php echo date('d-m-Y',strtotime($OrderD['order_date'])); ?></td>
								<td class="text-right"><?php echo CURR.round($OrderD['grand_total']); ?></td>
								<td><?= $orders_status[$OrderD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="invoice")
{
	$InvoiceR = $db->rp_getData("invoice_new","*","customer_id='".$_REQUEST['customer_id']."' AND isDelete=0
		","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Invoice No</th>
					<th>Dispatch No</th>
					<th>Invoice Date</th>
					<th>Invoice Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($InvoiceR)
				{
					while($InvoiceD = mysqli_fetch_assoc($InvoiceR))
					{
						$invoice_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
						?>
							<tr>
								<td><a href="invoice_viewer.php?invoice_id=<?= $InvoiceD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $InvoiceD['invoice_no']; ?></span></a></td>
								<td><?php echo $db->rp_getValue("dispatch_detail","dispatch_no","id='".$InvoiceD['dispatch_ids']."'",0) ?></td>
								<td><?php if($InvoiceD['invoice_date']!=""){ echo date('d-m-Y',strtotime($InvoiceD['invoice_date'])); }else{ echo "";}?></td>
								<td><?php echo CURR.$InvoiceD['grand_total']; ?></td>
								<td><?= $invoice_status[$InvoiceD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="pending_order")
{
	$POrderR = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND status=0
		","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Order No</th>
					<th>Quotation No</th>
					<th>Order Date</th>
					<th>Order Amount</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($POrderR)
				{
					while($POrderD = mysqli_fetch_assoc($POrderR))
					{
						$orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Ready For Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Ready For Partially Dispatched");
						?>
							<tr>
								<td><a href="order_viewer.php?order_id=<?= $POrderD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $POrderD['order_no']; ?></span></a></td>
								<td><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$POrderD['quotation_id']."'",0) ?></td>
								<td><?php echo date('d-m-Y',strtotime($POrderD['order_date'])); ?></td>
								<td><?php echo CURR.$POrderD['grand_total']; ?></td>
								<td><?= $orders_status[$POrderD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="visit")
{
	$VisitR = $db->rp_getData("visit","*","customer_id='".$_REQUEST['customer_id']."' AND isDelete=0","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Customer Name</th>
					<th>Mobile No</th>
					<th>Date & Time</th>
					<th>Address</th>
					<th>Remark</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($VisitR)
				{
					while($VisitD = mysqli_fetch_assoc($VisitR))
					{
						?>
							<tr>
								<td><?php echo $db->rp_getValue("executive","cname","id='".$VisitD['customer_id']."'") ?></td>
								<td><?php echo $db->rp_getValue("executive","phone","id='".$VisitD['customer_id']."'") ?></td>
								<td><?php echo date("d-m-Y H:i:s",strtotime($VisitD['created_date'])); ?></td>
								<td><?php echo $VisitD['app_address']; ?></td>
								<td><?php echo stripslashes($VisitD['remark']); ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
else if($_REQUEST['mode']=="complain")
{
	$ComplainR = $db->rp_getData("complain","*","customer_id='".$_REQUEST['customer_id']."' AND isDelete=0","id DESC",0);
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Complain No.</th>
					<th>Date and Time</th>
					<th>Customer Name</th>
					<th>Source of complain</th>
					<th>Complain Category</th>
					<th>Complain Sub Category</th>
					<th>Description</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($ComplainR)
				{
					while($ComplainD = mysqli_fetch_assoc($ComplainR))
					{
						$complin_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
						$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
						?>
							<tr>
								<td><?php echo "#".stripslashes($ComplainD['complain_no']); ?></td>
								<td><?php echo date("d-m-Y h:i A",strtotime($ComplainD['created_date'])); ?></td>
								<td><?php echo $db->rp_getValue("executive","cname","id='".$ComplainD['customer_id']."'")."<br/>".$db->rp_getValue("executive","phone","id='".$ComplainD['customer_id']."'") ?></td>
								<td><?php echo stripslashes($complain_type_array[$ComplainD['complain_type']]); ?></td>
								<td><?php echo  $db->rp_getValue("complain_category","name","id='".$ComplainD['complain_cat_id']."'"); ?></td>
	                			<td><?php echo  $db->rp_getValue("complain_sub_category","name","id='".$ComplainD['complain_subcat_id']."'");?></td>
	                			<td><?php echo $ComplainD['remark']; ?></td>
	                			<td><?php echo $complin_array[$ComplainD['status']]; ?></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
	<?php
}
?>
<?php require_once 'disconnect.php';  ?>