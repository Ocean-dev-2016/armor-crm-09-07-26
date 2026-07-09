<?php
$page_id=658;$page_slug='sales_executive_info_form';
$ctable 	= "sales_executive_information";
$ctable1 	= "Sales Executive Information";
$main_page 	= $ctable;
$page 		= "manage_".$ctable;
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"employee_information_manage.php","title"=>"Manage Employee Information"),array("link"=>"employee_information_crud.php","title"=>"Add/Edit Employee Information"));
include("connect.php");
require_once("../include/class.employee_information.php");
$objinfo= new LeaveRequest();
$leave_type			 = "";
$sales_executive_id	 = "";
$leave_details       = "";
$latitude            = "";
$longitude           = "";
$file_path           ="";
$user_id             =($_REQUEST['id'])?$_REQUEST['id']:$_SESSION[SITE_SESS.'_ADMIN_SESS_ID'];
// echo "<pre>";
// 	print_r($_FILES);
// 	echo "<hr>";
// 	print_r($_REQUEST);die;
if(isset($_REQUEST['submit'])){
	
	
	// echo "<pre>";
	// print_r($_FILES);
	// echo "<hr>";
	// print_r($_REQUEST);die;
	$detail['id']	        			= $_REQUEST['id'] != "" ? $db->clean($_REQUEST['id']) : "";
	$detail['post_applied']	        	= $_REQUEST['post_applied'] != "" ? $db->clean($_REQUEST['post_applied']) : "";
	$detail['reference']	        	= $_REQUEST['reference'] != "" ? $db->clean($_REQUEST['reference']) : "";
	$detail['first_name']	        	= $_REQUEST['first_name'] != "" ? $db->clean($_REQUEST['first_name']) : "";
	$detail['middle_name']	        	= $_REQUEST['middle_name'] != "" ? $db->clean($_REQUEST['middle_name']) : "";
	$detail['surname']	        		= $_REQUEST['surname'] != "" ? $db->clean($_REQUEST['surname']) : "";
	$detail['gender']	        		= $_REQUEST['gender'] != "" ? $db->clean($_REQUEST['gender']) : "";
	$detail['religion']	        		= $_REQUEST['religion'] != "" ? $db->clean($_REQUEST['religion']) : "";
	$detail['cast']	        			= $_REQUEST['cast'] != "" ? $db->clean($_REQUEST['cast']) : "";
	$detail['mother_tongue']	    	= $_REQUEST['mother_tongue'] != "" ? $db->clean($_REQUEST['mother_tongue']) : "";
	$detail['marital_status']	    	= $_REQUEST['marital_status'] != "" ? $db->clean($_REQUEST['marital_status']) : "";
	$detail['plaece_of_birth']	    	= $_REQUEST['plaece_of_birth'] != "" ? $db->clean($_REQUEST['plaece_of_birth']) : "";
	$detail['present_address']	    	= $_REQUEST['present_address'] != "" ? $db->clean($_REQUEST['present_address']) : "";
	$detail['permanent_address']		= $_REQUEST['permanent_address'] != "" ? $db->clean($_REQUEST['permanent_address']) : "";
	$detail['contact_no']	        	= $_REQUEST['contact_no'] != "" ? $db->clean($_REQUEST['contact_no']) : "";
	$detail['emergency_contact_person']	= $_REQUEST['emergency_contact_person'] != "" ? $db->clean($_REQUEST['emergency_contact_person']) : "";
	$detail['contact_person_relation']	= $_REQUEST['contact_person_relation'] != "" ? $db->clean($_REQUEST['contact_person_relation']) : "";
	$detail['blood_group']	        	= $_REQUEST['blood_group'] != "" ? $db->clean($_REQUEST['blood_group']) : "";
	$detail['email']	        		= $_REQUEST['email'] != "" ? $db->clean($_REQUEST['email']) : "";
	$detail['type_of_vehicle']	        = $_REQUEST['type_of_vehicle'] != "" ? $db->clean($_REQUEST['type_of_vehicle']) : "";
	$detail['vehicle_model_no']	        = $_REQUEST['vehicle_model_no'] != "" ? $db->clean($_REQUEST['vehicle_model_no']) : "";
	$detail['physical_disability']	    = $_REQUEST['physical_disability'] != "" ? $db->clean($_REQUEST['physical_disability']) : "";
	$detail['major_illness']	        = $_REQUEST['major_illness'] != "" ? $db->clean($_REQUEST['major_illness']) : "";
	$detail['rp1_name']	        		= $_REQUEST['rp1_name'] != "" ? $db->clean($_REQUEST['rp1_name']) : "";
	$detail['rp1_relation']	        	= $_REQUEST['rp1_relation'] != "" ? $db->clean($_REQUEST['rp1_relation']) : "";
	$detail['rp1_occupation']	        = $_REQUEST['rp1_occupation'] != "" ? $db->clean($_REQUEST['rp1_occupation']) : "";
	$detail['rp1_contact_no']	        = $_REQUEST['rp1_contact_no'] != "" ? $db->clean($_REQUEST['rp1_contact_no']) : "";
	$detail['rp2_name']	       			= $_REQUEST['rp2_name'] != "" ? $db->clean($_REQUEST['rp2_name']) : "";
	$detail['rp2_relation']	        	= $_REQUEST['rp2_relation'] != "" ? $db->clean($_REQUEST['rp2_relation']) : "";
	$detail['rp2_occupation']	        = $_REQUEST['rp2_occupation'] != "" ? $db->clean($_REQUEST['rp2_occupation']) : "";
	$detail['rp2_contact_no']	        = $_REQUEST['rp2_contact_no'] != "" ? $db->clean($_REQUEST['rp2_contact_no']) : "";
	$detail['date']           			= $_REQUEST['date'] != "" ? date("Y-m-d",strtotime(str_replace('/', '-',$_REQUEST['date']))) : "";
	$detail['birth_date']           	= $_REQUEST['birth_date'] != "" ? date("Y-m-d", strtotime(str_replace('/', '-', $_REQUEST['birth_date']))) : "";
	$detail['old_image_path']           	= $_REQUEST['old_image_path'] != "" ?($_REQUEST['old_image_path']) : "";
	
	// print_r($detail);exit;
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add"){
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objinfo->InsertInfo($detail,$_FILES);
		// print_r($reply);die;
		if($reply['ack']==1)
		{
			$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location("employee_information_crud.php?mode=edit&id=".$reply['data_id']."&msg=inserted");
		}else{
				 $db->addErrorMessage($reply['ack_msg']);
			}
		}
		
	else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit")
	{
		// 	echo "<pre>";
		// print_r($_FILES);
		// echo "<hr>";
		// print_r($_REQUEST);die;
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$reply=$objinfo->UpdateInfo($detail,$_FILES);
		// print_r($reply);die;
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location("employee_information_manage.php?msg=updated");
		}
		else{
				 $db->addErrorMessage($reply['ack_msg']);
			} 
		
	}
}

