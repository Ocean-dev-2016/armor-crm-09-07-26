<?php
$page_id=436;$page_slug='page_pro_forma';
$page_slug="issue_vendor_manage";
$ctable 	= "customer_order_request_info";
$ctable1 	= "Customer Order Request Info";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
$page_title = "Pro Forma Invoice";
//$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
include("../include/customer_order_request.class.php");
$obj= new CustomerOrderReq();
$customer_id		= "";
$customer_name		= "";
$customer_address	= "";
$customer_phone		= "";
$subtotal			= "";
$pro_forma_invoice_date 		= date("d-m-Y");
$place_of_supply		= 1;
$invoice_no			= $db->getlastInsertId($ctable);
$pro_forma_invoice_no	= PROFORMAINVOICE_NO.str_pad($invoice_no, 4, 0, STR_PAD_LEFT);


$invoice_reference="";
$invoice_buyer_order_dated="";
$invoice_buyer_order_no="";
$invoice_dispatch_document_dated="";
$invoice_dispatch_document_no="";
$invoice_dispatch_through="";
$invoice_terms_payment="";
$invoice_delivery_note="";
$due_date="";
/* if(isset($_REQUEST['submit'])){
		 
	$detail['customer_id']		                    = $db->clean($_REQUEST['customer_id']);
	$detail['invoice_date']		                    = $db->clean($_REQUEST['invoice_date']);
	$detail['place_of_supply']		                    = $db->clean($_REQUEST['place_of_supply']);
	$detail['pro_forma_invoice_no']		                = $db->clean($_REQUEST['pro_forma_invoice_no']);
	$detail['invoice_reference']		            = $db->clean($_REQUEST['invoice_reference']);
	$detail['invoice_buyer_order_dated']		    = $db->clean($_REQUEST['invoice_buyer_order_dated']);
	$detail['invoice_buyer_order_no']		        = $db->clean($_REQUEST['invoice_buyer_order_no']);
	$detail['invoice_dispatch_document_dated']		= $db->clean($_REQUEST['invoice_dispatch_document_dated']);
	$detail['invoice_dispatch_document_no']		    = $db->clean($_REQUEST['invoice_dispatch_document_no']);
	$detail['invoice_dispatch_through']		        = $db->clean($_REQUEST['invoice_dispatch_through']);
	$detail['invoice_terms_payment']		        = $db->clean($_REQUEST['invoice_terms_payment']);
	$detail['invoice_delivery_note']		        = $db->clean($_REQUEST['invoice_delivery_note']);
	$detail['due_date']		        = $db->clean($_REQUEST['due_date']);
	
	//Sales Invoice Item array
	$item=$_REQUEST['item'];
	//var_dump($item);
	//exit;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
			if($rights['insert_flag']!=1)
			{
				$db->rp_location('access_denied.php?msg=delete_access_denied');
			}
			//print_r($item);exit;
			$reply=$obj->InsertProformaInvoice($detail,$item);
			if($reply['ack']==1)
			{
				$db->addSuccessMessage($reply['ack_msg']);
				
				$db->rp_location("pro_forma_invoice_manage.php?msg=inserted");
			}
			else
			{
					 $db->addErrorMessage($reply['ack_msg']);
				
			}
		}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$obj->updateProformaInvoice($detail,$item);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			
		    $db->rp_location("pro_forma_invoice_manage.php?msg=updated");
		}
		else{
				$db->addErrorMessage($reply['ack_msg']);
			} 
		
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$platting_jobwork_issue_id=$_REQUEST['id'];
		$detail['id']=$_REQUEST['id'];
		$reply=$obj->GetProFormaInvoice($platting_jobwork_issue_id);
		$item_info=$obj->GetProFormaInvoiceItem($detail);		
		
	if($reply['ack']==1){
		
		$labourIssue		= $reply['result'];
		if($labourIssue['invoice_dispatch_document_dated']=="01-01-1970"){
			$labourIssue['invoice_dispatch_document_dated']="";
		}
		if($labourIssue['invoice_buyer_order_dated']=="01-01-1970"){
			$labourIssue['invoice_buyer_order_dated']="";
		}
		if($labourIssue['invoice_due_date']=="01-01-1970"){
			$labourIssue['invoice_due_date']="";
		}
		if($labourIssue['pro_forma_invoice_date']=="01-01-1970"){
			$labourIssue['pro_forma_invoice_date']="";
		}
		extract($labourIssue);
	}	
	if($item_info['ack']==1){

		$platting_jobwork_issue_id=$_REQUEST['id'];
		$item_info=$item_info['result'];

	}
	else{
		$item_info=array();
	}
} */
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	
	
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$obj->DeleteCustomerOrderReq($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location("customer_order_request_manage.php?msg=inserted");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);		
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location("customer_order_request_manage.php?msg=updated");
}


