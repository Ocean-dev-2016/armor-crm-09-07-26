<?php

$page_id=580;$page_slug='price_list_master';
$ctable 	= "price_list";
$ctable1 	= "Price List";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>"pricelist_master_manage.php","title"=>"Manage ".$ctable1),array("link"=>"pricelist_master_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/pricelist.class.php");
$objPricelist= new Pricelist();
$name			= "";
$code			= "";
$is_premium		= "";
if(isset($_REQUEST['submit'])){ 
	
	$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['is_premium']	= $db->clean($_REQUEST['is_premium']);
	$detail['is_premium']	= ($detail['is_premium']=='on')?1:0;
	$detail['tcid']			= ($_REQUEST['tcid']!="")?$db->clean($_REQUEST['tcid']):"";
	$detail['state_id']		= ($_REQUEST['state_id']!="")?implode(",",$_REQUEST['state_id']):"";
	$detail['isDelete']		= 0;
	 // print_r($detail);exit;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objPricelist->InsertPricelist($detail);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("pricelist_master_manage.php?msg=inserted");
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
		$reply=$objPricelist->UpdatePricelist($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("pricelist_master_manage.php?msg=updated");
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
		$reply=$objPricelist->GetEditDataPricelist($detail);
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
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
	$db->rp_location("pricelist_master_manage.php?msg=updated");
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
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">					
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
								<div class="row hidden">
									<div class="col-md-6">
										<div class="form-group">
											<label>Salect Category <code>*</code></label>
											<select class="form-control" name="tcid" id="tcid">
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
											<p class="help-block"></p>
										</div>
									</div> 
								</div>
								<div class="row hidden">
									<div class="col-md-6">
										<div class="form-group">
											<label>Salect State <code>*</code></label> 
											<select multiple="multiple" class="multi-select" id="state_id" name="state_id[]">
											<!-- <select class="form-control " name="state_id[]" multiple id="state_id" > -->
												<option value="">Select State</option>
												<?php 
												$stata_id_arr = explode(",",$state_id);
												$c_id=$db->rp_getData('class',"*","1=1 AND isDelete=0","",0);
												while($class_r=mysqli_fetch_assoc($c_id))
												{
												?>
												<option <?php echo in_array($class_r['id'],$stata_id_arr)?"selected":"" ; ?> value="<?php echo $class_r['id']?>">
												<?php echo $class_r['name'];?>
												</option>
												<?php
												}
												?>
											</select>
											<p class="help-block"></p>
										</div>
									</div> 
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Price List Name <code>*</code></label>
											<input type="text" class="form-control" name="name" id="name" value="<?php echo $pricelist_name; ?>">
											<p class="help-block"></p>
										</div>
									</div> 
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<?php
												if ($_REQUEST['mode']=='edit') 
												{
													$is_premium_check = $db->rp_getValue("price_list","is_premium","isDelete=0 AND isActive=1 AND id='".$_REQUEST['id']."'",0);
													
													if ($is_premium_check == 1) 
													{
														$selling_price_check = $db->rp_getTotalRecord("product_price_list","isDelete=0 AND isActive=1 AND price_list_id=".$_REQUEST['id']."",0);

														if ($selling_price_check > 0) 
														{
															$disabled = "disabled";
														}
													}
												}
											?>

											<input <?=$disabled?> type="checkbox" class="is_premium" name="is_premium" id="is_premium" <?= ($is_premium==1)?'checked':''; ?>>
											<label>Is Premium</label>
										</div>
									</div>
								</div>
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='pricelist_master_manage.php'">Back</button>
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
<script type="text/javascript" src="js/fSelect.js"></script> 
<script type="text/javascript">
$("#state_id").fSelect();
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	
	/*if($("#tcid").val()=="" || $("#tcid").val().split(" ").join("")==""){
			
		vd=aj.error('tcid',"Please Select Top Category.","add_error");
		isValid=false;
	}
	else if($("#state_id").val()=="" || $("#state_id").val().split(" ").join("")==""){
			
		vd=aj.error('state_id',"Please Select State.","add_error");
		isValid=false;
	}*/
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){
			
		vd=aj.error('name',"Please Enter Pricelist name.","add_error");
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