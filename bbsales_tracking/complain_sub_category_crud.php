<?php
$page_id=646;$page_slug='complain_sub_category';
$ctable 	= "complain_sub_category";
$ctable1 	= "Complain Sub Category";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." Complain/Request Sub Categor";
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"complain_sub_category_manage.php","title"=>"Manage Complain/Request Sub Category"),array("link"=>$ctable1."_crud.php","title"=>"Add/Edit Complain/Request Sub Category"));
include("connect.php");
require_once("../include/class.complain_sub_category.php");
$objWeight= new ComplainSubCategory();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['complain_category_id']			= $db->clean($_REQUEST['complain_category_id']);
	$detail['isDelete']		= 0;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objWeight->InsertComplainSubCategory($detail);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
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
		$reply=$objWeight->UpdateComplainSubCategory($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_manage.php?msg=updated");
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
	$reply=$objWeight->GetEditDataComplainSubCategory($detail);
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
		$reply=$objWeight->DeleteComplainSubCategory($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php?msg=inserted");
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
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Complain/Request Category<code>*</code></label>
										<select class="form-control" name="complain_category_id" id="complain_category_id">
											<option value="">Select Category</option>
											<?php
											$complain_categoryR = $db->rp_getData("complain_category","*","isDelete=0","",0);
											while ($complain_category = mysqli_fetch_assoc($complain_categoryR))
											{
											?>
											<option <?= ($complain_category_id==$complain_category['id'])?"selected":""; ?> value="<?=$complain_category['id']?>"><?=$complain_category['name']?></option>
											<?php
											}
											?>
										}
										</select>
										<p class="help-block"></p>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Complain/Request Sub Category<code>*</code></label>
										<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>">
										<p class="help-block"></p>
									</div>
								</div>
							</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
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
			
		vd=aj.error('name',"Please Enter Complain Sub Category.","add_error");
		isValid=false;
	}
	else if($("#complain_category_id").val()=="" || $("#complain_category_id").val().split(" ").join("")==""){
		vd=aj.error('complain_category_id',"Please Select Complain Category.","add_error");
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
</body>
</html>