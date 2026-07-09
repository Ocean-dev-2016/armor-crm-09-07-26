<?php
$page_id=560;$page_slug='page_order';
$page_slug="manage_inward_store";
$ctable 	= "inward_store";
$ctable1 	= "Inward Store";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Store"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
include('../include/product.class.php');
include("../include/inward_store.class.php");
$objInward= new Inward();
$uid=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
$product=new Product();
$isActive		= 0;
$count="";
$remark="";
$vendor="";
$total_qty="";
$grand_total="";
$item_info=array();
$flag=$_REQUEST['flag'];
//echo $_REQUEST['mode'];exit;
if(isset($_REQUEST['submit'])){

	$isActive					= 1;
	$detail['isDelete']			= 0;
	$product_id=$_REQUEST['product_id'];
	$detail['vendor']=$_REQUEST['vendor'];
	$detail['remark']=$_REQUEST['remark'];
	$detail['uid']=$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
	$qty=$_REQUEST['qty'];
	$price=$_REQUEST['price'];
	$pro_name=$_REQUEST['pro_name'];
	$weight_id=$_REQUEST['weight_id'];
	$size[]=sizeof($product_id);
	$size[]=sizeof($qty);
	$size[]=sizeof($price);
	$size[]=sizeof($pro_name);
	$weight_id[]=sizeof($weight_id);

	$value_check=sizeof($product_id);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}

	if($isValidArray && !empty($product_id))
	{
		for($i=0;$i<sizeof($product_id);$i++)
		{
			$item[]=array("qty"=>$qty[$i],"product_id"=>$product_id[$i],"price"=>$price[$i],"pro_name"=>$pro_name[$i],"weight_id"=>$weight_id[$i]);

		}
	}
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){		
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objInward->InsertInward($detail,$item);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("inward_store_manage.php?msg=inserted");
		}
		else{
				$db->addErrorMessage($reply['ack_msg']);
			}

	}
	
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		
		$reply=$objInward->UpdateInwardItem($detail,$item);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
				$db->rp_location("inward_store_manage.php?msg=inserted");
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

		$detail['id']=$_REQUEST['id'];
		$vendor=$db->rp_getValue("inward_store","vendor","id=".$_REQUEST['id']." AND isDelete=0",0);
		$vendor_name=$db->rp_getValue("vendor","cname","id=".$vendor." AND isDelete=0",0);
		$page_title=ucwords($_REQUEST['mode']).'&nbsp'."Inward"."- ".ucwords($vendor_name).'&nbsp';	
		$reply=$objInward->GetInward($detail);
		$item_info=$objInward->GetInwardItems($detail);
		
	if($reply['ack']==1){

		$id=$_REQUEST['id'];
		$result=$reply['result'];
		extract($result);
	}

	if($item_info['ack']==1){

		$store_inward_id=$_REQUEST['id'];
		$item_info=$item_info['result'];
		//print_r($item_info);exit;

	}
	else{
		$item_info=array();
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
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objInward->DeleteInward($detail);
	if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("inward_store_manage.php?msg=inserted");
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
			<?php
			$back="inward_store_manage.php";
			?>
				<h1><a href="<?php echo  $back;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="row">
								<div class="col-sm-12">
									<div class="tabbable-custom nav-justified">
										<ul class="nav nav-tabs ">
											<li class="active">
												<a href="#tab_inquiry_info" data-toggle="tab" aria-expanded="false"> Inward Store </a>
											</li>
										</ul>
									<div class="tab-content">
											<div class="tab-pane active" id="tab_super_stockist_info">
												<br/>
                                                 
<div class="row">
	<div class="col-md-12 col-sm-12">
		<div class="portlet grey-cascade box">
			<div class="portlet-title">
				<div class="caption">
				   <i class="fa fa-user"></i> &nbsp;Inward  Information
				</div>
			</div>
			<div class="portlet-body">
			   
				<div class="row">
												<div class="col-sm-12">
													
													<form role="form" action="" method="post" onSubmit="return check_form();">
													<div class="row">
														<div class="col-md-12">
															<div class="form-body">
																<div class="form-group">
																<div class="row">
																<div class="col-md-4">
																<label>Vendor <code>*</code></label>
																					<select class="form-control" name="vendor" id="vendor">
																					<option value="">select Vendor</option>
																					<?php
																					$vador_r=$db->rp_getData("vendor","*","");
																					while($vendor_d=mysqli_fetch_assoc($vador_r))
																					{
																						?>
																						<option value="<?php echo $vendor_d['id'];?>" <?php if($vendor_d['id']==$vendor){?> selected <?php } ?>>
																						<?php echo $vendor_d['cname']?>
																						</option>
																						<?php
																					}
																					?>																					
																					</select>
																					<p  class="help-block"> </p>
																</div>
																</div>
																	<div class="row">
																	
																		<div class="col-md-12">
																			<div class="row">
																				<div class="col-md-4">
																					<div class="form-group">
																					<label>Products <code>*</code></label>
																					<select class="form-control" name="product_id" id="product_id">
																					<option value="">select product</option>
																					<?php
																						$product_list_d=$db->rp_getData('product',"*","1=1","",0);
																						while($product_list_r=mysqli_fetch_assoc($product_list_d))
																						{
																							$current_prodcuts=$product->aj_getProductDetail($product_list_r['id'],$uid);
																							if(!empty($current_prodcuts))
																							{
																								
																								foreach($current_prodcuts as $product_detail)
																								{
																								?>
																								<option data-weight-id="<?php echo $product_detail['id']?>" data-name="<?php echo $product_detail['product_name']?>"  data-weight="<?php echo $product_detail['weight_id']?>" data-pricelist="<?php echo $product_detail['sell_price']?>" value="<?php echo $product_detail['product_id']."#".$product_detail['weight_id']?>">
																								<?php echo $product_detail['product_name']?>
																								</option>
																								<?php
																								}
																								
																							}
																							
																						}
																					?>
																					</select>
																			<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-3">
																		<div class="form-group">
																		<label>Quantity <code>*</code></label>
																		<input type="text" class="form-control" name="qty" id="qty" value="" />
																		<p class="help-block"></p>
																		</div>
																	</div>
																	<div class="col-md-3">
																		<div class="form-group">
																		<br/><button class="btn btn-primary" type="button" id="add">ADD</button>
																		<p class="help-block"></p>
																		</div>
																	</div>

												</div>

											</div>
										</div>
										<div class="row">
											<div class="col-md-12">
											<table border="1px" id="datatable_1" class="table table-striped table-bordered table-hover">
												<thead>
													<tr>
														<th>No.</th>
														<th>Product Name</th>
														<th>Product Price</th>
														<th>Quantity</th>				
														<th>Total</th>
														<th>Action</th>
													</tr>
													<?php 
													
													if(!empty($item_info))
													{
														foreach($item_info as $i)
														{
															//print_r($item_info);

														?>
														<tr>
															<td><?php echo ++$count; ?><input type='hidden' name='count[]' value="<?php echo $count;?>" class='count'></td>
															<td>
																<input type='hidden' name='product_id[]' value="<?php echo $i['product_id']; ?>">
																
																<input type='hidden'  style="text-align:right" name='subtotal[]' value="">
																<input type='hidden' style="text-align:right" name='total[]' value="">
																<input type='hidden' style="text-align:right" name='item_name[]'>
																<input type='hidden' name='pro_name[]' value="<?php echo $i['product_name']; ?>" id='pro_name'>
																<input type='hidden' name='weight_id[]' value="<?php echo $i['weight_id']; ?>" id='weight_id'>
																<?php echo $i['product_name']; ?>
															</td>
															<td  style="text-align:right">
																<input  type="text" name='price[]' class='form-control price' style="text-align:right;width:240px;" onChange='recalculateRow(this)' value="<?php echo $i['product_price']; ?>">
															</td>
															<td style="text-align:right"><input type='text' style="text-align:right;" class='qty form-control' name='qty[]' onChange='recalculateRow(this)' value="<?php echo $i['qty']; ?>"></td>
															<td style="text-align:right">
																<input type='text'style="text-align:right;width:240px;"  disabled class='total form-control' disabled onChange='recalculateRow(this)' name='subtotal[]' value="<?php echo $i['totalprice']; ?>">
																
															<td>
															<a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></a>
															
															</td>
														</tr>
														<?php
														}
													}
													?>
													
												</thead>
												
												<tbody>
												
												</tbody>
												<tfoot>
												<tr><td colspan='3' align="right">Total</td>
												
												<td style="text-align:right"> 
													<input type='text'style="text-align:right;" id='finalQty' class="form-control"  disabled name='finalQty[]' value="<?php echo $total_qty;?>">
												</td>
												<td colspan="3" style="text-align:right">
												<input type='text'style="text-align:right;width:240px;" id='finalTotal' align="right"  class="form-control" disabled value="<?php echo $db->rp_num($grand_total);?>" name='finalTotal[]'>
													</td>	
													
												</tr>
												
												<tr><td colspan='3'></td>
												<td align="right">Grand Total </td>
												<td style="text-align:right" colspan="3"> 
													<input type='hidden' style="text-align:right" class="form-control"id='finalQty' disabled name='finalQty[]' value="<?php echo $total_qty;?>">
													<input type='text' style="text-align:right;width:240px;" id='finalgrandTotal'  class="form-control" disabled value="<?php echo $db->rp_num($grand_total);?>" name='finalgrandTotal[]'>
													</td>	
													
												</tr>
												
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
										<textarea class="form-control" name="remark" ><?php echo $remark; ?></textarea>
										<p class="help-block"></p>
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
				<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
					<button type="submit" name="submit" class="btn green">Submit</button>
					<button type="button"  class="btn btn-default" onClick="window.location.href='<?php echo $back; ?>'">Back</button>								
				</div>
				</form>
			</div>
		</div>
	</div>
		</div>
	</div>
	 </div>
										




<script>
</script>

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

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
$("#qty").numeric();
$(".qty").numeric();
//----for enter only Number no '-' or '.'---//
$(document).ready(function() {
$("#qty").keyup(function(event) {
if ( event.keyCode == 46 || event.keyCode == 8 ) {
// let it happen, don't do anything
} else if (/\D/g.test(this.value)) {
	alert("sorry!! Only Digits Allowed");
this.value = this.value.replace(/\D/g, '');
}
});
});
$(document).ready(function() {
$(".qty").keyup(function(event) {
if ( event.keyCode == 46 || event.keyCode == 8 ) {
// let it happen, don't do anything
} else if (/\D/g.test(this.value)) {
	alert("sorry!! Only Digits Allowed");
this.value = this.value.replace(/\D/g, '');
}
});
});
//--------------Calculation for qty ,total------------------------//
   function recalculateRow(t)
   {
	  var row = $(t).parent('td').parent('tr');
	  var price=$(row).find("td").find("input.price").val();
	  var qty=$(row).find("td").find("input.qty").val();		
	  var total=price*qty;	
	  total=total.toFixed(2);
	  $(row).find("td").find("input.total").val(total);		
	recalculateFinalValues();
   } 
   function recalculateFinalValues()
   {
	  var sum=0;
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
	$("#finalgrandTotal").val(''+sum);
   }
$("#add").click(function(){
	var product_id=$("#product_id").val();
	var qty=$("#qty").val();
	count=0;
		if(product_id=="")
		{
			alert('Please Select product!!');
		}
		else if(qty=="" || qty==0)
		{
			alert('Please Enter At least one Quantity!!');
		}
		else{
	var count=$('.count').length;
	count=++count;
	var product_id=$("#product_id").val();
	var price=$("#product_id").find('option:selected').data('pricelist');
	var p_name=$("#product_id").find('option:selected').data('name');
	var weight=$("#product_id").find('option:selected').data('weight');
	var qty=$("#qty").val();
	qty=(qty!="")?parseFloat(qty):0;
	var total=qty*price;
	price=price.toFixed(2);
	total=total.toFixed(2);
	var duplicate=$("input.product_id[value='"+product_id+"']").length;
	if(duplicate==0)
	{
	var new_row="<tr><td><input type='hidden' name='count[]' value='"+count+"' id='count' class='count'/>"+count+"</td><td><input type='hidden' name='product_id[]' value='"+product_id+"' class='product_id' id='product_id'/><input type='hidden' name='pro_name[]' value='"+p_name+"' id='pro_name'><input type='hidden' name='weight_id[]' value='"+weight+"' id='weight_id'>"+p_name+"</td><td style='text-align:right'><input type='text' style='text-align:right' name='price[]' class='form-control price' value='"+price+"' onChange='recalculateRow(this)'></td><td><input type='text' name='qty[]' class='form-control qty' style='text-align:right' value='"+qty+"' id='qty'  onChange='recalculateRow(this)'/></td><td><input type='text' class='total form-control' disabled onChange='recalculateRow(this)' style='text-align:right;width:240px;' name='subtotal[]' value='"+total+"' style='width:150px !important'></td><td><a class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></td></td></tr>";
	$("#datatable_1").find('tbody').append(new_row);
	recalculateRow();
	recalculateFinalValues();
	}
	else{
		alert("Product already added!!");
	}
		}
		$("#qty").val("");
		//$("#product_id").select2("val","");
})

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	<?php if($_REQUEST['mode']=="add"){?>
	if($("#product_id").val()=="" || $("#product_id").val().split(" ").join("")==""){		
		vd=aj.error('product_id',"Please Select Product.","add_error");
		isValid=false;
	}
	<?php }?>
	var count_row=$("#datatable_1").find('tr').length;
	if(count_row<=3)
	{
		alert("Please Add At Least one Product!!");
		return false;
	}
	$(".qty").each(function () {
			if($(this).val()=="" || $(this).val()==0)
			{
			alert("Please Enter At Least one Quantity!!");
			$(this).focus();
			isValid=false;
			}
	});	
	if($("#vendor").val()==null || $("#vendor").val()=="" || $("#vendor").val().split(" ").join("")==""){		
			vd=aj.error('vendor',"Please Select Vendor First.","add_error");
			isValid=false;
		}
	if(isValid)
	{
		return true;
	}
	else
	{
		return false;
	}
}
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } });
 $(document).ready(function(){

 $("#datatable_1").on('click','.delete',function(){
	 //var rows = document.getElementById("#datatable_1").getElementsByTagName("tr").length;
	 //alert(rows);
       $(this).closest('tr').remove();
	   recalculateFinalValues();
     });

});
</script>

</body>
</html>