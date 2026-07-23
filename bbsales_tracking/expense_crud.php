<?php
$page_id=592;$page_slug='expense_page';
$page_slug="manage_inward_store";
$ctable 	= "expense";
$ctable1 	= "expense";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
include("connect.php");
include('../include/expense.class.php');
$objExpense= new Expense();
$isActive				= 0;
$sales_executive_id		="";
$category_id 	="";
$subcategory_id ="";
$total 				="";
$remark 				="";
$image_path				="";
$total_amount			="";

/* Expense Category from Sub Master (same list for Regular + Advance after Advance Type) */
$categoryOptionsHtml = '<option value="">Select Expence Category</option>';
$expence_cat_d = $db->rp_getData('expence_category', "id,name", "isDelete=0 AND isActive=1 AND (IFNULL(expense_claim_type,1)<>2)", "name ASC", 0);
if ($expence_cat_d) {
	while ($expence_cat_r = mysqli_fetch_assoc($expence_cat_d)) {
		$categoryOptionsHtml .= '<option value="'.(int)$expence_cat_r['id'].'">'.htmlspecialchars($expence_cat_r['name']).'</option>';
	}
}

if(isset($_REQUEST['submit'])){

	$expense_mode = isset($_REQUEST['expense_mode']) ? $db->clean($_REQUEST['expense_mode']) : "";
	$advance_expense_type = isset($_REQUEST['advance_expense_type']) ? $db->clean($_REQUEST['advance_expense_type']) : "0";

	$detail['sales_executive_id']		= $db->clean($_REQUEST['sales_executive_id']);
	$detail['category_id']				= $db->clean($_REQUEST['category_id']);
	$detail['subcategory_id']			= $db->clean(isset($_REQUEST['subcategory_id']) ? $_REQUEST['subcategory_id'] : 0);
	$detail['total']					= $db->clean($_REQUEST['total_amount']);
	$detail['remark']					= $db->clean($_REQUEST['remark']);
	$detail['image_path']   	   		= $db->clean($_REQUEST['image_path']);
	$detail['old_image_path']     		= $db->clean($_REQUEST['old_image_path']);
	$detail['advance_expense_type']		= $advance_expense_type;
	$detail['isActive']					= 1;
	$detail['entry_flag']  				= 1;
	$detail['update_entry_flag']  		= 1;

	// Live: expense_mode 0=Regular, 1=Advance → DB expense_claim_type 1/2
	$detail['expense_claim_type'] = ($expense_mode === "1") ? 2 : 1;

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}

		$reply=$objExpense->InsertExpense($detail,$_FILES);

		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		else{
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
	$reply=$objExpense->RejectExpense($detail);
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/select2/select2.css"/>
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
<style type="text/css">
	.expense-field-hidden { display: none !important; }
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="expense_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php echo $page_title; ?></h1>
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
				<div class="col-md-12">
					<div class="portlet box blue">
						<div class="portlet-body form">
							<div class="row">
								<div class="col-sm-12">
									<div class="tabbable-custom nav-justified">
										<ul class="nav nav-tabs ">
											<li class="active">
												<a href="#tab_inquiry_info" data-toggle="tab" aria-expanded="false"> Expense </a>
											</li>
										</ul>
										<div class="tab-content">
											<div class="tab-pane active" id="tab_super_stockist_info">
												<br/>
												<div class="row">
													<div class="col-md-12 col-sm-12">
														<div class="portlet grey-cascade box">
															<div class="portlet-title">
																<div class="caption">
																   <i class="fa fa-user"></i> &nbsp; Expense Information
																</div>
															</div>
															<div class="portlet-body">
																<div class="row">
																	<div class="col-sm-12">

																		<form role="form" action="" method="post" enctype="multipart/form-data" onSubmit="return check_form();">
																		<input type="hidden" name="advance_expense_type" id="advance_expense_type_hidden" value="0">

																		<!-- Row 1 fixed slots -->
																		<div class="row" id="row_1">
																			<div class="col-md-4">
																				<div class="form-group">
																				<label><b>Sales Person</b><code>*</code></label>
																				<?php
																				if($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $_SESSION[SITE_SESS.'_ADMIN_TYPE']==14)
																				{
																					?>
																					<select class="form-control" name="sales_executive_id" id="sales_executive_id">
																					<option value="">Select Sales Person</option>
																					<?php
																						$product_list_d=$db->rp_getData('sales_executive',"*","isDelete=0 AND isActive=1 AND type!='service_engineer'","",0);
																						while($product_list_r=mysqli_fetch_assoc($product_list_d))
																						{
																							?>
																								<option <?=($product_list_r['id']==$_SESSION[SITE_SESS.'REFERANCE_ID'])?"selected":"";?> value="<?php echo $product_list_r['id'];?>">
																								<?php echo $product_list_r['name']?>
																								</option>
																								<?php
																						}
																					?>
																					</select>
																					<?php
																				}
																				else
																				{
																					?>
																					<input type="hidden" class="form-control" name="sales_executive_id" id="sales_executive_id" value="<?=$_SESSION[SITE_SESS.'REFERANCE_ID']?>">
																					<input type="text" readonly="" class="form-control" value="<?=$db->rp_getValue('sales_executive',"name","id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."'","",0)?>">
																					<?php
																				}
																				?>
																				<p class="help-block"></p>
																				</div>
																			</div>
																			<div class="col-md-4">
																				<div class="form-group">
																				<label><b>Expense Type</b><code>*</code></label>
																				<select class="form-control" name="expense_mode" id="expense_mode" onchange="toggleExpenseMode();">
																					<option value="">Select Expense Type</option>
																					<option value="0">Regular</option>
																					<option value="1">Advance</option>
																				</select>
																				<p class="help-block"></p>
																				</div>
																			</div>
																			<div class="col-md-4" id="slot_row1_third"></div>
																		</div>

																		<!-- Row 2 / 3 slots -->
																		<div class="row" id="row_2" style="display:none;">
																			<div class="col-md-4" id="slot_row2_col1"></div>
																			<div class="col-md-4" id="slot_row2_col2"></div>
																			<div class="col-md-4" id="slot_row2_col3"></div>
																		</div>
																		<div class="row" id="row_3" style="display:none;">
																			<div class="col-md-4" id="slot_row3_col1"></div>
																			<div class="col-md-4" id="slot_row3_col2"></div>
																		</div>

																		<!-- Field templates (moved into slots by JS) -->
																		<div id="expense_field_bank" class="expense-field-hidden">
																			<div class="form-group" id="field_advance_type">
																				<label><b>Advance Type</b><code>*</code></label>
																				<select class="form-control" id="advance_expense_type" onchange="toggleAdvanceType();">
																					<option value="">Select Advance Type</option>
																					<option value="1">Advance Brand Approval Expense</option>
																					<option value="2">Advance Travelling Expense</option>
																				</select>
																				<p class="help-block"></p>
																			</div>

																			<div class="form-group" id="field_category">
																				<label><b>Expence Category</b><code>*</code></label>
																				<select class="form-control" name="category_id" id="category_id" onchange="amount_fun(this.value);">
																					<?php echo $categoryOptionsHtml; ?>
																				</select>
																				<p class="help-block"></p>
																			</div>

																			<div class="form-group" id="field_subcategory">
																				<label><b>Expence Sub Category</b><code>*</code></label>
																				<select class="form-control" name="subcategory_id" id="subcategory_id" onchange="fixed_amount(this.value);">
																					<option value="">Select Expence Sub Category</option>
																				</select>
																				<p class="help-block"></p>
																			</div>

																			<div class="form-group" id="field_amount">
																				<label><b>Amount</b><code>*</code></label>
																				<input type="text" class="form-control" name="total_amount" id="total_amount" value=""/>
																				<p class="help-block"></p>
																			</div>

																			<div class="form-group" id="field_image">
																				<input data-image="<?php echo ($image_path!="" && file_exists(EXPENCE_A.$image_path))?EXPENCE_A.$image_path:"";?>" type="file" name="image_path" id="image_path" data-old-image-dom="old_image_path" data-old-image-path="<?php echo $image_path ?>" value="" >
																			</div>

																			<div class="form-group" id="field_remark">
																				<label><b>Remark</b></label>
																				<textarea class="form-control" name="remark" id="remark"><?php echo $remark;?></textarea>
																				<p class="help-block"></p>
																			</div>
																		</div>

																		<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
																			<button type="submit" name="submit" class="btn green">Submit</button>
																			<button type="button"  class="btn btn-default" onClick="window.location.href='<?php echo $ctable1; ?>_manage.php'">Back</button>
																		</div>
																		</form>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[name=image_path]"),"","",
		function(isImageThumbnailLoadedReply,isImageThumbnailValidReply){
			isImageThumbnailLoaded=isImageThumbnailLoadedReply;
			isImageThumbnailValidT=isImageThumbnailValidReply;
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
			},
		function(data){
			isImageThumbnailLoadedReply
		},
		["png","PNG","jpeg","JPEG","jpg","JPG","gif","GIF"]
		);
	})
