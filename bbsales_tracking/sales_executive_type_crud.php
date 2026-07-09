<?php
$page_id=557;$page_slug='page_sales_executive_type';
$ctable 	= "sales_executive_type";
$ctable1 	= "Sales Officer Type";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']:"add";
$page_title = ucwords($mode)." ".$ctable1;
include("connect.php");
require_once("../include/sales_executive_type.class.php");
$objSalesType= new SalesType();
$name			= "";
$code			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['code']		= $db->clean($_REQUEST['code']);
	$detail['isDelete']		= 0;
	$detail['isActive']		= 1;
	if($mode=="add"){
		$db->checkRightFlag("insert_flag");
		$reply=$objSalesType->SalesTypeInsert($detail);
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
		$reply=$objSalesType->SalesTypeUpdate($detail);
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
		$reply=$objSalesType->SalesTypeGetEditData($detail);
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
	$reply=$objSalesType->SalesTypeDelete($detail);
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
	$reply=$objSalesType->SalesTypeActive($detail);	
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
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
				
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
		vd=aj.error('name',"Please Enter Sales Officer Type.","add_error");
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