if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location("customer_order_request_manage.php?msg=updated");
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/datetimepicker/jquery.datetimepicker.css"/>
<style>
.input-has-error{
	border:1px red solid!important;
}
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="pro_forma_invoice_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
			<div class="row">
				<div class="col-sm-12">
				<?php $db->printErrorMessage(); ?>
				<?php $db->printSuccessMessage(); ?>
				</div>
			</div>
			<!-- Employee ID-->
			<div class="row">
				<div class="col-md-12">
					<!-- Begin: life time stats -->
					<div class="portlet light portlet-fit portlet-datatable bordered">
					<form role="form" action="" onSubmit="return check_form();" method="post">
						<div class="portlet-body">
							<div class="row">
								<div class="col-md-6 col-sm-6">
									<div class="portlet grey-cascade box">
										<div class="portlet-title">
											<div class="caption">
											  Invoice Details
											</div>
										</div>
										<div class="portlet-body">
											<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Pro Forma Invoice No.<code>*</code></label>
													<input type="text" name="pro_forma_invoice_no" id="pro_forma_invoice_no" value="<?php if($_REQUEST['mode']=='edit'){echo $pro_forma_invoice_no;}else{echo $pro_forma_invoice_no;}?>" class="form-control" disabled>
													<input type="hidden" name="pro_forma_invoice_no" id="pro_forma_invoice_no" value="<?php if($_REQUEST['mode']=='edit'){echo $pro_forma_invoice_no;}else{echo $pro_forma_invoice_no;}?>" class="form-control" >
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Invoice Date<code>*</code></label>
													<input type="text" name="invoice_date" id="invoice_date" value="<?php echo $pro_forma_invoice_date;?>" class="form-control" Placeholder="Invoice Date">
													<p class='help-block'></p>
												</div>
											</div>
											
											</div>
											<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Supplier Ref.No.</label>
													<input type="text" name="invoice_reference" id="invoice_reference" value="<?php echo $invoice_reference;?>" class="form-control" Placeholder="Supplier Ref.No">
													<p class='help-block'></p>
												</div>
											</div>
											<div class="col-md-6">
												<div class="form-group">
													<label>Due Date</label>
													<input type="text" name="due_date" id="due_date" value="<?php echo $invoice_due_date;?>" class="form-control" Placeholder="Due Date">
													<p class='help-block'></p>
												</div>
											</div>
											
											</div>
										</div>
									</div>
								</div>
								<div class="col-md-6 col-sm-6">
									<div class="portlet grey-cascade box">
										<div class="portlet-title">
											<div class="caption">
											  Customer Details
											</div>
										</div>
										<div class="portlet-body">
										<?php
										if($_REQUEST['mode']=="add"){
											?>
											<div class="row static-info">
												<div class="col-md-3 name"> 
													Select Customer <code>*</code>
												</div>
												<div class="col-md-8 value"> 
													<select class="form-control" name="customer_id" id="customer_id" class="customer_id">
													<option value=""> -- Select Customer --</option>
													<?php 
															$customer_list_d=$db->rp_getData('customer',"*","isDelete=0","",0);
															while($customer_list_r=mysqli_fetch_assoc($customer_list_d))
															{
																
																?>
																<option <?php echo ($customer_id==$customer_list_r['id'])?"selected":"" ; ?> value="<?php echo $customer_list_r['id']?>" >
																<?php echo $customer_list_r['customer_company_name'];?>
																</option>
																<?php
															}
														?>
													</select>
													<p class="help-block"></p>
												</div>
											</div>
											<?php
										}
										?>
											<div class="row static-info v_name">
												<div class="col-md-3 name v_name">Customer Name: </div>
												<div class="col-md-8 value" id="customer_name"><?php echo $customer_name; ?> </div>
											</div>
											
											<div class="row static-info phone">
												<div class="col-md-3 name"> Phone No : </div>
											   <div class="col-md-8 value" id="customer_phone"><?php echo $customer_phone;?></div>
											</div>
											
											<div class="row static-info address">
												<div class="col-md-3 name"> Address: </div>
												<div class="col-md-8 value" id="customer_address"><?php echo $customer_address;?></div>
											</div>
											<div class="row static-info address">
												<div class="col-md-3 name"> Place of supply: </div>
												<div class="col-md-8 value">
												<select class="form-control" name="place_of_supply" id="place_of_supply" class="place_of_supply" onChange="recalculateFinalValues()">
												<option value=""> -- Select Place Of Supply --</option>
												<?php 
														$state_r=$db->rp_getData('state',"*","isDelete=0","",0);
														
														while($state_d=mysqli_fetch_assoc($state_r))
														{
															
															?>
															<option <?php echo ($place_of_supply==$state_d['id'])?"selected":"" ; ?> value="<?php echo $state_d['id']?>" >
															<?php echo $state_d['name'];?>
															</option>
															<?php
														}
													?>
												</select>
											</div>
											</div>
											
										</div>
									</div>
								</div>
							</div>
						
							<div class="row">
								<div class="col-md-12 col-sm-12">
									<div class="portlet-body">
										<div class="row">
											<div class="col-md-12 col-sm-6">
												<div class="portlet grey-cascade box">
													<div class="portlet-title">
														<div class="caption">
															<?php echo $page_title;?> </div>
													 </div>
													<div class="portlet-body">
														
														<div class="row">
															<div class="col-md-4">
																<div class="form-group">
																	<select  id="fg_item_id" name="fg_item_id" type="text" class="form-control">
																		<option value="">Select Product </option>
																		<?php
																			$list_d=$db->rp_getData('item_fg',"*","1=1 AND isDelete=0","",0);
																			while($list_r=mysqli_fetch_assoc($list_d))
																			{
																				
																		?>
																		<option data-stock_qty="<?php echo $list_r['fg_stock_qty'];?>"data-name="<?php echo $list_r['fg_item_name'];?>" data-price="<?php echo $list_r['fg_sell_price'];?>" data-cgst_tax="<?php echo $list_r['cgst_no'];?>" data-sgst_tax="<?php echo $list_r['sgst_no'];?>" data-igst_tax="<?php echo $list_r['igst_no'];?>" value="<?php echo $list_r['id']?>"><?php echo $list_r['fg_item_name'];?>
																		</option>
																				<?php
																			}
																		?>
																	</select>
																	<p class="help-block"></p>
																</div>
															</div>	
															<div class="col-md-3">
																
																<div class="input-group">
																	<input type="text" name="qty" id="qty" value="" class="form-control" Placeholder="Enter Qty" />
																	<p class="help-block"></p>	
																</div>
															</div>
																		
															<div class="col-md-1">
																<div class="form-group">	
																	<button type="button" data-mode="add_specification" name="add_invoice_item" id="add_invoice_item" class="btn pull-right green">&nbsp;Add</button>
																</div>
															</div>	
															<div class="col-md-12">
																<div class="portlet-body">
																	<div class="table-responsive">
																		<table class="table  table-hover table-bordered table-striped table-list-view" id="extra_spec_item">
																			<thead>
																				<tr>
																					<th style="min-width:200px" colspan="5"></th>
																					<th style="min-width:80px;max-width:80px;text-align:center;" colspan="2">CGST</th>
																					
																					<th style="min-width:80px;max-width:80px;text-align:center;" colspan="2"> SGST</th>
																					<th style="min-width:80px;max-width:80px;text-align:center;" colspan="2"> IGST</th>
																					<th style="min-width:50px;max-width:50px"></th>
																				</tr>
																				<tr>
																					<th style="min-width:50px;max-width:50px"> </th>
																					<th style="min-width:200px" > FG Item Name</th>
																					<th style="min-width:50px;max-width:50px"> Quantity</th>
																					
																					<th style="min-width:50px;max-width:50px"> Price</th>
																					<th style="min-width:50px;max-width:50px"> Subtotal</th>
																					<th style="min-width:80px;max-width:80px"> %</th>
																					<th style="min-width:80px;max-width:80px"> Amt</th>
																					
																					<th style="min-width:80px;max-width:80px"> %</th>
																					<th style="min-width:80px;max-width:80px"> Amt</th>
																					<th style="min-width:80px;max-width:80px"> %</th>
																					<th style="min-width:80px;max-width:80px"> Amt</th>
																					<th style="min-width:50px;max-width:50px"> Amount</th>
																				</tr>
																				
																			</thead>
																			<tbody>
																			<?php
																				 $serial=1;
																				 $subtotal=0;
																				 $CGSTTotal =0;
																				 $SGSTTotal =0;
																				 $IGSTTotal =0;
																				 $GrandTotal =0;
																				 $total_qty=0;
																				if(!empty($item_info))
																				{
																					
																					foreach($item_info as $extra)
																					{
																						 $CGSTAmnt=(($extra['fg_item_subtotal']*$extra['cgst_tax'])/100);
																						 $CGSTTotal =$CGSTTotal+$CGSTAmnt;
																					     $SGSTAmnt=(($extra['fg_item_subtotal']*$extra['sgst_tax'])/100);
																						 $SGSTTotal =$SGSTTotal+$SGSTAmnt;
																						
																						 $IGSTAmnt=(($extra['fg_item_subtotal']*$extra['igst_tax'])/100);
																						 $IGSTTotal =$IGSTTotal+$IGSTAmnt;
																						
																						$subtotal+=$extra['fg_item_subtotal'];
																						$total_qty+=$extra['fg_item_qty'];
																						$count=$extra['fg_item_id'];
																					?>
		<tr class='invoice_item'>
		<td class="delete text-center"><i class="fa fa-trash text-danger"></i>
		
		<input name='item[<?php echo $count ?>][fg_item_id]' id='item_id'  type='hidden' class='item_id' value='<?php echo $extra['fg_item_id'] ?>'></td>
		<td><input type='hidden' name='item[<?php echo $count ?>][fg_item_name]' id='item_name' class='item_name' value='<?php echo $extra['fg_item_name']; ?>'><?php echo $extra['fg_item_name'] ?></td>
		<td><input type='text' name='item[<?php echo $count ?>][fg_item_qty]' class='qty form-control input-small' id='qty<?php echo $extra['fg_item_id'] ?>' onChange='recalculateFinalValues(this)'  value='<?php echo $extra['fg_item_qty']; ?>' data-current_packing_stock='<?php echo $extra['current_packing_stock']+$extra['fg_item_qty'];?>' data-item_name='<?php echo $extra['fg_item_name'];?>'><p class='help-block'></p></td>
		
		
		<td><input type='text' name='item[<?php echo $count ?>][fg_item_price]' id='price<?php echo $extra['fg_item_id'] ?>' class='price form-control input-small' type='text' value='<?php echo $extra['fg_item_price'] ?>' onChange='recalculateFinalValues(this)'></td>
		
		<td><input type='text' name='item[<?php echo $count ?>][fg_item_subtotal]' class='total form-control text-right input-small' id='total'  value='<?php echo round($extra['fg_item_subtotal'],4) ?>' disabled></td>
		<td>
		
		<select name='item[<?php echo $count ?>][cgst_tax]' class='text-right cgst_tax tax_label_cgst form-control' style='width:150px;' id='cgst_tax' onChange='recalculateFinalValues(this)'>
		<option value="">--Select Tax--</option>
		<?php
		$cgst_taxs=$db->rp_getData("tax","*","isDelete=0 AND variant_for_cgst=1");
		while($cgst_tax=mysqli_fetch_assoc($cgst_taxs)){
			?>
			<option  data-cgst_tax="<?php echo $cgst_tax['variant_value']; ?>"  value="<?php echo $cgst_tax['variant_value']; ?>" <?php if($cgst_tax['variant_value']==$extra['cgst_tax']){echo "selected"; } ?>><?php echo $cgst_tax['variant_value']; ?></option>
			<?php
		}
		?>
		</select>
		
		</td>
		<td><input type='text' readonly class='cgst-tax-total form-control text-right tax_label_cgst' value='<?php echo $CGSTAmnt; ?>'></td>
		<td>
		
		
		<select name='item[<?php echo $count ?>][sgst_tax]' class='text-right sgst_tax tax_label_sgst form-control' style='width:150px;' id='sgst_tax' onChange='recalculateFinalValues(this)'>
		<option value="">--Select Tax--</option>
		<?php
		$sgst_taxs=$db->rp_getData("tax","*","isDelete=0 AND variant_for_sgst=1");
		while($sgst_tax=mysqli_fetch_assoc($sgst_taxs)){
			?>
			<option data-sgst_tax="<?php echo $sgst_tax['variant_value']; ?>"  value="<?php echo $sgst_tax['variant_value']; ?>" <?php if($sgst_tax['variant_value']==$extra['sgst_tax']){echo "selected"; } ?>><?php echo $sgst_tax['variant_value']; ?></option>
			<?php
		}
		?>
		</select>
		
		</td>
		<td><input type='text' readonly class='sgst-tax-total form-control text-right tax_label_cgst' value='<?php echo $SGSTAmnt; ?>'></td>
		<td>
		
		<select name='item[<?php echo $count ?>][igst_tax]' class='text-right igst_tax tax_label_igst form-control' style='width:150px;' id='igst_tax' onChange='recalculateFinalValues(this)'>
		<option value="">--Select Tax--</option>
		<?php
		$igst_taxs=$db->rp_getData("tax","*","isDelete=0 AND variant_for_igst=1");
		while($igst_tax=mysqli_fetch_assoc($igst_taxs)){
			?>
			<option data-igst_tax="<?php echo $igst_tax['variant_value']; ?>"  value="<?php echo $igst_tax['variant_value']; ?>" <?php if($igst_tax['variant_value']==$extra['igst_tax']){echo "selected"; } ?>><?php echo $igst_tax['variant_value']; ?></option>
			<?php
		}
		?>
		</select>
		</td>
		<td><input type='text' readonly class='igst-tax-total form-control text-right tax_label_cgst' value='<?php echo $IGSTAmnt; ?>'></td>
		<td><input type='text' name='item[<?php echo $count ?>][fg_item_amount]' class='amount text-right form-control input-small' id='amount'  value='<?php echo round($extra['amount'],4) ?>' disabled></td></tr>
  <?php
																			   $serial++;
																					}
																					$GrandTotal=$subtotal+$CGSTTotal+$SGSTTotal+$IGSTTotal;
																					$hide_fg="hidden";
																				}
																				else
																				{
																					$hide_fg="";
																				}
																				?>
																				<tr class="no-row-item text-center <?php echo $hide_fg; ?>"><td colspan="13"><i class="fa fa-cubes"></i> No Items</td> </tr>	
																			
																			</tbody>
																			<tfoot>
																				<tr>
																					<td></td>
																					<td >Total Qty </td>
																					<td> <input type='text' id='finalQty' disabled name='finalQty[]' class="form-control input-small" value="<?php echo $total_qty;?>" onChange=''></td>	
																					<td>SubTotal </td>
																					<td><input type='text' id='finalTotal' disabled value="<?php echo $subtotal;?>" onChange='' class="form-control input-small text-right" name='finalTotal[]'></td>
																					<td></td>
																					<td class="CGSTTotal text-right"><?php echo $CGSTTotal;?></td>
																					<td></td>
																					<td class="SGSTTotal text-right"><?php echo $SGSTTotal;?></td>
																					<td></td>
																					<td class="IGSTTotal text-right"> <?php echo $IGSTTotal;?></td>
																					<td class="GrandTotal text-right"><span value="" onChange='' ><?php echo $GrandTotal;?></td>
																				</tr>
																			</tfoot>
																		</table>
																		
																	</div>
					
																</div>
															</div>	
															
														</div>
																		
														
													</div>
												</div>
											</div>
										</div>
										
									</div>
								</div>
							</div>
						</div>
					
							<div class="row">
								<div class="col-md-12 col-sm-12">
									<div class="portlet grey-cascade box">
										<div class="portlet-title">
											<div class="caption">
											  Invoice Details
											</div>
										</div>
										<div class="portlet-body">
											<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Buyer's Order No.</label>
													<input type="text" name="invoice_buyer_order_no" id="invoice_buyer_order_no" value="<?php echo $invoice_buyer_order_no;?>" class="form-control" Placeholder="Buyer's Order No.">
													<p class='help-block'></p>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Buyer Order Dated</label>
													<input type="text" name="invoice_buyer_order_dated" id="invoice_buyer_order_dated" value="<?php echo $invoice_buyer_order_dated;?>" class="form-control" Placeholder="Buyer Order Dated">
													<p class='help-block'></p>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Dispatch Through</label>
													<input type="text" name="invoice_dispatch_through" id="invoice_dispatch_through" value="<?php echo $invoice_dispatch_through;?>" class="form-control" Placeholder="Dispatch Through">
													<p class='help-block'></p>
												</div>
											</div>
											</div>
											<div class="row">
											<div class="col-md-4">
												<div class="form-group">
													<label>Dispatch Document No.</label>
													<input type="text" name="invoice_dispatch_document_no" id="invoice_dispatch_document_no" value="<?php echo $invoice_dispatch_document_no;?>" class="form-control" Placeholder="Dispatch Document No.">
													<p class='help-block'></p>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Dispatch Document Dated</label>
													<input type="text" name="invoice_dispatch_document_dated" id="invoice_dispatch_document_dated" value="<?php echo $invoice_dispatch_document_dated;?>" class="form-control" Placeholder="Dispatch Document Dated">
													<p class='help-block'></p>
												</div>
											</div>
											<div class="col-md-4">
												<div class="form-group">
													<label>Mode/Terms Of Payment</label>
													<input type="text" name="invoice_terms_payment" id="invoice_terms_payment" value="<?php echo $invoice_terms_payment;?>" class="form-control" Placeholder="Mode/Terms Of Payment">
													<p class='help-block'></p>
												</div>
											</div>
											</div>
											<div class="row">
											<div class="col-md-6">
												<div class="form-group">
													<label>Delivery Note</label>
													<textarea type="text" name="invoice_delivery_note" id="invoice_delivery_note" value="" class="form-control" Placeholder="Delivery Note"><?php echo $invoice_delivery_note;?></textarea>
													<p class='help-block'></p>
												</div>
											</div>
										
											
											</div>
											<div class="row">
												<div class="col-md-12 ">
													<div class="form-group pull-left">
														<button  class="btn btn-success pull-right" id="submit" name="submit"> Submit</button>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
							</div>
							
					</form>
					</div>
				</div>
			</div>
			<!-- End: life time stats -->
		</div>
	</div>
