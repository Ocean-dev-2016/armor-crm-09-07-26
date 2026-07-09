<?php
$page_id=640;$page_slug='page_tax';
$ctable 	= "tax";
$ctable1 	= "Tax";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
require_once("../include/tax.class.php");
$objTax= new Tax();

$name			= "";
$value			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['name']			= $db->clean($_REQUEST['name']);
	$detail['value']		= $db->clean($_REQUEST['value']);
	$detail['isDelete']		= 0;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objTax->TaxInsert($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
			}
			else{
				 $db->addErrorMessage($reply['ack_msg']);
			}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objTax->TaxUpdate($detail);
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
		$reply=$objTax->TaxGetEditData($detail);
		if($reply['ack']==1){
			$result=$reply['result'];
			extract($result);
	
	}
	else{
			$db->addErrorMessage($reply['ack_msg']);
		}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
	$detail['id']=$_REQUEST['id'];
	$reply=$objTax->TaxDelete($detail);
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
				<h1><a href="<?php echo $ctable."_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
				
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
									<label>Tax  Name <code>*</code></label>
									<input type="text" class="form-control" name="name" id="name" value="<?php echo $name; ?>"  autofocus>
									<p class="help-block"></p>
								</div>
								</div>
								</div>
								<div class="row">
									<div class="col-md-6">
								<div class="form-group">
									<label>Value<code>*</code></label>
									<input type="text" class="form-control" name="value" id="value" value="<?php echo $value; ?>" onkeypress="return blockSpecialChar(event)"> 
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
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
$("#value").numeric();
 function blockSpecialChar(e){
        var k;
        document.all ? k = e.keyCode : k = e.which;
        return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
        }
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 
  
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
		vd=aj.error('name',"Please Enter Tax Name.","add_error");
		isValid=false;
	}
	if($("#value").val()=="" || $("#value").val().split(" ").join("")==""){		
		vd=aj.error('value',"Please Enter Tax value.","add_error");
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