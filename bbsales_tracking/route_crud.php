<?php
$page_id=631;$page_slug='route_page';
$ctable 	= "my_route";
$ctable1 	= "Sales Route Planning";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"master_route_manage.php","title"=>"Manage ".$ctable1),array("link"=>"route_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/route.class.php");
$objDepartment= new Department();
$name			= "";
$code			= "";
$process_info			= "";
$m_route_id  = isset($_REQUEST['master_route'])?$_REQUEST['master_route']:'';
$sales_id = $db->rp_getValue("my_route","sales_id","isDelete=0 AND id='".$_REQUEST['id']."'");
$route_id = $db->rp_getValue("my_route","route_id","isDelete=0 AND id='".$_REQUEST['id']."'");
$manage_url = "route_manage.php?msg=inserted&sales_id=".$sales_id."&route_id=".$route_id;
// print_r($_REQUEST['master_route']);exit;
// echo $m_route_d;exit;
if(isset($_REQUEST['submit'])){
	
	// m_route_id
	$detail['m_route_id']			= $db->clean($_REQUEST['m_route_id_hidden']);
	$detail['sales_id']			= $db->clean($_REQUEST['sales_id']);
	$detail['customer_type']			= $db->clean($_REQUEST['customer_type']);
	$detail['seid']			= 		(!empty($_REQUEST['seid']))?$_REQUEST['seid']:0;
	$detail['remark']			= $db->clean($_REQUEST['remark']);

	// $detail['state']			= $db->clean($_REQUEST['state']);
	// $detail['city']			= $db->clean($_REQUEST['city']);
	// print_r($detail['seid']);exit;

	$detail['date']			= $db->clean($_REQUEST['date']);
	$detail['isDelete']		= 0;


	

	//Insert Production Process
	$process_id=$_REQUEST['my_multi_select1'];
    $size[]=sizeof($process_id);
    $value_check=sizeof($process_id);
	if(in_array($value_check,$size))
	{
		$isValidArray=true;
	}
	else
	{
		$isValidArray=false;
	}

	if($isValidArray)
	{
		for($i=0;$i<sizeof($process_id);$i++)
		{
			$process[]=array("process_id"=>$process_id[$i]);
		}
	}
	
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objDepartment->InsertDepartment($detail,$process);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("route_crud.php?msg=inserted&mode=add&master_route=".$detail['m_route_id']);
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
		// $reply=$objDepartment->UpdateDepartment($detail,$process);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("route_crud.php?msg=updated&master_route=".$detail['m_route_id']);
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
		// $reply=$objDepartment->DepartGetEditData($detail);
		// $process_info=$objDepartment->GetProcess($detail);
		//print_r($process_info);exit;
		if($reply['ack']==1){
			//$SuccessMsg = $reply['ack_msg'];
			$result=$reply['result'];
			//print_r($result);
			extract($result);
		}else{
			$db->addErrorMessage($reply['ack_msg']);
		}
		if($process_info['ack']==1){

		$process_id=$_REQUEST['id'];
    		$process_info_r=$process_info['result'];
			
            $process_info=array();
            foreach($process_info_r as $i)
            {
                $process_info[]=$i['process_id'];
				
            }
			
        }
    	else{
    		//$process_info=array();
    	}
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];


		
		

			$reply=$objDepartment->DeleteDepartment($detail);		
			if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($manage_url);
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
<link rel="stylesheet" href="assets/global/plugins/jquery-ui/jquery-ui.min.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" />
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" /> 
 <link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="css/fSelect.css"/>
<style type="text/css">
	.fs-dropdown{
		width: 350px!important;
	}