</div>

<div id="loading" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body portlet box white">
				<div class="portlet-title" style="color:black;">
					<div class="caption">Loading.......
					<img src="../images/loading-spinner-blue.gif">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script src="js/jquery.numeric.min.js"></script>

<script type="text/javascript">

$("#invoice_date").datepicker({ dateFormat: 'dd-mm-yy',  minDate: 0, timepicker: false, autoclose: true });
$("#due_date").datepicker({ dateFormat: 'dd-mm-yy',  minDate: 0, timepicker: false, autoclose: true });
$("#invoice_buyer_order_dated").datepicker({ dateFormat: 'dd-mm-yy', timepicker: false, autoclose: true });
$("#invoice_dispatch_document_dated").datepicker({ dateFormat: 'dd-mm-yy', timepicker: false, autoclose: true });
$("#extra_spec_item").on('click','.delete',function(){
	maintainDatatable();
   $(this).closest('tr').remove();
   recalculateFinalValues();
 });
$("#customer_id").change(function (){ //change event for select
	var customer_id=$("#customer_id").val();
	getCustomer(customer_id);
})
$("#fg_item_id").select2();
$('#qty').numeric();
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
$(function(){
	recalculateFinalValues();
})
</script>
<script type="text/javascript">
/*var count=0;
 $("#add_invoice_item").click(function (){	
	
	if($("#fg_item_id").val()=="" || $("#fg_item_id").val().split(" ").join("")==""){		
		var _popover;
			var message="Select Product"
		_popover = $("#fg_item_id").popover({
			trigger: "manual,click",
			placement: "top",
			content: message,
			template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
		});
		$("#fg_item_id").addClass('input-has-error');
		return true;
	}
	if($("#qty").val()=="" || $("#qty").val().split(" ").join("")=="" || $("#qty").val()<=0){		
		//vd=aj.error('qty',"Please Enter Qty.","add_error");
		var _popover;
			var message="Enter Qty"
		_popover = $("#qty").popover({
			trigger: "manual,click",
			placement: "top",
			content: message,
			template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
		});
		$("#qty").addClass('input-has-error');
		valid=false;
		return true;
	}
	var qty=parseInt($("#qty").val());
	var remaining_qty=parseInt($("#original_qty").val());
	if(qty>remaining_qty){
		toastr.error("No More then "+remaining_qty+" Qty are avaliable!!","Error!!");
		$("#qty").focus();
		return true;
	}
	var item_id=$("#fg_item_id").val();
	var product_name=$("#fg_item_id").find("option:selected").data("name");
	var stock_qty=parseFloat($("#fg_item_id").find("option:selected").data("stock_qty"));
	
	var price=parseFloat($("#fg_item_id").find("option:selected").data("price"));
	var qty=parseFloat($("#qty").val());
	var cgst_tax=parseFloat($("#fg_item_id").find("option:selected").data("cgst_tax"));
	var igst_tax=parseFloat($("#fg_item_id").find("option:selected").data("igst_tax"));
	var sgst_tax=parseFloat($("#fg_item_id").find("option:selected").data("cgst_tax"));
	
	if(item_id!=0){
		var duplicate=$("input.item_id[value='"+item_id+"']").length;
		var design_name=$("#design_name").find('option:selected').data("name");
		if(duplicate==0)
		{
			var place_of_supply=$("#place_of_supply").val();
			var subtotal=parseFloat(price)*qty;
			$('#original_igst_tax'+item_id).val(igst_tax);
			if(place_of_supply=="1")
			{
				var cgst_tax_total=parseFloat(subtotal*cgst_tax/100);
				var sgst_tax_total=parseFloat(subtotal*sgst_tax/100);			
				var igst_tax_total=0;
				
				$('#igst_tax'+item_id).val(igst_tax_total);		
				$("#extra_spec_item").find('tbody').find('tr').find('td').find("span.tax_label_igst").html(0);
			   
			}
			else
			{
				var cgst_tax_total=0;
				var sgst_tax_total=0;			
				var igst_tax_total=parseFloat(subtotal*igst_tax/100);
				$('#igst_tax'+item_id).val(igst_tax);
				$('#sgst_tax'+item_id).val(sgst_tax_total);
				$('#sgst_tax'+item_id).val(cgst_tax_total);
				$("#datatable_1").find('tbody').find('tr').find('td').find("span.cgst_tax").html(0);
				$("#datatable_1").find('tbody').find('tr').find('td').find("span.sgst_tax").html(0);
			}
			serial=$("tr.invoice_item").length+1;
				count=item_id;
				
				$.ajax({
					url:"tax_spinner_of_pro_forma.php",
					type:"POST",
					data:{
						mode:"get_tax",
						item_id:item_id,
						count:count,
						cgst_tax:cgst_tax,
						
					},
					beforeSend:function(){
						$("#loading-modal").modal('show');
					},
					success:function(result){
						if(result!="")
						{
							var html=result;
							$("#cgst_tax_spinner"+item_id).append(html);
							$("#loading-modal").modal('hide');	
						}
						
					},
					error:function(){
						toastr.error("We could not process right now try again!!","Error");
					}
					
				});
				
				var new_row2="<tr class='invoice_item'><td class='delete text-center'><i class='fa fa-trash text-danger'></i><input name='item["+count+"][fg_item_id]' id='item_id'  type='hidden' class='item_id' value='"+item_id+"'></td><td>"+product_name+"</td><td class='center'>"+stock_qty+"</td><td><input type='text' name='item["+count+"][fg_item_qty]' class='qty form-control input-small' id='qty"+item_id+"' onChange='recalculateFinalValues(this)'  value='"+qty+"' data-item_name='"+product_name+"' data-current_packing_stock='"+stock_qty+"'><p class='help-block'></p></td><td><input type='text' name='item["+count+"][fg_item_price]' class='price form-control input-small' type='text' id='price"+item_id+"' value='"+price+"' onChange='recalculateFinalValues(this)'><p class='help-block'></p></td><td><input type='text' name=''item["+count+"][fg_item_subtotal]' class='total form-control input-small text-right' id='total'  value='"+aj.roundNumber(parseFloat(price)*qty,2)+"' disabled></td><td id='cgst_tax_spinner"+item_id+"'></td><td><input type='text' readonly class='text-right cgst-tax-total form-control' value='"+cgst_tax_total+"'></td><td><input name='item["+count+"][sgst_tax]' id='sgst_tax"+item_id+"'  type='text' class='text-right sgst_tax tax_label_sgst  form-control' value='"+sgst_tax+"' onChange='recalculateFinalValues(this)'></td><td><input type='text' readonly class='text-right sgst-tax-total form-control' value='"+sgst_tax_total+"'></td><td><input name='item["+count+"][igst_tax]' id='igst_tax"+item_id+"'  type='text' onChange='recalculateFinalValues(this)' class='text-right igst_tax tax_label_igst form-control' value=''></td><td><input type='text' readonly class='text-right igst-tax-total form-control' value='"+igst_tax_total+"'></td><td><input type='text' name=''item["+count+"][fg_item_amount]' class='amount form-control text-right input-small' id='amount"+item_id+"'  value='' disabled></td></tr>";
				$("#extra_spec_item").find('tbody').append(new_row2);
				
				//TAX
			
			 
			var tax_total=cgst_tax_total+sgst_tax_total+igst_tax_total+subtotal;
			$('#amount'+item_id).val(aj.roundNumber(tax_total,2));	
			maintainDatatable();
			recalculateFinalValues();
			$("#qty").val("").html();           
			$("#fg_item_name").select2("val","");
			$("#design_name").select2("val","");
				

		}
		else
		{
			toastr.error("Product already there remove first to add it again.","Error!!");
		}

	}
	else{
		
		toastr.error("Please Select AtLeast One Product.","Error!!");
	}
}) 
	*/
