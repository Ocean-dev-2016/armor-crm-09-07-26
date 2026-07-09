<?php
$page_id=603;$page_slug='salesexecutive_performance_report_page';
include("connect.php");
if($_REQUEST['mode']=="prospect")
{
	$ProspectR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='-1'","id DESC",0);
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$ProspectR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='-1' AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$ProspectR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='-1'","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<style type="text/css">
			.no-border td
			{
			    border:none!important;   
			    font-size: 20px;
			    /*font-weight: 300;*/
			}
			.no-border td.value
			{
			     /*font-size: 20px;
			    font-weight: 700;*/
			}
			.pad-0
			{
			    padding: 0px!important;
			}
		</style>
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcel('<?= $_REQUEST['sales_id'] ?>','prospect')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count = 1;
					while($prospectD = mysqli_fetch_assoc($ProspectR))
					{
						$status_array = array("0"=>"Generated","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"Not Interested","-2"=>"Non Relavent","11"=>"Lost");
						?>
							<tr>
								<td><?= $count++; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$InquiryR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='0' AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$InquiryR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='0' ","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<style type="text/css">
			.no-border td
			{
		    border:none!important;   
		    font-size: 20px;
		    /*font-weight: 300;*/
			}
			.no-border td.value
			{
		    /*font-size: 20px;
		    font-weight: 700;*/
			}
			.pad-0
			{
			  padding: 0px!important;
			}
		</style>
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcel('<?= $_REQUEST['sales_id'] ?>','inquiry')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count = 1;
					while($InquiryD = mysqli_fetch_assoc($InquiryR))
					{
						$status_array = array("0"=>"Generated","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"Not Interested","-2"=>"Non Relavent","11"=>"Lost");
						?>
							<tr>
								<td><?= $count++; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$LeadR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='1' AND inquiry_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND inquiry_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$LeadR = $db->rp_getData("no_order_inquiry","*","inquiry_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND inquiry_lead_flag='1'","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<style type="text/css">
			.no-border td
			{
		    border:none!important;   
		    font-size: 20px;
		    /*font-weight: 300;*/
			}
			.no-border td.value
			{
		    /*font-size: 20px;
		    font-weight: 700;*/
			}
			.pad-0
			{
			  padding: 0px!important;
			}
		</style>
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcel('<?= $_REQUEST['sales_id'] ?>','lead')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count = 1;
					while($LeadD = mysqli_fetch_assoc($LeadR))
					{
						$status_array = array("0"=>"Generated","1"=>"In Followup","3"=>"Buy Later","4"=>"Hot","5"=>"Cold","6"=>"Warm","-1"=>"Not Interested","-2"=>"Non Relavent","11"=>"Lost");
						?>
							<tr>
								<td><?= $count++; ?></td>
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
	$FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 ","id DESC",0);
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND DATE(followup_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND DATE(followup_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$FollowupR = $db->rp_getData("followup","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 ","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<style type="text/css">
			.no-border td
			{
			  border:none!important;   
			  font-size: 20px;
			  /*font-weight: 300;*/
			}
			.no-border td.value
			{
			  /*font-size: 20px;
			  font-weight: 700;*/
			}
			.pad-0
			{
			  padding: 0px!important;
			}
		</style>
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcel('<?= $_REQUEST['sales_id'] ?>','lead')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
					<th>Date and Time</th>
					<th>Description</th>
					<th>Through</th>
					<th>Response Date</th>
					<th>Response</th>
					<th>status</th>
					<th>Type of Follow up</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($FollowupR)
				{
					$count = 1;
					while($FollowupD = mysqli_fetch_assoc($FollowupR))
					{
						$Followup_status_array = array("0"=>"Cancel","1"=>"Responded");
						$followupthrough_array = array("1"=>"Call","2"=>"Sms","3"=>"Email");

						// if($FollowupD['reference_table'] == "no_order_inquiry")
						// {
						// 	$slag = "Inquiry";
						// }

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
							$slag = "Sales Person";
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
								<td><?= $count++; ?></td>
								<td><?php if($FollowupD['followup_date']!="1970-01-01" && $FollowupD['followup_date']!="0000-00-00" ){ echo date('d-m-Y',strtotime($FollowupD['followup_date']));}else{echo ""; }?>
								</td>
								<td><?php echo $FollowupD['description']; ?></td>
								<td><?php echo $followupthrough_array[$FollowupD['through']]?></td>
								<td><?php if($FollowupD['response_date']!="1970-01-01" && $FollowupD['response_date']!="0000-00-00 00:00:00" ){ echo date('d-m-Y',strtotime($FollowupD['response_date']));}else{echo ""; }?></td>
								<td><?php echo $FollowupD['response']; ?></td>
								<td><?php echo $Followup_status_array[$FollowupD['status']]; ?></td>
							
								<td><?php echo $slag; ?></td>
								<!-- <td><?php echo $Followup_status_array[$FollowupD['status']]; ?></td> -->
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
else if($_REQUEST['mode']=="quotation")
{
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$QuotationR = $db->rp_getData("quotation_detail","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND quotation_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND quotation_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$QuotationR = $db->rp_getData("quotation_detail","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelQOI('<?= $_REQUEST['sales_id'] ?>','quotation')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count=1;
					while($QuotationD = mysqli_fetch_assoc($QuotationR))
					{
						$quotationstatus_array = array("0"=>"Pending","1"=>"Approved","3"=>"Cancelled","-1"=>"Add to Cart","4"=>"Order Generated","-2"=>"Disapproved","5"=>"Lost");
						?>
							<tr>
								<td><?= $count++; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$OrderR = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0
		AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$OrderR = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0
		","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelQOI('<?= $_REQUEST['sales_id'] ?>','order')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count=1;
					while($OrderD = mysqli_fetch_assoc($OrderR))
					{
						$orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Ready For Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Ready For Partially Dispatched");
						?>
							<tr>
								<td><?= $count++; ?></td>
								<td><a href="order_viewer.php?order_id=<?= $OrderD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $OrderD['order_no']; ?></span></a></td>
								<td><?php echo $db->rp_getValue("quotation_detail","quotation_no","id='".$OrderD['quotation_id']."'",0); ?></td>
								<td><?php echo date('d-m-Y',strtotime($OrderD['order_date'])); ?></td>
								<td><?php echo CURR.$OrderD['grand_total']; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$InvoiceR = $db->rp_getData("invoice_new","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND invoice_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND invoice_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' 
		","id DESC",0);
	}
	else
	{
		$InvoiceR = $db->rp_getData("invoice_new","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0
		","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelQOI('<?= $_REQUEST['sales_id'] ?>','invoice')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
					<th>Invoice No</th>
					<th>Dispatch No</th>
					<th>Invoice Date</th>
					<th>Invoice Amount Without GST</th>
					<th>Invoice Amount With GST</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($InvoiceR)
				{
					$count=1;
					while($InvoiceD = mysqli_fetch_assoc($InvoiceR))
					{
						$invoice_status = array("0"=>"Pending","1"=>"Approved","2"=>"Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Partially Dispatched");
						?>
							<tr>
								<td><?= $count++; ?></td>
								<td><a href="invoice_viewer.php?invoice_id=<?= $InvoiceD['id'] ?>" target="_blank" title="View Order"><span class="text-success"><?php echo $InvoiceD['invoice_no']; ?></span></a></td>
								<td><?php echo $db->rp_getValue("dispatch_detail","dispatch_no","id='".$InvoiceD['dispatch_ids']."'",0) ?></td>
								<td><?php if($InvoiceD['invoice_date']!=""){ echo date('d-m-Y',strtotime($InvoiceD['invoice_date'])); }else{ echo "";}?></td>
								<td><?php echo CURR.$InvoiceD['taxable']; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$POrderR = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND status=0 AND order_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND order_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'","id DESC",0);
	}
	else
	{
		$POrderR = $db->rp_getData("orders","*","sales_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND status=0","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelQOI('<?= $_REQUEST['sales_id'] ?>','pending_order')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count=1;
					while($POrderD = mysqli_fetch_assoc($POrderR))
					{
						$orders_status = array("0"=>"Pending","1"=>"Approved","2"=>"Ready For Dispatch","3"=>"Cancelled","-1"=>"Add to Cart","-2"=>"Disapproved","4"=>"Ready For Partially Dispatched");
						?>
							<tr>
								<td><?= $count++; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$VisitR = $db->rp_getData("visit","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "'","id DESC",0);
	}
	else
	{
		$VisitR = $db->rp_getData("visit","*","user_id='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<style type="text/css">
			.no-border td
			{
		    border:none!important;   
		    font-size: 20px;
		    /*font-weight: 300;*/
			}
			.no-border td.value
			{
		    /*font-size: 20px;
		    font-weight: 700;*/
			}
			.pad-0
			{
			  padding: 0px!important;
			}
		</style>
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelvisit('<?= $_REQUEST['sales_id'] ?>','lead')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count=1;
					while($VisitD = mysqli_fetch_assoc($VisitR))
					{
						?>
							<tr>
								<td><?= $count++; ?></td>
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
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$ComplainR = $db->rp_getData("complain","*","complain_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0 AND created_date <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND created_date >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$ComplainR = $db->rp_getData("complain","*","complain_assign_to='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<style type="text/css">
			.no-border td
			{
			    border:none!important;   
			    font-size: 20px;
			    /*font-weight: 300;*/
			}
			.no-border td.value
			{
			     /*font-size: 20px;
			    font-weight: 700;*/
			}
			.pad-0
			{
			    padding: 0px!important;
			}
		</style>
		<button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelcomplain('<?= $_REQUEST['sales_id'] ?>','lead')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button>
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>Sr No</th>
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
					$count=1;
					while($ComplainD = mysqli_fetch_assoc($ComplainR))
					{
						$complin_array = array("0"=>"Generate","1"=>"In Progress","2"=>"Complete","-1"=>"Reject","-2"=>"Not Done");
						$complain_type_array = array("1"=>"Email","2"=>"Call","3"=>"Whatsapp");
						?>
							<tr>
								<td><?= $count++; ?></td>
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
else if($_REQUEST['mode']=="payment_collection")
{
	if($_REQUEST['ToDate']!="" && $_REQUEST['FromDate']!=="")
	{
		$PaymentR = $db->rp_getData("payment","*","sales_executive_id='".$_REQUEST['sales_id']."' AND isDelete=0 AND DATE(payment_date) <= '" . date_format(date_create($_REQUEST['ToDate']), "Y-m-d") . "' AND DATE(payment_date) >= '" . date_format(date_create($_REQUEST['FromDate']), "Y-m-d") . "' ","id DESC",0);
	}
	else
	{
		$PaymentR = $db->rp_getData("payment","*","sales_executive_id='".$_REQUEST['sales_id']."' AND isDelete=0","id DESC",0);
	}
	?>
	<div class="portlet-body" style="margin-top: 20px;">
			<style type="text/css">
			.no-border td
			{
			    border:none!important;   
			    font-size: 20px;
			    /*font-weight: 300;*/
			}
			.no-border td.value
			{
			     /*font-size: 20px;
			    font-weight: 700;*/
			}
			.pad-0
			{
			    padding: 0px!important;
			}
		</style>
		<!-- <button type="button" class="btn print btn-sm pull-right" style="background-color: #f0ad4e;color: #fff;margin-bottom: 10px;margin-top: -22px;" name="print" onClick="ExportExcelcomplain('<?= $_REQUEST['sales_id'] ?>','lead')" id="print" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export Excel</button> -->
		<table class="table table-striped table-bordered mt-15">
			<tr class="no-border">
        <td>Sales Person Type : - <?= $db->rp_getValue("sales_executive","type","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person Name : - <?= $db->rp_getValue("sales_executive","name","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
      <tr class="no-border">
        <td>Sales Person State : - <?= $db->rp_getValue("sales_executive","state","id='".$_REQUEST['sales_id']."'") ?></td>
        <td>Sales Person City : - <?= $db->rp_getValue("sales_executive","city","id='".$_REQUEST['sales_id']."'") ?></td>
      </tr>
		</table>
		<table class="table table-striped table-hover table-bordered customer_assigned_to_history" id="customer_assigned_to_history">
			<thead>
				<tr>
					<th>No.</th>
          <th>Company Name</th>
          <th>Customer Name</th>
          <th>Sales Person Name</th>
          <th>Receipt No.</th>
          <th>Payment by</th>
          <th>Payment Date</th>
          <th>Payment Amount</th>
          <th>Status</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($PaymentR)
				{
					$count=1;
					while($PaymentD = mysqli_fetch_assoc($PaymentR))
					{
						$payment_status = array("0"=>"Pending","1"=>"Approved");
          	$payment_type = array("1"=>"By Cash","2"=>"By Cheque","3"=>"Online","4"=>"Other");
						?>
							<tr>
		            <td><?php echo ++$count; ?></td> 
		            <td><?php echo $db->rp_getValue("executive","company_name","id='".$PaymentD['customer_id']."'");?></td>
		            <td><?php echo $db->rp_getValue("executive","cname","id='".$PaymentD['customer_id']."'");?></td>
		            <td><?php echo $db->rp_getValue("sales_executive","name","id='".$PaymentD['sales_executive_id']."'");?></td>
		            <td><?php echo stripslashes($PaymentD['receipt_no']); ?></td>
		            <td><?php echo stripslashes($payment_type[$PaymentD['payment_type']]); ?></td>
		            <td><?php echo date('d-m-Y',strtotime($PaymentD['payment_date'])); ?></td>
		            <td align="right"><?php echo stripslashes(CURR.($db->rp_num($PaymentD['paid_amount']))); ?></td>
		            <td><?php echo stripslashes($payment_status[$PaymentD['payment_status']]); ?></td>
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
require_once("disconnect.php"); 
?>