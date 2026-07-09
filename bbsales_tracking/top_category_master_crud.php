<?php
$page_id=639;$page_slug='page_top_category';
$ctable 	= "top_category_master";
$ctable2 	= "top_category_master";
// $ctable1 	= "Top Category";
$ctable1 	= "Category";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable2;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/top_category_master.class.php");
$objTopCate= new TopCategory();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	$detail['name']				= $db->clean($_REQUEST['name']);
	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);
	$detail['unit_id']   = $db->clean($_REQUEST['unit_id']);
	$detail['customer_unit_id']   = $db->clean($_REQUEST['customer_unit_id']);

	$detail['isDelete']		= 0;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objTopCate->InsertCategory($detail,$_FILES);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable2."_manage.php?msg=inserted");
		}else{
				 $db->addErrorMessage($reply['ack_msg']);
			}
		}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objTopCate->UpdateCategory($detail,$_FILES);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable2."_manage.php?msg=updated");
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
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$detail['id']=$_REQUEST['id'];	
	$reply=$objTopCate->GetEditDataCategory($detail);
	if($reply['ack']==1){
		//$SuccessMsg = $reply['ack_msg'];
		$result=$reply['result'];
		//print_r($result);
		extract($result);
	}else{
		$db->addErrorMessage($reply['ack_msg']);
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];
		$reply=$objTopCate->DeleteCategory($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable2."_manage.php?msg=inserted");
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
	$db->rp_location($ctable2."_manage.php?msg=updated");
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
				<h1><a href="<?php echo $ctable2."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
									<div class="col-md-6">
								<div class="form-group">
									<label> Name <code>*</code></label>
									<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
									<p class="help-block"></p>
								</div>
								</div>
								</div>
								<div class="row hidden">
									<div class="col-md-6">
										<div class="form-group">
											<label for="unit_id">Sales Order Unit</label>
											<select class="form-control" name="unit_id" id="unit_id">
												<option value="">Select Sales Order Unit</option>

												<option <?=($unit_id==-1)?"selected":""; ?> value="-1">Box</option>
												<option <?=($unit_id==-2)?"selected":""; ?> value="-2">Strip</option>
												<option <?=($unit_id==-3)?"selected":""; ?> value="-3">Pallet</option>
												<option <?=($unit_id==1)?"selected":""; ?> value="1">Carte</option>
												<option <?=($unit_id==2)?"selected":""; ?> value="2">Big Box</option>
												<option <?=($unit_id==100)?"selected":""; ?> value="100">Qty</option> 
											</select>
										</div>
									</div>
								</div>
								<div class="row hidden">
									<div class="col-md-6">
										<div class="form-group">
											<label for="customer_unit_id">Customer Order Unit</label>
											<select class="form-control" name="customer_unit_id" id="customer_unit_id">
												<option value="">Select Customer Order Unit</option>
												<option <?=($customer_unit_id==-1)?"selected":""; ?> value="-1">Box</option>
												<option <?=($customer_unit_id==-2)?"selected":""; ?> value="-2">Strip</option>
												<option <?=($customer_unit_id==-3)?"selected":""; ?> value="-3">Pallet</option>
												<option <?=($customer_unit_id==1)?"selected":""; ?> value="1">Caret</option>
												<option <?=($customer_unit_id==2)?"selected":""; ?> value="2">Big Box</option>
												<option <?=($customer_unit_id==100)?"selected":""; ?> value="100">Qty</option> 
											</select>
										</div>
									</div>
								</div>
								<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<input data-image="<?php echo ($image_path!="" && file_exists(TOP_CATEGORY_A.$image_path))?TOP_CATEGORY_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
									</div>
								</div>
							</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable2; ?>_manage.php'">Back</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/jquery-aj.js"></script>
<script type="text/javascript">
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){		
		vd=aj.error('name',"Please Enter Category Name.","add_error");
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

</body>
</html>