<?php
$page_id=586;$page_slug='expense_subcategory_page';
$ctable 	= "expence_sub_category";
$ctable2 	= "expence_subcategory";
$ctable1 	= "Expence SubCategory";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable2;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable2."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable2."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/expence_subcategory.class.php");
$objCategory= new ExpenceSubCategory();
$name			= "";
$cid			= "";
$code			= "";
$expense_type	= "";
$image_flag		= "";
$fix_amount		= "";
$min_time		= "";
$max_time		= "";
if(isset($_REQUEST['submit'])){
	// print_r($_REQUEST);exit;
	$detail['name']				= $db->clean($_REQUEST['name']);
	$detail['slug'] 			= $db->clean($db->rp_createProSlug($detail['name']));
	$detail['expense_type']		= $db->clean($_REQUEST['expense_type']);
	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);
	$detail['isDelete']			= 0;
	$detail['expense_category_id']		= $db->clean($_REQUEST['expense_category_id']);
	// $detail['sales_executive_id']		= $db->clean($_REQUEST['sales_executive_id']);
	$detail['image_flag']		= $db->clean($_REQUEST['image_flag']);
	$detail['fix_amount']		= $db->clean($_REQUEST['fix_amount']);
	$detail['min_time']		= $db->clean($_REQUEST['min_time']);
	$detail['max_time']		= $db->clean($_REQUEST['max_time']);

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$message_text = array();
		$text_message = array("ack"=>array(),"success_sales_executive"=>array(),"error_sales_executive"=>array());
		for($i=0;$i<sizeof($_REQUEST['sales_executive_id']);$i++)
		{
			$detail['sales_executive_id']=$_REQUEST['sales_executive_id'][$i];
		    $reply=$objCategory->InsertExpenceSubCategory($detail,$_FILES);
		    $message_text[] = $reply;
		}

		for($p=0;$p<sizeof($message_text);$p++)
		{
			$text_message['ack'][] = $message_text[$p]['ack'];
			if ($message_text[$p]['ack'] == 1) {
				$text_message['success_sales_executive'][] = $db->rp_getValue("sales_executive","name","id = ".$message_text[$p]['sales_executive_id']);
			}else{
					$text_message['error_sales_executive'][] = $db->rp_getValue("sales_executive","name","id = ".$message_text[$p]['sales_executive_id'],0);
			}
		}

		if(in_array(1,$text_message['ack']))
		{
			$message_text_for_show = "Expence Sub Category Added Successfully and Assign to ".implode(",",$text_message['success_sales_executive']);
			if (sizeof($text_message['error_sales_executive']) > 0) 
			{
				$message_text_for_show .= " And Already Assign to ".implode(",",$text_message['error_sales_executive']);
			}
			$db->addSuccessMessage($message_text_for_show);
			$db->rp_location($ctable2."_manage.php?msg=inserted");
		}
		else
		{
				$db->addErrorMessage($reply['ack_msg']);
				$db->rp_location("expence_subcategory_crud.php?mode=add");
		}
	}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		for($i1=0;$i1<sizeof($_REQUEST['sales_executive_id']);$i1++)
		{
			$detail['sales_executive_id']=$_REQUEST['sales_executive_id'][$i1];
		    $reply=$objCategory->UpdateExpenceSubCategory($detail,$_FILES);
		}
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable2."_manage.php?msg=updated");
		}
		else
		{
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
		$reply=$objCategory->GetEditDataExpenceSubCategory($detail);
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
		$reply=$objCategory->DeleteExpenceSubCategory($detail);
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
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $ctable2."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> 
				</h1>
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
								<div class="form-group">
									<label for="expense_category_id">Expence Category <code>*</code></label>
									<select class="form-control" name="expense_category_id" id="expense_category_id">
										<option value="">Select Expence Category</option>
										<?php
											$cat_r = $db->rp_getData("expence_category","*","isDelete=0");
											if(mysqli_num_rows($cat_r)>0){
												while($cat_d = mysqli_fetch_array($cat_r)){
												?>
												<option value="<?php echo $cat_d['id']; ?>" <?php if($cat_d['id']==$expense_category_id){?> selected <?php } ?>><?php echo $cat_d['name']; ?></option>
											<?php
												}
											}
											?>
									</select>
								</div>								
								<div class="form-group">
									<label for="name">Name <code>*</code></label>
									<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>">
								</div>
								<div class="form-group">
									<label for="sales_executive_id">Select Sales Executive <code>*</code></label>
									<select class="form-control" name="sales_executive_id[]" id="sales_executive_id" multiple>
										<option value="">Select Sales Executive</option>
										<?php
											$sales_executive_table_r = $db->rp_getData("sales_executive","*","isDelete=0");
											if(mysqli_num_rows($sales_executive_table_r)>0){
												while($sales_executive_d = mysqli_fetch_array($sales_executive_table_r)){
												?>
												<option <?=($sales_executive_id == $sales_executive_d['id'])?"selected":"";?> value="<?php echo $sales_executive_d['id']; ?>" >
													<?php echo $sales_executive_d['name']; ?>
												</option>
											<?php
												}
											}
											?>
									</select>
								</div>
								<div class="form-group">
									<label for="expense_type">Select Type <code>*</code></label>
									<select class="form-control" name="expense_type" id="expense_type" onchange="getTypeData(this.value);">
										<option value="">Select Type </option>
										<option value="1" <?php if($expense_type == "1"){echo"selected";}?>>General</option>
										<option value="2" <?php if($expense_type == "2"){echo"selected";}?>>KiloMeter</option>
										<option value="3" <?php if($expense_type == "3"){echo"selected";}?>>Food</option>
									</select>
								</div>
								<div class="form-group">
									<input data-image="<?php echo ($image_path!="" && file_exists(EXPENCE_SUB_CATEGORY_A.$image_path))?EXPENCE_SUB_CATEGORY_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable2; ?>_manage.php'">Back</button>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6 getSize">
				</div>
			</div>
		</form>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="js/fSelect.js"></script>
<script type="text/javascript">
	$("#sales_executive_id").fSelect();

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
		vd=aj.error('name',"Please Enter Expence Sub Category Name.","add_error");
		isValid=false;
	}
	if($("#expense_category_id").val()==""){
		alert("Please select Expence  category.");
		$("#expense_category_id").focus();
		return false;
	}

	if($("#expense_type").val()==""){
		alert("Please select Expense Type.");
		$("#expense_type").focus();
		return false;
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

<script type="text/javascript">
//---------code for enter only number(0-9) not '.' or '-'-------------//

//--------------------------------------------------------------//
$(document).ready(function(){
		var mode="<?= $_REQUEST['mode']; ?>";
		if(mode=="edit")	
		{
			var typeval=$("#expense_type").val();
			getTypeData(typeval);
		}
	});
</script>

<script type="text/javascript">
	function getTypeData(typeval)
	{
		var mode="<?= $_REQUEST['mode']; ?>";
		var id="<?= isset($_REQUEST['id'])?$_REQUEST['id']:""; ?>";
		$.ajax({
			type: "POST",
			url: "get_subcategory_extra_form.php",
			data: 'type='+typeval+'&mode='+mode+'&id='+id,
			success: function(result){
				$(".getSize").html(result);				
			}
		});
	}
</script>

</body>
</html>