if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	$detail['id']	        	= $_REQUEST['id'] != "" ? $db->clean($_REQUEST['id']) : "";
	if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
		$where = " id='".$_REQUEST['id']."' AND isDelete=0";
		$ctable_r = $db->rp_getData($ctable,"*",$where,"",0);
		$result = mysqli_fetch_assoc($ctable_r);
		$result["date"] = $result["date"] != "" && $result["date"] != "0000-00-00" ? date('d-m-Y',strtotime($result["date"])) : "";
		$result["birth_date"] = $result["birth_date"] != "" && $result["birth_date"] != "0000-00-00" ? date('d-m-Y',strtotime($result["birth_date"])) : "";
		// $result["birth_date"] = $result["birth_date"] != "" && $result["birth_date"] != "0000-00-00" ? birth_date('d-m-Y',strtotime($result["birth_date"])) : "0000-00-00";
		// echo $result["birth_date"];die;
		$detail['id']=$_REQUEST['id'];	
		// $reply=$objinfo->GetEditDataLeaveType($detail);
		// if($reply['ack']==1){
		// 	//$SuccessMsg = $reply['ack_msg'];
		// 	$result=$reply['result'];
			// print_r($result);
			extract($result);
		// }else{
		// 	$db->addErrorMessage($reply['ack_msg']);
		// }
	
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	// print_r($_REQUEST);die;
    if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}	
		$detail['id']=$_REQUEST['id'];
		$reply=$objinfo->DeleteInfo($detail);
		if($reply['ack']==1){
		$db->addSuccessMessage($reply['ack_msg']);
		$db->rp_location("employee_information_manage.php?msg=inserted");
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
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/redmond/jquery-ui.css">
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
<style type="text/css">
	.add {
	  	display: flex;
	  	justify-content: flex-end;
	}
	.add {
	  	display: grid;
	  	justify-items: end;
	}

</style>
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">
				<h1><a href="employee_information_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
			<form role="form" id="crud_form" action="" onSubmit="return check_form();" method="post" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-12">					
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="form-body">
									<div class="row">
										<div class="col-md-12">
											<div class="portlet grey-cascade box">
												<div class="portlet-title">
													<div class="caption">
														Employee Information Form
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-2">
											<div class="form-group">
												<label class="test"><b>Post Applied</b><code>*</code></label>
												<input class="form-control" type="text" name="post_applied" id="post_applied" value="<?php if($post_applied=="01-01-1970"){ echo $post_applied = "";} else{echo  $post_applied;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="test"><b>Reference</b></label>
												<input class="form-control" type="text" name="reference" id="reference" value="<?php if($reference==""){ echo $reference = "";} else{echo  $reference;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="test"><b>Date</b></label>
												<input class="form-control b-3 datepicker" type="text" name="date" id="date" value="<?php if($date=="01-01-1970"){ echo $date = "";} else{echo  $date;}?>">
												<p class="help-block"></p>
											</div>
										</div>									
										<div class="col-md-3">
											<div class="form-group ">
		                                        <input data-image="<?php echo ($image_path!="" && file_exists(EMPLOYEE_IMAGE_A.$image_path))?EMPLOYEE_IMAGE_A.$image_path:"";?>" type="file" accept="image/*" name="image_path[]" id="image_path" data-old-image-dom="old_image_path"  data-old-image-path="<?php echo $image_path ?>" value="" multiple>
												<input type="hidden" name="old_image_path" id="old_image_path" value="<?php echo $image_path;?>">
		                                    </div>		
										</div>
										<?php 
											if ($_REQUEST['mode'] == "edit"){
												?>
												<div class="col-md-3 col-sm-3 col-lg-3 col-xl-3">
													<?php 
													$img = explode(",", $image_path);
													$imgpath = array();
													for ($i=0; $i < sizeof($img); $i++)
													{ 
														$imgpath[] = EMPLOYEE_IMAGE_A.$db->rp_getValue("media","url","id=".$img[$i],0);
													}
													// print_r($imgpath);
													for ($i=0; $i < sizeof($imgpath); $i++)
													{
														if($i==0)
														{
															?>
																<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
															<?php 
														}
														else
														{
															?>
															<div class="hidden">
																<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
															</div>
															<?php
														}
													}
													?>
												</div>
												<?php 
											}
										?>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>First Name</b><code>*</code></label>
												<input class="form-control" type="text" name="first_name" id="first_name" value="<?php if($first_name==""){ echo $first_name = "";} else{echo  $first_name;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Middle Name</b><code>*</code></label>
												<input class="form-control" type="text" name="middle_name" id="middle_name" value="<?php if($middle_name==""){ echo $middle_name = "";} else{echo  $middle_name;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Surname</b><code>*</code>	</label>
												<input class="form-control" type="text" name="surname" id="surname" value="<?php if($surname==""){ echo $surname = "";} else{echo  $surname;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Date Of Birth</b></label>
												<input class="form-control b-3 datepicker" type="text" name="birth_date" id="birth_date" value="<?php if($birth_date=="01-01-1970"){ echo $birth_date = "";} else{echo  $birth_date;}?>">
												<p class="help-block"></p>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Gender</b><code>*</code></label>
												<input class="form-control" type="hidden" name="gender" value="<?php echo $gender; ?>">
												<div>
													<label><input type="radio" id="gender" name="gender" value="male" <?php if ($gender === 'male') echo 'checked'; ?>> Male</label>
													<label><input type="radio" id="gender" name="gender" value="female" <?php if ($gender === 'female') echo 'checked'; ?>> Female</label>
													<label><input type="radio" id="gender" name="gender" value="other" <?php if ($gender === 'other') echo 'checked'; ?>> Other</label>
												</div>
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Religion</b></label>
												<input class="form-control" type="text" name="religion" id="religion" value="<?php if($religion==""){ echo $religion = "";} else{echo  $religion;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Cast</b></label>
												<input class="form-control" type="text" name="cast" id="cast" value="<?php if($cast==""){ echo $cast = "";} else{echo  $cast;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Mother Tongue</b></label>
												<input class="form-control" type="text" name="mother_tongue" id="mother_tongue" value="<?php if($mother_tongue==""){ echo $mother_tongue = "";} else{echo  $mother_tongue;}?>">
												<p class="help-block"></p>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Marital Status</b></label>
												<select class="form-control" name="marital_status" id="marital_status">
													<option>Select Marital Status</option>
													<option <?php if($marital_status==1){ echo "selected"; } ?> value="1">Single</option>
													<option <?php if($marital_status==2){ echo "selected"; } ?> value="2">Married</option>
												</select>
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Place Of Birth</b></label>
												<input class="form-control" type="text" name="plaece_of_birth" id="plaece_of_birth" value="<?php if($plaece_of_birth==""){ echo $plaece_of_birth = "";} else{echo  $plaece_of_birth;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Present Address</b><code>*</code></label>
												<input class="form-control" type="text" name="present_address" id="present_address" value="<?php if($present_address==""){ echo $present_address = "";} else{echo  $present_address;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Permanent Address</b></label>
												<input class="form-control" type="text" name="permanent_address" id="permanent_address" value="<?php if($permanent_address==""){ echo $permanent_address = "";} else{echo  $permanent_address;}?>">
												<p class="help-block"></p>
											</div>
										</div>
									</div>
									<div class="row">

											<!-- 	<input class="form-control" type="text" name="contact_no" id="contact_no" maxlength="10"> value="<?php if($contact_no==""){ echo $contact_no = "";} else{echo  $contact_no;}?>">
												<p class="help-block"></p>
											</div>
										</div> -->
										<div class="col-md-3">
											<div class="form-group">
												<label><b>Contact No.</b><code>*</code></label>
												<input type="text" class="form-control" name="contact_no" id="contact_no" value="<?php echo $contact_no; ?>" maxlength="10" size="10">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Emergency Contact Person</b></label>
												<input class="form-control" type="text" name="emergency_contact_person" id="emergency_contact_person" value="<?php if($emergency_contact_person==""){ echo $emergency_contact_person = "";} else{echo  $emergency_contact_person;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Contact Person Relation</b></label>
												<input class="form-control" type="text" name="contact_person_relation" id="contact_person_relation" value="<?php if($contact_person_relation==""){ echo $contact_person_relation = "";} else{echo  $contact_person_relation;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Blood Group</b></label>
												<input class="form-control" type="text" name="blood_group" id="blood_group" value="<?php if($blood_group==""){ echo $blood_group = "";} else{echo  $blood_group;}?>">
												<p class="help-block"></p>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Email</b></label>
												<input class="form-control" type="text" name="email" id="email" value="<?php if($email==""){ echo $email = "";} else{echo  $email;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Type Of Vehicle</b></label>
												<input class="form-control" type="text" name="type_of_vehicle" id="type_of_vehicle" value="<?php if($type_of_vehicle==""){ echo $type_of_vehicle = "";} else{echo  $type_of_vehicle;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Vehicle No.</b></label>
												<input class="form-control" type="text" name="vehicle_model_no" id="vehicle_model_no" value="<?php if($vehicle_model_no==""){ echo $vehicle_model_no = "";} else{echo  $vehicle_model_no;}?>">
												<p class="help-block"></p>
											</div>
										</div>
									</div>
									<hr>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Have ou any physical disability?</b></label>
												<select class="form-control" onchange="handlePhysicalDisabilityChange()" name="physical_disability" id="physical_disability">
													<option>Select Yes Or No</option>
													<option <?php if($physical_disability==1){ echo "selected"; } ?> value="1">Yes</option>
													<option <?php if($physical_disability==2){ echo "selected"; } ?> value="2">No</option>
												</select>
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3" id="major_illness_container">
											<div class="form-group">
												<label class="test"><b>Details of any major illness</b></label>
												<input class="form-control" value="<?php if($major_illness==""){ echo $major_illness = "";} else{echo  $major_illness;}?>" type="text" name="major_illness" id="major_illness">
												<p class="help-block"></p>
											</div>
										</div>		
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="portlet grey-cascade box">
												<div class="portlet-title">
													<div class="caption">
														Reference Person 1
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Name</b></label>
												<input class="form-control" type="text" name="rp1_name" id="rp1_name" value="<?php if($rp1_name==""){ echo $rp1_name = "";} else{echo  $rp1_name;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Relation</b></label>
												<input class="form-control" type="text" name="rp1_relation" id="rp1_relation" value="<?php if($rp1_relation==""){ echo $rp1_relation = "";} else{echo  $rp1_relation;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Occupation</b></label>
												<input class="form-control" type="text" name="rp1_occupation" id="rp1_occupation" value="<?php if($rp1_occupation==""){ echo $rp1_occupation = "";} else{echo  $rp1_occupation;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Contact No</b></label>
												<input class="form-control" type="text" name="rp1_contact_no" id="rp1_contact_no" maxlength="10" value="<?php if($rp1_contact_no==""){ echo $rp1_contact_no = "";} else{echo  $rp1_contact_no;}?>">
												<p class="help-block"></p>
											</div>
										</div>		
									</div>
									<div class="row">
										<div class="col-md-12">
											<div class="portlet grey-cascade box">
												<div class="portlet-title">
													<div class="caption">
														Reference Person 2
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Name</b></label>
												<input class="form-control" type="text" name="rp2_name" id="rp2_name" value="<?php if($rp2_name==""){ echo $rp2_name = "";} else{echo  $rp2_name;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Relation</b></label>
												<input class="form-control" type="text" name="rp2_relation" id="rp2_relation" value="<?php if($rp2_relation==""){ echo $rp2_relation = "";} else{echo  $rp2_relation;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Occupation</b></label>
												<input class="form-control" type="text" name="rp2_occupation" id="rp2_occupation" value="<?php if($rp2_occupation==""){ echo $rp2_occupation = "";} else{echo  $rp2_occupation;}?>">
												<p class="help-block"></p>
											</div>
										</div>
										<div class="col-md-3">
											<div class="form-group">
												<label class="test"><b>Contact No</b></label>
												<input class="form-control" type="text" name="rp2_contact_no" id="rp2_contact_no" maxlength="10" value="<?php if($rp2_contact_no==""){ echo $rp2_contact_no = "";} else{echo  $rp2_contact_no;}?>">
												<p class="help-block"></p>
											</div>
										</div>		
									</div>
									<!-- <div class="row">
										<div class="col-md-3"></div>
										<div class="col-md-3"></div>
										<div class="col-md-3"></div>
									</div> -->
								</div>
								<div class="form-actions">
									<button type="submit" name="submit" class="btn green">Submit</button>

									<button type="button" class="btn btn-default" onClick="window.location.href='employee_information_manage.php'">Back</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
			<div class="row">
				<?php
					if($_REQUEST['mode']=="edit"){
				?>
						<div class="row">
							<div class="col-md-12">					
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp; <b>Do you know anyone in this organization before your joining? If yes,  please mention following details</b>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-11">
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>Name of Employee</b><code>*</code></label>
															<input class="form-control" type="text" name="pjcd_employee_name" id="pjcd_employee_name" value="">
															<p class="help-block"></p>
														</div>
													</div>				
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>Designation</b></label>
															<input class="form-control" type="text" name="pjcd_designation" id="pjcd_designation" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>Department</b></label>
															<input class="form-control" type="text" name="pjcd_department" id="pjcd_department" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>Relationship</b><code>*</code></label>
															<input class="form-control" type="text" name="pjcd_relatioship" id="pjcd_relatioship" value="">
															<p class="help-block"></p>
														</div>
													</div>
												</div>
												<div class="col-md-1" style="margin-top: 25px;padding-left: 0px;">
													<button type="button" id="add_pre_joining_connections_details" name="add_pre_joining_connections_details" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="pre_joining_connections_details"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>						
						<div class="row">
							<div class="col-md-12">					
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp;<b>Family Background</b> &nbsp;<span style="font-size: 15px; font-style: italic">(please start with father, mother, brother, sister, wife, son, daughter, etc)</span>
																<!-- <p style="font-size: 15px; margin-top: 5px; margin-left: 30px;"></p> -->
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Name</b><code>*</code></label>
														<input class="form-control" type="text" name="family_mamber_name" id="family_mamber_name" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Relation</b><code>*</code></label>
														<input class="form-control" type="text" name="family_mamber_relation" id="family_mamber_relation" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Date Of Birth</b></label>
														<input class="form-control b-3 datepicker" type="text" name="family_mamber_dob" id="family_mamber_dob" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Education</b></label>
														<input class="form-control" type="text" name="family_mamber_education" id="family_mamber_education" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Profession</b></label>
														<input class="form-control" type="text" name="family_mamber_profession" id="family_mamber_profession" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2" style="margin-top: 25px;padding-left: 50px;">
													<button type="button" id="add_family_background" name="add_family_background" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="family_background_grid"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">					
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp; <b>Educational Details</b>
																<span style="font-size: 15px; font-style: italic;">(please start with SSC, HSC..)</span>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-11">
													<div class="col-md-2">
														<div class="form-group">
															<label class="test"><b>Education</b><code>*</code></label>
															<input class="form-control" type="text" name="sped_education" id="sped_education" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="test"><b>Passing Year</b><code>*</code></label>
															<select class="form-control" name="sped_year" id="sped_year">
															  	<option value="">Select a year</option>
															  	<script>
															    	var currentYear = new Date().getFullYear();
															    	var dropdown = document.getElementById("sped_year");
															    	for (var i = currentYear; i >= 1900; i--) {
															      		var option = document.createElement("option");
															      		option.text = i + "-" + (i + 1);
															      		option.value = i + "-" + (i + 1);
															      		dropdown.appendChild(option);
															    	}
															  	</script>
															</select>
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="test"><b>Percentage</b><code>*</code></label>
															<input class="form-control" type="text" name="sped_percentage" id="sped_percentage" maxlength="5" size="5" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-2" style="margin-top: -20px;">
														<div class="form-group">
															<label class="test"><b>School / College /<br> University Name</b><code>*</code></label>
															<input class="form-control" type="text" name="sped_place_name" id="sped_place_name" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="test"><b>City</b></label>
															<input class="form-control" type="text" name="sped_city" id="sped_city" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="test"><b>Special Remarks</b></label>
															<input class="form-control" type="text" name="sped_special_remarks" id="sped_special_remarks" value="">
															<p class="help-block"></p>
														</div>
													</div>
												</div>
												<div class="col-md-1" style="margin-top: 25px;padding-left: 10px;">
													<button type="button" id="add_educational_details" name="add_educational_details" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="educational_details_grid"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">					
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp; <b>At present, are you undergoing any Course? If yes,  please mention following details</b>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-11">
													<div class="col-md-3">
												    	<label class="test"><b>Course Duration</b><code>*</code></label>
													    <div class="input-group">
													        <input class="form-control pcl_datetimerange-picker-input" id="pcl_course_range" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
													        <span class="input-group-addon pcl_datetimerange-picker-btn">
													            <i class="fa fa-calendar"></i>
													        </span>
													    </div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>Name of Course</b><code>*</code></label>
															<input class="form-control" type="text" name="pcl_course_name" id="pcl_course_name" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>Institute</b><code>*</code></label>
															<input class="form-control" type="text" name="pcl_institute" id="pcl_institute" value="">
															<p class="help-block"></p>
														</div>
													</div>
													<div class="col-md-3">
														<div class="form-group">
															<label class="test"><b>City</b></label>
															<input class="form-control" type="text" name="pcl_city" id="pcl_city" value="">
															<p class="help-block"></p>
														</div>
													</div>													
												</div>
												<div class="col-md-1" style="margin-top: 25px;padding-left: 0px;">
													<button type="button" id="add_present_course_learning" name="add_present_course_learning" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="present_course_grid"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp; Computer Proficiency
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label class="test"><b>Course</b><code>*</code></label>
														<input class="form-control" type="text" name="cp_course" id="cp_course" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-6" style="margin-top: 1px;">
													<div class="form-group">
														<label class="test"><b>Rating</b></label>
														<div>
															<label><input type="radio" id="cp_rating" name="cp_rating" value="1"> Basic</label>
															<label><input type="radio" id="cp_rating" name="cp_rating" value="2"> Good</label>
															<label><input type="radio" id="cp_rating" name="cp_rating" value="3"> Excellent</label>
														</div>
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2" style="margin-top: 25px;padding-left: 10px;">
													<button type="button" id="add_computer_proficiency" name="add_computer_proficiency" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="computer_proficiency_grid"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp; Language Details
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-4">
													<div class="form-group">
														<label class="test"><b>Language Name</b></label>
														<input class="form-control" type="text" name="ld_language_name" id="ld_language_name" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-6" style="margin-top: 1px;">
													<div class="form-group">
														<label class="test"><b>Language Skills</b></label>
														<div>
															<label><input type="checkbox" id="ld_language_skills" name="ld_language_skills" value="speak"> Speak </label>
															<label><input type="checkbox" id="ld_language_skills" name="ld_language_skills" value="read"> read </label>
															<label><input type="checkbox" id="ld_language_skills" name="ld_language_skills" value="write"> Write </label>
														</div>
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2" style="margin-top: 25px;padding-left: 10px;">
													<button type="button" id="add_language_details" name="add_language_details" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="language_details_grid"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>							
						</div>
						<div class="row">
							<div class="col-md-12">					
								<div class="portlet box blue">
									<div class="portlet-body form">
										<div class="form-body">
											<div class="row">
												<div class="col-md-12">
													<div class="portlet grey-cascade box">
														<div class="portlet-title">
															<div class="caption">
																<i class="fa fa-list"></i> &nbsp; <b>Work Experience</b>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="row">
												<div class="col-md-2">
											    	<label class="test"><b>Service Period</b><code>*</code></label>
												    <div class="input-group">
												        <input class="form-control datetimerange-picker-input" id="we_date_range" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
												        <span class="input-group-addon datetimerange-picker-btn">
												            <i class="fa fa-calendar"></i>
												        </span>
												    </div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Company Name</b><code>*</code></label>
														<input class="form-control" type="text" name="we_company_name" id="we_company_name" value="">
														<p class="help-block"></p>
													</div>
												</div>				
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Location</b></label>
														<input class="form-control" type="text" name="we_location" id="we_location" value="">
														<p class="help-block"></p>
													</div>
												</div>
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Kind of Business</b></label>
														<input class="form-control" type="text" name="we_kind_of_business" id="we_kind_of_business" value="">
														<p class="help-block"></p>
													</div>
												</div>											
												<div class="col-md-2">
													<div class="form-group">
														<label class="test"><b>Designation</b></label>
														<input class="form-control" type="text" name="we_designation" id="we_designation" value="">
														<p class="help-block"></p>
													</div>
												</div>												
												<div class="col-md-2" style="margin-top: 25px;padding-left: 50px;">
													<button type="button" id="add_work_experience" name="add_work_experience" class="btn sbold blue-ebonyclay" style="padding: 6px 6px 6px 6px">Add</button>
												</div>
											</div>
											<div class="row">
												<div class="col-md-12">
													<div id="work_experience_grid"></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-12">
								<div class="form-actions">
									<button type="button" id="submit_data" name="submit" class="btn green">Submit</button>

									<button type="button" class="btn btn-default" onClick="window.location.href='employee_information_manage.php'">Back</button>
								</div>
							</div>
						</div>
				<?php
					}
				?>
			</div>
		</div>
	</div>
</div>
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script>
	// Add event listener to the button
  document.getElementById('submit_data').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default button behavior

    // Create a hidden input field
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'submit';
    hiddenInput.style.display = 'none';
    hiddenInput.name = 'submit'; // Add the name attribute for the form submission

    // Append the hidden input to the form
    document.getElementById('crud_form').appendChild(hiddenInput);

    // Simulate the click event on the hidden input field
    hiddenInput.click();
  });
