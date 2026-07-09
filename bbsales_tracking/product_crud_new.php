<?php
$page_id=559;$page_slug='page_product';
$ctable 	= "product";
$ctable1 	= "Product";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
include("../include/product_new.class.php");
$objProduct1 = new Product();

$gst=$db->rp_getValue("dealer_distributor_network","gst","id=1");

$tcid                 = "";
$name                 = "";
$gujrati_name         = "";
$sku                  = "";
$weight               = "";
$max_price            = "";
$sell_price           = "";
$weight_ids           = array();
$attr                 = "";
$image_path           = "";
$status               = "";
$cgst                 = "";
$sgst                 = "";
$igst                 = "";
$descr                = "";
$pro_tax              = 0;
$qty                  = 0;
$min_qty_alert        = 0;
$display_order        = 0;
$isWhatsNew           = 0;
$isSale               = 0;
$isFeatured           = 0;
$isHot                = 0;
$isOffer              = 0;
$isDeal               = 0;
$ship_days            = 0;
$local_ship_charge    = 0;
$zonal_ship_charge    = 0;
$national_ship_charge = 0;
$product_code         = "";
$brand_id             = "";
$unit_id              = "";
$hsn_code             = "";

if(isset($_REQUEST['submit']))
{

	$detail=array();
	$detail['image_path']     = $db->clean($_REQUEST['image_path']);
	$detail['old_image_path'] = $db->clean($_REQUEST['old_image_path']);
	$detail['product_type']   = $db->clean($_REQUEST['product_type']);
	$detail['name']           = $db->clean($_REQUEST['name']);
	$detail['tcid']           = $db->clean($_REQUEST['tcid']);
	$detail['brand_id']       = 0;
	$detail['cid']            = $db->clean($_REQUEST['cid']);
	$detail['unit_id']        = $db->clean($_REQUEST['unit_id']);
	$detail['hsn_code']       = $db->clean($_REQUEST['hsn_code']);
	$detail['name']           = $db->clean(trim($_REQUEST['name']));
	$detail['product_code']   = $db->clean(trim($_REQUEST['product_code']));
	$detail['max_price']      = $db->clean($db->rp_num(trim($_REQUEST['max_price'])));
	$detail['sell_price']     = $db->clean($db->rp_num(trim($_REQUEST['sell_price'])));
	$detail['pro_tax']        = round($_REQUEST['pro_tax']);
	$detail['igst'] 	      = isset($_REQUEST['igst'])?$db->clean($_REQUEST['igst']):"18";
	$detail['cgst'] 	      = ($_REQUEST['igst']!=0)?$db->clean($_REQUEST['igst']/2):"9";
	$detail['sgst'] 	      = ($_REQUEST['igst']!=0)?$db->clean($_REQUEST['igst']/2):"9";
	$detail['status'] 	      = $db->clean($_REQUEST['status']);
	$detail['isDelete']		  = 0;
	$detail['slug'] 		  = $db->clean($db->rp_createProSlug($detail['name']));
	$detail['descr'] 		  = $db->clean(htmlentities($_REQUEST['descr']));

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objProduct1->InsertProduct($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			//$db->rp_location($ctable."_manage.php?msg=inserted");
			$db->rp_location("product_crud_new.php?mode=edit&id=".$reply['id']);
		}
		else
		{
			$db->addErrorMessage($reply['ack_msg']);
		}
	}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		$detail['id']=$_REQUEST['id'];
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objProduct1->UpdateProduct($detail,$_FILES);
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

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit")
{
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$detail['id']=$_REQUEST['id'];	
	$reply=$objProduct1->GetEditDataProduct($detail);
	$product_name=$db->rp_getValue("product","name","id='".$detail['id']."' AND isDelete=0",0);
	$page_title=ucwords($_REQUEST['mode']).'&nbsp'."Product".'&nbsp'." - ".ucwords($product_name);
	if($reply['ack']==1)
	{
		$result=$reply['result'];
		extract($result);
	}
	else
	{
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}	
	$detail['id']=$_REQUEST['id'];
	$reply=$objProduct1->DeleteProduct($detail);
	if($reply['ack']==1)
	{
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php?msg=inserted");
	}
	else
	{
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!="")
{
	$status = $_REQUEST['status'];
	$rows 	= array(
		"isActive"	=> $status
	);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($ctable."_manage.php?msg=updated");
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
<!-- <link href="assets/css/demo.html5imageupload.css?v1.3" rel="stylesheet"> -->
<link rel="stylesheet" type="text/css" href="css/fselect.css"/>
<link href="assets/global/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet">

</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
			<div class="row">
				<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
				<div class="col-md-12">
					<div class="box-header">
						<div class="col-md-12">
							<button type="submit" name="submit" id="submit" class="btn btn-success" value="Submit">Submit</button>
							<button type="button" class="btn btn-success" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
						</div>
					</div>
					<input type="hidden" name="mode" id="mode" value="<?php echo $_REQUEST['mode']; ?>">
					<input type="hidden" name="id" id="id" value="<?php echo $_REQUEST['id']; ?>">
					<input type="hidden" name="subpid" id="subpid" value="<?php echo $subpid; ?>">
					<div class="box-body"  >
						<?php if(isset($_REQUEST['msg']) && $_REQUEST['msg']=="duplicate")
						{ 
							?>
								<div class="alert alert-danger alert-dismissable"> <i class="fa fa-ban"></i>
								<button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
								<b>Error! This Name is Already Exist. Please Try Another Name</b> </div>
							<?php 
						} 
						?>
						<div class="col-md-6" style="margin-top:20px;">
							<div class="row">
								<div class="col-md-12">
									<div class="portlet grey-cascade box">
										<div class="portlet-title">
											<div class="caption">
												Product General Information
											</div>
										</div>
									  	<div class="portlet-body">
										  	<div class="form-group">
												<label for="product_type">Select Type <code>*</code></label>
												<select class="form-control" name="product_type" id="product_type" onchange="getTypeData(this.value);">
													<option value="">Select Type</option>
													<option value="1" <?= ($product_type==1)?"selected":""; ?>>With Variant</option>
													<option value="2" <?= ($product_type==2)?"selected":""; ?>>Without Variant</option>
												</select>
											</div>
											<div class="form-group">
												<label for="tcid">Category <code>*</code></label>
												<select class="form-control" name="tcid" id="tcid" onchange="getCategory(this.value);">
													<option value="">Select Category</option>
													<?php
														$cat_r = $db->rp_getData("top_category_master","*","isDelete=0");
														if(mysqli_num_rows($cat_r)>0){
															while($cat_d = mysqli_fetch_array($cat_r)){
															?>
													<option value="<?php echo $cat_d['id']; ?>" <?php if($cat_d['id']==$tcid){?> selected <?php } ?>><?php echo $cat_d['name']; ?></option>
													<?php
															}
														}
														?>
												</select>
											</div>
										  	<div class="form-group">
												<label for="cid">Sub Category <code>*</code></label>
												<select class="form-control" name="cid" id="cid">
													<option value="">Select Sub Category</option>
													<?php
													if($tcid!="" && $tcid>0)
													{
														$scat_r = $db->rp_getData("category_master","*","isDelete=0 AND tcid='".$tcid."'","","0");
														if(mysqli_num_rows($scat_r)>0)
														{
															while($scat_d = mysqli_fetch_array($scat_r))
															{
																?>
																<option value="<?php echo $scat_d['id']; ?>" <?php if($scat_d['id']==$cid){?> selected <?php } ?>><?php echo $scat_d['name']; ?></option>
																<?php
															}
														}
													}
													?>
												</select>
											</div>		

											<div class="form-group">
												<label for="name">Name<code>*</code></label>
												<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>">
											</div>
											<div class="form-group without_size1 hidden">
												<label for="product_code">Product Code</label>
												<input type="text" class="form-control" name="product_code" id="product_code" value="<?php echo $product_code; ?>">
											</div>
											<div class="form-group without_size1 hidden">
												<label for="qty">Qty</label>
												<input type="text" class="form-control positive" name="qty" id="qty" value="<?php echo $qty; ?>">
											</div>
											<div class="form-group hidden">
												<div class="row">
													<div class="col-md-4">
														<label for="gujrati_name">CGST<code>*</code></label>
														<input type="text" class="form-control" name="cgst" id="cgst" value="<?php echo $cgst; ?>" onchange="gst_value()">
													</div>
												
													<div class="col-md-4 ">
														<label for="gujrati_name">SGST<code>*</code></label>
														<input type="text" class="form-control" name="sgst" id="sgst" value="<?php echo $sgst; ?>"  onchange="gst_value()">
													</div>
													
													<div class="col-md-4 ">
														<label for="gujrati_name">IGST<code>*</code></label>
														<input type="text" class="form-control" name="igst" id="igst" value="<?php echo $igst; ?>" >
													</div>
												</div>
											</div>
											
											<div class="form-group">
												<label for="igst">GST <code>*</code></label>
												<select class="form-control" name="igst" id="igst">
													<option value="">Select GST</option>
													<?php
														$tax_r=$db->rp_getData("tax","*","isDelete=0");
														if($tax_r){
															while($tax_d=mysqli_fetch_assoc($tax_r))
															{
																?>
																<option value="<?= $tax_d['value'];?>" <?= ($igst==$tax_d['value'])?"selected":""; ?>><?= $tax_d['value'];?></option>
																<?php
															}
														}
													?>
												</select>
											</div>

											<div class="form-group">
												<label for="unit_id">Unit</label>
												<select class="form-control" name="unit_id" id="unit_id">
													<option value="">Select Unit</option>
													<?php
														$unit_r = $db->rp_getData("unit","*","isDelete=0");
														if($unit_r)
														{
															while($unit_d=mysqli_fetch_assoc($unit_r))
															{
																?>
																<option value="<?= $unit_d['id'];?>" <?= ($unit_id==$unit_d['id'])?"selected":""; ?>><?= $unit_d['name'];?></option>
																<?php
															}
														}
													?>
												</select>
											</div>	

											<div class="form-group">
												<label for="name">HSN Code</label>
												<input type="text" class="form-control" name="hsn_code" id="hsn_code" value="<?php echo $hsn_code; ?>">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="portlet grey-cascade box">
									  	<div class="portlet-title">
											<div class="caption">Product Image
									  		</div>
									  	</div>
									  	<div class="portlet-body">
									  		<div class="form-group pimg">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<input data-image="<?php echo ($image_path!="" && file_exists(PRODUCT_A.$image_path))?PRODUCT_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="row">
								<div class="col-md-12">
									<div class="portlet grey-cascade box">
										<div class="portlet-title">
											<div class="caption">Extra Information
										  	</div>
										</div>
										<div class="portlet-body">	
										 	<div class="form-group">
												<label for="descr">Description</label>
												<textarea type="text" class="form-control" id="descr" name="descr" rows="3" cols="60"><?php echo $descr; ?></textarea>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="box-footer">
								<div class="col-md-12">
									<button type="submit" name="submit" id="submit" class="btn btn-success" value="Submit">Submit</button>
									<button type="button" class="btn btn-success" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
								</div>
							</div>	
						</div>
						<?php
						if($_REQUEST['mode']=="edit")
						{ 
							?>	
							<div class="col-md-6 getSize" style="margin-top:20px;">
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script src="js/plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
<script  type="text/javascript" src="js/fselect.js"></script>
<script src="js/select2/select2.min.js"></script>
<script src="assets/global/plugins/bootstrap-select/bootstrap-select.min.js"></script>

<script type="text/javascript">
	$(function()
	{
		var isImageThumbnailLoadedReply = '';
		aj.imageHolder($("input[name=image_path]"),"","",
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

<script type="text/javascript">
	jQuery(document).on('icheck', function(){ jQuery('input.weights_chk').iCheck({ checkboxClass: 'icheckbox_square-blue' }); }).trigger('icheck');
	$(document).ready(function(){		
		$(".weightPriceInput").numeric();
		$("#qty").numeric();
		$(".weightStockInput").numeric();
		$(".weightMinAlertInput").numeric();
	});
</script>

<script type="text/javascript">
	$(document).ready(function()
	{
		CKEDITOR.replace('descr');
		var mode="<?= $_REQUEST['mode']; ?>";
		if(mode=="edit")	
		{
			var typeval=$("#product_type").val();
			getTypeData(typeval);
		}
	});
</script>

<script type="text/javascript">

function check_form(){
	var isValid=true;

	if($("#product_type").val()==""){
		toastr.error("Please select Type.");
		$("#product_type").focus();
		isValid= false;
	}	
	if($("#tcid").val()==""){
		toastr.error("Please select top category.");
		$("#tcid").focus();
		isValid= false;
	}	
	if($("#brand_id").val()==""){
		toastr.error("Please select top category.");
		$("#brand_id").focus();
		isValid= false;
	}	
	if($("#cid").val()==""){
		toastr.error("Please select category.");
		$("#cid").focus();
		isValid= false;
	}	
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){
		toastr.error("Please enter name.");
		$("#name").focus();
		isValid= false;
	}
	return isValid;
}

function getTypeData(typeval)
{	
	var mode="<?= $_REQUEST['mode']; ?>";
	var id="<?= isset($_REQUEST['id'])?$_REQUEST['id']:""; ?>";
	$.ajax({
		type: "POST",
		url: "get_size_from_product_type_new.php",
		data: 'type='+typeval+'&mode='+mode+'&id='+id,
		success: function(result){
			$(".getSize").html(result);				
		}
	});
}
</script>

<script>
function getCategory(id)
{
	$.ajax({
		type: "POST",
		url: "ajax_getCategory.php",
		data: 'tcid='+id,
		success: function(result){
			$("#cid").html(result);
		}
	});
}

$("#product_type").change(function(){
	var product_id 	= '<?php echo $_REQUEST['id'] ?>';
 	$.ajax({
		type: "POST",
		url: "ajax_check_product_type.php",
		data: 'product_id='+product_id,
		cache: false,
    		beforeSend:function(){
        	},
        	success:function(json)
	      	{
	        	json=$.parseJSON(json);
	      		msg=json.ack_msg;
				if(json.ack==0)
				{						
					toastr.error(msg, 'Error!!')
				}
	        }
	});
});

$(".positive").keypress(function(event) 
{
	if ( event.keyCode == 46 || event.keyCode == 8 ) 
	{
		// let it happen, don't do anything
	} 
	else if (/\D/g.test(this.value)) 
	{
		toastr.error("Sorry!! Only Digits Allowed");
		this.value = this.value.replace(/\D/g, '');
	}
});
</script>
</body>
</html>