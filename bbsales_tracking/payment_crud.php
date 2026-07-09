<?php
$page_id=571;$page_slug='payment';
$page_slug="manage_super_stockist";
$ctable 	= "payment";
$ctable1 	= "Payment";
$main_page 	= $ctable;
$page 		= $ctable."_crud";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Account"),array("link"=>$ctable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$ctable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/class.payment.php");
$objPayment= new Payment();
$customer_type			= ""; 
$customer_id	        = "";
$sales_executive_id	    = "";
$paid_amount 			= "";
$payment_date 			= "";
$payment_type		    = "";
$remark 			    = "";
$cheque_no 			    = "";

if(isset($_REQUEST['submit']))
{
	$detail['customer_type']		= $db->clean($_REQUEST['customer_type']);
	$detail['customer_id']			= $db->clean($_REQUEST['customer_id']);
	$detail['sales_executive_id']	= $db->clean($_REQUEST['sales_executive_id']);
	$detail['paid_amount']			= $db->clean($_REQUEST['paid_amount']);
	$detail['payment_date']			= date('Y-m-d',strtotime($db->clean($_REQUEST['payment_date'])));
	$detail['payment_type']			= $db->clean($_REQUEST['payment_type']); 
	$detail['remark']				= $db->clean($_REQUEST['remark']);
	$detail['cheque_no']			= $db->clean($_REQUEST['cheque_no']);
	$detail['isActive']				= 1; 
	$detail['receipt_type']		= $db->clean($_REQUEST['receipt_type']);
	if($detail['receipt_type']==2)
	{
		$detail['invoice_id']= ($_REQUEST['invoice_id']!="")?$_REQUEST['invoice_id']:"";  
	} 
	else
	{
		$detail['invoice_id']="";
	}

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add")
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply=$objPayment->InsertPayment($detail);
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
		// $detail['id']=$_REQUEST['id'];
		$reply=$objPayment->UpdatePayment($detail,$_REQUEST['id']);
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
	$reply=$objPayment->EditPayment($detail);
	if($reply['ack']==1)
	{
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
	$reply=$objPayment->DeletePayment($detail);
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
										    <div class="col-md-12">
												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
															<label for="customer_type">Select Customer Type<code>*</code></label>
															<select class="form-control"  name="customer_type" id="customer_type" onchange="getCustomer(this.value)">
											                    <option value="">Select Customer Type</option>
														<?php   
														$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
														$get_customer_type=$db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='". $check_id."'",0);
														?> 
														<?php
														if($_SESSION[SITE_SESS.'REFERANCE_TYPE']==3) 
														{ 
														if($get_customer_type==1)
														{ 
														?>
														<option value="1">Super Stockist</option> 
														<?php 
														}  
														?>
														<option value="2">Distributor</option>  
														<?php 
														if($get_customer_type!=1)
														{ 
														?>
														<option value="3">Retailer</option>    
														<?php 
														}
														}
														else
														{ 
														$cust_R = $db->rp_getData("customer_type", "name,id", "isDelete=0");
														if ($cust_R) {
															while ($C = mysqli_fetch_assoc($cust_R)) {
														?>
														<option value="<?= $C['id']; ?>"><?= $C['name']; ?></option>
														<?php
															}
														}
														}
														?>
													</select>
										                	</select>
															<p class="help-block"></p>	
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label for="customer_id">Select Customer<code>*</code></label>
															<select class="form-control" name="customer_id" id="customer_id" onchange="getsalesexecutive(this.value);"> <?php echo $disable;?>>
																<option value="">Select Customer Name</option>
																<?php
																if($_REQUEST['mode']=='edit' && $customer_type!="")
																$cus_r = $db->rp_getData("executive","*","type_of_executive='".$customer_type."' AND isDelete=0 AND isActive=1",0);
																if(mysqli_num_rows($cus_r)>0)
																{
																	while($cus_d = mysqli_fetch_array($cus_r))
																	{
																?>
																<option value="<?php echo $cus_d['id']; ?>" <?=($customer_id == $cus_d['id'])?"selected":"";?>><?php echo $cus_d['company_name']." - ".$cus_d['cname']; ?></option>
																<?php
																	}
																}
																?>
															</select>
															<p class="help-block"></p>	
														</div>
													</div> 
													<div class="col-md-3">
														<div class="form-group">
															<label for="receipt_type">Select Receipt Type<code>*</code></label>
															<select class="form-control" name="receipt_type" id="receipt_type" onchange="dispInvoice()">
																<option value="">Select Type</option>
																 
																<option <?=($receipt_type ==1) ?"selected":"";?> value="1">General</option>
																<option <?=($receipt_type ==2) ?"selected":"";?> value="2">Order</option> 
															</select> 	
														</div>
													</div> 
													<div class="col-md-3 invoiceShow hidden">
														<div class="form-group">
															<label for="invoice_id">Select Order<code>*</code></label>
															<select onchange="checkRemaining()" class="form-control" name="invoice_id" id="invoice_id">
																<option value="">Select Order</option>
															</select> 	
														</div>
													</div>  
												</div>

												<div class="row">
													<div class="col-md-3">
														<div class="form-group">
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
														</div>
													</div>
													
													<div class="col-md-3">
														<div class="form-group">
															<label>Amount <code>*</code></label> 
															<input type="text" class="form-control" name="paid_amount" id="paid_amount" value="<?php echo $paid_amount;?>" onchange="checkRemaining()">
															
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label>Date<code>*</code></label>
															<input type="text" class="form-control" name="payment_date" id="payment_date" data-date-end-date="0d" value="<?php echo $payment_date; ?>">
																<p class="help-block"></p>		
														</div>
													</div> 
												</div> 
												<div class="row">
													<div class="col-md-3">
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
													<div class="col-md-3">
														<div class="form-group">
															<label class="check_no">Cheque No </label>
															<input type="text" class="form-control check_no" name="cheque_no" id="cheque_no" value="<?php echo $cheque_no; ?>">
															<p class="help-block"></p>	
														</div>
													</div>
												</div> 
												<div class="row">
													<div class="col-md-3">
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
				if($("#customer_type").val()=="" || $("#customer_type").val().split(" ").join("")==""){		
					vd=aj.error('customer_type',"Please Select Customer Type.","add_error");
					isValid=false;
				}
				if($("#customer_id").val()=="" || $("#customer_id").val().split(" ").join("")==""){		
					vd=aj.error('customer_id',"Please Select Customer.","add_error");
					isValid=false;
				} 
				if($("#sales_executive_id").val()=="" || $("#sales_executive_id").val().split(" ").join("")==""){		
					vd=aj.error('sales_executive_id',"Please Select Sales Officer.","add_error");
					isValid=false;
				} 
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
			function getCustomer(customer_type)
			{
			 	$("#customer_id").select2("val","");
				$.ajax({
		          type: "POST",
		          url: "find_customer.php",
		          data: 'type_of_executive='+customer_type,
		          success: function(data){
		            $("#customer_id").html(data); 
		          }
		        });
			}
		</script>


		<script type="text/javascript">
			function getsalesexecutive(customer_id)
			{
				$.ajax({
		          type: "POST",
		          url: "get_sales_executive_ajax.php",
		          data: 'customer_id='+customer_id,
		          success: function(result){

		          	$("#sales_executive_id").select2("destroy");
						$("#sales_executive_id").html(result); 
						$("#sales_executive_id").select2();
		            }
		        });

		        getInvoice();
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

		<!-- get customer from customer type -->
		<script type="text/javascript">
			<?php 
			if($_REQUEST['mode']=='edit')
			{
			?>
			getInvoice();
			dispInvoice();
			<?php
			}
			?> 
			function getInvoice()
			{
				var invoice_id = '<?= $invoice_id; ?>';
			 	var customer_id = $("#customer_id").val();
			 	// alert(customer_id);
				$.ajax({
		          type: "POST",
		          url: "payment_customer_invoice.php",
		          data: 'customer_id='+customer_id+'&invoice_id='+invoice_id,
		          success: function(data){
		            $("#invoice_id").html(data); 
		          }
		        });
			}
			function dispInvoice()
			{
				var receipt_type = $("#receipt_type").val();
				if(receipt_type==2)
				{
					$(".invoiceShow").removeClass("hidden");
				}
				else
				{
					$(".invoiceShow").addClass("hidden");				
				}
			}
			function checkRemaining()
			{
				var paid_amount = $("#paid_amount").val();
				var receipt_type = $("#receipt_type").val();

				var mode = '<?= $_REQUEST['mode']?>';
				var remaining = $("#invoice_id").find("option:selected").data("remaining-amt");
				// alert(remaining);
				if((paid_amount > remaining) && receipt_type==2)
				{
					toastr.error("You can not enter more than invoice remaining amount!!");
					$("#paid_amount").val("");
				}
			}
		</script>
		<!-- get customer from customer type --> 
	</body>
</html>