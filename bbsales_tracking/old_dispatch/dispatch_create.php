<?php
$page_id=444;$page_slug='page_dispatch';
require_once("connect.php");
$ctable="dispatch";
$ctable1="Dispatch";
$page_hierarchy=array(array("link"=>"","title"=>$ctable1),array("link"=>"combine_".$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
/* include('../include/product.class.php');
include('../include/customer.class.php');
$page_title="Ready To Dispatch";
$product=new Product();
$customer=new Customer();
$page_id=407;$page_slug='purchase_order_pages';
if(isset($_REQUEST['submit']))
{
	if(isset($_REQUEST['order_item']) && isset($_REQUEST['billing_details']) && isset($_REQUEST['order_id']) && isset($_REQUEST['store_id']))
	{
		$order_item=$_REQUEST['order_item'];
		$billing_details=$_REQUEST['billing_details'];
		$order_id=$_REQUEST['order_id'];
		$store_id=$_REQUEST['store_id'];
		
		// Check Order Exist or Not
		$order_information=$db->rp_getData("order_detail","*","id='".$order_id."'");
		if($order_information)
		{
			$order_information=mysqli_fetch_assoc($order_information);
			if($order_information['order_status']==2 || $order_information['order_status']==3 || $order_information['order_status']==4)
			{
				$customer_infomation=$db->rp_getData("customer","*","id='".$order_information['cid']."'");
				if($customer_infomation)
				{
					$customer_infomation=mysqli_fetch_assoc($customer_infomation);
					$customer_infomation['payment_due_date']=date("Y-m-d",strtotime("+".$customer_infomation['credit_limit_days']));
					$customer_branch_information=$db->rp_getData("customer_branch","*","id='".$order_information['cbid']."'");
					if($customer_branch_information)
					{
						$customer_branch_information=mysqli_fetch_assoc($customer_branch_information);
						$customer_branch_account_information=$db->rp_getData("account_info","*","cbid='".$order_information['cbid']."'");
						if($customer_branch_account_information)
						{
							$customer_branch_account_information=mysqli_fetch_assoc($customer_branch_account_information);
							$isValidOrder=true;
						}
						else
						{
							$isValidOrder=false;
							$error_msg[]="Customer branch account either blocked or not found";
						}
						
					}
					else
					{
						$isValidOrder=false;
						$error_msg[]="Customer branch either blocked or not found";
					}
					
				}
				else
				{
					$isValidOrder=false;
					$error_msg[]="Customer either blocked or not found";
				}
				
			}
			else
			{
				$error_msg[]=" Either order not approved,cancelled or already dispatched.";
				$isValidOrder=false;
			}
		}
		
		
		if($isValidOrder)
		{
			$inValidOrderedItem=false;
			// Check Items
			foreach($order_item as $key=>$item)
			{
				if($item['qty']!=0)
				{
					// Check Whether Item Avaialble In Product master or not
					$order_item_information=$db->rp_getData("order_item","*","id='".$item['id']."'","",0);
					if($order_item_information)
					{
						$order_item_information=mysqli_fetch_assoc($order_item_information);
						$item_id=$order_item_information['order_item_id'];
						$item_qty=$item['qty'];
						$item_information=$db->rp_getData("product","*","id='".$item_id."'","",0);
						if($item_information)
						{
							$item_information=mysqli_fetch_assoc($item_information);
							$store_item_info=$product->getStoreProductInformation($store_id,$item_information['id']);
							if($store_item_info)
							{
								if($store_item_info['stock_qty']-$item_qty<0)
								{
									$inValidOrderedItem=true;
									$error_msg[]=$item_information['product_name']."".$item_qty." Not Avaialble In Selected Store. Avaialble Qty.".$store_item_info['stock_qty'];
								}							
							}
							else
							{
								$inValidOrderedItem=true;
								$error_msg[]=$item_information['product_name']." Not Avaialble In Selected Store.";
							}
						}
						else
						{
							$inValidOrderedItem=true;
							$error_msg[]="Count ".$key." Skipped no information found for item.";
						}
					}
					else
					{
						$inValidOrderedItem=true;
						$error_msg[]="Order item not found";
					}
					
					
				}
				else
				{
					$error_msg[]="Count ".$key." Skipped due to 0 Qty.";
				}
			}
			
			if(!$inValidOrderedItem)
			{
				// Create Entry In Dispatch Info Table First
				$dispatch_no=$db->getlastInsertId("order_detail");
				$dispatch_no=DISPATCH_NO.str_pad($dispatch_no, 4, 0, STR_PAD_LEFT);
				$dispatch_information_columns=array(
				"dispatch_no",
				"order_id",
				"dispatch_customer_name",
				"dispatch_cid",
				"dispatch_cbid",
				"dispatch_customer_phone",
				"dispatch_customer_city",
				"dispatch_customer_pincode",
				"dispatch_customer_address",
				"dispatch_billing_address",
				"dispatch_billing_name", 
				"dispatch_billing_phone", 
				"dispatch_billing_email", 
				"dispatch_billing_city", 
				"dispatch_billing_pincode",
				"dispatch_date",
				"status",
				"payment_due_date",
				);
				$dispatch_information_values=array(
				$dispatch_no,
				$order_information['id'],
				$customer_infomation['name'],
				$customer_infomation['id'],
				$order_information['cbid'],
				$customer_infomation['cellphone'],
				$customer_infomation['city'],
				$customer_infomation['pincode'],
				$customer_infomation['address'],
				$billing_details['billing_address'],
				$billing_details['billing_company_name'],
				$billing_details['billing_phone'],
				$billing_details['billing_email'],
				$billing_details['billing_city'],
				$billing_details['billing_pincode'],
				date("Y-m-d"),
				0,
				$customer_infomation['payment_due_date']
				);
				$dispatch_id=$db->rp_insert("dispatch_info",$dispatch_information_values,$dispatch_information_columns,0);
				if($dispatch_id!=0)
				{
					$total_dispatch_qty=0;
					$total_dispatch_discount_amount=0;
					$total_dispatch_discount_subtotal=0;
					$total_dispatch_discount_grandtotal=0;
					foreach($order_item as $key=>$item)
					{
						if($item['qty']!=0 && $item['qty']!="")
						{
							// Check Whether Item Avaialble In Product master or not
							$order_item_information=$db->rp_getData("order_item","*","id='".$item['id']."'","",0);
							if($order_item_information)
							{
								$order_item_information=mysqli_fetch_assoc($order_item_information);
								$item_id=$order_item_information['order_item_id'];
								$item_qty=$item['qty'];
								$item_information=$db->rp_getData("product","*","id='".$item_id."'","",0);
								if($item_information)
								{
									$item_information=mysqli_fetch_assoc($item_information);
									$store_item_info=$product->getStoreProductInformation($store_id,$item_information['id']);
									if($store_item_info)
									{
										$sub_total=$order_item_information['order_item_selling_price']*$item_qty;
										$discount_percentage=$order_item_information['order_item_discount'];
										$discount_amount=($sub_total*$discount_percentage)/100;
										$OtherTax=$order_item_information['other_tax'];
										$VATTax=$order_item_information['tax'];
										$OtherTaxAmount=($OtherTax*$sub_total)/100;
										$VATTaxAmount=($sub_total*$VATTax)/100;
										$grand_total=$sub_total;
										$columns=array("dispatch_id", "order_id", "order_item_id", "dispatch_item_id", "dispatch_item_name", "dispatch_item_code", "dispatch_item_selling_price","dispatch_item_orignal_price", "order_qty", "remaining_qty", "dispatch_item_qty", "dispatch_item_sub_total","dispatch_item_discount", "dispatch_item_discount_amount","dispatch_item_grand_total","dispatch_item_vat_tax","dispatch_item_vat_tax_amount","dispatch_item_other_tax","dispatch_item_other_tax_amount", "created_date");
										$values=array($dispatch_id, $order_id, $order_item_information['id'], $item_id,$order_item_information['order_item_name'], $order_item_information['order_item_code'], $order_item_information['order_item_selling_price'],$order_item_information['order_item_original_price'], $order_item_information['order_item_qty'], $order_item_information['order_item_qty'], $item_qty, $sub_total,$discount_percentage,$discount_amount, $grand_total,$VATTax,$VATTaxAmount,$OtherTax,$OtherTaxAmount,date("Y-m-d H:i:s"));
										$dispatch_item_id=$db->rp_insert("dispatch_item",$values,$columns,0);
										
										$new_stock_qty=$store_item_info['stock_qty']-$item_qty;
										$StoreUpdated=$product->UpdateStoreStock($store_id,$item_information['id'],$new_stock_qty);
										$total_dispatch_discount_amount+=$discount_amount;	
										$total_dispatch_discount_subtotal+=$sub_total;	
										$total_dispatch_discount_grandtotal+=$grand_total;	
										$total_dispatch_qty+=$item_qty;	
										$total_tax_amount+=$VATTaxAmount;	
										$total_other_tax_amount+=$OtherTaxAmount;	
									}
									
								}
							}
							
							
							
						}
						
					}
					$total_dispatch_discount_grandtotal=$total_dispatch_discount_subtotal-$total_dispatch_discount_amount;
					$isUpdated=$db->rp_update("dispatch_info",array("dispatched_qty"=>$total_dispatch_qty,"dispatch_discount"=>$total_dispatch_discount_amount,"dispatch_subtotal"=>$total_dispatch_discount_subtotal,"dispatch_grandtotal"=>$total_dispatch_discount_grandtotal,"tax_amount"=>$total_tax_amount,"other_tax_amount"=>$total_other_tax_amount),"id='".$dispatch_id."'");
					if($isUpdated)
					{
						// Make Debit Entry To Customer Account;
						$customer->debitAmount($customer_branch_information['id'],$customer_branch_account_information['id'],$total_dispatch_discount_grandtotal,"Dispatch#".$dispatch_no." Of Order # ".$order_information['order_no'],$dispatch_id,"dispatch_info");
						
						// Generate Invoice
						$sales_invoice_reply=$customer->GenerateSalesInvoice($dispatch_id);
						
						//Update order status
						$order_items=$db->rp_getData("order_item","*","order_id='".$order_id."'","",0);
						if($order_items)
						{
							while($order_item=mysqli_fetch_assoc($order_items))
							{
								$ordered_qty=$order_item['order_item_qty'];
								$total_dispatch_qty=$db->rp_getValue("dispatch_item","SUM(dispatch_item_qty)","order_id='".$order_id."' AND dispatch_item_id='".$order_item['order_item_id']."'");
								$total_dispatch_qty=($total_dispatch_qty!="")?floatval($total_dispatch_qty):0;
								$remaining_to_dispatch_qty=$ordered_qty-$total_dispatch_qty;
								$remaining_to_dispatch_qty=($remaining_to_dispatch_qty>0)?$remaining_to_dispatch_qty:0;
								$db->rp_update("order_item",array("order_item_dispatched_qty"=>$total_dispatch_qty,"order_item_remaining_qty"=>$remaining_to_dispatch_qty),"order_id='".$order_id."' AND id='".$order_item['id']."'",0);
							}
							
							// Check whether all item dispatched or not
							$ItemRemainToDispatch=$db->rp_getTotalRecord("order_item","order_id='".$order_id."' AND order_item_remaining_qty>0");
							if($ItemRemainToDispatch>=1)
							{
								$order_status=3;
							}
							else
							{
								$order_status=4;
							}
							
							$isOrderStatusUpdated=$db->rp_update("order_detail",array("order_status"=>$order_status),"id='".$order_id."'");
						}
						
						$db->addSuccessMessage("Dispatched Saved Successfully");
					}
					else
					{
						$db->rp_delete("dispatch_info","id='".$dispatch_id."'");
						$db->rp_delete("dispatch_items","id='".$dispatch_id."'");
						$db->addErrorMessage("Dispatch Could Not Made Try Again!!");
					}
					
				}
				else
				{
					$db->addErrorMessage("Dispatch Could Not Made Try Again!!");
				}
				
				
				
			}
			else{
				$db->addErrorMessage("Required Information Not Available Try Again!!<br/>".implode("<br/>",$error_msg));
			}
		}
		else
		{
			$db->addErrorMessage(implode("<br/>",$error_msg));
		}
		
		
	}
	else{
		
		$db->addErrorMessage("Required Information Not Available Try Again!!");
	}
	
} */
?>
<!DOCTYPE html>
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->

   <head>
<meta charset="utf-8"/>
<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>
<?php include("include_css.php"); ?>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
</head>
    <!-- END HEAD -->

    <body class="page-md">
       <?php include("header.php");?>
      
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            
            <!-- BEGIN CONTENT -->
          <div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
			<?php
			$back="combine_dispatch_manage.php";
			?>
				<h1><a href="<?php echo  $back;?>" class="btn primary"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
			</div>
		</div>
	</div>
                <!-- BEGIN CONTENT BODY -->
<div class="page-content">


<!-- BEGIN PAGE BASE CONTENT -->
<!-- END DASHBOARD STATS 1-->
<div class="row">
<div class="col-md-12">						
<div class="portlet box">
<div class="portlet-body">
<?php $db->printErrorMessage(); ?>
<?php $db->printSuccessMessage(); ?>
	<div class="row">	
		
		<div class="portlet light" id="order-selection">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-truck"></i>
					<span class="caption-subject bold uppercase"> Dispatch</span>
					
				</div>
				<div class="actions">
					<a href="javascript:;" data-page="3" class="btn btn-circle btn-default page-change">
						Next  <i class="fa fa-arrow-right"></i></a>

				   
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label>Select Order</label>
								<select class="form-control" name="order_id" placeholder="Select Order" id="order_id" type="text">
									<option>Select Order</option>
									<?php   
									$status=$db->rp_getData("orders","*","isDelete=0 AND isActive=1 AND status=1","",0);
									while($row=mysqli_fetch_assoc($status))
									{
										?>
										<option value="<?php echo $row['order_no']; ?>"><?php echo $row['order_no']; ?></option>
										<?php
									}
									?>
								</select>
							</div>
							
						</div>
						<div class="col-sm-3">
							<div class="form-group">
								<label>Select Store To Dispatch</label>
								<select name="store_id" class="form-control" id="store_id" placeholder="Select Store">
									<option>Select Store</option>
									
								</select>
							</div>
							
						</div>
						
						
					</div>
					<div class="row">
					<div class="col-sm-3 ">
						<div class="row">
						<div class="col-sm-12 ">
							<h4 class="bold"><i class="fa fa-truck"></i>&nbsp;Billing Information</h4>
							<hr/>
						</div>
						</div>
						<div class="row">
						<div class="col-sm-12 ">
							<div class="form-group">
								<input id="billing_customer" class="form-control" type="hidden">
							</div>
							<div class="form-group">
								<label class="control-label">Company Name</label>
								<input id="billing_company_name" class="form-control" placeholder="Enter Company Name" type="text" name="billing_details[billing_company_name]">
							</div>
							<div class="form-group">
								<label class="control-label">Address</label>
								<input id="billing_address" class="form-control" placeholder="Enter Address" type="text" name="billing_details[billing_address]">
							</div>
							<div class="form-group">
								<label class="control-label">Pincode</label>
								<input id="billing_pincode" class="form-control" placeholder="Enter Pincode" type="text" name="billing_details[billing_pincode]">
							</div>
							<div class="form-group">
								<label class="control-label">City</label>
								<input id="billing_city" class="form-control" placeholder="Enter City" type="text" name="billing_details[billing_city]">
							</div>
							<div class="form-group">
								<label class="control-label">Tel.</label>
								<input id="billing_phone" class="form-control" placeholder="Enter Phone" type="text" name="billing_details[billing_phone]">
							</div>
							<div class="form-group">
								<label class="control-label">Email</label>
								<input id="billing_email" class="form-control" placeholder="Enter Email" type="text" name="billing_details[billing_email]">
							</div>
						</div>
						</div>
					</div>
					<div class="col-sm-9 ">
						<div class="row">
						<div class="col-sm-12 ">
							<h4 class="bold"><i class="fa fa-cubes"></i> Order Items</h4>
							<hr/>
						</div>
						</div>
						<div class="row">
						<div class="col-sm-12 ">
							<table id="order-items" class="table table-responsive table-bordered">
								<thead>
								<tr>
									<td colspan="4"></td>
									<td colspan="1">
										<input class="form-control input-lg" placeholder="######">
									</td>
								</tr>
								<tr>
									<th width="1%">	No.</th>
									<th colspan="2">Product Name</th>
									<th>Ordered Qty.</th>
									<th width="15%">Dispatch Qty.</th>
									
								</tr>
								</thead>
								<tbody>
								</tbody>
								
							</table>
						</div>
						</div>
					</div>
					</div>
			</div>
		</div>
			<div class="row">
				<div class="col-sm-12">
					<hr/>
					<a href="javascript:;" data-page="3" class="btn btn-circle btn-default page-change pull-right">
						Next  <i class="fa fa-arrow-right"></i></a>
				   
				</div>
			</div>
		</div>
	   <div class="portlet light" id="dispatch-preview">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-print"></i>
					<span class="caption-subject bold uppercase"> Preview</span>
					
				</div>
				<div class="actions">
				<a href="javascript:;" data-page="1" class="btn btn-circle btn-default page-change"><i class="fa fa-arrow-left"></i>
						Previous  </a>
					<button href="javascript:;" type="submit" name="submit" value="0" class="btn btn-circle btn-default page-change pull-right">
						Save  <i class="fa fa-save"></i></button>
					<button href="javascript:;" name="submit" type="submit" value="1" class="btn btn-circle btn-default page-change pull-right">
						Save & Print  <i class="fa fa-print"></i></button>
						
				   
				</div>
			</div>
			<div class="portlet-body">
				<div class="form-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-xs-12" id="dispatch_preview">
								</div>
							
							</div>
						
						</div>
					</div>
				</div>
				<div class="row">
				<div class="col-sm-12">
					<hr/>
					 <a href="javascript:;" data-page="1" class="btn btn-circle btn-default page-change"><i class="fa fa-arrow-left"></i>
						Previous  </a>
					<button href="javascript:;" type="submit" name="submit" value="0" class="btn btn-circle btn-default page-change pull-right">
						Save  <i class="fa fa-save"></i></button>
					<button href="javascript:;" name="submit" type="submit" value="1" class="btn btn-circle btn-default page-change pull-right">
						Save & Print  <i class="fa fa-print"></i></button>
				</div>
			</div>
		</div>

	</div>						
</div>
</div>	
</div>
</div>
<!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->

</div>

<!-- END CONTAINER -->
        <?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript"> 
var validFlags={validDispatchItems:false};
var ErrorMsg={invalidItemQty:"Enter some qty to dispatch.",invalidOrder:"Select order first",invalidStore:"Select store first",invalidVehical:"Select vehical first"};
var order=null;
var order_id=null;
var store=null;
var dispatch_billing_information={};
var dispatch_items=[];
var input_billing_customer;   
var input_billing_company_name;     
var input_billing_address;   
var input_billing_pincode;   
var input_billing_city;   
var input_billing_phone;   
var input_billing_email;   
$(function(){
	initComponent();
	showPage(1);
	GetApprovedOrders();
	GetStore();
	// Listners
	$(".page-change").on("click",function(){
		var page=$(this).data("page");
		showPage(page);
	});
	
	$("#dispatch-form").on("submit",function(){
		
		if(order!=null && order!="")
		{	
			if(store!=null && store!="")
			{
				if(validFlags.validDispatchItems)
				{
					return true;	
				}
				else
				{
					$(".store-qty-input")[0].focus();
					toastr.error(ErrorMsg.invalidItemQty);
				}
			}
			else
			{
				toastr.error(ErrorMsg.invalidStore);
			}
		}
		else
		{
			toastr.error(ErrorMsg.invalidOrder);
		}
		
		return false;
	});
	
	$("#order-items").find("tbody").on("focus",".store-qty-input",function(){
		highlightRow($(this));
	})
	
	$("#order-items").find("tbody").on("blur",".store-qty-input",function(){
		dimRow($(this));
	})
	$("#order_id").on("change",function(){
		order_id=$(this).val();
		GetOrderDetail($(this).val());
	})
	$("#store_id").on("change",function(){
		store=$(this).val();
	})
	
	$("#order-items").find("tbody").on("change",".store-qty-input",function(){
		var order_item_id=$(this).data("order-item-id");
		var qty=$(this).val();
		gatherDispatchItem()
		
	})
	
})

// Functions 
function gatherDispatchItem()
{
	validFlags.validDispatchItems=false;
	dispatch_items=[];
	$("#order-items").find("tbody").find("tr").each(function(i,v){
		var order_item_id=$(v).find(".store-qty-input").data("order-item-id");
		var remaining_qty=$(v).find(".store-qty-input").data("order-item-remaining-qty");
		var qty=$(v).find(".store-qty-input").val();
		qty=(qty!="")?parseFloat(qty):0;
		if(qty>remaining_qty)
		{
			$(v).find(".store-qty-input").val(0);
		}
		else
		{
			if(qty!=0)
			validFlags.validDispatchItems=true;
			dispatch_items.push({"id":order_item_id,"qty":qty})
		}
		
	});
}
function initComponent()
{
	input_billing_customer=$("#billing_customer");   
	input_billing_company_name=$("#billing_company_name");   
	input_billing_address=$("#billing_address");      
	input_billing_pincode=$("#billing_pincode");      
	input_billing_city=$("#billing_city");     
	input_billing_phone=$("#billing_phone");     
	input_billing_email=$("#billing_email");     
}
function showPage(page)
{
	if(page==1)
	{
		$("#order-selection").show();
		$("#store-selection").hide();
		$("#dispatch-preview").hide();
	}
	
	else if(page==3)
	{
		if(order!=null && order!="")
		{	
			if(store!=null && store!="")
			{
				if(validFlags.validDispatchItems)
				{
					$("#order-selection").hide();
					$("#store-selection").hide();
					$("#dispatch-preview").show();
					GetPreview();	
				}
				else
				{
					$(".store-qty-input")[0].focus();
					toastr.error(ErrorMsg.invalidItemQty);
				}
			}
			else
			{
				toastr.error(ErrorMsg.invalidStore);
			}
		}
		else
		{
			toastr.error(ErrorMsg.invalidOrder);
		}
		
	}
		
}

function highlightRow(input){
	$(input).closest("tr").addClass("bg-grey-steel bg-font-grey-steel");
}
function dimRow(input){
	$(input).closest("tr").removeClass("bg-grey-steel bg-font-grey-steel");
}
function setBillingAddress(cid,customer_name,customer_address,customer_phone,customer_email,customer_city,customer_pincode,customer_country)
{
	$(input_billing_customer).val(cid);
	$(input_billing_company_name).val(customer_name);
	$(input_billing_address).val(customer_address);
	$(input_billing_city).val(customer_city);
	$(input_billing_pincode).val(customer_pincode);
	$(input_billing_phone).val(customer_phone);
	$(input_billing_email).val(customer_email);
}
function setVehical(vid,vehical_name,vehical_no,driver_name,driver_email,driver_phone,driver_other_contact,driver_address_permenant,driver_address_residential)
{
	vehical_information="<tr><td><b>Vehical Name</b></td><td>"+vehical_name+"</td><td><b>Vehical No</b></td><td>"+vehical_no+"</td></tr>"
	vehical_information+="<tr><td><b>Driver Name</b></td><td>"+driver_name+"</td><td><b>Email</b></td><td>"+driver_email+"</td></tr>"
	vehical_information+="<tr><td><b>Phone</b></td><td>"+driver_phone+"</td><td><b>Other Contact</b></td><td>"+driver_other_contact+"</td></tr>"
	vehical_information+="<tr><td><b>Permenant Address</b></td><td>"+driver_address_permenant+"</td><td><b>Residential Address</b></td><td>"+driver_address_residential+"</td></tr>"
	$("#vehical_infomation").html(vehical_information);
}
// AJAX Functions
function GetApprovedOrders(){
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"approved_orders",
		},
		success:function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				var options='<option value=""> Select Order </option>';
				$.each(result.result,function(i,v){
					 options+='<option  value="'+v.id+'">'+v.order_no+" - "+v.customer_name+'</option>';
				});
				$("#order_id").html(options);
			}
			else{
				toastr.error(result.ack_msg);
			}
		}
	});
}
function GetPreview(){
	var dispatch_billing_information={};
	dispatch_billing_information.billing_company_name=$(input_billing_company_name).val();
	dispatch_billing_information.billing_address=$(input_billing_address).val();
	dispatch_billing_information.billing_city=$(input_billing_city).val();
	dispatch_billing_information.billing_phone=$(input_billing_phone).val();
	dispatch_billing_information.billing_email=$(input_billing_email).val();
	dispatch_billing_information.billing_pincode=$(input_billing_pincode).val();
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"preview_dispatch",
			billing_details:dispatch_billing_information,
			dispatch_items:dispatch_items,
			order_id:order_id,
		},
		success:function(result){
			$("#dispatch_preview").html(result);
		}
	});
}
function GetVehicals(){
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"vehical",
		},
		success:function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				var options='<option value=""> Select Vehical </option>';
				$.each(result.result,function(i,v){
					 options+='<option  value="'+v.id+'">'+v.vehical_name+" - "+v.vehical_no+'</option>';
				});
				$("#vehical_id").html(options);
			}
			else{
				toastr.error(result.ack_msg);
			}
		}
	});
}
function GetStore(){
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"stores",
		},
		success:function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				var options='<option value=""> Select Store </option>';
				$.each(result.result,function(i,v){
					 options+='<option  value="'+v.id+'">'+v.store_name+'</option>';
				});
				$("#store_id").html(options);
			}
			else{
				toastr.error(result.ack_msg);
			}
		}
	});
}
function GetVehicalInformation(vehical_id){
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"vehical_information",
			vehical_id:vehical_id,
		},
		success:function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				var count=1;
				var items_row="";
				
				// Get Billing Information
				vehical=result.result;
				
				setVehical(vehical.vid,vehical.vehical_name,vehical.vehical_no,vehical.driver_detail.driver_name,vehical.driver_detail.driver_email,vehical.driver_detail.driver_phone,vehical.driver_detail.driver_other_contact,vehical.driver_detail.driver_address_permenant,vehical.driver_detail.driver_address_residential)	;
				$.each(vehical.stock_item,function(i,item){
					items_row+='<tr>'+
						'<td width="1%">'+
						''+count+
						'</td>'+
						'<td width="1%">'+
						'<img src="'+item.product_image+'" width="80" height="60">'+
						'</td>'+
						'<td>'+
						'<span class="h4 bold">'+item.product_name+'</span>'+
						'<br/><b>SKU: <small>'+item.product_code+'</small></b>'+
						'<br/><b>Packing: <small>Box</small></b>'+
						'<br/><b>Dimension: <small>24 X 24 X 24 X 24</small></b>'+
						'<br/><b>Weight: <small>25 Kg</small></b>'+
						'</td>'+
						'<td>'+
						item.product_qty+
						'</td>'+					
					'</tr>';
					count++;
					
				});
				$("#vehical_stock_items").find("tbody").html(items_row);
			}
			else{
				toastr.error(result.ack_msg);
			}
		}
	});
}