</script>

<script type="text/javascript">
	function amount_fun(id)
	{
		var sales_id = $("#sales_executive_id").val();
		$.ajax({
			type: "POST",
			url: "ajax_get_fixed_amount.php",
			data: 'id='+id+"&sales_id="+sales_id,
			success: function(result){
				$("#subcategory_id").html(result);
			}
		});
	}

	function fixed_amount(val)
	{
		$("#total_amount").val($("#subcategory_id").find("option:selected").data("expense_amount"));
		var id = $("#subcategory_id").val();
		if(id=='6' || id=='7' || id=='8')
		{
			$('#total_amount').prop('readonly', true);
		}
		else
		{
			$('#total_amount').prop('readonly', false);
		}
	}

	function hideAllFields() {
		$("#field_category, #field_advance_type, #field_subcategory, #field_amount, #field_image, #field_remark").appendTo("#expense_field_bank").hide();
		$("#row_2, #row_3").hide();
	}

	function showRegularLayout() {
		hideAllFields();
		$("#field_category").appendTo("#slot_row1_third").show();
		$("#field_subcategory").appendTo("#slot_row2_col1").show();
		$("#field_amount").appendTo("#slot_row2_col2").show();
		$("#field_image").appendTo("#slot_row2_col3").show();
		$("#field_remark").appendTo("#slot_row3_col1").show();
		$("#row_2, #row_3").show();
	}

	function showAdvanceLayout() {
		hideAllFields();
		var advType = $("#advance_expense_type").val();
		$("#field_advance_type").appendTo("#slot_row1_third").show();
		if (advType == "1" || advType == "2") {
			$("#field_category").appendTo("#slot_row2_col1").show();
			$("#field_subcategory").appendTo("#slot_row2_col2").show();
			$("#field_amount").appendTo("#slot_row2_col3").show();
			$("#field_image").appendTo("#slot_row3_col1").show();
			$("#field_remark").appendTo("#slot_row3_col2").show();
			$("#row_2, #row_3").show();
		}
	}

	function toggleExpenseMode() {
		$("#advance_expense_type").val("");
		$("#advance_expense_type_hidden").val("0");
		$("#category_id").val("");
		$("#subcategory_id").html('<option value="">Select Expence Sub Category</option>');
		$("#total_amount").val("").prop("readonly", false);
		var mode = $("#expense_mode").val();
		if (mode === "0") {
			showRegularLayout();
		} else if (mode === "1") {
			showAdvanceLayout();
		} else {
			hideAllFields();
		}
	}

	function toggleAdvanceType() {
		var advType = $("#advance_expense_type").val();
		$("#advance_expense_type_hidden").val(advType != "" ? advType : "0");
		if ($("#expense_mode").val() === "1") {
			showAdvanceLayout();
		}
	}