</script>
<script type="text/javascript">
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")==""){		
		vd=aj.error('customer_id',"Please Select Customer Name.","add_error");
		isValid=false;
	}
	if($("#invoice_date").val()=="" || $("#invoice_date").val().split(" ").join("")==""){		
		vd=aj.error('invoice_date',"Please Select Invoice Date.","add_error");
		isValid=false;
	}
	var row_length=$("#extra_spec_item").find("tbody").find("tr.invoice_item").length;
	
	if(row_length==0){
		toastr.error("Please Add Item!!","Error!!");
		isValid=false;
	}
	
	//var remaining_qty=parseInt($("#original_qty").val());
	
	$('.qty').each(function () {
		var	qty=parseInt($(this).val());
		var	current_packing_stock=parseInt($(this).data('current_packing_stock'));
		var	item_name=$(this).data('item_name');
		if($(this).val()<=0 ||  $(this).val()=="" || $(this).val().split(" ").join("")==""){
			//toastr.error("Please enter of "+item_name+".","Error!!");
			var id=$(this).prop("id");
			aj.errorTooltip($(this),"bottom","Please Enter  Qty.",true);
			isValid=false;
			$(this).focus();
		}
		else
		{
			aj.errorTooltip($(this),"bottom","Please Enter Qty.",false)
		}
		if($(this).val()>current_packing_stock){
			toastr.error(item_name +" Product Only "+current_packing_stock+" Qty are avaliable!!","Error!!");
			$(this).focus();
			isValid=false;
		}
		
	});
	
	$('.price').each(function () {
		var	price=parseInt($(this).val());		
		if($(this).val()<=0 || $(this).val()=="" || $(this).val().split(" ").join("")==""){
			
			//toastr.error("Please enter of "+item_name+".","Error!!");
			var id=$(this).prop("id");
			aj.errorTooltip($(this),"bottom","Please Enter Price.",true);
			isValid=false;
		}
		else
		{
			aj.errorTooltip($(this),"bottom","Please Enter Price.",false)
		}
	});
	if(isValid)
	{
		return true;
	}
	else
	{
		return false;
	}
}