</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo $manage_url;?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
				
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
							       		<input type="hidden" name="m_route_id_hidden" value="<?= $m_route_id ?>">
										<label>Master Route Planning <code>*</code></label>
										<select disabled  class="form-control" id="m_route_id" name="m_route_id" onChange="GetRoutes(this.value);">
												<option value=""> Select Master Route Planning</option>
												<?php

												if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
												{
													   if($rights['personal_flag']==1)
														{
															$ctable_where_sales .=" AND sales_id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
														}
														else
														{ 
															if($rights['chain_vise_flag'] == 1)
														 	{
																$check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
																$get_sales_typer=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
															    if ($get_sales_typer== "sales_manager") 
															    {
															        $sales_executive_type = "Regional Sales Manager";
															        $key="sm_id";
															        $WhereConditionr.=' ' .$key.'='.$check_id;
															    }
															    else if ($get_sales_typer == "area_sales_manager") 
															    {
															        $sales_executive_type = "National Sales Manager";//Business Development Manager
															        $key="asm_id";
															        $WhereConditionr.=' ' .$key.'='.$check_id;
															    }
															    else if ($get_sales_typer == "sales_officer") 
															    {
															        $sales_executive_type = "Area Sales Manager";//Area Sales Manager
															        $key="so_id";
															        $WhereConditionr.=' ' .$key.'='.$check_id;
															    }
															    else if ($get_sales_typer == "sales_executive") 
															    {
															        $sales_executive_type = "Sales Officer";
															        $key="se_id";
															        $WhereConditionr.=' ' .$key.'='.$check_id;
															    }
															    else
															    {
															    	$WhereConditionr.=' type = "service_engineer"';
															    }

															    $data_r = $db->rp_getData("sales_executive","id",$WhereConditionr,"",0);

															    $SALEID2=array();
																if($data_r)
																{
																	while($data_dd=mysqli_fetch_assoc($data_r))
																	{
																		$SALEID2[]=$data_dd['id'];
																	}
																}
																if(!empty($SALEID2))
																{
																	$SALEID2=implode(",", $SALEID2);
																	$ctable_where_sales .= " AND sales_id IN (".$SALEID2.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
																}
																else
																{
																	$ctable_where_sales .= " AND sales_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
																}
															}
															else
															{
																// $ctable_where_sales .= " isDelete=0 ";
															}
														}
												}
												$m_route_r=$db->rp_getData("master_route","*","isDelete=0 AND isActive=1".$ctable_where_sales,"",0);
												if($m_route_r)
												{
													while($m_route_d=mysqli_fetch_assoc($m_route_r))
													{
														$sales_name = $db->rp_getValue("sales_executive","name","isDelete=0 AND id='".$m_route_d['sales_id']."'");

														?>
														<option <?= ($m_route_id==$m_route_d['id'])?"selected":""; ?> value="<?= $m_route_d['id']; ?>"><?= $sales_name .' - '.$m_route_d['state'] .' - '.$m_route_d['city'].' ('.$m_route_d['start_date'].' To '.$m_route_d['end_date'].')'; ?></option>
														<?php
													}
												}
												?>
										</select>
										<p class="help-block"></p>
							        </div>
							    </div>
								<div class="col-md-6">
									<ul>
									  <li>
									    <span><strong>Employee Name:</strong> <span id="employee_name"></span></span>
									  </li>
									  <li>
									    <span><strong>Start Date:</strong> <span id="start_date"></span></span>
									  </li>
									  <li>
									    <span><strong>End Date:</strong> <span id="end_date"></span></span>
									  </li>
									  <li>
									    <span><strong>State:</strong> <span id="state_name"></span></span>
									  </li>
									  <li>
									    <span><strong>City:</strong> <span id="main_city_name"></span></span>
									  </li>
									  <li>
									    <span><strong>Route:</strong> <span id="area_name"></span></span>
									  </li>
									</ul>

								</div>
							</div>

							<div class="row">
									<div class="col-md-6">
								       <div class="form-group">
											<label>Sales Person Name <code>*</code></label>
											<select disabled class="form-control" id="sales_id" name="sales_id">
													<option value="">Select Sales Person Name</option>

													<?php
													if ($_REQUEST['mode']=="edit") 
													{
														$sales_id = $db->rp_getValue("sales_executive", "name", "id='" . $sales_id . "'",0);
														?>
														<option  value="<?php echo $sales_id; ?>" <?php echo $sales_id ?> selected> <?php echo $sales_id; ?> </option>
														<?php
													}
													?>

													<!-- <?php
													$boxtype_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1");
													if($boxtype_r)
													{
														while($BT=mysqli_fetch_assoc($boxtype_r))
														{
															?>
															<option <?= ($sales_id==$BT['id'])?"selected":""; ?> value="<?= $BT['id']; ?>"><?= $BT['name']; ?></option>
															<?php
														}
													}
													?> -->
											</select>
											<p class="help-block"></p>
								        </div>
								    </div>
								
							</div>

							
								
									
								<div class="row">
									<div class="col-md-6">
								       <div class="form-group">
											<label>Customer Type <code>*</code></label>
											<select class="form-control" name="customer_type" id="customer_type" onChange="CustomerType(this.value);" autofocus>
													<option value="">Select Customer Type</option>
													<?php
													$customer_type_r = $db->rp_getData("customer_type", "*","isDelete=0", 0);
													if (mysqli_num_rows($customer_type_r) > 0) {
														while ($customer_type_d = mysqli_fetch_array($customer_type_r)) {
													?>
													<?php
													if($_REQUEST['mode']=="edit")
													{
														// $sid = $db->rp_getData("my_route","customer_id","isDelete=0 AND id='".$_REQUEST['id']."'","",1);
														$customer_type = $db->rp_getValue("executive","type_of_executive","isDelete=0 AND id='".$customer_id."'",0);
													}
													?>
															<option value="<?php echo $customer_type_d['id']; ?>" <?= ($customer_type == $customer_type_d['id']) ? "selected" : ""; ?>><?php echo $customer_type_d['name']; ?></option>
													<?php
														}
													}
													?>
											</select>
											<div class="tilting"></div>
											<p class="help-block"></p>
								        </div>
								    </div>
								
							    </div>

							<div class="row">
								<div class="col-md-6">
							       <div class="form-group">
										<label>Customer <code>*</code></label>
										<select class="form-control" id="seid" name="seid[]" multiple>
										<option value="">Select Customer</option>
												
										</select>
										<p class="help-block"></p>
										<span><strong>NOTE: <span style="color: red;">COMPANY NAME - PERSON NAME - MOBILE NO - CITY - ROUTE</span></strong></span>
							        </div>
							    </div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label class="test"> Date <code>*</code></label>
										<input class="form-control b-3 datepicker" type="text" name="date" id="date" value="<?php if($date=="01-01-1970"){ echo $date = "";} else{echo  $date;}?>">
										<p class="help-block"></p>
									</div>
								</div>
						    </div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label>Remark</label><br>
										<textarea class="form-control" id="remark" value="" name="remark" ><?= $remark ?></textarea>
									</div>
								</div>
							</div>

							<!-- <div class="row">
									<div class="col-md-6">
								       <div class="form-group">
											<label for="country">State</label>
											<select name="state" id="state" class="form-control" onChange="return City(this.value);">
												<option value="">--Select State--</option>
												<?php
												$state_r = $db->rp_getData("state", "*","isDelete=0");
												if (mysqli_num_rows($state_r) > 0) 
												{
													while ($state_d = mysqli_fetch_array($state_r)) 
													{
														?>
														<option value="<?php echo $state_d['name']; ?>" <?php if ($state_d['name'] == $state) { ?> selected <?php } ?>><?php echo $state_d['name']; ?></option>
														<?php
													}
												}
												?>
											</select>
								        </div>
								    </div>
								
							</div> -->


							<!-- <div class="row">
									<div class="col-md-6">
								       <div class="form-group">
											<label for="city">City</label>
											<select name="city" id="city" class="form-control">
												<option value="">--Select City--</option>

												<?php
												if ($_REQUEST['mode'] == 'edit') 
												{
													$city_name = $db->rp_getValue("city", "name", "name='" . $city . "'", 0);
													?>
													<option value="<?php echo $city; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?> </option>
													<?php
												}
												?>

											</select>
											<p class="help-block"></p>
								        </div>
								    </div>
								
							</div> -->
							</div>
							<div class="form-actions">
								<input type="hidden" name="middatePic" id="middatePic">
								<input type="hidden" name="mxddatePic" id="mxddatePic">
								<button type="submit" name="submit" class="btn green">Save & Next</button>
								<button type="button" class="btn btn-default" onClick="window.location.href='master_route_manage.php'">Back</button>
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
<script src="assets/global/plugins/jquery-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="js/fSelect.js"></script>