</script>
<script type="text/javascript">
	$("#contact_no").numeric();
	$("#rp1_contact_no").numeric();
	$("#rp2_contact_no").numeric();
	$("#sped_percentage").numeric();
	$("#checkAll").change(function () {
	    $(".md-check").prop('checked', $(this).prop("checked"));
	});
	$('#date').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	startDate: "",
	  	clearBtn: false,
	  	// background-color: #fff;
	});
	$('#birth_date').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	maxDate:"appCtrl.maxDate",
	  	startDate: "",
	  	clearBtn: false
	});
	$('#family_mamber_dob').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	maxDate:"appCtrl.maxDate",
	  	startDate: "",
	  	clearBtn: false
	});
	$('#we_from_date').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	startDate: "",
	  	clearBtn: false
	});
	$('#we_to_date').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	startDate: "",
	  	clearBtn: false
	});
	$('#pcl_start_date').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	startDate: "",
	  	clearBtn: false
	});
	$('#pcl_end_date').datepicker({
	  	dateFormat: 'dd/mm/yy',
	  	orientation: "auto",
	  	startDate: "",
	  	clearBtn: false
	});
</script>
<script type="text/javascript">
	$('#start_time').timepicker({timepicker: true, autoclose: true });
	$('#end_time').timepicker({timepicker: true, autoclose: true });
