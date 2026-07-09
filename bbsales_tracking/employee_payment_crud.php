<?php
$page_id=635;$page_slug='employee_payment';
$page_slug="manage_super_stockist";
$ctable 	= "employee_payment";
$ctable1 	= "Employee Payment";
$main_page 	= $ctable;
$page 		= $ctable."_crud";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Account"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.employee_payment.php");
include("../include/define.php");

$objEmployeePayment= new EmployeePayment();
$customer_type			= ""; 
$sale_id	        = "";
$sales_executive_id	    = "";
$paid_amount 			= "";
$payment_date 			= "";
$payment_type		    = "";
$remark 			    = "";
$cheque_no 			    = "";

if(isset($_REQUEST['submit']))
{
	$detail['sales_type']		= $db->clean($_REQUEST['sales_type']);
	$detail['sale_id']			= $db->clean($_REQUEST['sale_id']);

	// $detail['sales_executive_id'] = 0;
	// $_SESSION[SITE_SESS.'_ADMIN_SESS_ID']
	// $detail['sales_executive_id'] = $_SESSION[SITE_SESS.'REFERANCE_ID'];
	$detail['sales_executive_id'] = $_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
	// $detail['sales_executive_id']	= $db->clean($_REQUEST['sales_executive_id']);
	$detail['paid_amount']			= $db->clean($_REQUEST['paid_amount']);
	$detail['payment_date']			= $db->clean($_REQUEST['payment_date']);
	$detail['payment_type']			= $db->clean($_REQUEST['payment_type']); 
	$detail['remark']				= $db->clean($_REQUEST['remark']);
	$detail['cheque_no']			= $db->clean($_REQUEST['cheque_no']);
	$detail['isActive']				= 1; 



	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply=$objEmployeePayment->InsertPayment($detail);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($ctable."_manage.php?msg=inserted");
		}
		else
		{
		 	$db->addErrorMessage($reply['ack_msg']);				 
		}
	}
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$detail['id']=$_REQUEST['id'];
		$reply=$objEmployeePayment->UpdatePayment($detail);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($ctable."_manage.php?msg=updated");
		}
		else
		{
		 	$db->addErrorMessage($reply['ack_msg']);
		}  
	}
}
 
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit")
{
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
	$detail['id']=$_REQUEST['id']; 
	$reply=$objEmployeePayment->EditPayment($detail);
	if($reply['ack']==1)
	{

		// echo "<pre>"; print_r($reply); exit;
		$result=$reply['result'];
		extract($result);
	}
	else
	{
		$db->addErrorMessage($reply['ack_msg']);
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete")
{
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objEmployeePayment->DeletePayment($detail);
	if($reply['ack']==1)
	{
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location($ctable."_manage.php?msg=inserted");
	}
	else
	{
		$db->addErrorMessage($reply['ack_msg']);		
	}
} 
?>
<!DOCTYPE html> 
<html lang="en"> 
	<head>
		<meta charset="utf-8"/>
		<title><?php echo $page_title; ?> | <?php echo SITETITLE; ?></title>

		<?php include("include_css.php"); ?>
		<link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
		<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
	</head>
	<body class="page-md">
		<?php include("header.php"); ?>
		<div class="page-container">
			<div class="page-head bg-grey">
				<div class="container">
					<div class="page-title">
						
						<h1><a href="<?php echo  $ctable;?>_manage.php" class="btn primary"><i class="fa  fa-arrow-circle-o-left"></i>&nbsp;back</a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
						
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
					<form id="inquiry_form_distributor" onSubmit="return check_form()" action="" method="post"> 
						<div class="row">
							<div class="col-md-12">
								<div class="portlet grey-cascade box">
									<div class="portlet-title">
										<div class="caption">
										   <i class="fa fa-user"></i> &nbsp; Payment
										</div>
									</div>
									<div class="portlet-body">
									   	<div class="row">
										    <div class="col-md-6">
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label for="sales_type">Select Sales Officer Type<code>*</code></label>
																<select class="form-control status" name="sales_type" id="sales_type" onchange="getCustomer(this.value)" >
					                                             	<option value="">Select Sales Officer Type</option>
					                                                <option value="sales_manager" <?= ($sales_executive_type=="sales_manager")?"selected":""; ?>>Regional Sales Manager</option>
					                                               <option value="area_sales_manager" <?= ($sales_executive_type=="area_sales_manager")?"selected":""; ?>>Business Development Manager</option>

					                                               <option value="dispatch_sales_manager" <?= ($sales_executive_type=="dispatch_sales_manager")?"selected":""; ?>>Dispatch Manager</option>
					                                               
					                                               <option value="sales_officer" <?= ($sales_executive_type=="sales_officer")?"selected":""; ?>>Area Sales Manager</option>
					                                               <option value="sales_executive" <?= ($sales_executive_type=="sales_executive")?"selected":""; ?>>Sales Officer</option>
					                                               <option value="service_executive" <?= ($sales_executive_type=="service_executive")?"selected":""; ?>>Service Executive</option>
					                       
					                                             </select>
															<p class="help-block"></p>	
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label for="sale_id">Select Employee<code>*</code></label>


															<select class="form-control" name="sale_id" id="sale_id" onchange="getsalesexecutive(this.value);"> <?php echo $disable;?>>
																<option value="">Select Employee Name</option>

																<?php
																if($_REQUEST['mode']=='edit' && $sales_executive_type!="")
																{
																	$exe_r = $db->rp_getData("sales_executive","*","isDelete=0",0);
																	if(mysqli_num_rows($exe_r)>0)
																	{
																		while($exe_d = mysqli_fetch_array($exe_r))
																		{
																			?>
																			<option value="<?php echo $exe_d['id']; ?>" <?=($sales_id == $exe_d['id'])?"selected":"";?>><?php echo $exe_d['name']; ?></option>
																			<?php
																		}
																	}	
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
															<label>Amount <code>*</code></label>
															<div class="input-group">
																<input type="text" class="form-control" name="paid_amount" id="paid_amount" value="<?php echo $paid_amount;?>" >
															</div>
															<p class="help-block"></p>
														</div>
													</div>

													<div class="col-md-6">
														<!-- <div class="form-group">
															<label>Sales Person<code>*</code></label> 
															<select class="form-control" name="sales_executive_id" id="sales_executive_id">
																<option value="">Select Sales Person</option>
																<?php
																if($_REQUEST['mode']=='edit')
																{
																	$exe_r = $db->rp_getData("sales_executive","*","isDelete=0",0);
																	if(mysqli_num_rows($exe_r)>0)
																	{
																		while($exe_d = mysqli_fetch_array($exe_r))
																		{
																			?>
																			<option value="<?php echo $exe_d['id']; ?>" <?=($sales_executive_id == $exe_d['id'])?"selected":"";?>><?php echo $exe_d['name']; ?></option>
																			<?php
																		}
																	}	
																}
																?>
															</select> 
															<p class="help-block"></p>		
														</div> -->
													</div>
												</div> 
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label>Date<code>*</code></label>
															<input type="text" class="form-control" name="payment_date" id="payment_date" data-date-end-date="0d" value="<?php echo $payment_date; ?>">
																<p class="help-block"></p>		
														</div>
													</div> 
													<div class="col-md-6">
													 	<div class="form-group">
					                                        <label>Payment BY<code>*</code> </label>
					                                        <select class="form-control" name="payment_type" id="payment_type"  value="<?php echo $payment_type;?>" autofocus >
																<option value="">--- Select Payment Type ---</option>
					                                            <option value="1" <?php if("1"==$payment_type){echo "selected";}?>>By Cash</option>
					                                            <option value="2" <?php if("2"==$payment_type){echo "selected";}?>>By Cheque</option>
					                                            <option value="3" <?php if("3"==$payment_type){echo "selected";}?>>Online</option>
					                                            <option value="4" <?php if("4"==$payment_type){echo "selected";}?>>Other</option>
				                                         	</select>
				                                         	<p class="help-block"></p>	
														</div>
													</div>	
												</div> 
												<div class="row">
													<div class="col-md-6">
														<div class="form-group">
															<label class="check_no">Cheque No </label>
															<input type="text" class="form-control check_no" name="cheque_no" id="cheque_no" value="<?php echo $cheque_no; ?>">
															<p class="help-block"></p>	
														</div>
													</div>
													<div class="col-md-6">
														<div class="form-group">
															<label>Remark </label>
															<textarea class="form-control" name="remark" id="remark" ><?php echo $remark; ?></textarea>
															<p class="help-block"></p>	
														</div>
													</div>										
												</div> 
											</div>
										</div>
										<div class="row">
											<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
												<button type="submit" name="submit" class="btn green">Submit</button>
												<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $ctable; ?>_manage.php'">Back</button>
											</div>
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
		<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script> 
		<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
		<script src="assets/global/plugins/jquery.quicksearch.js" type="text/javascript"></script>
		<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
		<script type="text/javascript" src="js/fSelect.js"></script>
		<script type="text/javascript">
			//-------#numeric field  validation----------//
			$("#paid_amount").numeric();
			$('#payment_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy', maxDate:0 }); 
			  
			$(document).ready(function(){$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) {$(this).parent().find('.help-block').html(""); $(this).parent().removeClass("has-error"); } }); });

			function check_form(){
				$(".form-body").children().removeClass("has-error");
				var isValid=true;
				if($("#sales_type").val()=="" || $("#sales_type").val().split(" ").join("")==""){		
					vd=aj.error('sales_type',"Please Select Customer Type.","add_error");
					isValid=false;
				}
				if($("#sale_id").val()=="" || $("#sale_id").val().split(" ").join("")==""){		
					vd=aj.error('sale_id',"Please Select Customer.","add_error");
					isValid=false;
				} 
				// if($("#sales_executive_id").val()=="" || $("#sales_executive_id").val().split(" ").join("")==""){		
				// 	vd=aj.error('sales_executive_id',"Please Select Sales Officer.","add_error");
				// 	isValid=false;
				// } 
				if($("#paid_amount").val()=="" || $("#paid_amount").val().split(" ").join("")==""){
					aj.error('paid_amount','Please enter Amount!!','add_error');
					isValid=false;
				}
				if($("#payment_date").val()=="" || $("#payment_date").val().split(" ").join("")==""){
					aj.error('payment_date','Please Select Payment Date!!','add_error');
					isValid=false; 
				}
				if($("#payment_type").val()=="" || $("#payment_type").val().split(" ").join("")==""){
					aj.error('payment_type','Please Select Payment Type.','add_error');
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
		<!-- get customer from customer type -->
		<script type="text/javascript">
			function getCustomer(sales_type)
			{
			 	$("#sale_id").select2("val","");
				$.ajax({
		          type: "POST",
		          url: "ajax_get_employee_plain_list.php",
		          data: 'sales_type='+sales_type,
		          success: function(data){
		            $("#sale_id").html(data); 
		          }
		        });
			}
		</script>


		<script type="text/javascript">
			function getsalesexecutive(sale_id)
			{
				$.ajax({
		          type: "POST",
		          url: "get_sales_executive_ajax.php",
		          data: 'sale_id='+sale_id,
		          success: function(result){

		          	$("#sales_executive_id").select2("destroy");
						$("#sales_executive_id").html(result); 
						$("#sales_executive_id").select2();
		            }
		        });
			}
		</script>
		<!-- get customer from customer type --> 

		<script type="text/javascript">
		$("#payment_type").on('change',function()
		{
			var b_id=$("#payment_type").val();
			if(b_id==2)
			{
			    $("div").find("input.check_no").removeClass("hidden");
				$("div").find("label.check_no").removeClass("hidden");	
			}
			else
			{
			    $("div").find("input.check_no").addClass("hidden");
				$("div").find("label.check_no").addClass("hidden");
			}
		})
	</script>
	</body>
</html>