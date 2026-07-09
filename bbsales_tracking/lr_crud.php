<?php
$page_id=615;$page_slug='lr_details';
$ctable 	= "lr_detail";
$ctable1 	= "LR Detail";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Sales & Marketing"),array("link"=>"lr_manage.php","title"=>"Manage ".$ctable1),array("link"=>"lr_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.lr.php");
$objLRDetail= new LRDetail();
$name			= "";
$code			= "";

if(isset($_REQUEST['submit'])){
	
	$detail['lr_number']	= $db->clean($_REQUEST['lr_number']);
	$detail['invoice_id']	= $db->clean($_REQUEST['invoice_id']);
	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);
	$detail['remark']		= $db->clean($_REQUEST['remark']);

	$detail['isDelete']		= 0;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objLRDetail->InsertLR($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("lr_manage.php?msg=inserted");
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
	$reply=$objLRDetail->DeleteLR($detail);
	if($reply['ack']==1)
	{
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("lr_manage.php?msg=deleted");
	}
	else
	{
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "pricelist_master_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
		<form role="form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
			<div class="row">
				<div class="col-md-6 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label>Invoice No: <code>*</code></label>
											<select class="form-control" id="invoice_id" name="invoice_id" >
												<option value="">Select Invoice No</option>
												<?php
												$invoiceR = $db->rp_getData("invoice_new", "*", "isDelete=0 AND status=1");
												if ($invoiceR) 
												{
													while ($invoiceD = mysqli_fetch_assoc($invoiceR))
													{
														?>
														<option <?= ($invoice_id == $invoiceD['id']) ? "selected" : ""; ?> value="<?= $invoiceD['id']; ?>"><?= $invoiceD['invoice_no']; ?></option>
														<?php
													}
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-8">
										<div class="form-group">
											<label>Lr Number <code>*</code></label>
											<input type="text" class="form-control" name="lr_number" id="lr_number" value="<?php echo $lr_number; ?>">
											<p class="help-block"></p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label for="#"><b>Add Attachment</b></label>
											<input data-image="<?php echo ($image_path!="" && file_exists(LRCOPY_DOCUMENTS.$image_path))?LRCOPY_DOCUMENTS.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" multiple >									
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-8">
										<div class="form-group">
											<label>Remark </label>
											<textarea class="form-control" name="remark" id="remark" value="<?php echo $remark; ?>"><?php echo $remark; ?></textarea>
											<p class="help-block"></p>
										</div>
									</div>
								</div>
								<div class="form-actions">
									<button type="submit" name="submit" class="btn green">Submit</button>
									<button type="button" class="btn btn-default" onClick="window.location.href='lr_manage.php'">Back</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>

<script type="text/javascript">

$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
function check_form(){
	// alert("test");
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	
	if($("#invoice_id").val()=="" || $("#invoice_id").val().split(" ").join("")==""){
			// alert("test");
		vd=aj.error('invoice_id',"Please Enter Invoice No..","add_error");
		isValid=false;
	}
	if($("#lr_number").val()=="" || $("#lr_number").val().split(" ").join("")==""){
			
		vd=aj.error('lr_number',"Please Enter Lr Number..","add_error");
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
</script>
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[id=image_path]"),"","",
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