</script>

<script type="text/javascript">

	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) { $(this).parent().removeClass("has-error"); $(this).parent().find('p.help-block').html(""); } }); 

	var mode = "<?=$_REQUEST['mode']?>";

	function handlePhysicalDisabilityChange() {
	    var selectedValue = $("#physical_disability").val();
	    var majorIllnessContainer = document.getElementById('major_illness_container');
	    var majorIllnessInput = document.getElementById('major_illness');

	    if (selectedValue == 1) {
	      majorIllnessContainer.style.display = 'block';
	      majorIllnessInput.disabled = false;
	    } else {
	      majorIllnessContainer.style.display = 'none';
	      majorIllnessInput.disabled = true;
	    }
	  }

	function check_form(){
		$(".form-body").children().removeClass("has-error");
		var isValid=true;	
		
		if($("#post_applied").val()=="" || $("#post_applied").val().split(" ").join("")==""){
				
			vd=aj.error('post_applied',"Please Add Post Applied.","add_error");
			isValid=false;
		}
		// alert($("#old_image_path").val());
		if($("#image_path").val()=="" || $("#image_path").val().split(" ").join("")==""){
			if(mode == "edit"){
				if($("#old_image_path").val()=="" || $("#old_image_path").val().split(" ").join("")==""){
					vd=aj.error('image_path',"Please Select Image.","add_error");
					isValid=false;
				}
			}else{	
				vd=aj.error('image_path',"Please Select Image.","add_error");
				isValid=false;
			}
		}
		if($("#first_name").val()=="" || $("#first_name").val().split(" ").join("")==""){
				
			vd=aj.error('first_name',"Please Add Name.","add_error");
			isValid=false;
		}
		
		if($("#middle_name").val()=="" || $("#middle_name").val().split(" ").join("")==""){
				
			vd=aj.error('middle_name',"Please Add Middle Name.","add_error");
			isValid=false;
		}
		
		if($("#surname").val()=="" || $("#surname").val().split(" ").join("")==""){
				
			vd=aj.error('surname',"Please Add Surname.","add_error");
			isValid=false;
		}

		if($("#gender").val()=="" || $("#gender").val().split(" ").join("")==""){
				
			vd=aj.error('gender',"Please Select Gender.","add_error");
			isValid=false;
		}
		
		if($("#present_address").val()=="" || $("#present_address").val().split(" ").join("")==""){
			vd=aj.error('present_address',"Please Select Present Address.","add_error");
			isValid=false;
		}

		if($("#contact_no").val()=="" || $("#contact_no").val().split(" ").join("")==""){
				
			vd=aj.error('contact_no',"Please Enter Contact No.","add_error");
			isValid=false;
		}
		if ($("#email").val() != "") {
			if (/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test($("#email").val())) {

			} else {
				//alert("email");
				aj.error('email', 'Please enter valid email!!', 'add_error');
				isValid = false;
			}
		}
		// alert "dsfsdaf"
		if(isValid)
		{
			return true;
		}
		else
		{
			return false;
		}
		
	}

	function formatDate(dateString) {
	    var parts = dateString.split('/');
	    return parts[2] + '-' + parts[1] + '-' + parts[0];
	}

