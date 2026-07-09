<?php
$page_id=558;$page_slug='page_class';
$ctable 	= "class";
$ctable1 	= "Class";
$main_page 	= $ctable;
$page 		= $ctable."_manage";
$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$page_title = ucwords($mode)." "."State";
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage State"),array("link"=>$ctable."_crud.php","title"=>"Add/Edit State"));
include("connect.php");
require_once("../include/class.class.php");
$objClassType= new ClassType();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['country_id']			= $db->clean($_REQUEST['country_id']);
	$detail['isDelete']		= 0;
	$detail['isActive']		= 1;
	if($mode=="add"){
		$db->checkRightFlag("insert_flag");
		$reply=$objClassType->ClassTypeInsert($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		else{
			 $db->addErrorMessage($reply['ack_msg']);
		}
		
	}
	else if($mode=="edit"){
		$db->checkRightFlag("update_flag");
		$reply=$objClassType->ClassTypeUpdate($detail);
		if($reply['ack']==1){
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
		$reply=$objClassType->ClassTypeGetEditData($detail);
		if($reply['ack']==1){
			$result=$reply['result'];
			extract($result);
	
	}
	else{
			$db->addErrorMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=0");
			
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	$db->checkRightFlag("delete_flag");
	$detail['id']=$_REQUEST['id'];
	$reply=$objClassType->ClassTypeDelete($detail);
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
	$reply=$objClassType->ClassTypeActive($detail);	
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
		<?php $db->getMessageBlock(); ?>			
		<form role="form" action="" onSubmit="return check_form();" method="post">
			<div class="row">
				<div class="col-md-6 ">
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="form-body">
							<div class="row">
									<div class="col-md-6">

										<div class="form-group">
											<label for="tcid">Country <code>*</code></label>
											<select class="form-control" name="country_id" id="country_id">
												<option value="">Select Country</option>
												<?php
													$cat_r = $db->rp_getData("country","*","isDelete=0");
													if(mysqli_num_rows($cat_r)>0){
														while($cat_d = mysqli_fetch_array($cat_r)){
														?>
												<option value="<?php echo $cat_d['id']; ?>" <?php if($cat_d['id']==$country_id){?> selected <?php } ?>><?php echo $cat_d['name']; ?></option>
												<?php
														}
													}
													?>
											</select>
											<p class="help-block"></p>
								         </div>	
								<div class="form-group">
									<label>Name<code>*</code></label>
									<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>" autofocus>
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
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#name").val()=="" || $("#name").val().split(" ").join("")==""){		
		vd=aj.error('name',"Please Enter Class Name.","add_error");
		isValid=false;
	}

	if($("#country_id").val()=="" || $("#country_id").val().split(" ").join("")==""){		
		vd=aj.error('country_id',"Please Enter Country Name.","add_error");
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