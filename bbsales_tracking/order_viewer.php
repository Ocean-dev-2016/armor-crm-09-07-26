<?php
$page_id=565;$page_slug='page_order';
$ctable 	= "order";
$ctable1 	= "Sales Officer Order";
$main_page 	= $ctable;
$page 		= "view_".$ctable;
$page_title = "View ".$ctable1;
require_once '../Numbers/Words.php';
require_once 'Numbers_Words_Locale_en_IN.php';
include("connect_in.php");
$classname = "Numbers_Words_Locale_en_IN" ;
$obj = new $classname; 
$admin_type=$_SESSION[SITE_SESS.'_ADMIN_TYPE'];
$flag_r=$db->rp_getData("page_admin_right","*","page_id='".$page_id."' AND admin_id='".$admin_type."' AND isDelete=0","",0);
$flag_d = mysqli_fetch_array($flag_r);

$bid 	= $_REQUEST['order_id'];
$order_status=$db->rp_getValue("orders","status","id='".$_REQUEST['order_id']."' AND isDelete=0");
$cp_order_flag_v = (int) $db->rp_getValue("orders", "channel_partner_order_flag", "id='" . (int) $bid . "' AND isDelete=0", 0);
$cp_order_mode_v = $db->rp_getValue("orders", "cp_order_mode", "id='" . (int) $bid . "' AND isDelete=0", 0);
$cp_end_cust_v = (int) $db->rp_getValue("orders", "channel_partner_customer_id", "id='" . (int) $bid . "' AND isDelete=0", 0);
$is_cp_supply_order_v = ($cp_order_flag_v === 1 && $cp_order_mode_v !== 'customer' && $cp_end_cust_v <= 0);

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> W<![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>

<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css"/>
<style type="text/css">
	 #wrapper
	{
	    width:190mm;
	    margin:0 50mm;
	}
	#wrapper {
   		 width: auto!important;
   	}

	#wrapper1 {
		max-width: 980px;
		margin: 0 auto;
		background: #fff;
	}

	#wrapper1 .quote-suggest-body {
		width: 100%;
		max-width: 250mm;
		margin: 0 auto;
		box-sizing: border-box;
	}

	#wrapper1 .quote-suggest-body table {
		width: 100% !important;
		max-width: 100% !important;
		margin: 0 !important;
	}

	#wrapper1 .quote-suggest-body td,
	#wrapper1 .quote-suggest-body th {
		height: auto !important;
		padding: 0 !important;
	}

	#wrapper1 .quote-suggest-body .qp-suggest-print-grid {
		table-layout: fixed !important;
		width: 100% !important;
	}

	#wrapper1 .quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell {
		border: 1px solid #595959 !important;
		min-height: 220px;
		height: auto;
		vertical-align: top;
		padding: 0 !important;
		box-sizing: border-box;
		overflow: visible;
	}

	#wrapper1 .qp-suggest-cell-inner {
		box-sizing: border-box;
		padding: 0 12px 6px;
		width: 100%;
		height: auto;
	}

	#wrapper1 .qp-suggest-print-box {
		display: block;
		width: 100%;
		min-height: 220px;
		height: auto;
		box-sizing: border-box;
		page-break-inside: avoid;
		break-inside: avoid-page;
		overflow: visible;
	}

	#wrapper1 .qp-prod-card,
	#wrapper1 .qp-prod-card td {
		border: none !important;
	}

	#wrapper1 .qp-prod-badge-row {
		height: 34px;
		padding: 4px 8px 0 !important;
		text-align: right !important;
	}

	#wrapper1 .qp-prod-badge-bar {
		display: flex;
		align-items: center;
		justify-content: flex-end;
		gap: 6px;
		width: 100%;
		min-height: 30px;
	}

	#wrapper1 .qp-prod-disc-label {
		display: inline-block;
		border: 1px solid #d9534f;
		color: #d9534f;
		font-size: 10px;
		font-weight: bold;
		line-height: 1.2;
		padding: 2px 6px;
		background: #fff;
		white-space: nowrap;
	}

	#wrapper1 .qp-prod-disc {
		display: inline-block;
		width: 30px;
		height: 30px;
		line-height: 30px;
		border-radius: 50%;
		background: #e74c3c;
		color: #fff;
		font-size: 9px;
		font-weight: bold;
		text-align: center;
	}

	#wrapper1 .qp-prod-img-cell {
		background: #f7f7f7;
		border-bottom: 1px solid #e8e8e8 !important;
		height: 66px;
		padding: 3px !important;
		text-align: center;
		vertical-align: middle !important;
	}

	#wrapper1 .qp-prod-img {
		max-height: 54px;
		max-width: 96%;
		object-fit: contain;
		vertical-align: middle;
	}

	#wrapper1 .qp-prod-code-cell {
		font-size: 11px;
		font-weight: 600;
		color: #555555 !important;
		padding: 3px 10px 0 !important;
	}

	#wrapper1 .qp-prod-name-cell {
		font-size: 11px;
		font-weight: bold;
		color: #000000 !important;
		text-transform: uppercase;
		line-height: 1.25;
		padding: 2px 10px 0 !important;
		min-height: 36px;
		max-height: 40px;
		overflow: hidden;
	}

	#wrapper1 .qp-prod-price-cell {
		padding: 2px 10px 8px !important;
		text-align: center !important;
		vertical-align: bottom !important;
		overflow: visible !important;
	}

	#wrapper1 .qp-prod-price-line,
	#wrapper1 .qp-prod-price {
		font-size: 11px;
		font-weight: bold;
		color: #0a5c24 !important;
		text-align: center;
		line-height: 1.4;
	}

	#wrapper1 .qp-prod-unit {
		display: inline;
		font-size: 10px;
		color: #333333 !important;
		font-weight: 600;
		white-space: nowrap;
		line-height: 1.4;
	}

	#wrapper1 .qp-suggest-print-header {
		background: #4a4a4a !important;
	}

	#wrapper1 .qp-suggest-print-title {
		color: #fff !important;
	}

	#wrapper1 .qp-suggest-print-subtitle {
		color: #e0e0e0 !important;
	}

	#wrapper1 .qp-suggest-cat-header {
		background: #ffeb3b !important;
		font-weight: bold;
		text-align: center;
		text-transform: uppercase;
	}

	@media print {
		#wrapper1 .qp-prod-badge-bar {
			display: flex !important;
			align-items: center !important;
			justify-content: flex-end !important;
			gap: 6px !important;
		}

		#wrapper1 .quote-suggest-body .qp-suggest-print-grid td.qp-suggest-print-cell,
		#wrapper1 .qp-prod-card,
		#wrapper1 .qp-suggest-print-box,
		#wrapper1 .qp-suggest-cell-inner {
			min-height: 220px !important;
			height: auto !important;
			overflow: visible !important;
			page-break-inside: avoid !important;
			break-inside: avoid-page !important;
		}

		#wrapper1 .qp-prod-price-line,
		#wrapper1 .qp-prod-price {
			color: #0a5c24 !important;
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}

		#wrapper1 .qp-suggest-cat-header,
		#wrapper1 .qp-suggest-print-header {
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}
	}
