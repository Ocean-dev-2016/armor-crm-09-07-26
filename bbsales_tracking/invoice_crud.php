<?php
$page_id=562;$page_slug='page_top_category';
$ctable 	= "invoice";
$ctable2 	= "invoice";
$ctable1 	= "Invoice";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable2;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Master"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.invoice.php");
$objTopCate= new Invoice();
$invoice_no		= "";
$customer_id	= "";
$amount			= "";
$remark			= "";
// $value=$db->getlastInsertId($db->ctable);
// $invoice_no=INVOICE_NO.str_pad($value, 3, '0', STR_PAD_LEFT);
if(isset($_REQUEST['submit'])){

	$detail['invoice_no']		= $db->clean($_REQUEST['invoice_no']);
	$detail['customer_id']		= $db->clean($_REQUEST['customer_id']);
	$detail['amount']			= $db->clean($_REQUEST['amount']);
	$detail['remark']			= $db->clean($_REQUEST['remark']);
	$detail['invoice_date']		= $db->clean($_REQUEST['invoice_date']);
	$detail['image_path']   	= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']   = $db->clean($_REQUEST['old_image_path']);
	
	$sales_executive_id=implode(",",$_REQUEST['sales_executive_id']);
	$detail['sales_executive_id'] = addslashes($sales_executive_id);

	$detail['isDelete']		= 0;
	$detail['isActive']		= 1;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objTopCate->InsertInvoice($detail,$_FILES);
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
		$reply=$objTopCate->UpdateInvoice($detail,$_FILES);
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
			//print_r($result);exit;
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
	$reply=$objTopCate->DeleteInvoice($detail);
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
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
								<div class="col-md-12">
									<div class="form-group">
										<label> Customer <code>*</code></label>
										<select class="form-control" name="customer_id" id="customer_id">
											<option value="">-----Select Customer-----</option>
											<?php
												$customer_r = $db->rp_getData("executive","*","isDelete=0");
												if(mysqli_num_rows($customer_r)>0){
													while($customer_d = mysqli_fetch_array($customer_r)){
													?>
													<option value="<?php echo $customer_d['id']; ?>" <?php if($customer_d['id']==$customer_id){?> selected <?php } ?>><?php echo $customer_d['company_name']; ?></option>
												<?php
													}
												}
												?>
										</select>
									</div>
									<div class="form-group">
										<label> Sales Officer <code>*</code></label>
										<select class="form-control" name="sales_executive_id[]" id="sales_executive_id" multiple="multiple">
											<option value="">-----Select Sales Officer-----</option>
											<?php
												$sales_executive_r = $db->rp_getData("sales_executive","*","isDelete=0");
												if(mysqli_num_rows($sales_executive_r)>0){
													while($sales_executive_d = mysqli_fetch_array($sales_executive_r)){
													?>
													<option <?php echo (in_array($sales_executive_d['id'],$sales_executive_id))?"selected":""; ?> value="<?php echo $sales_executive_d['id']; ?>"><?php echo $sales_executive_d['name']; ?></option>
												<?php
													}
												}
												?>
										</select>
									</div>
									<div class="form-group">
										<label for="name">Invoice No<code>*</code></label>
										<input type="text" class="form-control" name="invoice_no" id="invoice_no" value="<?php echo $invoice_no; ?>">
									</div>
									
									<div class="form-group">
										<label for="name">Amount <code>*</code></label>
										<input type="text" class="form-control" name="amount" id="amount" value="<?php echo $amount; ?>">
									</div>
	                                <div class="form-group">
										<label for="name">Remark <code>*</code></label>
										<textarea type="text" class="form-control" name="remark" id="remark" value=""><?php echo $remark; ?></textarea>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<input data-image="<?php echo ($image_path!="" && file_exists(INVOICE_A.$image_path))?INVOICE_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
									</div>
									<div class="form-group">
	                                    <label>Invoice Date  <code>*</code></label>
	                                    <input type="text"  name="invoice_date" class="form-control input-small" id="invoice_date" value="<?php echo $invoice_date; ?>" placeholder="From Date">
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
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>

<script type="text/javascript">
	$("#sales_executive_id").fSelect();
</script>

<script type="text/javascript">
	$("#amount").numeric();
</script>
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
	if($("#invoice_no").val()=="" || $("#invoice_no").val().split(" ").join("")==""){		
		vd=aj.error('invoice_no',"Please Enter Invoice No.","add_error");
		isValid=false;
	}
	if($("#amount").val()=="" || $("#amount").val().split(" ").join("")==""){		
		vd=aj.error('amount',"Please Enter Amount.","add_error");
		isValid=false;
	}
	if($("#remark").val()=="" || $("#remark").val().split(" ").join("")==""){		
		vd=aj.error('remark',"Please Enter Remark.","add_error");
		isValid=false;
	}
	if($("#invoice_date").val()=="" || $("#invoice_date").val().split(" ").join("")==""){		
		vd=aj.error('invoice_date',"Please Enter Invoice Date","add_error");
		isValid=false;
	}
	if($("#customer_id").val()==""){
		alert("Please select Customer.");
		$("#customer_id").focus();
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

<script src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>

<script type="text/javascript">
var date = new Date();
$('#invoice_date').datepicker({  datepicker: true, autoclose: true ,format:'dd-mm-yyyy'});
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