// ---------------------------------- For Pre Joining Connections Details----------------------

	$("#add_pre_joining_connections_details").on("click", function () {
		var pjcd_employee_name 	= $("#pjcd_employee_name").val();
		var pjcd_designation 		= $("#pjcd_designation").val();
		var pjcd_department = $("#pjcd_department").val();
		var pjcd_relatioship 	= $("#pjcd_relatioship").val();
		var sales_executive_id = "<?=$id?>";
		var isValid=true;
		if(pjcd_employee_name=="" || pjcd_employee_name.split(" ").join("")==""){
				
			vd=aj.error('pjcd_employee_name',"Please Employee Name.","add_error");
			isValid=false;
		}
		if(pjcd_relatioship=="" || pjcd_relatioship.split(" ").join("")==""){
				
			vd=aj.error('pjcd_relatioship',"Please Add Relatioship.","add_error");
			isValid=false;
		}
		if(isValid == true){
			$.ajax({
			    type: "POST",
			    url: "add_employee_information_extra.php",
			    data: {
			      	sales_executive_id: sales_executive_id,
			      	pjcd_employee_name:pjcd_employee_name,
			      	pjcd_designation:pjcd_designation,
			      	pjcd_department:pjcd_department,
			      	pjcd_relatioship:pjcd_relatioship,
			      	mode: "add_pre_joining_connections_details",
			    },
			    cache: false,
			    success: function (json) {
			      	json = $.parseJSON(json);
			      	msg = json.ack_msg;
			      	if (json.ack == 1) {
			      		we_from_date = "";
						we_to_date = "";
			        	$("#pjcd_employee_name").val("");
			        	$("#pjcd_designation").val("");
			        	$("#pjcd_department").val("");
			        	$("#pjcd_relatioship").val("");		
			        	toastr.success(msg, 'Success!!');
				        get_pre_joining_connections_details();	
			      	} else {
			        	toastr.error(msg, 'Error!!');
			      	}
			    },
		  	});
		}
	  	
	});


	function del_pre_joining_connections_details(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_pre_joining_connections_details',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_pre_joining_connections_details();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}


	function get_pre_joining_connections_details()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_pre_joining_connections_details",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#pre_joining_connections_details").html(json);
			}
		});
	}

// ---------------------------------- For Pre Joining Connections Details----------------------


// ---------------------------------- For Family Background-----------------------

	$("#add_family_background").on("click", function () {
		var family_mamber_name = $("#family_mamber_name").val();
		var family_mamber_relation = $("#family_mamber_relation").val();
		var family_mamber_dob = $("#family_mamber_dob").val();
		var family_mamber_education = $("#family_mamber_education").val();
		var family_mamber_profession = $("#family_mamber_profession").val();
		family_mamber_dob 			= formatDate(family_mamber_dob);
		var sales_executive_id = "<?=$id?>";
		isValid=true;
		if(family_mamber_name=="" || family_mamber_name.split(" ").join("")==""){
				
			vd=aj.error('family_mamber_name',"Please Add Name.","add_error");
			isValid=false;
		}
		if(family_mamber_relation=="" || family_mamber_relation.split(" ").join("")==""){
				
			vd=aj.error('family_mamber_relation',"Please Add Relation.","add_error");
			isValid=false;
		}
		if(isValid == true){
		  	$.ajax({
			    type: "POST",
			    url: "add_employee_information_extra.php",
			    data: {
			      	sales_executive_id: sales_executive_id,
			      	family_mamber_name:family_mamber_name,
			      	family_mamber_relation: family_mamber_relation,
			      	family_mamber_dob: family_mamber_dob,
			      	family_mamber_education: family_mamber_education,
			      	family_mamber_profession:family_mamber_profession,		      
			      	mode: "add_family_background",
			    },
			    cache: false,
			    success: function (json) {
			      	json = $.parseJSON(json);
			      	msg = json.ack_msg;
			      	if (json.ack == 1) {
			        	$("#family_mamber_name").val("");
			        	$("#family_mamber_relation").val("");
			        	$("#family_mamber_dob").val("");
			        	$("#family_mamber_education").val("");
			        	$("#family_mamber_profession").val("");
			        	toastr.success(msg, 'Success!!');
				        get_sales_person_family_background();	
			      	} else {
			        	toastr.error(msg, 'Error!!');
			      	}
			    },
		  	});
		}
	});

	function del_family_background(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_family_background',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_sales_person_family_background();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}
	
	function get_sales_person_family_background()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_family_background",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#family_background_grid").html(json);
			}
		});
	}

