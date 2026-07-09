<?php
$page_id=569;$page_slug='dispatch_pages';
$page_slug="manage_inward_store";
$ctable 	= "dispatch_detail";
$ctable1 	= "Dispatch Order";
$main_page 	= $ctable;
$page 		=$ctable."_crud";
$page_title = ucwords($_REQUEST['mode'])." "."Dispatch";
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"dispatch_manage.php","title"=>$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
include('../include/product.class.php');
include("../include/dispatch.class.php");
$objDispatch= new Dispatch();
$product=new Product();
$isActive		= 0;
$count="";
$total_qty="";
$total_amount="";
$grand_total="";
$item_info=array();
if(isset($_REQUEST['submit'])){
	//print_r($_REQUEST); exit;
	$mode=$_REQUEST['submit'];
	$isActive					= 1;
	$detail['isDelete']			= 0;
	$detail['order_id']=$_REQUEST['order_id'];
	$detail['remark']=$_REQUEST['remark'];
	$detail['dispatch_date']=$_REQUEST['dispatch_date'];
	$detail['expected_dispatch_date']=$_REQUEST['expected_dispatch_date'];
	$detail['warehouse_id'] = isset($_REQUEST['warehouse_id'])?$db->clean(implode(",", $_REQUEST['warehouse_id'])):"";
	$detail['transport_through'] = isset($_REQUEST['transport_through'])?$db->clean($_REQUEST['transport_through']):"";
	$detail['transport_name'] = isset($_REQUEST['transport_name'])?$db->clean($_REQUEST['transport_name']):"";
	$item=$_REQUEST['order_item']; 
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{		
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objDispatch->InsertDispatch($detail,$item,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$type=$db->rp_getValue("orders","customer_type","id='".$reply['type']."'");
			if($mode=="print")
			{
				$dispatch_id=$reply['dispatch_id'];
				$_SESSION['print']=$dispatch_id;
				$db->rp_location("dispatch_crud.php?mode=add&id=".$dispatch_id);
			}
			else
			{
				$db->rp_location("dispatch_manage.php?msg=inserted");
			}
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit")
{
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}

	$detail['id']=$_REQUEST['id'];
	$Order=$db->rp_getValue("orders","customer_id","id=".$_REQUEST['id']." AND isDelete=0",0);
	$customer_name=$db->rp_getValue("executive","cname","id=".$Order." AND isDelete=0",0);
	$page_title=ucwords($_REQUEST['mode']).'&nbsp'."Order"."- ".ucwords($customer_name).'&nbsp';	
	$reply=$objDispatch->GetOrder($detail);
	$item_info=$objDispatch->GetOrderItems($detail);
		
	if($reply['ack']==1)
	{
		$id=$_REQUEST['id'];
		$result=$reply['result'];
		extract($result);
	}
	if($item_info['ack']==1)
	{
		$store_inward_id=$_REQUEST['id'];
		$item_info=$item_info['result'];
	}
	else
	{
		$item_info=array();
	}

}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$detail['order_id']=$_REQUEST['order_id'];
	$reply=$objDispatch->DeleteDispatch($detail);
	if($reply['ack']==1)
	{
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("dispatch_manage.php?id=".$_REQUEST['order_id']."&order_no=".$_REQUEST['order_no']."&msg=inserted");
	}
	else
	{
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
	$db->rp_location($ctable."_manage.php?msg=updated");
}


if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!="")
{
	$status = $_REQUEST['status'];
	$rows 	= array("isActive"	=> $status);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="cash_payment_flag")
{
	$rows  = array("cash_payment" => 1, "status" => 1);
	$where = "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->addSuccessMessage("Manual close update successfully!");
	$db->rp_location("dispatch_manage.php?msg=updated");
	// if($cash_payment_update)
	// {
	// 	// $db->addSuccessMessage($reply['ack_msg']);
	// 	$db->rp_location("dispatch_manage.phpmsg=updated");
	// }
	// else
	// {
	// 	$db->addErrorMessage($reply['ack_msg']);
	// }
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
<link rel="stylesheet" type="text/css" href="css/fSelect.css" />
</head>
<body class="page-md">
<?php include("header.php"); ?>
<form role="form" enctype="multipart/form-data" action="" method="post" onSubmit="return check_form();">
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
			<?php
			?>
				<h1><a href="dispatch_manage.php?id=<?php echo $order_id;?>&flag=<?php echo $_REQUEST['flag'];?>&order_no=<?php echo $_REQUEST['order_no'];?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
			<div class="row" id="order-selection">
				<div class="col-md-12">
					<div class="portlet light">
						<div class="portlet-title">
							<div class="caption">
								<i class="fa fa-truck"></i>
								<span class="caption-subject bold uppercase"> Dispatch</span>
								
							</div>
							<div class="actions">
								<a href="javascript:;" data-page="2" class="btn btn-circle btn-default page-change">
									Next  <i class="fa fa-arrow-right"></i></a>
							<button href="javascript:;" type="submit" name="submit" value="0" class="btn btn-circle btn-default page-change pull-right">
							Save  <i class="fa fa-save"></i></button>

							   
							</div>
						</div>
						<div class="portlet-body form">
							<div class="row">
								<div class="col-sm-12">
									<div class="row">
										<div class="col-md-12 col-sm-12">
											<div class="row">
												<div class="col-sm-12">
													<div class="row">
														<div class="col-md-12">
															<div class="form-body">
																<div class="form-group">
																	<div class="row">
																		<div class="col-md-12">
																			<div class="row">
																				<div class="col-md-2">
																					<div class="form-group">
																					<label>Orders <code>*</code></label>
																					<select class="form-control" name="order_id" id="order_id">
																					<option value="">Select Order</option>
																					<?php
																						if($_REQUEST['mode']=="add" && $_REQUEST['order_id'])
																						{
																							$order_no = $db->rp_getValue("orders","order_no","id='".$_REQUEST['order_id']."'",0);
																						}

																						$product_list_d=$db->rp_getData('orders',"*","isDelete=0 AND status!=2 AND status!=0","",0);
																						while($product_list_r=mysqli_fetch_assoc($product_list_d))
																						{
																							?>
																							<option <?=($order_no == $product_list_r['order_no'])?"selected":"";?> value="<?php echo $product_list_r['id']; ?>"  data-customer_id="<?php echo $product_list_r['customer_id'] ?>">
																							<?php echo $product_list_r['order_no']." - ".$product_list_r['company_name']; ?>
																							</option>
																							<?php
																						}
																						?>
																					</select>
																					<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-2">
																					<div class="form-group">
																						<label>Dispatch Date <code>*</code></label>
																						<input type="text" name="dispatch_date" id="dispatch_date" class="form-control" value="<?=date('d-m-Y');?>">
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-2">
																					<div class="form-group">
																						<label>Expected Dispatch Date</label>
																						<input type="text" name="expected_dispatch_date" id="expected_dispatch_date" class="form-control">
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-2">
																					<div class="form-group">
																						<label>Transport By</label>
																						<!-- <input id="transport_by" type="text" class="form-control" disabled=""> -->
																						<select class="form-control" name="transport_through" id="transport_through" onchange="getTransportname(this.value);">
									                                                    	<option value="">Select Transport By</option>
									                                                        <?php
									                                                        $transport_by_r = $db->rp_getData("transport_by","*","isDelete=0");
									                                                        if(mysqli_num_rows($transport_by_r)>0)
									                                                        {
									                                                        	if($_REQUEST['mode']=='add' && $_REQUEST['order_id']!="")
									                                                        	{
									                                                        		$transport_through = $db->rp_getValue("orders","transport_through","id='".$_REQUEST['order_id']."' AND isDelete=0");
									                                                        	}
									                                                            while($transport_by_d = mysqli_fetch_array($transport_by_r))
									                                                            {
									                                                                ?>
									                                                                <option value="<?php echo $transport_by_d['id']; ?>" <?php if($transport_by_d['id']==$transport_through){?> selected <?php } ?>><?php echo $transport_by_d['name']; ?></option>
									                                                                <?php
									                                                            }
									                                                        }
									                                                        ?>
									                                                    </select>
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-2">
																					<div class="form-group">
																						<label>Transporter Detail</label>
																						<select class="form-control" name="transport_name" id="transport_name">
																							<option value="">Select Transporter Detail</option>
																							<?php
									                                                        $transport_nameR = $db->rp_getData("transport_master","*","isDelete=0");
									                                                        if(mysqli_num_rows($transport_nameR)>0)
									                                                        {
									                                                        	if($_REQUEST['mode']=='add' && $_REQUEST['order_id']!="")
									                                                        	{
									                                                        		$transport_name = $db->rp_getValue("orders","transport_name","id='".$_REQUEST['order_id']."' AND isDelete=0",0);
									                                                        	}
									                                                            while($transport_named = mysqli_fetch_array($transport_nameR))
									                                                            {
									                                                                ?>
									                                                                <option value="<?php echo $transport_named['id']; ?>" <?php if($transport_named['id']==$transport_name){?> selected <?php } ?>><?php echo $transport_named['name']; ?></option>
									                                                                <?php
									                                                            }
									                                                        }
									                                                        ?>
																						</select>
																						<p class="help-block"></p>
																					</div>
																				</div>

																				<div class="col-md-2">
																					<div class="form-group">
																						<label>Select Warehouse</label>
																						<select class="form-control" name="warehouse_id[]" id="warehouse_id" multiple="">
																							<option value="">Select Warehouse</option>
																							<?php
																								$WarehouseR=$db->rp_getData('warehouse',"*","isDelete=0","",0);
																								while($WarehouseD=mysqli_fetch_assoc($WarehouseR))
																								{
																									?>
																									<option <?=($warehouse_id == $WarehouseD['id'])?"selected":"";?> value="<?php echo $WarehouseD['id']; ?>">
																									<?php echo $WarehouseD['name']; ?>
																									</option>
																									<?php
																								}
																								?>
																						</select>
																						<p class="help-block"></p>
																					</div>
																				</div>
																			</div>


																			<?php if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0) { ?>

																			<div class="row">

																				<div class="col-md-2">
																					<div class="form-group">
																						<label>Category <code>*</code></label>
																						<select class="form-control" name="category_id[]" id="category_id" multiple="multiple">
																							<option value="">select Category</option>
																							<?php
																							$cat_r=$db->rp_getData("top_category_master","*","isDelete=0 AND isActive=1",1);
																							while($cat_d=mysqli_fetch_assoc($cat_r))
																							{
																							?>
																							<option value="<?= $cat_d['id'] ?>"><?= $cat_d['name'] ?></option>
																							<?php
																							}
																							?>
																						</select>
																						<p class="help-block"></p>
																					</div>
																				</div>

																				<div class="col-md-4">
																					<div class="form-group">
																						<label>Products <code>*</code></label>
																						<select class="form-control" name="product_id" id="product_id">
																							<option value="">Select Product</option>
																						</select>
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-3">
																					<div class="form-group">
																						<label>Quantity<code>*</code></label>
																						<input type="text" class="form-control positive" name="qty" id="qty" value="" />
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<div class="col-md-1">
																					<div class="form-group">
																						<br /><button class="btn btn-primary" type="button" id="add">ADD</button>
																						<p class="help-block"></p>
																					</div>
																				</div>
																			</div>

																			<?php } ?>

																		</div>
																	</div>
																	<div class="row">
																		<div class="col-md-12">
																			<table border="1px" style="border:1px solid black;" id="order-items" class="table table-striped table-bordered table-hover">
																				<thead>
																					<tr>
																						<th>No.</th>
																						<th>Product Name</th>
																						<th>Unit</th>
																						<th>Product Description</th>

																						<!-- <th>Bag Qty</th>
																						<th>Box Qty</th>
																						
																						<th>Loose Qty</th> -->

																						<th>Ordered Qty. | Available Qty</th>	
																						<th>Quantity</th>
																						<th>Action</th>
																					</tr>
																				</thead>
																				<tbody>
																				</tbody>
																				<tfoot>
																				</tfoot>
																			</table>
																		</div>
																	</div>
																	<div class="row">
																		<div class="col-md-12">
																			<div class="row">
																				<div class="col-md-4">
																					<div class="form-group">
																						<label>Remarks</label>
																						<textarea class="form-control" name="remark" value="<?php echo $remark; ?>"></textarea>
																						<p class="help-block"></p>
																					</div>
																				</div>
																				<!-- <div class="col-md-4">
																					<div class="form-group">
																						<label>LR Copy </label>
																						<input type="file" name="LR_copy" id="LR_copy" value="" />
																						<p class="help-block"></p>
																						<input class="form-control " value="<?php echo $LR_copy; ?>" name="old_path" id="old_path" type="hidden">
																					</div>
																				</div> -->
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
							</div>
						</div>
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
						<button href="javascript:;" name="submit" type="submit" value="print" class="btn btn-circle btn-default page-change pull-right">
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
							<a href="javascript:;" data-page="1" class="btn btn-circle btn-default page-change"><i class="fa fa-arrow-left"></i>Previous  </a>
							<button href="javascript:;" type="submit" name="submit" value="0" class="btn btn-circle btn-default page-change pull-right">Save  <i class="fa fa-save"></i></button>
							<button href="javascript:;" name="submit" type="submit" value="print" class="btn btn-circle btn-default page-change pull-right">Save & Print  <i class="fa fa-print"></i></button>
						</div>
					</div>
				</div>
			</div>						
		</div>
	</div>	
</div>
</div>
</form>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>

<script type="text/javascript">
		$("#category_id").fSelect({
		    numDisplayed: 1,
		});

	</script>
	
<script type="text/javascript">
$("#qty").numeric();
$(".qty").numeric();
$("#warehouse_id").fSelect();

$(document).ready(function() {

	$("#qty").keyup(function(event) 
	{
		if ( event.keyCode == 46 || event.keyCode == 8 ) {
		// let it happen, don't do anything
		} 
		else if (/\D/g.test(this.value)) 
		{
			alert("sorry!! Only Digits Allowed");
			this.value = this.value.replace(/\D/g, '');
		}
	});
});
// , minDate:0
 $('#dispatch_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });
 $('#expected_dispatch_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy' });
 var dispatch_items=[];
 var order_id=null;
 var dispatch_date=null;
 var validFlags={validDispatchItems:false};
 var ErrorMsg={invalidItemQty:"Enter some qty to dispatch.",invalidOrder:"Select order first",invalidStore:"Select store first",invalidVehical:"Select vehical first",invaidDate:"Select valid dispatch date"};
 $(function(){
 	showPage(1);

 	$("#order_id").change(function(){
 		var order_id=$(this).val();
 		GetItems(order_id); 
 		var cid = $("#order_id").find('option:selected').data('customer_id');
 		getProductList(cid);
 	})

 	$("#order-items").find("tbody").on("change",".qty",function(){
		var order_item_id=$(this).data("order-item-id");
		var qty=$(this).val();
		gatherDispatchItem()
		
	})

	$("#order-items").find("tbody").on("change",".pro_description",function(){
		var order_item_id=$(this).data("order-item-id");
		var qty=$(".qty").val();
		var pro_description=$(this).val();
		gatherDispatchItem()
		
	})

	$(".page-change").on("click",function(){
		var page=$(this).data("page");
		showPage(page);
	});

	var order_id = "<?=$_REQUEST['order_id']?>";
	if(order_id!="")
	{
		$("#order_id").change();
	}

 });
//-------------------------Calculattion--------------------------------//
function showPage(page)
{
	if(page==1)
	{
		$("#order-selection").show();
		$("#dispatch-preview").hide();
	}
	else if(page==2 || page=="undefined")
	{
		order_id=$("#order_id").val();
		dispatch_date=$("#dispatch_date").val();
		if(order_id!=null && order_id!="")
		{	
			if(dispatch_date!=null && dispatch_date!="")
			{
				if(validFlags.validDispatchDescription)
				{
					if(validFlags.validDispatchItems)
					{
						$("#order-selection").hide();
						$("#dispatch-preview").show();
						GetPreview();	
					}
					else
					{
						$(".qty")[0].focus();
						toastr.error(ErrorMsg.invalidItemQty);
					}
				}
				else
				{
					toastr.error(ErrorMsg.invalidDescription);
				}	
			}
			else
			{
				toastr.error(ErrorMsg.invaidDate);
			}
		}
		else
		{
			toastr.error(ErrorMsg.invalidOrder);
		}
	}
	else
	{
		order_id=$("#order_id").val();
		dispatch_date=$("#dispatch_date").val();
		if(order_id!=null && order_id!="")
		{	
			if(dispatch_date!=null && dispatch_date!="")
			{
				if(validFlags.validDispatchDescription)
				{
					if(validFlags.validDispatchItems)
					{
						$("#order-selection").hide();
						$("#dispatch-preview").show();
						GetPreview();	
					}
					else
					{
						$(".qty")[0].focus();
						toastr.error(ErrorMsg.invalidItemQty);
					}
				}
				else
				{
					toastr.error(ErrorMsg.invalidDescription);
				}
			}
			else
			{
				toastr.error(ErrorMsg.invaidDate);
			}
		}
		else
		{
			toastr.error(ErrorMsg.invalidOrder);
		}
	}
		
}
   function recalculateFinalValues()
   {
	  /*var sum=0;
	  var final_sum=0; 
	  var qtytotal=0; 
	  var grand_total=0; 
	  $('.total').each(function () {
			
			total=parseFloat($(this).val());
			total=(total!="")?parseFloat(total):0;
			sum +=total;
			
	});	
	$('.qty').each(function () {
			qty=parseFloat($(this).val());
			//qty=(qty!="")?parseInt(qty):0;
			if (isNaN(qty))
			{			
				qty = 0;
			}
			else{
				qty=parseFloat($(this).val());
			}
			qtytotal +=qty;
	});	
	
	sum=sum.toFixed(2);
	$("#finalTotal").val(''+sum);
	$("#finalQty").val(''+qtytotal);
	$("#finalgrandTotal").val(''+sum);*/
   }
function GetItems(order_id){
	$.ajax({
       // url :  instead of writing the function to execute the request we use Select2's convenient helper
	    url: 'orders_item_get_ajax.php',
        type : 'POST',
        dataType : 'json',
        allowClear: true,		
        quietMillis: 250,
		beforeSend:function(){
			$("#loading-modal").modal({backdrop: 'static', keyboard: false});
		},
        data:{          
				order_id:order_id,
            },
        success: function (data) { // parse the results into the format expected by Select2.
        	var po_items=data.result.items;	
			var count=0;
			
			$("#transport_by").val(data.result.transport_by);
			$("#transport_through").val(data.result.transport_by);
			$("#transport_name_selected_id").val(data.result.transport_by);

			$.each(po_items,function(index,value){
				count=++count;
				var single_po_item=po_items[index];	
				var total=single_po_item.remaining_qty*single_po_item.unitprice;	
				var new_row="<tr>"+
						"<td><input type='hidden' name='count[]' value='"+count+"' id='count' class='count'/>"+count+"</td>"+
						"<td><input type='hidden' name='order_item["+single_po_item.id+"][product_id]' value='"+single_po_item.pro_id+"' class='product_id' id='product_id'/><input type='hidden' name='order_item["+single_po_item.id+"][weight_id]' value='"+single_po_item.weight_id+"' id='weight_id'><input type='hidden' name='order_item["+single_po_item.id+"][free_item]' value='1' id='free_item'><input type='hidden' name='order_item["+single_po_item.id+"][order_item_id]' value='"+single_po_item.id+"' id='order_item_id'>"+single_po_item.pro_name+"</td>"+
						"<td>"+single_po_item.unit_name+"</td>"+
						"<td><textarea class='pro_description' rows='4' cols='30' id='pro_description' name='order_item["+single_po_item.id+"][pro_description]' value='"+single_po_item.pro_description+"' style='margin: 0!important;'>"+single_po_item.pro_description+"</textarea></td>"+

						// "<td>"+single_po_item.box_qty+"</td>"+
						// "<td>"+single_po_item.cartoon_qty+"</td>"+
						// "<td>"+single_po_item.loose_qty+"</td>"+

						"<td>"+single_po_item.remaining_qty+" |"+single_po_item.stock_qty+" <input type='hidden' name='ordered_qty[]' class='form-control ordered_qty' style='text-align:right;width:100px;' value='"+single_po_item.pro_qty+"' id='ordered_qty' disabled><input type='hidden' name='remaining_qty[]' class='form-control remaining_qty' style='text-align:right;width:100px;' value='"+single_po_item.remaining_qty+"' id='remaining_qty' disabled></td>"+
						"<td><input type='text' name='order_item["+single_po_item.id+"][qty]' class='form-control qty' style='text-align:right;width:100px;' value='"+single_po_item.remaining_qty+"' id='qty' data-order-item-id='"+single_po_item.id+"' data-order-item-remaining-qty='"+single_po_item.remaining_qty+"' data-order-item-available-qty='"+single_po_item.stock_qty+"' data-free_item='1'></td>"+
						"<td><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></tr>";
				
				$("#order-items").find('tbody').append(new_row);
				gatherDispatchItem();
				recalculateFinalValues();
				$("#loading-modal").modal('hide');
				//numeric value 
				$(".qty").numeric();
				//Minus value can't enter validation
				
			});			
        },
		cache: true
    });
	$("#order-items").find('tbody').empty();
}
function gatherDispatchItem()
{
	validFlags.validDispatchItems=false;
	validFlags.validDispatchDescription=false;
	dispatch_items=[];
	$("#order-items").find("tbody").find("tr").each(function(i,v){
		var order_item_id=$(v).find(".qty").data("order-item-id");
		var remaining_qty=$(v).find(".qty").data("order-item-remaining-qty");
		var available_qty=$(v).find(".qty").data("order-item-available-qty");
		var free_item=$(v).find(".qty").data("free_item");
		var qty=$(v).find(".qty").val();
		var description=$(v).find(".pro_description").val();
		qty=(qty!="")?parseFloat(qty):0;
		var price=$(v).find(".price").val();
		price=(price!="")?parseFloat(price):0;
		var total=price*qty;	
		total=total.toFixed(2);

		if(qty>remaining_qty)
		{
			$(v).find(".qty").val(0);
			$(v).find("input.total").val(0);	
			toastr.error("Enter less than or equal to order qty ");
		}
		else
		{
			if(description!="" && description!=null)
			{
				validFlags.validDispatchDescription=true;
				if(qty>available_qty)
				{
					//alert('3');
					$(v).find(".qty").val(0);
					$(v).find("input.total").val(0);	
					toastr.error("Enter less than or equal to available qty in store");
				}
				else
				{
					if(qty!=0)
					{
						validFlags.validDispatchItems=true;
						dispatch_items.push({"id":order_item_id,"qty":qty,"free_item":free_item})
						$(v).find("input.total").val(total);
					}
					else
					{
						validFlags.validDispatchItems=false;

					}
					/*if(qty!=0 && description!="" && description!=null)
					/*if(qty!=0)
					{
						validFlags.validDispatchItems=true;
						dispatch_items.push({"id":order_item_id,"qty":qty,"free_item":free_item})
						$(v).find("input.total").val(total);	
					}*/
					/*else
					{
						validFlags.validDispatchItems=false;
						toastr.error("Enter Product Description");
					}*/
				}
			}
			else
			{
				validFlags.validDispatchDescription=false;
				// toastr.error("Enter Product Description");
			}
		}
	});
	recalculateFinalValues();
}
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	if($("#dispatch_date").val()=="" || $("#dispatch_date").val().split(" ").join("")==""){		
		vd=aj.error('dispatch_date',"Please Select Dispatch Date.","add_error");
		isValid=false;
	}

	// if($("#pro_description").val()=="" || $("#pro_description").val().split(" ").join("")==""){		
	// 	vd=aj.error('pro_description',"Please Select","add_error");
	// 	isValid=false;
	// }
	/*var count_row=$("#order-items").find('tr').length;
	alert(count_row);
	if(count_row<=2)
	{
		toastr.error("Please Add At Least one Product!!");
		return false;
	}*/
	$(".qty").each(function () {
			if($(this).val()=="" || $(this).val()==0)
			{
			//alert("Please Enter At Least one Quantity!!");
			$(this).focus();
			isValid=false;
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
function GetPreview(){
	$.ajax({
		url:"dispatch_ajax_function.php",
		type:"GET",
		data:{
			mode:"preview_dispatch",
			dispatch_items:dispatch_items,
			order_id:order_id,
			dispatch_date:dispatch_date
		},
		success:function(result){
			$("#dispatch_preview").html(result);
		}
	});
}
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } });
 $(document).ready(function(){

 $("#order-items").on('click','.delete',function(){
	 //var rows = document.getElementById("#order-items").getElementsByTagName("tr").length;
	 //alert(rows);
       $(this).closest('tr').remove();
	   recalculateFinalValues();
	   gatherDispatchItem();
     });

});
 <?php 
if((isset($_SESSION['print']) && $_SESSION['print']!="")  )
{
	$id=$_SESSION['print'];
	unset($_SESSION['print']);
    echo "printInwardStore(".$id.");" ;
}
?>
function printInwardStore(id) 
{
	var printWindow = window.open('dispatch_generate.php?dispatch_id='+id+"&p=1",'','width=800,height=800')
}
</script>

<script type="text/javascript">
		/*var mode = '<?= $_REQUEST['mode']; ?>';
		var ordersid = '<?= $_REQUEST['order_id']; ?>';
		if(mode=="add" && ordersid!="")
		{
			var id = $("#transport_through").val(); 
			var transport_name_selected_id = $("#transport_name_selected_id").val();
			getTransportname(id,transport_name_selected_id);	
		}*/
		function getTransportname(id,transport_name_selected_id="")
		{	
			$.ajax({
				type: "post",
				url: "ajax_get_transport_detail.php",
				data: "id=" + id+"&selected_id="+transport_name_selected_id,
				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					setTimeout(function() {
						$("#transport_name").select2("destroy");
						$('#transport_name').html(result);
						$("#transport_name").select2();
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}
			})
		}
	</script>

	<script type="text/javascript">

		$("#category_id").on('change', function() {
				var tcid = $("#category_id").val();
				getProductList(tcid);
			});


		function getProductList(tcid) {

			var cid = $("#customer_id").val();

			$.ajax({
				type: "post",
				url: "ajax_get_product.php",
				// data: "cid=" + cid,

				data: "cid=" + cid+"&tcid="+tcid,

				beforeSend: function() {
					$(".transCover").fadeIn(800);
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					/*var cd=$("#customer_id").find("option:selected").data("cash-discount");
					$("#cash_discount").val(cd);
					var ad=$("#customer_id").find("option:selected").data("add-discount");
					$("#additional_discount").val(ad);*/
					setTimeout(function() {
						$('#product_id').html(result);
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}

			})
			var mode = '<?= $_REQUEST['mode']; ?>';
			if (mode == "add") {
				var l = $("#datatable_1").find('tbody').find('tr').length;
				if (l > 0) {
					alert("You lost all added Product");
					$("#datatable_1").find('tbody').html("");
					recalculateRow();
					recalculateFinalValues();
				}
			}
		}

		$("#add").click(function() {
			var product_id = $("#product_id").val();
			var qty = $("#qty").val();
			count = 0;
			//var isProductAvailable=check_form();
			if (product_id == "") {
				toastr.error('Please Select product!!');
			} else if (qty == "" || qty == 0) {
				toastr.error('Please Enter At least one Quantity!!');
			} else {
				/*var stockcheck = $("#product_id").find('option:selected').data('stock_qty');
				if(stockcheck>=qty)
				{*/
				var count = $('.count').length;
				count = ++count;
				var product_id = $("#product_id").val();
				var price = $("#product_id").find('option:selected').data('pricelist');
				var p_name = $("#product_id").find('option:selected').data('name');
				var weight = $("#product_id").find('option:selected').data('weight');
				var weight_id = $("#product_id").find('option:selected').data('weight-id');
				var inner_size = $("#product_id").find('option:selected').data('inner_size');
				var outer_size = $("#product_id").find('option:selected').data('outer_size');
				var pro_id = $("#product_id").find('option:selected').data('pro_id');
				var original_price = $("#product_id").find('option:selected').data('original-price');
				var discountPer = $("#product_id").find('option:selected').data('discount');
				var stock = $("#product_id").find('option:selected').data('stock');
				var catno = $("#product_id").find('option:selected').data('catno');
				var unit_name = $("#product_id").find('option:selected').data('unit_name');

				// alert(pro_id);
				var qty = $("#qty").val();
				var brand_id = $("#brand_id").val();
				// alert(duplicate);
				//var duplicate = hasValue($("input.pro_id[value='" + pro_id + weight_id + "']"));
				var duplicate = 0;
				if (duplicate == 0) {
					var new_row="<tr>"+
					"<td><input type='hidden' name='count[]' value='"+count+"' id='count' class='count'/>"+count+"</td>"+
					"<td><input type='hidden' name='order_item["+product_id+"][product_id]' value='"+pro_id+"' class='product_id' id='product_id'/><input type='hidden' name='order_item["+product_id+"][weight_id]' value='"+weight_id+"' id='weight_id'><input type='hidden' name='order_item["+product_id+"][free_item]' value='0' id='free_item'>"+p_name+"</td>"+
					"<td>"+unit_name+"</td>"+
					"<td><textarea class='pro_description' data-validation='required' data-validation-error_msg='Please Enter Description' rows='4' cols='30' id='pro_description' name='order_item["+product_id+"][pro_description]' value='' style='margin: 0!important;'></textarea></td>"+

					

					"<td>"+qty+" |"+stock+" <input type='hidden' name='ordered_qty[]' class='form-control ordered_qty' style='text-align:right;width:100px;' value='"+qty+"' id='ordered_qty' disabled><input type='hidden' name='remaining_qty[]' class='form-control remaining_qty' style='text-align:right;width:100px;' value='"+qty+"' id='remaining_qty' disabled></td>"+
					"<td><input type='text' name='order_item["+product_id+"][qty]' class='form-control qty' style='text-align:right;width:100px;' value='"+qty+"' id='qty' data-order-item-id='"+product_id+"' data-order-item-remaining-qty='"+qty+"' data-order-item-available-qty='"+stock+"' data-free_item='0'></td>"+
					"<td><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></tr>";
					$("#order-items").find('tbody').append(new_row);
					gatherDispatchItem();
					recalculateFinalValues();
				} else {
					$mainInputBox = $("input.pro_id[value='" + pro_id + weight_id + "']").parent().parent().find("td>input.qty");
					var old_va = $mainInputBox.val();
					var new_va = parseFloat(old_va) + parseFloat(qty);
					$mainInputBox.val(new_va);
					$mainInputBox.change();
					toastr.success("Product Qty Update Successfuly.");
					// toastr.error("Product already added!!");
				}
				/*}
				else
				{
					toastr.error('Entered Quantity Is Not Available In Stock!!');
				}*/
			}
			$("#qty").val("");
			$("#product_id").select2("val", "");
		})


		
		

	</script>

</body>
</html>