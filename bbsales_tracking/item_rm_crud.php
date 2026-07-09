<?php
$page_id=430;$page_slug='page_item';
$ctable 	                    = "item_rm";
$ctable1 	                    = "Row Material Item";
$main_page 	                    = "product_mgmt";
$page 		                    = "manage_".$ctable;
$mode                           = isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$page_title                     = ucwords($mode)." ".$ctable1;
include("connect.php");
require_once("../include/item_rm.class.php");
//require_once("../include/messurement.class.php");
//require_once("../include/valve_type.class.php");
//require_once("../include/standard_material.class.php");
require_once("../include/class.media.php");
$objRMItemMaster                = new RMItemMaster();
$media							= new Media();

$rm_item_code			        = "";
$rm_item_name			            = "";
$rm_item_category			                = "";
$rm_packaging_type			            = "";
$rm_sku			    = "";
//$rm_opening_qty			            = "";
$rm_min_stock_qty			            = "";
$rm_max_stock_qty			        = "";
$rm_unit			    = "";
$primary_unit_value			    = "";
$item_info= array();
if(isset($_REQUEST['submit'])){
	
	$detail['primary_unit_value']			              	= $db->clean($_REQUEST['primary_unit_value']);
	$detail['rm_item_code']			              	= $db->clean($_REQUEST['rm_item_code']);
	$detail['rm_item_name']		                    = $db->clean($_REQUEST['rm_item_name']);
	$detail['rm_item_category']		            	= $db->clean($_REQUEST['rm_item_category']);
	$detail['rm_packaging_type']		            = $db->clean($_REQUEST['rm_packaging_type']);
	$detail['rm_sku']		             		 	 = $db->clean($_REQUEST['rm_sku']);
	//$detail['rm_opening_qty']		              	 = $db->clean($_REQUEST['rm_opening_qty']);
	$detail['rm_min_stock_qty']		                  = $db->clean($_REQUEST['rm_min_stock_qty']);
	$detail['rm_max_stock_qty']		                  = $db->clean($_REQUEST['rm_max_stock_qty']);
	$detail['rm_unit']		                  		 = $db->clean($_REQUEST['rm_unit']);
	$detail['isDelete']		                          = 0;
	$detail['isActive']		                          = 1;
	//$detail['photo']		                          = $_FILES['photo'];
	
	//Insert Unit mapping
	
	$unit_id=$_REQUEST['unit_id'];
	$unit_value=$_REQUEST['unit_value'];  
	
	$size[]=sizeof($unit_id);
	$size[]=sizeof($unit_value);
		
	$value_check=sizeof($unit_id);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}
		
	if($isValidArray  && !empty($unit_id))
	{
		for($i=0;$i<sizeof($unit_id);$i++)
		{
			$item[]=array("unit_id"=>$unit_id[$i],"unit_value"=>$unit_value[$i]);
		}
	}
	
	if($mode=="add"){
		$db->checkRightFlag("insert_flag");
		$reply            = $objRMItemMaster->RMItemMasterInsert($detail,$item);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg =inserted");
		}
		else{
			 $db->addErrorMessage($reply['ack_msg']);
		}
		
	}
	else if($mode=="edit"){
		$db->checkRightFlag("update_flag");
		$reply=$objRMItemMaster->RMItemMasterUpdate($detail,$item);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_manage.php?msg=updated");
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		} 
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
		$db->checkRightFlag("update_flag");
		$where = " id='".$_REQUEST['id']."' AND isDelete=0";
		$ctable_r = $db->rp_getData($ctable,"*",$where);
		$detail['id']=$_REQUEST['id'];	
		$reply=$objRMItemMaster->RMItemMasterGetEditData($detail);
		$item_info=$objRMItemMaster->GetUnitMapItem($detail);
		if($reply['ack']==1){
			$result=$reply['result'];
			extract($result);
		}
		else{
			
			$db->addErrorMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		if($item_info['ack']==1){
			$item_info=$item_info['result'];
		}
		else{
			$item_info=array();
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$db->checkRightFlag("delete_flag");
	$detail['id']=$_REQUEST['id'];
	$reply=$objRMItemMaster->RMItemMasterDelete($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);
		}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	
	$db->checkRightFlag("update_flag");
	$id = $_REQUEST['id'];
	$status = $_REQUEST['status'];
	$detail 	= array(
				"isActive"	=> $status,
				"id"	=> $id
			);
	$reply=$objRMItemMaster->RMItemMasterActive($detail);	
	if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php?msg=inserted");
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
 <link href="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css" />
       
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;
				<?php if($_REQUEST['mode']=="edit")
				{
					echo "Edit Item - ".$db->rp_getValue($ctable,"rm_item_name","id='".$_REQUEST['id']."'"); 
				}
				else { 
						echo $page_title;
				} ?></h1>
				
			</div>
		</div>
	</div>
	<div class="page-content">
		<div class="container">
		<?php $db->getMessageBlock(); ?>			
		<div class="row">
				<div class="col-md-12 ">
					<div class="portlet light portlet-fit portlet-datatable bordered">
						<div class="portlet-body form">
							<div class="tabbable-line">
								 <ul class="nav nav-tabs ">
									<li class="active">
										<a href="#tab_fg_item_info" data-toggle="tab" aria-expanded="false"> Row Material Item Information </a>
									</li>
									 <!--li <?php echo ($mode!="edit")?'class="disabled"':'';?>>
										<a title="Save Order First" <?php echo ($mode!="edit")?'class="disable-target"':'href="#tab_attachments" data-toggle="tab" aria-expanded="false"';?> > Attachments</a>                                                    
									</li-->
								</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="tab_fg_item_info">	
							<form role="form" onSubmit="return check_form();" method="post" enctype="multipart/form-data">							
							<div class="form-body">
								<div class="row">
								<div class="col-md-6">
								
								<div class="col-md-12">
								<div class="row">
									<div class="col-md-6">
									<div class="form-group">
										<label>Item Category<code>*</code></label>
										<select class="form-control" name="rm_item_category" id="rm_item_category" value="<?php echo $rm_item_category; ?>" autofocus>
											<option value="">Select Category</option>
										<?php 
											$getcategory = $db->rp_getData("item_rm_category","*","isDelete=0","",0);
											if($getcategory)
											{
												//$messurements=$messurements['result'];
												while($category_item=mysqli_fetch_assoc($getcategory))
												{
												?>
													<option <?php echo ($category_item['id']==$rm_item_category)?"selected":""; ?> value="<?php echo $category_item['id']; ?>"><?php echo $category_item['item_rm_category_name']; ?></option>
												<?php
												}
											}
										?>
										</select>
										
										<p class="help-block"></p>
									</div>
									</div>
									<div class="col-md-6">
									<div class="form-group">
										<label>Item Name<code>*</code></label>
										<input type="text" class="form-control" name="rm_item_name" id="rm_item_name" value="<?php echo $rm_item_name; ?>" autofocus>
										<p class="help-block"></p>
									</div>
									</div>
									</div>
									<div class="row">
									
									<div class="col-md-6">
									<div class="form-group">
										<label>Item Code<code>*</code></label>
										<input type="text" class="form-control" name="rm_item_code" id="rm_item_code" value="<?php echo $rm_item_code; ?>">
										<p class="help-block"></p>
									</div>
									</div>
									<div class="col-md-6">
									<div class="form-group">
										<label>Primary Unit<code>*</code></label>
										<select class="form-control" name="rm_unit" id="rm_unit" value="<?php echo $rm_unit; ?>" autofocus>
											<option value="">Select Unit</option>
										<?php 
											$unit = $db->rp_getData("unit","*","isDelete=0","",0);
											if($unit)
											{
												
												while($unit_item=mysqli_fetch_assoc($unit))
												{
												?>
													<option data-name="<?php echo $unit_item['name']; ?>" <?php echo ($unit_item['id']==$rm_unit)?"selected":""; ?> value="<?php echo $unit_item['id']; ?>"><?php echo $unit_item['name']; ?></option>
												<?php
												}
											}
										?>
										</select>
										
										<p class="help-block"></p>
									</div>
									</div>
									
									</div>
									
								</div>
								<div class="col-md-12">
								
									<div class="row">
										<div class="col-md-6">
									<div class="form-group">
										<label>Packaging Type<code>*</code></label>
										<select class="form-control" name="rm_packaging_type" id="rm_packaging_type" >
											<option value="">Select Type</option>
										
										<option <?php echo ($rm_packaging_type=="0")?"selected":""; ?> value="0">Box</option>
										<option <?php echo ($rm_packaging_type=="1")?"selected":""; ?> value="1">Pounch</option>
										<option <?php echo ($rm_packaging_type=="2")?"selected":""; ?> value="2">Loose</option>
									
										</select>
										<p class="help-block"></p>
									</div>
										</div>
									
										<div class="col-md-6">
											<div class="form-group">
												<label>Item Sku.<code>*</code></label>
												<input type="text" class="form-control" name="rm_sku" id="rm_sku" value="<?php echo $rm_sku; ?>">
												<p class="help-block"></p>
											</div>
										</div>
									
									</div>
									<div class="row">
										<!--div class="col-md-6">
									<div class="form-group">
										<label>Opening Qty<code>*</code></label>
										<input type="text" class="form-control nagative" name="rm_opening_qty" id="rm_opening_qty" value="<?php echo $rm_opening_qty; ?>" autofocus>
										<p class="help-block"></p>
									</div>
									</div-->
									<div class="col-md-6">
									<div class="form-group">
										<label>Minimum Stock Qty</label>
										<input type="text" class="form-control nagative" name="rm_min_stock_qty" id="rm_min_stock_qty" value="<?php echo $rm_min_stock_qty; ?>" autofocus>
										<p class="help-block"></p>
									</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Maximum Stock Qty</label>
											<input type="text" class="form-control nagative" name="rm_max_stock_qty" id="rm_max_stock_qty" value="<?php echo $rm_max_stock_qty; ?>">
											<p class="help-block"></p>
										</div>
									</div>
									
									</div>
								</div>
								</div>
								
								<div class="col-md-6">
								<h2>Alternate Unit Mapping</h2>
								<hr/>
								<div class="col-md-12">
									<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Primary Unit Value<code>*</code></label>
											<div class="input-group">
											<input type="text" class="form-control unitname" name="primary_unit_value" id="primary_unit_value" value="<?php echo $primary_unit_value; ?>">
											<span class="input-group-addon" id="rm_unit_display" name="fg_unit_display" value=""><?php echo $rm_unit; ?></span>
										
										</div>
										</div>
									<p class="help-block"></p>
								</div>
								</div>
								<table class="table table-hover table-bordered table-striped" id="unit_items_list">
									<thead>
									<tr>
										<th>Sr No.</th>
										<th>Unit Name</th>
										<th>Value</th>
									</tr>
									</thead>
									<tbody>
									<?php
									if($_REQUEST['mode']=="edit"){	
										$primary_unit=$db->rp_getData("unit","*","isDelete=0",0);
										if($primary_unit){
											$count=1;
										while($mapped_unit=mysqli_fetch_assoc($primary_unit))
										{
											
											if($mapped_unit['id']!=$rm_unit)
												{
											$alter_unit_value=$db->rp_getValue("item_map_unit","alter_unit_value","alter_unit_id = '".$mapped_unit['id']."' AND item_id='".$_REQUEST['id']."'",0);
											
											//$alter_unit_id=$db->rp_getValue("item_rm_map_unit","alter_unit_id","item_rm_id ='".$_REQUEST['id']."'",1);
											
											?>
										<tr>
											<td><input type='hidden' name='count[]' id='count' value='<?php echo $count; ?>'><?php echo $count; ?></td>
											<td><input type='hidden' name='unit_id[]' id='unit_id' value='<?php echo $mapped_unit['id']; ?>'><?php echo $mapped_unit['name']; ?></td>
											<td><input type='text' name='unit_value[]' class='form-control unitname unitValue' value="<?php echo $alter_unit_value; ?>" style='width:100px;'/></td>
										</tr>
										<?php
											$count++;
												}
										}
										}
									}
									else
									{ ?>
										<tr class="no-item">
										<td colspan='9' class="text-center">
										<span >
											<i class="fa fa-cubes"> </i>No Data Found!!
										</span>
										</td>
										</tr>
								<?php 	}
									?>
									</tbody>
									
								</table>	
								</div>
								</div>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
							</div>
							</div>
							</form>
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
<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="js/jquery.numeric.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
       
<script type="text/javascript">
$(document).ready(function() {       
	$("#rm_max_stock_qty").numeric();
	$("#rm_min_order_qty").numeric();
	$("#rm_min_stock_qty").numeric();
	$("#rm_mrp").numeric();
	$(".unitname").numeric();
	//$("#rm_opening_qty").numeric();
	
});
	
$(".nagative").keypress(function(event) {
		 if ( event.keyCode == 46 || event.keyCode == 8 ) {
		 // let it happen, don't do anything
		 } else if (/\D/g.test(this.value)) {
		  toastr.error("Sorry!! Only Digits Allowed");
		 this.value = this.value.replace(/\D/g, '');
		 }
		 });
//var reference_id=<?php echo ($_REQUEST['id'])?$_REQUEST['id']:0;?>;
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } });
function check_form(){

	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#rm_item_category").val()=="" || $("#rm_item_category").val().split(" ").join("")==""){		
		vd=aj.error('rm_item_category',"Select Item Category.","add_error");
		isValid=false;
	}
	
	if($("#rm_item_name").val()=="" || $("#rm_item_name").val().split(" ").join("")==""){		
		vd=aj.error('rm_item_name',"Enter Item Name.","add_error");
		isValid=false;
	}
	if($("#rm_item_code").val()=="" || $("#rm_item_code").val().split(" ").join("")==""){		
		vd=aj.error('rm_item_code',"Enter Item Code","add_error");
		isValid=false;
	}
	
	
	if($("#rm_packaging_type").val()=="" || $("#rm_packaging_type").val().split(" ").join("")==""){		
		vd=aj.error('rm_packaging_type',"Select Pakaging Type","add_error");
		isValid=false;
	}
	
	if($("#rm_sku").val()=="" || $("#rm_sku").val().split(" ").join("")==""){		
		vd=aj.error('rm_sku',"Enter Sku","add_error");
		isValid=false;
	}
	/*if($("#rm_opening_qty").val()==0 || $("#rm_opening_qty").val().split(" ").join("")==""){		
		vd=aj.error('rm_opening_qty',"Enter Opening Quntity","add_error");
		isValid=false;
	}*/
	
	if($("#rm_unit").val()=="" || $("#rm_unit").val().split(" ").join("")==""){		
		vd=aj.error('rm_unit',"Select Unit.","add_error");
		isValid=false;
	}
	var validUnit=false;
	$(".unitValue").each(function(){
		if($(this).val()!="")
		{
			validUnit=true;
		}
	})
	if(!validUnit){		
		toastr.error("Enter atleast one alternative unit value");
		isValid=false;
	}
	if($("#primary_unit_value").val()=="" || $("#primary_unit_value").val().split(" ").join("")==""){
		vd=aj.error('primary_unit_value',"Enter Primary Unit value.","add_error");
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
 var rm_unit_name= $("#rm_unit").find(":selected").data('name');
	$("#rm_unit_display").html(rm_unit_name);
///// append all unit

 $("#rm_unit").change(function (){ //change event for select
 var rm_unit_id=$(this).val();
  var rm_unit_name= $("#rm_unit").find(":selected").data('name');
	$("#rm_unit_display").html(rm_unit_name);
    $.ajax({
		url: 'item_unit_search.php',
        type : 'POST',
        dataType : 'json',
        allowClear: true,		
        quietMillis: 250,
        data:{          
				rm_unit_id:rm_unit_id,
            },
        success: function (data) { 
			var count=0;
			var unit_list=data.result.results;
			$.each(unit_list,function(index,value){
				count=++count;
				var unit=unit_list[index];
				var new_row="<tr><td><input type='hidden' name='count[]' id='count' value='"+count+"'>"+count+"</td><td><input type='hidden' name='unit_id[]' id='unit_id' value='"+unit.unit_id+"'>"+unit.
				name+"</td><td><input type='text' name='unit_value[]' class='form-control unitname unitValue' style='width:100px;'/></td></tr>";
				
				$("#unit_items_list").append(new_row);
				
			});
		 },
		
        cache: true
    });
	$("#unit_items_list").find('tbody').empty();
   })
</script>
</body>
</html>