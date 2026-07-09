<?php
$page_id=637;$page_slug='master_route_planning';
$ctable 	= "my_route";
$ctable1 	= "Master Route Planning";
$main_page 	= "product_mgmt";
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"Utility"),array("link"=>"master_route_manage.php","title"=>"Manage ".$ctable1),array("link"=>"route_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
require_once("../include/master_route.class.php");
$objMasterRoute= new MasterRoute();
$name			= "";
$code			= "";
$process_info			= "";
if(isset($_REQUEST['submit'])){
	
	$detail['sales_id']			= $db->clean($_REQUEST['sales_id']);
	$detail['start_date']			= $db->clean($_REQUEST['start_date']);
	$detail['end_date']			= $db->clean($_REQUEST['end_date']);
	$detail['state']			= $db->clean($_REQUEST['state']);
	$detail['city']			= $db->clean($_REQUEST['city']);
	$detail['main_city']			= $db->clean($_REQUEST['main_city']);
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
		$reply=$objMasterRoute->InsertMasterRoute($detail,$process);
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("master_route_manage.php?msg=inserted");
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
		$reply=$objMasterRoute->UpdateMasterRoute($detail,$process);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("master_route_manage.php?msg=updated");
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
		$reply=$objMasterRoute->MasterRouteGetEditData($detail);
		$process_info=$objMasterRoute->GetProcess($detail);
		// print_r($reply);exit;
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
		$sales_route_record = $db->rp_getTotalRecord("my_route","isDelete=0 AND route_id='".$detail['id']."'");
		if ($sales_route_record > 0) {
			$db->addErrorMessage("You cannot delete this master route because sales routes are created of this route!!");
			$db->rp_location("master_route_manage.php?msg=inserted");
		} else {
			$reply=$objMasterRoute->DeleteMasterRoute($detail);		
			if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("master_route_manage.php?msg=inserted");
			}
			else{
				$db->addErrorMessage($reply['ack_msg']);
			}
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
 <link href="assets/global/plugins/jquery-multi-select/css/multi-select.css" rel="stylesheet" type="text/css" />
 <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
<link href="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.min.css" rel="stylesheet" type="text/css" /> 
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="<?php echo "master_route_manage.php";?>" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
				
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
											<label>Sales Employee Name <code>*</code></label>
											<select  class="form-control" id="sales_id" name="sales_id" onChange="return State(this.value);">
													<option value="">Select Employee Name</option>
													<?php
													if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
													{
														  if($rights['personal_flag']==1)
													{
														$ctable_where_sales .=" AND id='".$_SESSION[SITE_SESS.'REFERANCE_ID']."' ";
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
																$ctable_where_sales .= " AND id IN (".$SALEID2.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";	
															}
															else
															{
																$ctable_where_sales .= " AND id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";		
															}
														}
														else
														{
															// $ctable_where_sales .= " isDelete=0 ";
														}
													}
													}
													$boxtype_r=$db->rp_getData("sales_executive","*","isDelete=0 AND isActive=1".$ctable_where_sales);

													if($boxtype_r)
													{
														while($BT=mysqli_fetch_assoc($boxtype_r))
														{
															?>
															<option <?= ($sales_id==$BT['id'])?"selected":""; ?> value="<?= $BT['id']; ?>"><?= $BT['name']; ?></option>
															<?php
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
										<label class="test"> Start Date <code>*</code></label>
										<input class="form-control b-3 datepicker" type="text" name="start_date" id="start_date" value="<?php if($start_date=="01-01-1970"){ echo $start_date = "";} else{echo  $start_date;}?>">
										<p class="help-block"></p>
									</div>
								</div>

						    </div>

						     <div class="row">

								<div class="col-md-6">
									<div class="form-group">
										<label class="test"> End Date <code>*</code></label>
										<input class="form-control b-3 datepicker" type="text" name="end_date" id="end_date" value="<?php if($end_date=="01-01-1970"){ echo $end_date = "";} else{echo  $end_date;}?>">
										<p class="help-block"></p>
									</div>
								</div>

						    </div>

							<div class="row">
									<div class="col-md-6">
								       <div class="form-group">
											<label for="country">State <code>*</code></label>
											<select name="state" id="state" class="form-control" onChange="return city_data(this.value);">
												<option value="">--Select State--</option>
												<!-- <?php
												$state_r = $db->rp_getData("class", "*","isDelete=0");
												if (mysqli_num_rows($state_r) > 0) 
												{
													while ($state_d = mysqli_fetch_array($state_r)) 
													{
														?>
														<option value="<?php echo $state_d['name']; ?>" <?php if ($state_d['name'] == $state) { ?> selected <?php } ?>><?php echo $state_d['name']; ?></option>
														<?php
													}
												}
												?> -->

												<?php
												if ($_REQUEST['mode'] == 'edit')
												{
													$state_r = $db->rp_getData("class", "*","isDelete=0");
													if (mysqli_num_rows($state_r) > 0) 
													{
														while ($state_d = mysqli_fetch_array($state_r)) 
														{
															?>
															<option data-sales_id="<?=$sales_id;?>" data-state_id="<?php echo $state_d['id']; ?>" value="<?php echo $state_d['name']; ?>" <?php if (strtolower($state_d['name']) == strtolower($state)) { ?> selected <?php } ?>><?php echo $state_d['name']; ?></option>
															<?php
														}
													}	
												}
												
												?>

												<!-- <?php
												if ($_REQUEST['mode'] == 'edit') 
												{
													$state_name = $db->rp_getValue("class", "name", "name='" . $state . "'", 0);
													?>
													<option value="<?php echo $state; ?>" <?php echo $state_name ?> selected> <?php echo $state_name; ?> </option>
													<?php
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
											<label for="city">City</label>
											
											<select name="main_city" id="main_city" class="form-control" onChange="return City(this.value);">
												<option value="">--Select City--</option>

												<?php
												if ($_REQUEST['mode'] == 'edit') 
												{
													$city_name = $db->rp_getValue("city", "name", "id='" . $main_city_id . "'",0);	
													?>
													<option value="<?php echo $main_city_id; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?> </option>
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
										<label for="city">Route</label>
										
										<select name="city" id="city" class="form-control">
											<option value="">--Select Route--</option>

											<?php
											if ($_REQUEST['mode'] == 'edit') 
											{
												$city_name = $db->rp_getValue("area", "name", "name='" . $city . "'",0);	
											?>
											<option value="<?php echo $city; ?>" <?php echo $city_name ?> selected> <?php echo $city_name; ?> </option>
											<?php
											}
											?>

										</select>
										<p class="help-block"></p>
							        </div>
							    </div>
								
							</div> 

								
								
							</div>
							<div class="form-actions">
								<button type="submit" name="submit" class="btn green">Submit</button>
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
<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/jquery.datetimepicker.full.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.min.js"></script>
<script type="text/javascript">
$("#checkAll").change(function () {
    $(".md-check").prop('checked', $(this).prop("checked"));
});
</script>
<script type="text/javascript">
$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

  //           function CustomerType(ctype,customer_id="") {
		// 	$('#seid').select2("val", "");
		// 	$.ajax({
		// 		type: "post",
		// 		url: "ajax_get_customer11.php",
		// 		data: "customer_type=" + ctype + "&customer_id=" + customer_id   ,
		// 		beforeSend: function() {
		// 			// $("#loading-modal").modal('show');
		// 			$('.preloader').fadeIn('slow');
		// 		},
		// 		success: function(result) {
		// 			// alert("result");
		// 			setTimeout(function() {
		// 				$('#seid').html(result);
		// 				$("#seid").select2("destroy");
		// 		        // $("#seid").html(data);
		// 		        $("#seid").select2();
		// 				// $("#loading-modal").modal('hide');
		// 				$('.preloader').fadeOut('slow');
		// 			});
		// 		}

		// 	})
			
		// }

	var date = new Date();
	date.setDate(date.getDate());

	

	$('#start_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});

	$('#end_date').datepicker({
	  format: "dd-mm-yyyy",
	  orientation: "auto",
	  startDate: "",
	  clearBtn: false
	});

	$(document).ready(function() {

			var mode = "<?= $_REQUEST['mode']; ?>";
			if (mode == "edit") {
				
				var ctype = "<?= $customer_type; ?>";
				var customer_id = "<?= $customer_id; ?>";
				CustomerType(ctype,customer_id);
				
			}
		})


function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;	
	if($("#sales_id").val()=="" || $("#sales_id").val().split(" ").join("")==""){		
		vd=aj.error('sales_id',"Please Select Employee Name.","add_error");
		isValid=false;
	}

	if($("#start_date").val()=="" || $("#start_date").val().split(" ").join("")==""){		
		vd=aj.error('start_date',"Please Enter Start Date.","add_error");
		isValid=false;
	}

	if($("#end_date").val()=="" || $("#end_date").val().split(" ").join("")==""){		
		vd=aj.error('end_date',"Please Enter End Date.","add_error");
		isValid=false;
	}

	if($("#state").val()=="" || $("#state").val().split(" ").join("")==""){		
		vd=aj.error('state',"Please Select State.","add_error");
		isValid=false;
	}

	// if($("#city").val()=="" || $("#city").val().split(" ").join("")==""){		
	// 	vd=aj.error('city',"Please Select City.","add_error");
	// 	isValid=false;
	// }


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



function State(val,state_id="") {
	// City(val);
	var state_id = $("#state").find(':selected').attr('data-state_id');
	$.ajax({
		type: "POST",
		url: "ajax_get_sales_state.php",
		data: 'sales_id=' + val + "&state_id="+state_id,
		success: function(result) {
			$("#state").html(result);
			
		}
	});
}
function City(val,city_name="") {

	var sales_id = $("#state").find(':selected').attr('data-sales_id');

	var state_id = $("#state").find(':selected').attr('data-state_id');
	var main_city = $("#main_city").val();

	

	$.ajax({
		type: "POST",
		url: "ajax_get_sales_city.php",
		data: 'sid=' + state_id + '&sales_id='+sales_id+ '&main_city='+main_city,
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


function city_data(val,city_name="") {

	var sales_id = $("#state").find(':selected').attr('data-sales_id');

	var state_id = $("#state").find(':selected').attr('data-state_id');

	

	$.ajax({
		type: "POST",
		url: "ajax_get_sales_main_city.php",
		data: 'sid=' + state_id + '&sales_id='+sales_id,
		success: function(result) {
			$("#main_city").html(result);
			var class_id = $("#state").find(':selected').attr('data-state_id');
			$("#class_id").val(class_id);
			$("#main_city").select2("destroy");
	        $("#main_city").html(result);
	        $("#main_city").select2();

		}
	});
}
</script>
</body>
</html>