// ---------------------------------- For Family Background-----------------------


//--------------------------------- For Educational Details ----------------------

	$("#add_educational_details").on("click", function () {
		var sped_education = $("#sped_education").val();
		var sped_year = $("#sped_year").val();
		var sped_percentage = $("#sped_percentage").val();
		var sped_place_name = $("#sped_place_name").val();
		var sped_city = $("#sped_city").val();
		var sped_special_remarks = $("#sped_special_remarks").val();
		var sales_executive_id = "<?=$id?>";
		isValid=true;
		if(sped_education=="" || sped_education.split(" ").join("")==""){
				
			vd=aj.error('sped_education',"Please Add Education.","add_error");
			isValid=false;
		}
		if(sped_year=="" || sped_year.split(" ").join("")==""){
				
			vd=aj.error('sped_year',"Please Select Passing Year.","add_error");
			isValid=false;
		}
		if(sped_percentage=="" || sped_percentage.split(" ").join("")==""){
				
			vd=aj.error('sped_percentage',"Please Add Percentage.","add_error");
			isValid=false;
		}
		if(sped_place_name=="" || sped_place_name.split(" ").join("")==""){
				
			vd=aj.error('sped_place_name',"Please Add Place Name.","add_error");
			isValid=false;
		}
		if(isValid == true){
		  	$.ajax({
		    type: "POST",
		    url: "add_employee_information_extra.php",
		    data: {
		      	sales_executive_id: sales_executive_id,
		      	sped_education:sped_education,
		      	sped_year: sped_year,
		      	sped_percentage: sped_percentage,
		      	sped_place_name: sped_place_name,
		      	sped_city:sped_city,		      
		      	sped_special_remarks:sped_special_remarks,		      
		      	mode: "add_educational_details",
		    },
		    cache: false,
		    success: function (json) {
		      	json = $.parseJSON(json);
		      	msg = json.ack_msg;
		      	if (json.ack == 1) {
		        	$("#sped_education").val("");
		        	$("#sped_year").select2("destroy");
		        	$("#sped_year").val("");
		        	$("#sped_year").select2();
		        	$("#sped_percentage").val("");
		        	$("#sped_place_name").val("");
		        	$("#sped_city").val("");
		        	$("#sped_special_remarks").val("");
		        	toastr.success(msg, 'Success!!');
			        get_sales_person_educational_details();	
		      	} else {
		        	toastr.error(msg, 'Error!!');
		      	}
		    },
	  	});
		}
	});

	function del_educational_details(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_educational_details',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_sales_person_educational_details();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}
	
	function get_sales_person_educational_details()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_educational_details",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#educational_details_grid").html(json);
			}
		});
	}

//--------------------------------- For Educational Details ----------------------


//--------------------------------- For Present Course Learning ----------------------

	$("#add_present_course_learning").on("click", function () {
		var pcl_course_name = $("#pcl_course_name").val();
		var pcl_institute = $("#pcl_institute").val();
		var pcl_city = $("#pcl_city").val();
		var pcl_course_range = $("#pcl_course_range").val();
		var sales_executive_id = "<?=$id?>";
		isValid=true;
		if(pcl_course_name=="" || pcl_course_name.split(" ").join("")==""){
				
			vd=aj.error('pcl_course_name',"Please Add Course Name.","add_error");
			isValid=false;
		}
		if(pcl_institute=="" || pcl_institute.split(" ").join("")==""){
				
			vd=aj.error('pcl_institute',"Please Add Institute.","add_error");
			isValid=false;
		}
		if(pcl_course_range != "" && pcl_course_range != undefined && pcl_course_range != null ){
			var pcl_dates = pcl_course_range.split(" to ");
			var pcl_start_date = pcl_dates[0].trim();
			var pcl_end_date = pcl_dates[1].trim();		
		}
		if(isValid == true){
		  	$.ajax({
		    type: "POST",
		    url: "add_employee_information_extra.php",
		    data: {
		      	sales_executive_id: sales_executive_id,
		      	pcl_course_name:pcl_course_name,
		      	pcl_institute: pcl_institute,
		      	pcl_city: pcl_city,
		      	pcl_start_date: pcl_start_date,
		      	pcl_end_date:pcl_end_date,		      	      
		      	mode: "add_present_course_learning",
		    },
		    cache: false,
		    success: function (json) {
		      	json = $.parseJSON(json);
		      	msg = json.ack_msg;
		      	if (json.ack == 1) {
		        	$("#pcl_course_name").val("");
		        	$("#pcl_institute").val("");
		        	$("#pcl_city").val("");
		        	$("#pcl_course_range").val("");
		        	pcl_start_date = "";
		        	pcl_end_date = "";
		        	toastr.success(msg, 'Success!!');
			        get_sales_person_add_present_course_learning();	
		      	} else {
		        	toastr.error(msg, 'Error!!');
		      	}
		    },
	  	});
		}
	});

	function del_present_course_learning(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_present_course_learning',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_sales_person_add_present_course_learning();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}
	
	function get_sales_person_add_present_course_learning()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_present_course_learning",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#present_course_grid").html(json);
			}
		});
	}

	$(".pcl_datetimerange-picker-btn").on("click", function() {
	    $(".pcl_datetimerange-picker-input", $(this).closest(".pcl_date")).focus();
	});

	$(".pcl_datetimerange-picker-input").daterangepicker({
	    "format": "dd-mm-yy",
	    autoUpdateInput: false,
	    timePicker: false,
	});

	$('.pcl_datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
	    $(".pcl_datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
	});

	$('#pcl_start_date').datepicker({ 
	    datepicker: true, 
	    autoclose: true, 
	    dateFormat: 'dd-mm-yy',
	    "setDate": new Date(),
	});

	$("#pcl_start_date").datepicker({
	    minDate: 0,
	    onSelect: function(date) {
	        $("#pcl_end_date").datepicker('option', 'minDate', date);
	    }
	});

	$('#pcl_end_date').datepicker({ 
	    datepicker: true, 
	    autoclose: true, 
	    dateFormat: 'dd-mm-yy',
	    "setDate": new Date(),
	});

	$("#pcl_end_date").datepicker({});


//--------------------------------- For Present Course Learning ----------------------


//------------------ For Computer Proficiency ----------------------

	$("#add_computer_proficiency").on("click", function () {
  		var sales_executive_id = "<?=$id?>";
  		var cp_course = $("#cp_course").val();
  		var cp_rating = $('input[name="cp_rating"]:checked').val();
	  	$("input[name='cp_language_skills']:checked").each(function() {
	    	cp_language_skills.push($(this).val());
	  	});
	  	isValid=true;
		if(cp_course=="" || cp_course.split(" ").join("")==""){
				
			vd=aj.error('cp_course',"Please Add Course Name.","add_error");
			isValid=false;
		}
		if(isValid == true){
		  	$.ajax({
		    	type: "POST",
		    	url: "add_employee_information_extra.php",
		    	data: {
		      		sales_executive_id: sales_executive_id,
		      		cp_course: cp_course,
		      		cp_rating: cp_rating,
		      		mode: "add_computer_proficiency",
		    	},
		    	cache: false,
		    	success: function (json) {
	  				json = $.parseJSON(json);
				  	msg = json.ack_msg;
				  	if (json.ack == 1) {
				    	$("#cp_course").val("");
				    	$('input[name="cp_rating"]').prop("checked", false);
				   	 	$("span.checked").removeClass('checked');
				    	toastr.success(msg, 'Success!!');
				    	get_sales_person_get_computer_proficiency();
				  	} else {
				    	toastr.error(msg, 'Error!!');
				  	}
				},
		  	});
		}
	});

	function del_computer_proficiency(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_computer_proficiency',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_sales_person_get_computer_proficiency();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}
	
	function get_sales_person_get_computer_proficiency()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_computer_proficiency",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#computer_proficiency_grid").html(json);
			}
		});
	}