function recalculateRow(t)
{
	 
	  var row = $(t).parent('td').parent('tr');
	  var price=$(row).find("td").find("input.price").val();
	  var qty=$(row).find("td").find("input.qty").val();
	  var box=$(row).find("td").find("input.box").val();
	  var box_qty=$(row).find("td").find("input.box").data('box_qty');
	 
	
	  var total=price*qty;
	 
	  $(row).find("td").find("input.total").val(aj.roundNumber(total,2));
	  $(row).find("td").find("input.box").val(box);
		
		
	///tax 
	var place_of_supply=$("#place_of_supply").val();
	var cgst_tax=$(row).find("td").find("option:selected").val();
    var sgst_tax=$(row).find("td").find("input.sgst_tax").val();
    var igst_tax=$(row).find("td").find("input.igst_tax").val();
	if(cgst_tax>100){cgst_tax=0;$(row).find("td").find("option:selected").val(100)};
	if(sgst_tax>100){sgst_tax=0;$(row).find("td").find("input.sgst_tax").val(100)};
	if(igst_tax>100){igst_tax=0;$(row).find("td").find("input.igst_tax").val(100)};
	if(place_of_supply=="1")
	{
		$(row).find("td").find("span.tax_label_cgst").html(cgst_tax);
		$(row).find("td").find("span.tax_label_sgst").html(sgst_tax);
		$(row).find("td").find("span.tax_label_igst").html(0);
		$(row).find("td").find("input.tax_label_igst").val(0);
	   var cgst_tax_total=parseFloat(total*cgst_tax/100);
	  var sgst_tax_total=parseFloat(total*sgst_tax/100);
	  var igst_tax_total=0;
	}
	else
	{
		var cgst_tax_total=0;
	    var sgst_tax_total=0;
		$(row).find("td").find("span.tax_label_igst").html(igst_tax);
		$(row).find("td").find("span.tax_label_cgst").html(0);
		$(row).find("td").find("span.tax_label_sgst").html(0);
		
		$(row).find("td").find("span.tax_label_cgst").val(0);
		$(row).find("td").find("span.tax_label_sgst").val(0);
		var igst_tax_total=parseFloat(total*igst_tax/100);
	}
	
	var tax_total=cgst_tax_total+sgst_tax_total+igst_tax_total+total;
	$(row).find("td").find("input.amount").val(aj.roundNumber(tax_total,2));	
	recalculateFinalValues();
 }