</script>

<script type="text/javascript">
$('#expense_date').datepicker({  datepicker: true, autoclose: true ,  maxDate:0  });
$("#total_amount").numeric();

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;

	if($("#sales_executive_id").val()=="" || $("#sales_executive_id").val().split(" ").join("")==""){
		vd=aj.error('sales_executive_id',"Please Select Sales Officer.","add_error");
		isValid=false;
	}
	if($("#expense_mode").val()==="" || $("#expense_mode").val().split(" ").join("")==""){
		vd=aj.error('expense_mode',"Please Select Expense Type.","add_error");
		isValid=false;
	}
	if($("#expense_mode").val()==="0" || ($("#expense_mode").val()==="1" && ($("#advance_expense_type").val()=="1" || $("#advance_expense_type").val()=="2"))){
		if($("#category_id").val()=="" || $("#category_id").val().split(" ").join("")==""){
			vd=aj.error('category_id',"Please Select Expence Category.","add_error");
			isValid=false;
		}
		if($("#subcategory_id").val()=="" || $("#subcategory_id").val().split(" ").join("")==""){
			vd=aj.error('subcategory_id',"Please Select Expence Sub Category.","add_error");
			isValid=false;
		}
		if($("#total_amount").val()=="" || $("#total_amount").val().split(" ").join("")==""){
			vd=aj.error('total_amount',"Please Enter Total Amount.","add_error");
			isValid=false;
		}
	}
	if($("#expense_mode").val()==="1"){
		if($("#advance_expense_type").val()=="" || $("#advance_expense_type").val()=="0"){
			vd=aj.error('advance_expense_type',"Please Select Advance Type.","add_error");
			isValid=false;
		}
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
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } });

$(document).ready(function(){
	toggleExpenseMode();
});
</script>

</body>
</html>