function GetOrderDetail(order_id)
{
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"order_detail",
			order_id:order_id,
		},
		success:function(result){
			result=$.parseJSON(result);
			if(result.ack==1)
			{
				var count=1;
				var items_row="";
				
				// Get Billing Information
				order=result.result;
				setBillingAddress(order.cid,order.customer_name,order.customer_address,order.customer_phone,order.customer_email,order.customer_city,order.customer_pincode,order.customer_country);
				$.each(order.order_item,function(i,item){
					items_row+='<tr>'+
						'<td width="1%">'+
						''+count+
						'</td>'+
						'<td width="1%">'+
						'<img src="'+item.media_url+'" width="80" height="60">'+
						'</td>'+
						'<td>'+
						'<span class="h4 bold">'+item.order_item_name+'</span>'+
						'<br/><b>SKU: <small>'+item.order_item_code+'</small></b>'+
						'<br/><b>Packing: <small>'+item.packaging_type+'</small></b>'+
						'<br/><b>Dimension: <small>'+item.width+' X '+item.height+' X '+item.depth+' X '+item.length+'</small></b>'+
						'<br/><b>Weight: <small>'+item.weight+' '+item.unit_name+'</small></b>'+
						'</td>'+
						'<td>'+
						item.order_item_remaining_qty+
						'</td>'+
						'<td>'+
						'<input class="store-item-id" type="hidden" name="order_item['+item.id+'][id]" value="'+item.id+'" >'+
						'<input class="form-control input-sm store-qty-input" value="'+item.order_item_remaining_qty+'" data-order-item-id="'+item.id+'" data-order-item-remaining-qty="'+item.order_item_remaining_qty+'" name="order_item['+item.id+'][qty]" placeholder="Enter Qty." >'+
						'</td>'+
					'</tr>';
					count++;
					
				});
				$("#order-items").find("tbody").html(items_row);
				$("#order-items").find("tbody").find(".store-qty-input").numeric();
				gatherDispatchItem();
				
			}
			else{
				toastr.error(result.ack_msg);
			}
		}
	});
}

</script>
</body>

</html>