</style>
</head>
<body class="page-md">
<div class="transCover"><img src="assets/admin/layout/img/89.gif" alt="" style="margin-top:20%;padding-left:48%;" ></div>
<?php include("header.php"); ?>
<div class="page-container">
	
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h2><?php echo $page_title;?></h2>
				
			</div>
			<div class="page-toolbar">
			<?php   
			if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=11)
			{ 
				$check_scheme=$db->rp_getTotalRecord("order_scheme_items","isDelete=0 AND order_id='".$bid."'");
				if($check_scheme >0)
				{
				?>
				<div class="btn-group btn-theme-panel hide-app-dis">
					<a class="btn btn-success"  data-toggle="modal" data-target="#exampleModal" title="Print">Edit Scheme Item</a>
				</div>
				<?php
				}
				?>
				<?php
					if($order_status==0)
					{
						if($flag_d['approve_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
						{
				?>

				<div class="btn-group btn-theme-panel hide-app-dis">
					<a class="btn btn-success" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','1');" title="Print">Approve</a>
				</div>
				<div class="btn-group btn-theme-panel hide-app-dis">
					<a class="btn btn-danger" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','-2');" title="Print">Disapprove</a>
				</div>
				<div class="btn-group btn-theme-panel hide-app-dis">
					<a class="btn btn-danger" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','3');" title="Cancel">Cancel</a>
				</div>
				<?php
						}
					}
					?>

					<?php
					if($flag_d['print_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{
						?>
						<div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="printReport('<?php echo $bid; ?>');" title="Print">Print</a>
						</div>
						<?php
					}

					if($flag_d['pdf_download_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
					{ 
						?>
						<!-- <div class="btn-group btn-theme-panel">
							<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="genReport('<?php echo $bid; ?>');" title="Download">Download</a>
						</div> -->
						<?php
					}
					?>


					<?php
					if($order_status==1)
					{
						if($flag_d['email_flag']==1 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==0)
						{
							?>
							<!-- <div class="btn-group btn-theme-panel hide-app-dis">
								<a onclick="sendEmail('<?= $bid; ?>')" class="btn btn-success" title="Send Mail">Send Mail</a>
							</div> -->
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-success" href='#SendMail' data-title="Quotation" data-id="<?= $_REQUEST['order_id'] ?>" data-mailid="<?= $customer_mail_id ?>" data-ccmailid="<?= $customer_ccmail_id ?>" data-type="orders" data-toggle='modal'>Send Mail</a>
							</div>
							<?php
						}
					}
					?>

					<!-- direct dispatch -->
					<?php
					if($order_status==4)
					{
							if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
							{

								$get_dispatch_right=$db->rp_getValue("page_admin_right","view_flag","isDelete=0 AND page_id='569' AND admin_id='".$_SESSION[SITE_SESS.'_ADMIN_TYPE']."'",0);
								if($get_dispatch_right == 1)
								{
									?>
									<!-- 	<div class="btn-group btn-theme-panel hide-app-dis">
											<a target="blank" href="dispatch_crud.php?mode=add&order_id=<?php echo $bid ?>" class="btn btn-danger" title="Send Mail">Create Dispatch</a>
										</div> -->
										 <!-- <div class="btn-group btn-theme-panel hide-app-dis">
											<a target="blank" href="dispatch_crud_new.php?mode=add&order_id=<?php echo $bid ?>" class="btn btn-danger" title="Send Mail">Create Dispatch</a>
										</div> -->
								<?php

								}
							}
							else
							{
							?>

							<!--  <div class="btn-group btn-theme-panel hide-app-dis">
									<a target="blank" href="dispatch_crud.php?mode=add&order_id=<?php echo $bid ?>" class="btn btn-danger" title="Send Mail">Create Dispatch</a>
							</div>  -->
							<!-- <div class="btn-group btn-theme-panel hide-app-dis">
									<a target="blank" href="dispatch_crud_new.php?mode=add&order_id=<?php echo $bid ?>" class="btn btn-danger" title="Send Mail">Create Dispatch</a>
							</div> -->
 

							<?php
							}
					}
							if($order_status==1 || ($is_cp_supply_order_v && (int)$order_status===0)){
								if(isset($_REQUEST['type']) && $_REQUEST['type']==7)
								{
									$_REQUEST['type']="";
								}
							?>
								<div class="btn-group btn-theme-panel hide-app-dis">
									<a class="btn btn-warning" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','4');" title="Account Approve">Account Approve</a>
								</div>

							
							<?php

							}
							?>
							<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-info" href="orders_crud.php?order_id=<?= $bid ?>&mode=add&c_type=<?= $_REQUEST['type']?>"><span  class="text-white"><i class="fa fa-refresh"></i> Repeat Order</span></a>
							</div>

							<?php
								$pdf_attachment = $db->rp_getValue("orders","pdf_attachment","isDelete=0 AND id='".$bid."' ",0);

								if ($pdf_attachment) 
								{
									?>
									
								<div class="btn-group btn-theme-panel hide-app-dis">
									<a class="btn blue-ebonyclay" href="#pdfattachment" data-pdfid="<?=$db->rp_getValue("orders","pdf_attachment","isDelete=0 AND id='".$bid."'");?>" data-id="<?php echo  stripslashes($bid); ?>" data-toggle="modal" title="Show PDF Attachment">
										<span >
											<i class="fa fa-paperclip" aria-hidden="true"></i>
											&nbsp;PDF Attachment
										</span>
									</a>
								</div>

									<?php
								}

							?>

							<?php
							// echo $order_status;die;
								if($order_status==4){
							?>
									<div class="btn-group btn-theme-panel hide-app-dis">
								<a class="btn btn-warning" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','5');" title="Print">Dispatch</a>
							</div>
							<?php
								}
								// if($order_status==5){
								// 	$lr_detail_r=$db->rp_getData("orders","lr_image,lr_number","isDelete=0 AND id='".$order_id."'");
								// 	$lr_detail_d=mysqli_fetch_array($lr_detail_r);
								// 	$lr_image=$lr_detail_d['lr_image'];
								// 	$lr_number=$lr_detail_d['lr_number'];
								// 	if($lr_image!="" && $lr_number!="")
								// 	{
							?>

								<!-- 	<div class="btn-group btn-theme-panel hide-app-dis">
										<a class="btn btn-success" href="#lrattachment" data-id="<?php echo  stripslashes($_REQUEST['order_id']); ?>" data-toggle="modal" title="Add LR Attachment">
												<span class="text-white">
													<i class="fa fa-paperclip" aria-hidden="true"></i>
													&nbsp;LR Attachment
												</span>
											</a> -->

										<!-- <a class="btn btn-warning" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','7');" title="Print">LR Pending</a> -->
									<!-- </div> -->
							<?php
							//	}
							//}
								// if($order_status==7){
								if($order_status==5){
							?>
									<div class="btn-group btn-theme-panel hide-app-dis">
										<a class="btn btn-warning" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','6');" title="Print">Order Completed</a>
									</div>
							<?php
								}
								// $lr_detail_r=$db->rp_getData("orders","lr_number,lr_image","isDelete=0 AND id='".$bid."'");
								// $lr_detail_d=mysqli_fetch_array($lr_detail_r);
								// $lr_image=$lr_detail_d['lr_image'];
								// $lr_number=$lr_detail_d['lr_number'];
								// if($order_status==5 && $lr_image!="" && $lr_number!=""){
							?>
								<!-- 	<div class="btn-group btn-theme-panel hide-app-dis">
										<a class="btn btn-warning" href="javascript:;" onClick="OrderStatus('<?php echo $bid; ?>','6');" title="Print">Order Complate</a>
									</div> -->
							<?php
								//}

						} ?>
					<!-- direct dispatch -->
				</div>
		</div>
	</div>
	
	<div class="page-content">
		<div class="container">
			<div class="row">
				
				<div class="col-md-12" id="report_content">
					<div id="wrapper1">
						<?php   
						include("view_order_new_1.php"); 
						?>
					</div>
					
				</div>
			</div>
		</div>
	</div>
	
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Edit Scheme Items</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <table class="table table-borderd" border="1"  style=" overflow: scroll;">
			<thead>
				<tr>
					<th colspan="17"><center>Scheme Products</center></th>
				</tr>
				<tr>
					<th colspan="2">Sr.no</th>
					<th colspan="6">Product Name</th>
					<th colspan="4">Hsn Code</th>
					<th colspan="4">Pro Qty</th>
					<th colspan="4">Action</th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$get_scheme_item=$db->rp_getData("order_scheme_items","*","isDelete=0 AND order_id='".$order_id."'","",0);
				$count_items=0;
				while ($get_scheme_item_d=mysqli_fetch_assoc($get_scheme_item)) 
				{
					?>
					<tr>
						<td colspan="2"><?=++$count_items?></td>
						<td colspan="6"><?php 

									if($get_scheme_item_d['weight_id'] == -1)
									{

										echo $db->rp_getValue("product","name","isDelete=0 AND id='".$get_scheme_item_d['pro_id']."'",0);

									}
									else
									{
										echo $db->rp_getValue("product","name","isDelete=0 AND id='".$get_scheme_item_d['pro_id']."'",0).'-'.$db->rp_getValue("weight","name","id='".$get_scheme_item_d['weight_id']."' AND isDelete=0",0);
									}

									// $db->rp_getValue("product","name","isDelete=0 AND id='".$get_scheme_items_d['pro_id']."'",0).'-'.$db->rp_getValue("weight","name","id='".$get_scheme_items_d['weight_id']."' AND isDelete=0",0);
									?>

					</td>
						<td colspan="4">


							<?=$db->rp_getValue("product","hsn_code","isDelete=0 AND id='".$get_scheme_item_d['pro_id']."'",0)?>


						</td>
						<td colspan="4">
							<?php 
							$pro_qty=$db->rp_getValue("order_scheme_items","SUM(pro_qty)","isDelete=0 AND pro_id='".$get_scheme_item_d['pro_id']."' AND order_id='".$order_id."' AND weight_id='".$get_scheme_item_d['weight_id']."'",0);

							?>
							<input type="text" name="pro_qty" id="pro_qty" value="<?=$get_scheme_item_d['pro_qty']?>" class="form-control" onkeypress='validate(event)' onchange="editschemeqty(this.value,<?=$get_scheme_item_d['id']?>,'edit')">
							<!-- <?=$db->rp_getValue("order_scheme_items","SUM(pro_qty)","isDelete=0 AND pro_id='".$get_scheme_items_d['pro_id']."' AND order_id='".$order_id."' AND weight_id='".$get_scheme_items_d['weight_id']."'",0)?> -->
						</td>
						<td colspan="4">
							<a style="padding: 6px 9px 6px 9px;!important" class='delete btn btn-danger btn-sm' title='Delete' onclick="editschemeqty(this.value,<?=$get_scheme_item_d['id']?>,'delete')"><i class='fa fa-times'></i></a>
						</td>
						
					</tr>

					<?php
				}

				?>
			</tbody>
	   </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
       <!--  <button type="button" class="btn btn-primary">Save changes</button> -->
      </div>
    </div>
  </div>
</div>

<div id="lrattachment" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form role="form" action="" method="post" id="formLocation" enctype="multipart/form-data">
				<div class="modal-header ">
				  	<h4 class="modal-title model_title" id="myModalLabel1">View LR Information</h4>
			  		<button style="margin-top: -15px!important;" type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span>
			  		</button>
				</div>
					<div class="modal-body">
							 <fieldset class="form-group ">
		
					 		<input type="file" name="lr_image" id="lr_image"  value="" /> 
					 		<span class="help-block"></span>
						</fieldset>

								<fieldset class="form-group floating-label-form-group">
						<label for="email">LR No</label>
						<!-- <input type="hidden" id="invoice_id" value="" > -->
						<input type="text" class="form-control" name="lr_number" id="lr_number" value="">
					</fieldset>
				
					</div>
						<div class="modal-footer">
  				<button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
					<button type="button" id="save_lr_detail" class="btn btn-success">Submit </button>
				</div>
			</form>
  		</div>
	</div>
</div>

<div id="pdfattachment" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View PDF Attachment Information </div>
					<div class="tools">

						<a href="javascript:;" id="pdf_requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title="" ><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>

		</div>
	</div>
</div>

			<!-- <div class="modal-body portlet box blue">
				<div class="portlet-title">
					<div class="caption">
						<i class="fa fa-gift"></i>View LR Information </div>
					<div class="tools">

						<a href="javascript:;" id="lr_requesting_ajax" data-load="true" data-url="" class="reload" data-original-title="" title=""><i class="fa fa-reload"></i> </a>

						<a href="javascript:;" data-original-title="" title="" data-dismiss="modal" style="color:white;"> <i class="fa fa-close"></i></a>
					</div>
				</div>
				<div class="portlet-body portlet-empty" style="">
				</div>
			</div>

		</div>
	</div>
</div>
 -->
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script>
function genReport(bid){
	var rc = encodeURIComponent($("#report_content").html());
	$.ajax({
		type: "POST",
		url: "order_generate.php",
		data: 'order_id='+bid+'&staic=2',
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result){ 
				setTimeout(function(){
					window.location.href=result;
					$(".transCover").fadeOut(100);				
				},1500);
			}
	});
}
function OrderStatus(oid,status)
{
		// alert(status)
	if(status==1)
	{
		var txt="Approve";
	}
	else if(status==-2)
	{
		var txt="Dispprove";
	}
	else if(status==3)
	{
		var txt="Cancel";
	}
	else if(status==4)
	{
		var txt="Account's Approve";
	}
	else if(status==7)
	{
		var txt="Lr Pending";
	}
	else if(status==6)
	{
		var txt="Order Complete";
	}
	var r=confirm("Are You Sure you want to "+txt+" this Order??");
	if(r)
	{		
		$.ajax({
			type: "POST",
			url: "update_order_status.php",
			data: 'order_id='+oid+'&status='+status,
			beforeSend: function() {
				$(".transCover").fadeIn(800);
			},
			success: function(result){ 
				var result=$.parseJSON(result);
				if(result.ack==1)
				{
					$(".hide-app-dis").addClass("hidden");
					setTimeout(function(){					
						$(".transCover").fadeOut(100);				
						toastr.success(result.ack_msg);
						location.reload();
					},1500);
				}
				else
				{
					toastr.error(result.ack_msg);
				}
			}
		});
	}
}

$('#lrattachment').on('show.bs.modal', function (event) {
  		var button = $(event.relatedTarget) // Button that triggered the modal
  		var requesting_id=button.data("id");
	})

	$(function(){
		$("#save_lr_detail").on('click',function(){
			UpdateLrAtatchment();
		});
	})


// $('#lrattachment').on('show.bs.modal', function (event) {
// 	  var button = $(event.relatedTarget) // Button that triggered the modal
// 	  var requesting_id=button.data("id");
// 		$("#lr_requesting_ajax").attr("data-url","update_lrattachment_status.php?id="+requesting_id);
// 		$("#lr_requesting_ajax").click();
// 		//OrderStatus(requesting_id,'7');
// 	});

		$('#pdfattachment').on('show.bs.modal', function (event) {
		  var button = $(event.relatedTarget) // Button that triggered the modal
		  var requesting_id=button.data("id");
		  var pdf_ids=button.data("pdfid");
		  // alert(pdf_ids)
			$("#pdf_requesting_ajax").attr("data-url","pdf_attachment_detail_ajax.php?id="+requesting_id+"&pdf_ids="+pdf_ids+"&flag=show");
			$("#pdf_requesting_ajax").click();
			
		});
function printReport(id) 
{	
	// var myWindow =  window.open('view_order_new_1.php?order_id='+id+"&p=1",'','width=500,height=800');

	var myWindow =  window.open('view_order_new_print.php?order_id='+id+"&p=1",'','width=500,height=800');

	setTimeout(function () 
	{
		myWindow.print();
		var ival = setInterval(function() 
		{
		    myWindow.close();
		    clearInterval(ival);
		}, 500);
	}, 1500);
}

// for mail send
function sendEmail(id)
{
	$.ajax({
		type: "POST",
		url: "generate_email.php",
		data: {
			ref_id: id,
			type: "orders",
		},
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result) {
			var result = $.parseJSON(result);
			if (result.ack == 1) { 
				$(".transCover").fadeOut(100);
				toastr.success(result.ack_msg);
			} else {
				toastr.error(result.ack_msg);
			}
		}
	});
}
// for mail send


function editschemeqty(pro_qty="",id,mode)
{
	// alert(pro_qty)
	$.ajax({
		type: "POST",
		url: "update_item_shecme_qty_ajax.php",
		data: {
			pro_qty: pro_qty,
			id: id,
			mode: mode,
			
		},
		beforeSend: function() {
			$(".transCover").fadeIn(800);
		},
		success: function(result) {
			var result = $.parseJSON(result);
			if (result.ack == 1) { 
				$(".transCover").fadeOut(100);
				toastr.success(result.ack_msg);
				if(mode == "delete")
				{
					location.reload();
				}

			} else {
				toastr.error(result.ack_msg);
			}
		}
	});
	
}
 
function validate(evt) {
  var theEvent = evt || window.event;

  // Handle paste
  if (theEvent.type === 'paste') {
      key = event.clipboardData.getData('text/plain');
  } else {
  // Handle key press
      var key = theEvent.keyCode || theEvent.which;
      key = String.fromCharCode(key);
  }
  var regex = /[0-9]/;
  if( !regex.test(key) ) {
    theEvent.returnValue = false;
    if(theEvent.preventDefault) theEvent.preventDefault();
  }
}

function UpdateLrAtatchment()
	{		
		$('#save_lr_detail').prop('disabled', true);

 		var order_id = '<?php echo $bid ?>';
 		
 		var lr_number = $('#lr_number').val();
 		var lr_image = $("#lr_image").val();

 		var myFormData = new FormData();
 		myFormData.append('lr_number',lr_number);
 		myFormData.append('order_id',order_id);
 		myFormData.append("file_path",$('#lr_image')[0].files[0]);

 		if(order_id!="")
 		{
 			$.ajax({
			    url:"update_lrattachment_status.php",
			    type:"POST",
			    data:myFormData,
			    processData: false, // important
			    contentType: false, // important
			    beforeSend:function() {
			      },
			    success:function(result)
			    {
			        result=$.parseJSON(result);
			        if(result.ack==1)
			        {                          
			          toastr.success(result.ack_msg);
			          $("#lrattachment").modal('hide');
			          location.reload();
			        }               
			        else
			        {
			         	toastr.error(result.ack_msg);
			         	$('#save_lr_detail').prop('disabled', false);
			        }         
			    },                    
    		});
 		}
 		else
 		{
 			toastr.error("Please Select lr details");
 			$('#save_lr_detail').prop('disabled', false);
 		}
 	}
		//alert();

		
$(function(){
		aj.imageHolder($("input[name=lr_image]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
			//toastr.success("Old Image Found!!");
		},
		function(file,img)
		{
			if(!file)
			{
				toastr.error("File may be corrupted or missing. Try again!!");
			}
		},
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply,image_width,image_height){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
				//toastr.success("Selected File Dimension: "+image_width+" X "+image_height);
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
	
</script>
</body>
</html>