function recalculateFinalValues()
{
		checkNumerics();
	 	var sum=0;
	 	var sumQty=0;
	 	var whole_cgst_tax_total=0;
		var whole_sgst_tax_total=0;
		var whole_igst_tax_total=0;
		var whole_tax_total=0;
		var subTotal=0;
		
		$("#extra_spec_item").find("tbody").find("tr.invoice_item").each(function(i,row){
			var item_id=$(row).find("td").find("input.item_id").val();
			var price=$(row).find("td").find("input.price").val();
			price=(price!="")?parseFloat(price):0;
			price=(isNaN(price))?0:price;
			price=(price<0)?0:price;
			price=(price>100000000000)?100000000000:price;
			var qty=$(row).find("td").find("input.qty").val();
			qty=(qty!="")?parseFloat(qty):0;
			qty=(isNaN(qty))?0:qty;
			qty=(qty<0)?0:qty;
			qty=(qty>100000000000)?100000000000:qty;
			var total=price*qty;
			if(total>1000000000000000){qty=0;price=0;total=0;}
			
			$(row).find("td").find("input.qty").val(qty);
			$(row).find("td").find("input.price").val(price);
			$(row).find("td").find("input.total").val(aj.roundNumber(total,2));
			
			///tax 
			var place_of_supply=$("#place_of_supply").val();
			var cgst_tax=$(row).find("td").find('select.cgst_tax').find("option:selected").data("cgst_tax");
			var sgst_tax=$(row).find("td").find("select.sgst_tax").find("option:selected").data("sgst_tax");
			var igst_tax=$(row).find("td").find("select.igst_tax").find("option:selected").data("igst_tax");
			if(cgst_tax>100){cgst_tax=100;$(row).find("td").find("input.cgst_tax").val(100)};
			if(sgst_tax>100){sgst_tax=100;$(row).find("td").find("input.sgst_tax").val(100)};
			if(igst_tax>100){igst_tax=100;$(row).find("td").find("input.igst_tax").val(100)};
			if(place_of_supply=="1")
			{
				
				$(row).find("td").find("select.tax_label_igst").attr("disabled","disabled");
				$(row).find("td").find("select.tax_label_cgst").removeAttr("disabled");
				$(row).find("td").find("select.tax_label_sgst").removeAttr("disabled");
				$(row).find("td").find("input.tax_label_igst").val(0);
			    var cgst_tax_total=parseFloat(total*cgst_tax/100);
			    var sgst_tax_total=parseFloat(total*sgst_tax/100);
				var igst_tax_total=0;
				
			}
			else
			{
				var cgst_tax_total=0;
				var sgst_tax_total=0;
				$(row).find("td").find("select.tax_label_igst").removeAttr("disabled","disabled");
				$(row).find("td").find("select.tax_label_cgst").attr("disabled","disabled");
				$(row).find("td").find("select.tax_label_sgst").attr("disabled","disabled");
				$(row).find("td").find("input.tax_label_cgst").val(0);
				$(row).find("td").find("input.tax_label_sgst").val(0);
				$(row).find("td").find("input.tax_label_igst").val(igst_tax);
				var igst_tax_total=parseFloat(total*igst_tax/100);
				
			}
				$(row).find("td").find("input.cgst-tax-total").val(cgst_tax_total);
				$(row).find("td").find("input.sgst-tax-total").val(sgst_tax_total);
				$(row).find("td").find("input.igst-tax-total").val(igst_tax_total);
				var tax_total=cgst_tax_total+sgst_tax_total+igst_tax_total+total;
				whole_cgst_tax_total=whole_cgst_tax_total+cgst_tax_total;
				whole_sgst_tax_total=whole_sgst_tax_total+sgst_tax_total;
				whole_igst_tax_total=whole_igst_tax_total+igst_tax_total;
				whole_tax_total=whole_tax_total+tax_total;
				subTotal=subTotal+total;
				sumQty=qty+sumQty;
				$(row).find("td").find("input.amount").val(aj.roundNumber(tax_total,2));
						
							
							
		})	
		
		$(".CGSTTotal").html(whole_cgst_tax_total);	 
		$(".SGSTTotal").html(whole_sgst_tax_total);	 
		$(".IGSTTotal").html(whole_igst_tax_total);	 
		$(".GrandTotal").html(whole_tax_total);	
		$("#finalTotal").val(''+subTotal);
	    $("#finalQty").val(''+sumQty);	
}

