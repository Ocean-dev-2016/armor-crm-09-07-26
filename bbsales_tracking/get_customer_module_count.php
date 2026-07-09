<?php 
include ('connect.php');
$id=$_REQUEST['id'];
// echo $id;exit;


$total_prospect=$db->rp_getTotalRecord("no_order_inquiry","inquiry_type=-1 AND isDelete=0 AND dealer_id = '".$id."' ",0);
if($total_prospect==0)
{
	$total_prospect=$db->rp_getTotalRecord("no_order_inquiry"," isDelete=0 AND id = '".$id."' AND inquiry_type=-1 ",0);
}

// $total_inquiry=$db->rp_getTotalRecord("no_order_inquiry","inquiry_lead_flag=1 AND isDelete=0 AND dealer_id = '".$id."' ",0);
// if($total_inquiry==0)
// {
// 	$total_inquiry=$db->rp_getTotalRecord("no_order_inquiry"," isDelete=0 AND id = '".$id."' AND inquiry_lead_flag=1 ",0);
// }
$total_inquiry=$db->rp_getTotalRecord("no_order_inquiry","inquiry_type=0 AND isDelete=0 AND dealer_id = '".$id."' ",0);
if($total_inquiry==0)
{
	$total_inquiry=$db->rp_getTotalRecord("no_order_inquiry"," isDelete=0 AND id = '".$id."' AND inquiry_type=0 ",0);
}
$total_lead=$db->rp_getTotalRecord("no_order_inquiry","inquiry_type=1 AND isDelete=0 AND dealer_id = '".$id."' ",0);
if($total_lead==0)
{
	$total_lead=$db->rp_getTotalRecord("no_order_inquiry"," isDelete=0 AND id = '".$id."' AND inquiry_type=1 ",0);
}
$total_followup=$db->rp_getTotalRecord("followup"," isDelete=0 AND visitor_id = '".$id."' ",0);
$total_visit=$db->rp_getTotalRecord("visit"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_complain=$db->rp_getTotalRecord("complain"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_quotation=$db->rp_getTotalRecord("quotation_detail"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_order=$db->rp_getTotalRecord("orders"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_dispatch=$db->rp_getTotalRecord("dispatch_detail"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_packing_slip=$db->rp_getTotalRecord("packing_slip"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_invoice_new=$db->rp_getTotalRecord("invoice_new"," isDelete=0 AND customer_id = '".$id."' ",0);
$total_payment=$db->rp_getTotalRecord("payment"," isDelete=0 AND customer_id = '".$id."' ",0);
$count = 0;

?>
<style type="text/css">
	th , td, tr{
		border: 2px solid #EFEFEF;
		text-align: center;
	}
</style>
	<div class="row">
		<br/>
		<div class="col-md-12" style="margin: 0 8px;">
			<div class="col-md-1"></div>
			<div class="col-md-10">
				<table class="table table-hover" style="border: 1px solid #EFEFEF;">
				<thead>
			        <tr>
						<th><strong>Module Name</strong></th>
						<th><strong>Total Count</strong></th>
					</tr>
			       
			    </thead>
			    <tbody>
			    	<tr>
						<td>Prospect</td>
						<td><?=$total_prospect;?></td>
			    	</tr>
			    	<tr>
						<td>Inquiry</td>
						<td><?=$total_inquiry;?></td>
			    	</tr>
			    	<tr>
						<td>Lead</td>
						<td><?=$total_lead;?></td>
			    	</tr>
			    	<tr>
						<td>Followup</td>
						<td><?=$total_followup;?></td>
			    	</tr>
			    	<tr>
						<td>Visit</td>
						<td><?=$total_visit;?></td>
			    	</tr>
			    	<tr>
						<td>Complain</td>
						<td><?=$total_complain;?></td>
			    	</tr>
			    	<tr>
						<td>Quotation</td>
						<td><?=$total_quotation;?></td>
			    	</tr>
			    	<tr>
						<td>Order</td>
						<td><?=$total_order;?></td>
			    	</tr>
			    	<tr>
						<td>Dispatch</td>
						<td><?=$total_dispatch;?></td>
			    	</tr>
			    	<tr>
						<td>Packing slip</td>
						<td><?=$total_packing_slip;?></td>
			    	</tr>
			    	<tr>
						<td>Invoice</td>
						<td><?=$total_invoice_new;?></td>
			    	</tr>
			    	<tr>
						<td>Payment</td>
						<td><?=$total_payment;?></td>
			    	</tr>
			    </tbody>
			</table>
			</div>
			
			<div class="col-md-1"></div>
		</div>
	</div>