//------------------ For Computer Proficiency ----------------------

//------------------ For Language Details ----------------------

	$("#add_language_details").on("click", function () {
  		var sales_executive_id = "<?=$id?>";
  		var ld_language_name = $("#ld_language_name").val();
  		var ld_language_skills = [];
	  	$("input[name='ld_language_skills']:checked").each(function() {
	    	ld_language_skills.push($(this).val());
	  	});
	  	isValid=true;
		if(ld_language_name=="" || ld_language_name.split(" ").join("")==""){
				
			vd=aj.error('ld_language_name',"Please Add Course Name.","add_error");
			isValid=false;
		}
		if(isValid == true){
		  	$.ajax({
		    	type: "POST",
		    	url: "add_employee_information_extra.php",
		    	data: {
		      		sales_executive_id: sales_executive_id,
		      		ld_language_name: ld_language_name,
		      		ld_language_skills: ld_language_skills,
		      		mode: "add_language_details",
		    	},
		    	cache: false,
		    	success: function (json) {
	  				json = $.parseJSON(json);
				  	msg = json.ack_msg;
				  	if (json.ack == 1) {
				    	$("#ld_language_name").val("");
				    	$("input[name='ld_language_skills']").prop("checked", false);
				   	 	$("span.checked").removeClass('checked');
				    	toastr.success(msg, 'Success!!');
				    	get_sales_person_get_language_details();
				  	} else {
				    	toastr.error(msg, 'Error!!');
				  	}
				},
		  	});
		}
	});

	function del_language_details(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_language_details',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_sales_person_get_language_details();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}
	
	function get_sales_person_get_language_details()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_language_details",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#language_details_grid").html(json);
			}
		});
	}

//------------------ For Language Details ----------------------
	
// ---------------------------------- For Wark Experience----------------------

	$("#add_work_experience").on("click", function () {
		var we_date_range= $("#we_date_range").val();
		var dates = we_date_range.split(" to ");
		var we_company_name 	= $("#we_company_name").val();
		var we_location 		= $("#we_location").val();
		var we_kind_of_business = $("#we_kind_of_business").val();
		var we_designation 		= $("#we_designation").val();
		var sales_executive_id = "<?=$id?>";
		var isValid=true;
		if(we_company_name=="" || we_company_name.split(" ").join("")==""){
				
			vd=aj.error('we_company_name',"Please Add Company Name.","add_error");
			isValid=false;
		}
		if(we_date_range!=""){
			var we_from_date = dates[0].trim();
			var we_to_date = dates[1].trim();
			// we_from_date 			= formatDate(we_from_date);
			// we_to_date 			= formatDate(we_to_date);			
		}else{
			vd=aj.error('we_date_range',"Please Add Employee Name.","add_error");
			isValid=false;
		}
		if(we_from_date > we_to_date){
			toastr.error("The selected 'From Date' date is later than the 'to Date' date. ", 'Error!!');
			vd=aj.error('we_date_range',"","add_error");
			isValid=false;
		}
		if(isValid == true){
			$.ajax({
			    type: "POST",
			    url: "add_employee_information_extra.php",
			    data: {
			      	sales_executive_id: sales_executive_id,
			      	company_name:we_company_name,
			      	location: we_location,
			      	kind_of_business: we_kind_of_business,
			      	designation:we_designation,
			      	from_date:we_from_date,
			      	to_date:we_to_date,
			      	mode: "add_work_experience",
			    },
			    cache: false,
			    success: function (json) {
			      	json = $.parseJSON(json);
			      	msg = json.ack_msg;
			      	if (json.ack == 1) {
			      		we_from_date = "";
						we_to_date = "";
			        	$("#we_company_name").val("");
			        	$("#we_location").val("");
			        	$("#we_kind_of_business").val("");			        	
			        	$("#we_designation").val("");
			        	$("#we_date_range").val("");			
			        	toastr.success(msg, 'Success!!');
				        get_woek_experience();	
			      	} else {
			        	toastr.error(msg, 'Error!!');
			      	}
			    },
		  	});
		}
	  	
	});


	function del_work_experience(sales_executive_id)
	{
		var r = confirm("Are you sure you want to delete?");
		if(r)
		{
			$.ajax({
				type: "POST",
				url: "add_employee_information_extra.php",
				data: {
					mode:'delete_work_experience',
					sales_executive_id:sales_executive_id,
				},
				cache: false,
				success: function(json)
				{
					json=$.parseJSON(json);
					msg=json.ack_msg;
					if(json.ack==1)
					{						
						toastr.success(msg,"Success!!");
						get_woek_experience();	
					}
					else
					{
						toastr.error(msg, 'Error!!')
					}
				}
			});
		}
	}


	function get_woek_experience()
	{	
		var sales_executive_id = "<?=$id?>";		
		$.ajax({
			type: "POST",
			url: "get_employee_information_extra.php",
			data: {
				sales_executive_id:sales_executive_id,
				mode: "get_work_experience",
			},
			cache: false,
			beforeSend: function() {
				
			},
			success: function(json)
			{
				$("#work_experience_grid").html(json);
			}
		});
	}

    $(".datetimerange-picker-btn").on("click", function() {
        $(".datetimerange-picker-input", $(this).closest(".date")).focus();
    });
    $(".datetimerange-picker-input").daterangepicker({
        "format": "dd-mm-yy",
        autoUpdateInput: false,
        timePicker: false,
    });
    $('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
        $(".datetimerange-picker-input").val(picker.startDate.format('DD-MM-YYYY') + " to " + picker.endDate.format('DD-MM-YYYY'));
    });
	$('#start_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy',"setDate": new Date(), });
	//$("#start_date").datepicker("setDate", new Date());

	$("#start_date").datepicker({
	  minDate: 0,

	  onSelect: function(date) {
	    $("#end_date").datepicker('option', 'minDate', date);
	  }
	});
   
   	$('#end_date').datepicker({  datepicker: true, autoclose: true, dateFormat: 'dd-mm-yy',"setDate": new Date(), });
	$("#end_date").datepicker({});