function checkNumerics(){
	$(".qty").numeric();
	$(".price").numeric();
	$(".tax_label_cgst").numeric();
	$(".tax_label_sgst").numeric();
	$(".tax_label_igst").numeric();
	$('.qty').each(function () {
		var quantity_input=$(this);
		var qty=$(this).val();
		qty=(qty!="")?parseFloat(qty):0;
		qty=(isNaN(qty))?0:qty;
		qty=(qty<0)?0:qty;
		qty=(qty>100000000000)?100000000000:qty;
		$(quantity_input).val(qty)
		var	item_name=$(this).data('item_name');
		
		
	});
	$('.price').each(function () {
		var price_input=$(this);
		var price=$(this).val();
		price=(price!="")?parseFloat(price):0;
		price=(isNaN(price))?0:price;
		price=(price<0)?0:price;
		price=(price>100000000000)?100000000000:price;
		$(price_input).val(price)
	});
	$('.tax_label_sgst').each(function () {
		var price_input=$(this);
		var price=$(this).val();
		price=(price!="")?parseFloat(price):0;
		price=(isNaN(price))?0:price;
		price=(price<0)?0:price;
		
		$(price_input).val(price)
	});
	$('.tax_label_cgst').each(function () {
		var price_input=$(this);
		var price=$(this).val();
		price=(price!="")?parseFloat(price):0;
		price=(isNaN(price))?0:price;
		price=(price<0)?0:price
		$(price_input).val(price)
	});
	$('.tax_label_igst').each(function () {
		var price_input=$(this);
		var price=$(this).val();
		price=(price!="")?parseFloat(price):0;
		price=(isNaN(price))?0:price;
		price=(price<0)?0:price
		$(price_input).val(price)
	});
	
}

