<?php
$page_id=436;$page_slug='page_pro_forma';
$ctable 	= "pro_forma_invoice_info";
$ctable1 	= "Pro Forma Invoice";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
$page_title = "Pro Forma Invoice";
$page_hierarchy=array(array("link"=>"dashboard.php","title"=>"Order"),array("link"=>"pro_forma_invoice_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
//$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
include("../include/pro_forma_invoice.class.php");
$obj= new ProFormaInvoice();
$customer_order_id		= "";
$pro_forma_invoice_date 		= date("d-m-Y");
$place_of_supply		= "Gujarat";
$invoice_no			= $db->getlastInsertId($ctable);
$pro_forma_invoice_no	= PROFORMAINVOICE_NO.str_pad($invoice_no, 4, 0, STR_PAD_LEFT);

if(isset($_REQUEST['submit'])){
		 
	$detail['customer_order_id'] = $db->clean($_REQUEST['customer_order_id']);
	$detail['invoice_date']		= $db->clean($_REQUEST['invoice_date']);
	$detail['place_of_supply']	= $db->clean($_REQUEST['place_of_supply']);
	$detail['cash_discount']	= $db->clean($_REQUEST['cash_discount']);
	
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
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	
	
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$obj->DeleteProFormaInvoice($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location("pro_forma_invoice_manage.php?msg=inserted");
	}
	else{
		$db->addErrorMessage($reply['ack_msg']);		
	}
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
				<h1><a href="pro_forma_invoice_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?></h1>
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
								<div class="col-md-12 col-sm-12">
										<div class="row">
											<div class="col-md-12 col-sm-6">
												
														<div class="row">
															<div class="col-md-3"> 
															<label>Customer Order<code>*</code></label>
													<select class="form-control" name="customer_order_id" id="customer_order_id" class="customer_order_id">
													<option value=""> -- Select Customer Order --</option>
													<?php 
															$customer_orders=$db->rp_getData('customer_order_request_info',"*","isDelete=0 AND status!=2","",0);
															while($customer_order=mysqli_fetch_assoc($customer_orders))
															{
																
																?>
																<option <?php echo ($customer_order_id==$customer_order['id'])?"selected":"" ; ?> value="<?php echo $customer_order['id']?>" >
																<?php echo $customer_order['company_name']." (".$customer_order['request_no']." )";?>
																</option>
																<?php
															}
														?>
													</select>
													<p class="help-block"></p>
												</div><div class="col-md-3"> 
															<label>Place Of Supply<code>*</code></label>
													<select class="form-control noSelect2" name="place_of_supply" id="place_of_supply" class="place_of_supply" onChange="recalculateFinalValues()">
													<option value=""> -- Select Place of Supply --</option>
													<?php 
															$states=$db->rp_getData('state',"*","isDelete=0","",0);
															while($state=mysqli_fetch_assoc($states))
															{
																
																?>
																<option <?php echo ($place_of_supply==$state['name'])?"selected":"" ; ?> value="<?php echo $state['name']?>"  >
																<?php echo $state['name'];?>
																</option>
																<?php
															}
														?>
													</select>
													<p class="help-block"></p>
												</div>
											<div class="col-md-3">
												<div class="form-group">
													<label>Pro Forma Invoice No.<code>*</code></label>
													<input type="text" name="pro_forma_invoice_no" id="pro_forma_invoice_no" value="<?php if($_REQUEST['mode']=='edit'){echo $pro_forma_invoice_no;}else{echo $pro_forma_invoice_no;}?>" class="form-control" disabled>
													<input type="hidden" name="pro_forma_invoice_no" id="pro_forma_invoice_no" value="<?php if($_REQUEST['mode']=='edit'){echo $pro_forma_invoice_no;}else{echo $pro_forma_invoice_no;}?>" class="form-control" >
												</div>
											</div>
											<div class="col-md-3">
												<div class="form-group">
													<label>Invoice Date<code>*</code></label>
													<input type="text" name="invoice_date" id="invoice_date" value="<?php echo $pro_forma_invoice_date;?>" class="form-control" Placeholder="Invoice Date">
													<p class='help-block'></p>
												</div>
											</div>
											
											</div>
														<div class="row">
															<div class="col-md-12">
																<div class="portlet-body">
																	<div class="table-responsive">
																		<table class="table  table-hover table-bordered table-striped table-list-view" id="order_request_table">
									<thead>
										<tr>
											<th style="min-width:5%;max-width:5%"> </th>
											<th style="min-width:120px" >Item Name</th>
											<th style="min-width:8%;max-width:8%" >HSN</th>
											<th style="min-width:8%;max-width:8%"> Qty</th>
											
											<th style="min-width:8%;max-width:8%"> Price</th>
											<th style="min-width:8%;max-width:8%"> Subtotal</th>
											<th style="min-width:7%;max-width:7%"> Disc.</th>
											<th style="min-width:8%;max-width:8%"> Disc. Amnt</th>
											<th style="min-width:8%;max-width:8%"> Taxable</th>
											<th class="cgst-column" style="min-width:5%;max-width:5%">CGST%</th>
											<th class="sgst-column" style="min-width:5%;max-width:5%">SGST%</th>
											<th class="igst-column" style="min-width:5%;max-width:5%">IGST%</th>
											<th style="min-width:8%;max-width:8%">Amnt</th>
										</tr>
									</thead>
									<tbody>
										<tr class="no-row-item">
											<td colspan="13" align="center">No Data Available</td>
										</tr>
									</tbody>
								<tfoot>
									<tr class="text-right">
										<td></td>
										<td ></td>
										<td ></td>
										<td style="padding-right: 20px"> <span class="order-total-qty">0</span></td>	
										<td></td>
										<td style="padding-right: 20px"><span class='order-total-price'>0</span></td>
										<td></td>
										<td style="padding-right: 20px"><span class='order-total-discount-amount'>0</span></td>
										<td style="padding-right: 20px"><span class='order-total-taxable'>0</span></td>
										<td></td>
										<td></td>
										<td></td>
										<td style="padding-right: 20px"><span class='order-subtotal'>0</span></td>
									</tr>
									<tr class="text-right">
										<td colspan="13">&nbsp;</td>
										
									</tr>
									<tr class="text-right">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>Subtotal</b></td>
										<td style="padding-right: 20px"><span class='whole-order-subtotal'>0</span></td>
									</tr>
									<tr class="text-right">
										<td colspan="9"></td>
										<td colspan="1" class="text-left"><b>Cash</b></td>
										<td colspan="2" class="text-right"><input name="cash_discount" class="cash-discount form-control changable-input percentage text-right" /></td>
										<td style="padding-right: 20px"><span class='whole-order-cash-discount-amount'>0</span></td>
									</tr>
									<tr class="text-right">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>Asse. Value</b></td>
										<td style="padding-right: 20px"><span class='whole-order-asses-amount'>0</span></td>
									</tr>
									<tr class="text-right  igst-row">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>IGST</b></td>
										<td style="padding-right: 20px"><span class='whole-order-igst-amount'>0</span></td>
									</tr>
									<tr class="text-right cgst-row">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>CGST</b></td>
										<td style="padding-right: 20px"><span class='whole-order-cgst-amount'>0</span></td>
									</tr>
									<tr class="text-right sgst-row">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>SGST</b></td>
										<td style="padding-right: 20px"><span class='whole-order-sgst-amount'>0</span></td>
									</tr>
									<tr class="text-right">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>Round Off</b></td>
										<td style="padding-right: 20px"><span class='whole-order-roundoff'>0</span></td>
									</tr>
									<tr class="text-right">
										<td colspan="9"></td>
										<td colspan="3" class="text-left"><b>Grand Total</b></td>
										<td style="padding-right: 20px"><span class='whole-order-grandtotal'>0</span></td>
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
							<div class="row">
								<div class="col-md-12 ">
									<div class="form-group"><br/>
										<button  class="btn btn-success" id="submit" name="submit"> Submit</button>
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
$("#order_request_table").on('click','.delete',function(){
	maintainDatatable();
   $(this).closest('tr').remove();
   recalculateFinalValues();
 });
$("#customer_order_id").change(function (){ //change event for select
	var customer_order_id=$("#customer_order_id").val();
	getCustomerOrder(customer_order_id);
})
$('#qty').numeric();
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
$(function(){
	$("table#order_request_table").on('change','input.changable-input',recalculateFinalValues);

		recalculateFinalValues();
	})
</script>
<script type="text/javascript">
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#customer_order_id").val()=="" || $("#customer_order_id").val().split(" ").join("")==""){		
		vd=aj.error('customer_order_id',"Please Select Customer Order.","add_error");
		isValid=false;
	}
	if($("#invoice_date").val()=="" || $("#invoice_date").val().split(" ").join("")==""){		
		vd=aj.error('invoice_date',"Please Select Invoice Date.","add_error");
		isValid=false;
	}
	
	if(isValid)
	{
		isValid=recalculateFinalValues();
		return isValid;
	}
	else
	{
		return false;
	}
}

function recalculateFinalValues()
{
	var isValid=true;
	var ToFix=2;
	var table=$("table#order_request_table");
	$(table).find('input.changable-input').each(function(i,v){
		
		if(/^[+]?([0-9]+(?:[\.][0-9]*)?|\.[0-9]+)(?:[eE][+-]?[0-9]+)?$/.test($(this).val()))
		{
			if($(this).hasClass("percentage"))
			{
				if($(this).val()>100)
				{
					isValid=false;
					$(this).val(100);
					aj.errorTooltip($(this),'DOWN','Only 100 percentage allowed',true);
				}
				else
				{
					aj.errorTooltip($(this),'DOWN','Only 100 percentage allowed',false);
				}
			}
			aj.errorTooltip($(this),'DOWN','Numbers allowed',false);
		}
		else
		{
			isValid=false;
			$(this).val(0);
			aj.errorTooltip($(this),'UP','Numbers allowed',true);
		}
	})
	var order_total_qty=0;
	var order_total_price=0;
	var order_total_discount_amount=0;
	var order_total_taxable=0;
	var order_total_cgst_tax_amount=0;
	var order_total_sgst_tax_amount=0;
	var order_total_igst_tax_amount=0;
	var order_total_cash_discount=0;
	var order_total_subtotal=0;
	var order_total_grandtotal=0;
	var order_total_roundoff=0;

	var cash_discount=$(table).find("input.cash-discount").val();
	cash_discount=(cash_discount!="")?parseFloat(cash_discount):0;
	var place_of_supply=$("#place_of_supply").val();
	var count =0;
	$(table).find("tr.order-item-row").each(function(){
		count++;
		var qty=$(this).find("input.qty").val();
		var price=$(this).find("input.price").val();
		var discount=$(this).find("input.discount").val();
		qty=(qty!="")?parseFloat(qty):0;
		price=(price!="")?parseFloat(price):0;
		discount=(discount!="")?parseFloat(discount):0;
		var total_price=qty*price;
		var discount_amount=(discount*total_price)/100;
		var mid_taxable=total_price-discount_amount;
		var item_cash_discount_amount=(mid_taxable*cash_discount)/100;
		var subtotal=mid_taxable-item_cash_discount_amount;

		var cgst_tax=$(this).find("span.cgst-tax-lable").data('value');
		var sgst_tax=$(this).find("span.sgst-tax-lable").data('value');
		var igst_tax=$(this).find("span.igst-tax-lable").data('value');
		var cgst_tax_amount=0;
		var sgst_tax_amount=0;
		var igst_tax_amount=0;

		if(place_of_supply=='Gujarat')
		{
			cgst_tax_amount=(subtotal*cgst_tax)/100;
			sgst_tax_amount=(subtotal*sgst_tax)/100;
			igst_tax=0;
				
		}
		else
		{
			igst_tax_amount=(subtotal*igst_tax)/100;
			cgst_tax=0;
			sgst_tax=0;
		}

		grand_total=subtotal+cgst_tax_amount+sgst_tax_amount+igst_tax_amount;
		order_total_qty+=qty;
		order_total_price+=mid_taxable;
		order_total_discount_amount+=discount_amount;
		order_total_taxable+=mid_taxable;
		order_total_cash_discount+=item_cash_discount_amount;
		order_total_subtotal+=subtotal;
		order_total_cgst_tax_amount+=cgst_tax_amount;
		order_total_sgst_tax_amount+=sgst_tax_amount;
		order_total_igst_tax_amount+=igst_tax_amount;
		order_total_grandtotal+=grand_total;
		$(this).find("input.total-price").val(total_price.toFixed(ToFix));
		$(this).find("input.discount-amount").val(discount_amount.toFixed(ToFix));
		$(this).find("input.mid-taxable").val(mid_taxable.toFixed(ToFix));
		$(this).find("input.subtotal").val(mid_taxable.toFixed(ToFix));
		$(this).find("span.cgst-tax-lable").html(cgst_tax.toFixed(ToFix));
		$(this).find("span.sgst-tax-lable").html(sgst_tax.toFixed(ToFix));
		$(this).find("span.igst-tax-lable").html(igst_tax.toFixed(ToFix));
	})

	$(table).find("span.order-total-qty").html(order_total_qty.toFixed(ToFix));
	$(table).find("span.order-total-price").html(order_total_price.toFixed(ToFix));
	$(table).find("span.order-total-discount-amount").html(order_total_discount_amount.toFixed(ToFix));
	$(table).find("span.order-total-taxable").html(order_total_taxable.toFixed(ToFix));
	$(table).find("span.order-subtotal").html(order_total_taxable.toFixed(ToFix));


	$(table).find("span.whole-order-subtotal").html(order_total_taxable.toFixed(ToFix));
	$(table).find("span.whole-order-cash-discount-amount").html(order_total_cash_discount.toFixed(ToFix));
	$(table).find("span.whole-order-asses-amount").html(order_total_subtotal.toFixed(ToFix));

	if(place_of_supply=='Gujarat')
	{
		$(table).find("tr.cgst-row").show();
		$(table).find("tr.sgst-row").show();
		$(table).find("tr.igst-row").hide();
		/*$(".cgst-column").show();	
		$(".sgst-column").show();	
		$(".igst-column").hide();*/
	}
	else
	{
		$(table).find("tr.cgst-row").hide();
		$(table).find("tr.sgst-row").hide();
		$(table).find("tr.igst-row").show();

		/*$(".cgst-column").hide();	
		$(".sgst-column").hide();	
		$(".igst-column").show();*/
	}
	$(table).find("span.whole-order-cgst-amount").html(order_total_cgst_tax_amount.toFixed(ToFix));
	$(table).find("span.whole-order-sgst-amount").html(order_total_sgst_tax_amount.toFixed(ToFix));
	$(table).find("span.whole-order-igst-amount").html(order_total_igst_tax_amount.toFixed(ToFix));
	order_total_grandtotal=order_total_grandtotal.toFixed(ToFix);
	order_total_rounded_grandtotal=Math.round(order_total_grandtotal);
	if(order_total_rounded_grandtotal>order_total_grandtotal)
	{
		order_total_roundoff="+"+((order_total_rounded_grandtotal-order_total_grandtotal).toFixed(ToFix));
	}
	else
	{
		order_total_roundoff="-"+((order_total_grandtotal-order_total_rounded_grandtotal).toFixed(ToFix));
	}
	$(table).find("span.whole-order-roundoff").html(order_total_roundoff);
	$(table).find("span.whole-order-grandtotal").html(order_total_rounded_grandtotal.toFixed(ToFix));
	if(count<=0){isValid=false}else{isValid=true;}
	return isValid;
}


function getCustomerOrder(customer_order_id)
{
	$.ajax({
		url:"get_customer_order_item.php",
		type : 'POST',
        dataType : 'json',
        allowClear: true,		
        quietMillis: 250,
		data:{
			mode:"order_detail",
			customer_order_id:customer_order_id,
		},
		success:function(result){
			//alert(JSON.stringify(result.result.items));
			
				var count=1;
				var items_row="";
				
				var order_request=result.result.items;
				var place_of_supply=result.result.place_of_supply;
				$("#place_of_supply").val(place_of_supply);
				$.each(order_request,function(i,item){
					var subtotal=item.price*item.pending_qty;
					items_row+='<tr class="order-item-row">'+
						'<td>'+
						''+count+
						'</td>'+
						'<td>'+
						item.item_name+
						'</td>'+
						'<td>'+
						item.item_code+
						'</td>'+
						'<td>'+
						'<input type="hidden" class="order-request-item-id" name="item['+item.order_request_item_id+'][order_request_item_id]" value="'+item.order_request_item_id+'">'+
						'<input type="hidden" class="item-id" name="item['+item.order_request_item_id+'][item_id]" value="'+item.item_id+'">'+
						'<input type="hidden" class="weight-id" name="item['+item.order_request_item_id+'][weight_id]" value="'+item.weight_id+'">'+
						'<input type="text"   class="form-control qty text-right changable-input" name="item['+item.order_request_item_id+'][qty]"  value="'+item.pending_qty+'">'+
						'</td>'+
						'<td>'+
						'<input type="text"   class="form-control price pull-right text-right changable-input " name="item['+item.order_request_item_id+'][price]" value="'+item.price+'"/>'+
						'</td>'+
						'<td>'+
						'<input type="text"  class="form-control total-price text-right pull-right" disabled item['+item.order_request_item_id+'][total_price] value=""/>'+
						'</td>'+
						'<td>'+
						'<input type="text"  class="form-control pull-right discount text-right changable-input percentage" name="item['+item.order_request_item_id+'][discount]"  id="discount" value="0"/>'+
						'</td>'+
						'<td>'+
						'<input type="text"  class="form-control discount-amount pull-right text-right" disabled name="item['+item.order_request_item_id+'][discount_amount]" value=""/>'+
						'</td>'+
						'<td>'+
						'<input type="text"  class="form-control mid-taxable pull-right text-right" disabled name="item['+item.order_request_item_id+'][taxable]" value=""/>'+
						'</td>'+
						'<td class="text-center cgst-column" style="padding-top:15px;">'+
						'<span class="cgst-tax-lable" data-value="'+item.cgst+'">'+item.cgst+'</span>'+
						'</td>'+
						'<td class="text-center sgst-column" style="padding-top:15px;">'+
						'<span class="sgst-tax-lable" data-value="'+item.sgst+'">'+item.sgst+'</span>'+
						'</td>'+
						'<td class="text-center igst-column" style="padding-top:15px;">'+
						'<span class="igst-tax-lable" data-value="'+item.igst+'">'+item.igst+'</span>'+
						'</td>'+
						'<td>'+
						'<input type="text" class="form-control subtotal text-right pull-right" disabled name="item['+item.order_request_item_id+'][subtotal]" value=""/>'+
						'</td>'+
					'</tr>';
					count++;
					
				});
				$("#order_request_table").find("tbody").html(items_row);
				recalculateFinalValues();
		}
	});
}
function maintainDatatable()
{
	if($("#order_request_table").find("tbody").find("tr.invoice_item").length>=1)
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
</body>
</html>