// ---------------------------------- For Wark Experience----------------------

	// function AddInformation() {
	// 	    if (check_form()) { // If check_form() returns true
	// 	        var formData = new FormData();
	// 	        formData.append('date', $("#date").val());
	// 	        formData.append('post_applied', $("#post_applied").val());
	// 	        formData.append('reference', $("#reference").val());
	// 	        formData.append('first_name', $("#first_name").val());
	// 	        formData.append('middle_name', $("#middle_name").val());
	// 	        formData.append('surname', $("#surname").val());
	// 	        formData.append('birth_date', $("#birth_date").val());
	// 	        formData.append('gender', $("#gender").val());
	// 	        formData.append('religion', $("#religion").val());
	// 	        formData.append('cast', $("#cast").val());
	// 	        formData.append('mother_tongue', $("#mother_tongue").val());
	// 	        formData.append('marital_status', $("#marital_status").val());
	// 	        formData.append('plaece_of_birth', $("#plaece_of_birth").val());
	// 	        formData.append('present_address', $("#present_address").val());
	// 	        formData.append('permanent_address', $("#permanent_address").val());
	// 	        formData.append('contact_no', $("#contact_no").val());
	// 	        formData.append('emergency_contact_person', $("#emergency_contact_person").val());
	// 	        formData.append('contact_person_relation', $("#contact_person_relation").val());
	// 	        formData.append('blood_group', $("#blood_group").val());
	// 	        formData.append('email', $("#email").val());
	// 	        formData.append('type_of_vehicle', $("#type_of_vehicle").val());
	// 	        formData.append('vehicle_model_no', $("#vehicle_model_no").val());
	// 	        formData.append('physical_disability', $("#physical_disability").val());
	// 	        formData.append('major_illness', $("#major_illness").val());
	// 	        formData.append('rp1_name', $("#rp1_name").val());
	// 	        formData.append('rp1_relation', $("#rp1_relation").val());
	// 	        formData.append('rp1_occupation', $("#rp1_occupation").val());
	// 	        formData.append('rp1_contact_no', $("#rp1_contact_no").val());
	// 	        formData.append('rp2_name', $("#rp2_name").val());
	// 	        formData.append('rp2_relation', $("#rp2_relation").val());
	// 	        formData.append('rp2_occupation', $("#rp2_occupation").val());
	// 	        formData.append('rp2_contact_no', $("#rp2_contact_no").val());

	// 	        var imageFiles = $("#image_path")[0].files; // Get the selected image files
	// 	        for (var i = 0; i < imageFiles.length; i++) {
	// 	            formData.append('image_path[]', imageFiles[i]);
	// 	        }

	// 	        $.ajax({
	// 	            url: "add_employee_information_get_ajax.php",
	// 	            type: "POST",
	// 	            data: formData,
	// 	            processData: false,
	// 	            contentType: false,
	// 	            beforeSend: function() {
	// 	                // Show loading or any other indication
	// 	            },
	// 	            success: function(success) {
	// 	                success = $.parseJSON(success);
	// 	                msg = success.ack_msg;
	// 	                if (success.ack == 1) {
	// 	                    toastr.success(msg, "Success!!");
	// 	                    // Clear input values
	// 	                    $('#date').val('');
	// 	                    $('#post_applied').val('');
	// 	                    $('#reference').val('');
	// 	                    $('#first_name').val('');
	// 	                    $('#middle_name').val('');
	// 	                    $('#surname').val('');
	// 	                    $('#birth_date').val('');
	// 	                    $('#gender').val('');
	// 	                    $('#religion').val('');
	// 	                    $('#cast').val('');
	// 	                    $('#mother_tongue').val('');
	// 	                    $('#marital_status').val('');
	// 	                    $('#plaece_of_birth').val('');
	// 	                    $('#present_address').val('');
	// 	                    $('#permanent_address').val('');
	// 	                    $('#contact_no').val('');
	// 	                    $('#emergency_contact_person').val('');
	// 	                    $('#contact_person_relation').val('');
	// 	                    $('#blood_group').val('');
	// 	                    $('#email').val('');
	// 	                    $('#type_of_vehicle').val('');
	// 	                    $('#vehicle_model_no').val('');
	// 	                    $('#physical_disability').val('');
	// 	                    $('#major_illness').val('');
	// 	                    $('#rp1_name').val('');
	// 	                    $('#rp1_relation').val('');
	// 	                    $('#rp1_occupation').val('');
	// 	                    $('#rp1_contact_no').val('');
	// 	                    $('#rp2_name').val('');
	// 	                    $('#rp2_relation').val('');
	// 	                    $('#rp2_occupation').val('');
	// 	                    $('#rp2_contact_no').val('');
	// 	                } else {
	// 	                    toastr.error(msg, 'Error!!');
	// 	                }
	// 	            },
	// 	            error: function() {
	// 	                toastr.error("Error");
	// 	            }
	// 	        });
	// 	    } else {
	// 	        toastr.error("Form validation failed. Please check the entered values.", 'Error!!');
	// 	    }
	// 	}



</script>
<script type="text/javascript">
	$(function(){
		aj.imageHolder($("input[id=image_path]"),"","",
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

<script type="text/javascript">
	$("#add_item").click(function(){
		var sales_executive_id = $("#sales_executive_id").val();
		var start_date	=$('#start_date').val();
		var start_time 	= $("#start_time").val();
		var end_date    = $("#end_date").val();
		var end_time 	= $("#end_time").val();

		if(sales_executive_id!=0 && start_date!='' && start_time!='' &&  end_date!=""  && end_time!="")
		{
			createItem(sales_executive_id,start_date,start_time,end_date,end_time);
		}
		else
		{
			toastr.error("Please Select Sales Executive,Date And Time");
		}
	})

	function createItem(sales_executive_id,start_date,start_time,end_date,end_time)
	{
		// var sd=start_date.replace(" ","");
		//  sd=sd.replace(":","");
		// var st=start_time.replace(" ","");
		//  st=st.replace(":","");
		// var et=end_time.replace(" ","");
		//  et=et.replace(":","");
		
		// var duplicate=$("#po_items_list").find("tbody").find("tr.leave_"+sd+st+et).length;
		// if(duplicate!=1)
		// {
			// var timefrom = new Date();
			// temp = start_time.val().split(":");
			// timefrom.setHours((parseInt(temp[0]) - 1 + 24) % 24);
			// timefrom.setMinutes(parseInt(temp[1]));

			// var timeto = new Date();
			// temp = $('#timeto').val().split(":");
			// timeto.setHours((parseInt(temp[0]) - 1 + 24) % 24);
			// timeto.setMinutes(parseInt(temp[1]));

			//if (timeto < timefrom) 
			    // alert('start time should be smaller');
			$.ajax({
				url:"leave_ajax_function.php",
				type:"POST",
				data:{
					sales_executive_id:sales_executive_id,
					start_date:start_date,
					start_time:start_time,
					end_date:end_date,
					end_time:end_time,
					m:"create_item",
					
				},
				beforeSend:function(){
					// $("#loading-modal").modal('show');
					$('.preloader').fadeIn('slow');
				},
				success:function(result){
					if(result!="")
					{
						var html=result;
						$("#po_items_list").find('tbody').append(html);
						// $("#loading-modal").modal('hide');
						$('.preloader').fadeOut('slow');
						// $("#start_date").val("");
						// $("#start_time").val("");
						// $("#end_time").val("");
					}
				},
				error:function(){
						toastr.error("We could not process right now try again!!","Error");
					}
				})
		// }
		// else
		// {
		// 	toastr.error("Record already exist Please Remove First to add");
		// }
	}

	function maintainDatatable()
	{
		if($("#po_items_list").find("tbody").find("tr").length>=1)
		{
			$(".no-item").hide();
		}
		else
		{
			$(".no-item").show();
		}
	}

 $(document).ready(function(){
 	$("#po_items_list").on('click','.delete',function(){	
       $(this).closest('tr').remove();
	   // recalculateFinalValues();
    });
    get_pre_joining_connections_details();
    get_sales_person_family_background();
    get_sales_person_educational_details();
    get_sales_person_add_present_course_learning();
    get_sales_person_get_computer_proficiency();
    get_sales_person_get_language_details();
    get_woek_experience();

    handlePhysicalDisabilityChange()
});
</script>

</body>
</html>