<script type="text/javascript">
var MINDATE = "";
var MAXDATE = "";
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
$("#seid").fSelect();
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

            function CustomerType(ctype,customer_id="") {
			$('#seid').fSelect("destroy");
			$('#seid').val("");
			$('#seid').fSelect("create");
			$.ajax({
				type: "post",
				url: "ajax_get_customer11.php",
				data: "customer_type=" + ctype + "&customer_id=" + customer_id   ,
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// alert(result);
					// setTimeout(function() {
				        $('#seid').fSelect("destroy");
						$('#seid').val("");
						$('#seid').html(result);
						$('#seid').fSelect("create");
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
						$('.tilting').html('<a href=executive_crud.php?type='+ctype+'&mode=add&r_type=1 target=_blank ><button type="button" >Add Customer</button></a>');
					// });
				}

			})
			// if (mode == "add") {
			// 	var l = $("#datatable_1").find('tbody').find('tr').length;
			// 	if (l > 0) {
			// 		alert("You lost all added Product");
			// 		$("#datatable_1").find('tbody').html("");
			// 		recalculateRow();
			// 		recalculateFinalValues();
			// 	}
			// }

		}


		function GetRoutes(mr_id) {
			// $('#seid').select2("val", "");
			fetchDataMasterRoute(mr_id)
			$.ajax({
				type: "post",
				url: "ajax_get_master_routes.php",
				data: "mr_id=" + mr_id,
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					// alert("result");
					setTimeout(function() {
						$('#sales_id').html(result);
						$("#sales_id").select2("destroy");
				        // $("#seid").html(data);
				        $("#sales_id").select2();
				        $("#sales_id").prop( "disabled", true );           

						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
					});
				}

			})

		}

		function fetchDataMasterRoute(master_id){
			// alert("fds");
			$.ajax({
				type: "post",
				url: "ajax_get_master_routes_data.php",
				data: "master_id=" + master_id,
				beforeSend: function() {
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success: function(result) {
					result = JSON.parse(result);
					$("#employee_name").html(result.sales_name);
					$("#start_date").html(result.start_date);
					$("#end_date").html(result.end_date);
					$("#state_name").html(result.state);
					$("#main_city_name").html(result.main_city);
					$("#area_name").html(result.area);
					$("#middatePic").val(result.min_date);
					$("#mxddatePic").val(result.max_date);
					MINDATE = new Date(result.min_date);
					MAXDATE = new Date(result.max_date);
					// alert(MINDATE + " - " + MAXDATE);
					$('#date').datepicker({
					  dateFormat: 'dd-mm-yy',
					  orientation: "auto",
					  startDate: "",
					  clearBtn: false,
					  minDate: MINDATE,
					  maxDate:MAXDATE
					});
					$('.preloader').fadeOut('slow');
				}

			})
		}

		

	var date = new Date();
	date.setDate(date.getDate());
	

	// function check_form()
	// {
	// 	$(".form-body").children().removeClass("has-error");
	// 	var isValid=true;	
	// 	if($("#sales_id").val()=="" || $("#sales_id").val().split(" ").join("")==""){		
	// 		vd=aj.error('sales_id',"Please Enter Category Name.","add_error");
	// 		isValid=false;
	// 	}
	// 	if(isValid)
	// 	{
	// 		return true;
	// 	}
	// 	else
	// 	{
	// 		return false;
	// 	}
 //    }

	$(document).ready(function() {

			var mode = "<?= $_REQUEST['mode']; ?>";
			if (mode == "edit") {
				
				var ctype = "<?= $customer_type; ?>";
				var customer_id = "<?= $customer_id; ?>";
				CustomerType(ctype,customer_id);
				
			}
			GetRoutes('<?=$m_route_id ?>')
			// fetchDataMasterRoute('<?=$m_route_id ?>')
			// var MINDATE = new Date($("#middatePic").val());
			// var MAXDATE = new Date($("#mxddatePic").val());
		})

			

function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	

	

	if($("#m_route_id").val()=="" || $("#m_route_id").val().split(" ").join("")==""){		
		vd=aj.error('m_route_id',"Please Select Route Planning .","add_error");
		isValid=false;
	}

	if($("#sales_id").val()=="" || $("#sales_id").val().split(" ").join("")==""){		
		vd=aj.error('sales_id',"Please Select Person Name.","add_error");
		isValid=false;
	}

	if($("#customer_type").val()=="" || $("#customer_type").val().split(" ").join("")==""){		
		vd=aj.error('customer_type',"Please Select Customer Type.","add_error");
		isValid=false;
	}

	if($("#seid").val()==null){		
		vd=aj.error('seid',"Please Select Customer.","add_error");
		toastr.error("Select Customer");
		isValid=false;
	}

	if($("#date").val()=="" || $("#date").val().split(" ").join("")==""){		
		vd=aj.error('date',"Please Select Date.","add_error");
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
$(function(){
	$("#user_multi_select").multiSelect();
	
});


function City(val,city_name="") {
	$.ajax({
		type: "POST",
		url: "ajax_get_city.php",
		data: 'sid=' + val + '&city='+city_name,
		success: function(result) {
			$("#city").html(result);
			var class_id = $("#state").find(':selected').attr('data-state_id');
			$("#class_id").val(class_id);
			$("#city").select2("destroy");
	        $("#city").html(result);
	        $("#city").select2();

		}
	});
}



</script>
</body>
</html>