function getCustomer(customer_id)
{
	$.ajax({
       // url :  instead of writing the function to execute the request we use Select2's convenient helper
	    url: 'sales_invoice_customer_search.php?id='+ customer_id,
        type : 'POST',
        dataType : 'json',
        allowClear: true,		
        quietMillis: 250,
        data:{          
				customer_id:customer_id,
            },
        success: function (data) {
		// parse the results into the format expected by Select2.
				$("#customer_name").html(data.result.customer.customer_name);
				$("#customer_address").html(data.result.customer.customer_address);
				$("#customer_phone").html(data.result.customer.customer_phone);		
				var labour_list=data.result.customer;
				$.each(labour_list,function(index,value){
				var p_customer_id=labour_list[index];
				
			});	
			
        },
		
        cache: true
    });
}

function maintainDatatable()
{
	if($("#extra_spec_item").find("tbody").find("tr.invoice_item").length>=1)
	{
		
		$("tr.no-row-item").hide();
	}
	else
	{
		$("tr.no-row-item").removeClass("hidden");
		$("tr.no-row-item").show();
	}
}

</script>
<script type="text/javascript">
var count=0;
 $("#add_invoice_item").click(function (){	
	
	if($("#fg_item_id").val()=="" || $("#fg_item_id").val().split(" ").join("")==""){		
		var _popover;
			var message="Select Product"
		_popover = $("#fg_item_id").popover({
			trigger: "manual,click",
			placement: "top",
			content: message,
			template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
		});
		$("#fg_item_id").addClass('input-has-error');
		return true;
	}
	if($("#qty").val()=="" || $("#qty").val().split(" ").join("")=="" || $("#qty").val()<=0){		
		//vd=aj.error('qty',"Please Enter Qty.","add_error");
		var _popover;
			var message="Enter Qty"
		_popover = $("#qty").popover({
			trigger: "manual,click",
			placement: "top",
			content: message,
			template: "<div class=\"popover\"><div class=\"arrow\"></div><div class=\"popover-inner\"><div class=\"popover-content\"><p></p></div></div></div>"
		});
		$("#qty").addClass('input-has-error');
		valid=false;
		return true;
	}
	var qty=parseInt($("#qty").val());
	var remaining_qty=parseInt($("#original_qty").val());
	if(qty>remaining_qty){
		toastr.error("No More then "+remaining_qty+" Qty are avaliable!!","Error!!");
		$("#qty").focus();
		return true;
	}
	var item_id=$("#fg_item_id").val();
	var product_name=$("#fg_item_id").find("option:selected").data("name");
	var stock_qty=parseFloat($("#fg_item_id").find("option:selected").data("stock_qty"));
	
	var price=parseFloat($("#fg_item_id").find("option:selected").data("price"));
	var qty=parseFloat($("#qty").val());
	var cgst_tax=parseFloat($("#fg_item_id").find("option:selected").data("cgst_tax"));
	var igst_tax=parseFloat($("#fg_item_id").find("option:selected").data("igst_tax"));
	var sgst_tax=parseFloat($("#fg_item_id").find("option:selected").data("cgst_tax"));
	
	if(item_id!=0){
		var duplicate=$("input.item_id[value='"+item_id+"']").length;
		var design_name=$("#design_name").find('option:selected').data("name");
		if(duplicate==0)
		{
			var place_of_supply=$("#place_of_supply").val();
			var subtotal=parseFloat(price)*qty;
			$('#original_igst_tax'+item_id).val(igst_tax);
			if(place_of_supply=="1")
			{
				var cgst_tax_total=parseFloat(subtotal*cgst_tax/100);
				var sgst_tax_total=parseFloat(subtotal*sgst_tax/100);			
				var igst_tax_total=0;
				
				$('#igst_tax'+item_id).val(igst_tax_total);		
				$("#extra_spec_item").find('tbody').find('tr').find('td').find("span.tax_label_igst").html(0);
			   
			}
			else
			{
				var cgst_tax_total=0;
				var sgst_tax_total=0;			
				var igst_tax_total=parseFloat(subtotal*igst_tax/100);
				$('#igst_tax'+item_id).val(igst_tax);
				$('#sgst_tax'+item_id).val(sgst_tax_total);
				$('#sgst_tax'+item_id).val(cgst_tax_total);
				$("#datatable_1").find('tbody').find('tr').find('td').find("span.cgst_tax").html(0);
				$("#datatable_1").find('tbody').find('tr').find('td').find("span.sgst_tax").html(0);
			}
			serial=$("tr.invoice_item").length+1;
				count=item_id;
				
				$.ajax({
					url:"tax_spinner_of_pro_forma.php",
					type:"POST",
					data:{
						mode:"get_tax",
						item_id:item_id,
						count:count,
						qty:qty,
						
					},
					beforeSend:function(){
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
					success:function(result){
						if(result!="")
						{
							var html=result;
							//$("#cgst_tax_spinner"+item_id).append(html);
							$("#extra_spec_item").find('tbody').append(html);
							var tax_total=cgst_tax_total+sgst_tax_total+igst_tax_total+subtotal;
							$('#amount'+item_id).val(aj.roundNumber(tax_total,2));	
							maintainDatatable();
							recalculateFinalValues();
							$("#qty").val("").html();           
							$("#fg_item_name").select2("val","");
							$("#design_name").select2("val","");
							// $("#loading-modal").modal('hide');
							$('.preloader').fadeOut('slow');	
						}
						
					},
					error:function(){
						toastr.error("We could not process right now try again!!","Error");
					}
					
				});
				
			
				
				
				//TAX
			
			 
		
				

		}
		else
		{
			toastr.error("Product already there remove first to add it again.","Error!!");
		}

	}
	else{
		
		toastr.error("Please Select AtLeast One Product.","Error!!");
	}
}) 
	
</script>
</body>
</html>