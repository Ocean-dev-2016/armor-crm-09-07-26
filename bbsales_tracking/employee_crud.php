<?php
$page_id=554;$page_slug='page_employee';

$etable 	= "employee";
$ctable 	= "emp_personal_info";
$ctable1 	= "Employee";
$main_page 	= $ctable;
$page 		= $ctable."_add";
$page_title = ucwords($_REQUEST['mode'])." ".$ctable1;
$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>$etable."_manage.php","title"=>"Manage ".$ctable1),array("link"=>$etable."_crud.php","title"=>"Add/Edit ".$ctable1));
include("connect.php");
include("../include/employee.class.php");

$objEmployee= new Employee();

//$rtid		= "";
$emp_id			= "";
$emp_code			= "";
$first_name			= "";
$middle_name		= "";
$last_name			= "";
$phone			= "";
$other_contact		= "";
$perment_address	= "";
$residential_address= "";
$birth_date			= "";
$blood_group		= "";
$remark 			= "";
$identification_proof= "";
$proof_document	= "";
$image				="";
$isActive	= 0;
/////company inforamtion
$designation	="";
$department		="";
$joining_date	="";
$shift			="";
$account_number ="";
$bank_name		="";
/////salary inforamtion
$year		="";
$basic		="";
$hra		="";
$medical	="";
$conv		="";
$wash		="";
$edu		="";
$lt			="";
$spe		="";
$gross		="";
$it			="";
$pt			="";
$pf			="";
$net_payable="";
$remark		="";

//$unique="S/".FINANCIAL_YEAR."/".(intval($db->rp_getValue($ctable,"max(`id`)","1=1"))+1);
	;

if(isset($_REQUEST['submit'])){
	
	$detail['emp_code']					= $db->clean($_REQUEST['emp_code']);
	$detail['first_name']				= $db->clean($_REQUEST['first_name']);
	$detail['middle_name']				= $db->clean($_REQUEST['middle_name']);
	$detail['last_name']				= $db->clean($_REQUEST['last_name']);
	$detail['phone']					= $db->clean($_REQUEST['phone']);
	$detail['other_contact']			= $db->clean($_REQUEST['other_contact']);
	$detail['perment_address']			= $db->clean($_REQUEST['perment_address']);
	$detail['residential_address']		= $db->clean($_REQUEST['residential_address']);
	$detail['birth_date']				=date_format(date_create($_REQUEST["birth_date"]),"Y-m-d");
	$detail['blood_group']				= $db->clean($_REQUEST['blood_group']);
	$detail['remark']					= $db->clean($_REQUEST['remark']);
	$detail['identification_proof']		= $db->clean($_REQUEST['identification_proof']);
	$image								= $_REQUEST['file'];
	$proof_document						= $_REQUEST['file_document'];
	$detail['isActive']					= 1;
	
	if(isset($_FILES['file']))
	{
		$reply=$objEmployee->updateEmpImage($_FILES,$id);
		
	}
	if(isset($_FILES['file_document']))
	{
		$reply=$objEmployee->UpdateEmpDocument($_FILES,$id);
		
	}

	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="add" && isset($_FILES['file'])){
		
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply=$objEmployee->InsertEmpPersonalInfo($detail,$_FILES);
			if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);
			$db->rp_location($etable."_manage.php?msg=inserted");
			}
			else{
				 $db->addErrorMessage($reply['ack_msg']);				 
			}
		
	}else if(isset($_REQUEST['mode']) && $_REQUEST['mode']=="edit"){
	
		
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=update_access_denied');
		}
		$reply=$objEmployee->UpdateEmpPersonalInfo($detail);
		if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);
		    $db->rp_location($etable."_manage.php?msg=updated");
		}
		else{
				$db->addErrorMessage($reply['ack_msg']);		
			}
	}
}
//// Company Information

if(isset($_REQUEST['submitCompany'])){
	
	$detail['emp_id']				=$db->clean($_REQUEST['id']);
	$detail['designation']			= $db->clean($_REQUEST['designation']);
	$detail['department']			= $db->clean($_REQUEST['department']);
	$detail['joining_date']			= $db->clean($_REQUEST['joining_date']);
	$detail['shift']				= $db->clean($_REQUEST['shift']);
	$detail['account_number']		= $db->clean($_REQUEST['account_number']);
	$detail['bank_name']			= $db->clean($_REQUEST['bank_name']);
	$detail['isActive']				= 1;
	$check=$objEmployee->isCompanyInfoAvailable($detail);
	if($check['ack']==1)
	{
		if($rights['update_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=update_access_denied');
		}
		$reply=$objEmployee->UpdateEmpCompanyInfo($detail);
		if($reply['ack']==1){
				$db->addSuccessMessage($reply['ack_msg']);			
		}
		else{
			$db->addErrorMessage($reply['ack_msg']);		
		}
	}
	else
	{
		if($rights['insert_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=insert_access_denied');
		}
		$reply=$objEmployee->InsertEmpCompanyInfo($detail);
		if($reply['ack']==1){
			$db->addSuccessMessage($reply['ack_msg']);		
		}
		else{
			 $db->addErrorMessage($reply['ack_msg']);				 
		}
	}
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="edit"){
	
	if($rights['update_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=update_access_denied');
	}
	$where = " id='".$_REQUEST['id']."' AND isDelete=0";
	$ctable_r = $db->rp_getData($ctable,"*",$where);
	$detail['id']=$_REQUEST['id'];	
	$reply=$objEmployee->getEmpPersonalInfo($detail);
	$company_info=$objEmployee->getEmpCompanyInfo($detail);
	if($reply['ack']==1){
		
		$emp_id=$_REQUEST['id'];
		$result=$reply['result'];
		
		extract($result);
	}
	    $page_title="Edit Employee - ".ucfirst($first_name)." ".ucfirst($last_name);
	
	if($company_info['ack']==1){
		
		
		$emp_id=$_REQUEST['id'];
		$company_info=$company_info['result'];
		extract($company_info);	
	
	}
	
		
}
if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="delete"){
	
	
	if($rights['delete_flag']!=1)
	{
		$db->rp_location('access_denied.php?msg=delete_access_denied');
	}
	$detail['id']=$_REQUEST['id'];
	$reply=$objEmployee->DeleteEmpPersonalInfo($detail);
	if($reply['ack']==1){
	$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location($etable."_manage.php?msg=inserted");
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
	$db->rp_location($etable."_manage.php?msg=updated");
}


if(isset($_REQUEST['id']) && $_REQUEST['id']>0 && $_REQUEST['mode']=="isActive" && isset($_REQUEST['status'])  && $_REQUEST['status']!=""){
	$status = $_REQUEST['status'];
	$rows 	= array(
				"isActive"	=> $status
			);
	$where	= "id='".$_REQUEST['id']."'";
	$db->rp_update($ctable,$rows,$where);
	$db->rp_location($etable."_manage.php?msg=updated");
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
</head>
<body class="page-md">
<?php include("header.php"); ?>
<div class="page-container">
	<div class="page-head bg-grey">
		<div class="container">
			<div class="page-title">				
				<h1><a href="<?php echo  $etable;?>_manage.php" class="primary"><i class="fa  fa-arrow-circle-o-left" style="font-size: 22px!important;"></i></a> &nbsp;<?php $db->pageBar($page_hierarchy);?> </h1>
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
			<!-- Employee ID-->
			<input name="emp_id" id="emp_id" value="<?php echo $emp_id;?>" type="hidden"/>
			<input name="id" id="emp_id" value="<?php echo $emp_id;?>" type="hidden"/>
				<div class="row">
					<div class="col-md-12">						
						<div class="portlet box blue">
							<div class="portlet-body form">
								<div class="row">	
									<div class="col-sm-12">
										<div class="tabbable-custom nav-justified">
											<ul class="nav nav-tabs ">
												<li class="active">
													<a href="#tab_employee_info" data-toggle="tab" aria-expanded="false"> Employee Information </a>
												</li>
												<li>
													<a href="#tab_employee_comapny_info" data-toggle="tab" aria-expanded="false"> Company Information </a>
												</li>
												<li>
													<a href="#tab_employee_salary_info" data-toggle="tab" aria-expanded="false"> Salary Information </a>
												</li>
											</ul>
											<div class="tab-content">
											<div class="tab-pane active" id="tab_employee_info">
												<div class="row">
													<div class="col-sm-6">
														<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">								
															<h4><i class="fa fa-user"></i> &nbsp; Employee Information</h4>														
														</div>
															<form role="form" action="" onSubmit="return check_form();" method="post" enctype='multipart/form-data'>
															<div class="row">
															<div class="col-md-12">
																	<div class="form-body">
																	<div class="form-group">
																		<div class="row">
																													
																			<div class="col-md-12">
																				<div class="row">
																					
																					<div class="col-md-6">
																						<div class="form-group">
																						<label>Employee Code <code>*</code></label>
																						<input type="text" class="form-control" name="emp_code" id="emp_code" value="<?php echo $emp_code; ?>" autofocus>
																							<p class="help-block"></p>	
																						</div>
																					</div>
																				</div>
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																						<label >First Name <code>*</code></label>
																						<input type="text" class="form-control" name="first_name" id="first_name" value="<?php echo $first_name; ?>">
																							<p class="help-block"></p>		
																						</div>
																					</div>
																				</div>
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label>Middle Name </label>
																							<input type="text" class="form-control" name="middle_name" id="middle_name" value="<?php echo $middle_name; ?>">
																							<p class="help-block"></p>	
																						</div>
																					</div>														
																				</div>
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label>Last Name</label>
																							<input type="text" class="form-control" name="last_name" id="last_name" value="<?php echo $last_name; ?>">
																							<p class="help-block"></p>	
																						</div>
																					</div>
																				</div>
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label>Mobile No <code>*</code></label>
																							<input type="text" class="form-control" name="phone" id="phone" value="<?php echo $phone; ?>" maxlength="10">
																							<p class="help-block"></p>	
																						</div>
																					</div>														
																				</div>
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label>Other Contact</label>
																							<input type="text" class="form-control" name="other_contact" id="other_contact" value="<?php echo $other_contact; ?>">
																							<p class="help-block"></p>	
																						</div>
																					</div>
																				</div>
																				<div class="row">

																					<div class="col-md-6">
																						<div class="form-group">
																	<label for="address">Birth Date </label>
																	<input type="text" class="form-control" name="birth_date" id="birth_date" >
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
													<div class="col-sm-6">
														<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">											
															<h4><i class="fa fa-building"></i>&nbsp;&nbsp;Address Information</h4>														
														</div>
														<div class="row">
															<div class="col-md-12">
																<div class="form-body">
																<div class="form-group">
																	<label>Permanent Address</label>
																	<input type="text" class="form-control" name="perment_address" id="perment_address" value="<?php echo $perment_address; ?>">
																</div>
																
																<div class="form-group">
																	<label for="address">Residential Address </label>
																	<input type="text" class="form-control" name="residential_address" id="residential_address" value="<?php echo $residential_address; ?>">

																</div>
																
																<div class="form-group">
																	<div class="row">
																		<div class="col-md-6">
																			<label for="zip">Blood Group </label>
																			<input type="text" class="form-control" name="blood_group" id="blood_group" value="<?php echo $blood_group; ?>">
																		</div>
																		
																	</div>
																</div>
															<div class="form-group">
															<div class="row">
															<div class="col-md-6">
																<label for="zip">Identification Proof</label>
																<select class="form-control" name="identification_proof" id="identification_proof" >
																	<option value="">Select Identification Proof</option>
																	 <?php
    																	$proof_list_d=$db->rp_getData("identification_proof","*","isDelete=0","title asc",0);
    																	while($proof_list_r=mysqli_fetch_assoc($proof_list_d))
    																	{
    																		?>
    																		<option <?php echo ($identification_proof==$proof_list_r['id'])?"selected":"" ; ?> value="<?php echo $proof_list_r['id']?>">
    																		<?php echo $proof_list_r['title'];?>
    																		</option>
    																		<?php
    																	}
    																?>
															</select>
															</div>
															</div>
															</div>
															<div class="form-group">
																<div class="row">
																	<div class="col-md-4">
																		<label for="proof_document">Select Document</label>
																		<input type="file"  name="file_document" id="proof_document" value="<?php echo $proof_document; ?>"><span><?php echo $proof_document; ?></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<div class="row">
																	<div class="col-md-6">
																		<label for="remark">Remark</label>
																		<textarea rows="2" cols="7" class="form-control" name="remark" id="remark"><?php echo $remark; ?> </textarea>
																	</div>
																</div>
															</div>
															
															<div class="form-group">
																<div class="row">
																	<div class="col-md-4">
																		<label for="country">Image</label>
																		<?php
																		if($image!=""){
																			$image="../images/employee/".$image;
																		}
																		?>
																		<input type="file"  name="file" id="image"><span><img src="<?php echo $image; ?>" height="100" width="100"></span>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
											<div class="col-sm-12 col-lg-12 col-xs-12 form-group " style="padding-right:30px;">
											<button type="submit" name="submit" class="btn green pull-right"><i class="fa fa-floppy-o"></i> Save</button>								
										</div>
									</form>
												</div>
											</div>
											<div class="tab-pane" id="tab_employee_comapny_info">
												<div class="row">
													<div class="col-sm-12">
														<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">								
															<h4><i class="fa fa-sitemap"></i>&nbsp; Company Information</h4> 												
														</div>	
														<form role="form" action="" name="companyInfo" onSubmit="return isEmployeeAvailable();" method="post">															
														<div class="row">
															<div class="col-md-12">
																<div class="form-body">
																	<div class="form-group">
																		<div class="row">
																			<div class="col-md-12">
																				<div class="row">														
																					<div class="col-md-6">
																						<div class="form-group">
																						<label>Designation <code>*</code></label>
																						<select class="form-control" name="designation" id="designation" >
																								<option value="">Select Designation</option>
																								<?php
																									$designation_list_d=$db->rp_getData('designation',"*","1=1 AND isDelete=0","name asc",0);
																									while($designation_list_r=mysqli_fetch_assoc($designation_list_d))
																									{
																										?>
																										<option <?php echo ($designation==$designation_list_r['id'])?"selected":"" ; ?> value="<?php echo $designation_list_r['id']?>">
																										<?php echo $designation_list_r['name'];?>
																										</option>
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
																						<label >Department <code>*</code></label>
																						<select class="form-control" name="department" id="department" >
																								<option value="">Select Department</option>
																								<?php 
																									$department_list_d=$db->rp_getData('department',"*","1=1 AND isDelete=0","name asc",0);
																									while($department_list_r=mysqli_fetch_assoc($department_list_d))
																									{
																										?>
																										<option <?php echo ($department==$department_list_r['id'])?"selected":"" ; ?> value="<?php echo $department_list_r['id']?>">
																										<?php echo $department_list_r['name'];?>
																										</option>
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
																							<label>Select Shift <code>*</code></label>
																							<select class="form-control" name="shift" id="shift" >
																								<option value="">Select Shift</option>
																								<?php 
																									$shift_list_d=$db->rp_getData('working_shift',"*","1=1 AND isDelete=0","",0);
																									while($shift_list_r=mysqli_fetch_assoc($shift_list_d))
																									{
																										?>
																										<option <?php echo ($shift==$shift_list_r['id'])?"selected":"" ; ?> value="<?php echo $shift_list_r['id']?>">
																										<?php echo $shift_list_r['name'];?>
																										</option>
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
																							<label>Joining Date </label>
																							<input type="text" class="form-control" name="joining_date" id="joining_date" value="<?php echo $joining_date; ?>">
																							<p class="help-block"></p>	
																						</div>
																					</div>														
																				</div>
																				
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label>Account Number </label>
																							<input type="text" class="form-control" name="account_number" id="account_number" value="<?php echo $account_number; ?>">
																							<p class="help-block"></p>	
																						</div>
																					</div>														
																				</div>
																				<div class="row">
																					<div class="col-md-6">
																						<div class="form-group">
																							<label>Bank Name </label>
																							<input type="text" class="form-control" name="bank_name" id="bank_name" value="<?php echo $bank_name; ?>">
																							<p class="help-block"></p>	
																						</div>
																					</div>														
																				</div>
																				<div class="form-actions">
																				<button type="submit" name="submitCompany" class="btn green">Submit</button>
																				<button type="button" class="btn btn-default" onClick="window.location.href='<?php echo $etable; ?>_manage.php'">Back</button>
																			</div>
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
											<div class="tab-pane" id="tab_employee_salary_info">
												<div class="row">
													<div class="col-sm-12">							
														<form role="form" action="" id="salaryInfo" name="salaryInfo" onSubmit="return isSalaryAvailable();" method="post">
														<div class="row">
														<div class="col-sm-12">
																<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">		<h4><i class="fa fa-building"></i>&nbsp;&nbsp;Year</h4></div>
																<div class="row">	
																<div class="form-body">
																		<div class="col-md-3">
																		<div class="form-group">
																			<label for="month">Month<code>*</code> </label>
																			<select class="form-control" name="month" id="month" >
																	<option value="">Select Month</option>
																	<?php 
																		for($i=1;$i<=12;$i++)
																		{

                                                                            ?>
                                                                            <option value="<?php echo $i; ?>" <?php echo ($i==$month)?"selected":"" ; ?>><?php echo $i; ?></option>
                                                                            <?php
																		}
																	?>
															</select>
															<p class="help-block"></p>
															</div>
																		</div>
																	</div><div class="form-body">
																		<div class="col-md-3">
																		<div class="form-group">
																			<label for="year">Year<code>*</code> </label>
																			<select class="form-control" name="year" id="year" >
																	<option value="">Select Year</option>
																	<?php 
																		
																		$firstYear = (int)date('Y') - 1;
																		$lastYear = $firstYear + 15;
																		for($i=$firstYear;$i<=$lastYear;$i++)
																		{

                                                                            ?>
                                                                            <option value="<?php echo $i; ?>" <?php echo ($i==$year)?"selected":"" ; ?>><?php echo $i; ?></option>
																			<!--<option value="<?php echo $cid_d['id']; ?>" <?php if($cid_d['id']==$cid){?> selected <?php } ?>><?php echo $cid_d['name']; ?></option>-->
                                                                            <?php
																		}
																	?>
															</select>
															<p class="help-block"></p>
															</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col-sm-12">
																<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">								
																	<h4><i class="fa fa-sitemap"></i>&nbsp; Earning</h4> 												
																</div>	
																<div class="row">
																	<div class="col-md-12">
																		<div class="form-body">
																			<div class="form-group">
																				<div class="row">
																				<div class="col-md-12">
																						<div class="col-md-3">
																							<div class="form-group">
																							<label>BASIC </label>
																							<input type="text" class="form-control" name="basic" id="basic" value="<?php echo $basic; ?>" autofocus>
																								<p class="help-block"></p>	
																							</div>
																						</div>
																						<div class="col-md-3">
																							<div class="form-group">
																							<label >HRA </label>
																							<input type="text" class="form-control" name="hra" id="hra" value="<?php echo $hra; ?>">
																								<p class="help-block"></p>		
																							</div>
																						</div>
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>MEDICAL </label>
																								<input type="text" class="form-control" name="medical" id="medical" value="<?php echo $medical; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>CONV.ALL</label>
																								<input type="text" class="form-control" name="conv" id="conv" value="<?php echo $conv; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>WASH.ALL</label>
																								<input type="text" class="form-control" name="wash" id="wash" value="<?php echo $wash; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>EDU.ALL</label>
																								<input type="text" class="form-control" name="edu" id="edu" value="<?php echo $edu; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																					
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>L.T.ALL</label>
																								<input type="text" class="form-control" name="lt" id="lt" value="<?php echo $lt; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																					
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>SPEC.ALL</label>
																								<input type="text" class="form-control" name="spe" id="spe" value="<?php echo $spe; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																					
																						<div class="col-md-3">
																							<div class="form-group">
																								<label>Gross</label>
																								<input type="text" class="form-control" name="gross" id="gross" value="<?php echo $gross; ?>">
																								<p class="help-block"></p>	
																							</div>
																						</div>														
																					</div>
																				</div>
																			</div>															
																		</div>														
																	</div>							
																</div>							
															</div>	
															<div class="col-sm-12">
																<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">											
																	<h4><i class="fa fa-building"></i>&nbsp;&nbsp;Deduction</h4>														
																</div>
																<div class="row">																									
																	<div class="form-body">
																		<div class="col-md-3">
																		<div class="form-group">
																			<label for="it">IT </label>
																			<input type="text" class="form-control" name="it" id="it" value="<?php echo $it; ?>">
																		</div>
																		</div>
																		<div class="col-md-3">
																			<div class="form-group">
																				<label for="pt">PT </label>
																				<input type="text" class="form-control" name="pt" id="pt" value="<?php echo $pt; ?>">
																			</div>
																		</div>
																		<div class="col-md-3">																
																		<div class="form-group">
																			<label for="pf">PF </label>
																			<input type="text" class="form-control" name="pf" id="pf" value="<?php echo $pf; ?>">																
																		</div>
																		</div>
																	</div>
																</div>
															</div>
															<div class="col-sm-12">
																<div class="col-md-12 col-sm-12 col-xs-12 bg-grey">											
																	<h4><i class="fa fa-building"></i>&nbsp;&nbsp;Net Payable</h4>														
																</div>
																<div class="row">
																	<div class="form-body">
																	<div class="col-md-12">
																		<div class="col-md-6">
																			<div class="form-group">
																			<label >Net Payable</label>
																			<input type="text" class="form-control" name="net_payable" id="net_payable" value="<?php echo $net_payable; ?>">
																				<p class="help-block"></p>		
																			</div>
																		</div>
																		<div class="col-md-6">
																			<div class="form-group">
																			<input type="hidden" class="form-control" name="remark" id="remark" value="<?php echo $remark; ?>">
																				<p class="help-block"></p>		
																			</div>
																		</div>
																	</div>
																				
																</div>
															</div>
															<div class="row">
																	<div class="form-body">
																		<div class="col-md-2 pull-left">
																			<div class="form-group">	
																			<button type="button" data-mode="add_salary" name="add-salary-info" id="add-salary-info" class="btn  green"><i class="fa fa-user-plus"></i>&nbsp;Add Salary</button>						
																			</div>											
																		</div>
																	</div>
																</div>													
															<div class="col-md-12 col-sm-12 col-xs-12">
																<div id="results2">
																</div>
															</div>
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
<?php include("footer.php"); ?>
<?php include("include_js.php"); ?>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="js/jquery.numeric.min.js"></script>
<script type="text/javascript">
var date = new Date();
<?php

	if($_REQUEST['mode']=="edit" )
	{
		$data=array();
		
		$data[]=explode("-",$birth_date);
		
		?>
		
		
		$('#birth_date').datepicker("setDate", new Date(<?php echo $data[0][2]; ?>,<?php echo $data[0][1]-1; ?>,<?php echo $data[0][0]; ?>));
		<?php
	}
	else
	{
		?>
		$('#birth_date').datepicker("setDate", new Date(date.getFullYear() - 18,1));
		$('#birth_date').datepicker({  datepicker: true, autoclose: true });	
		<?php
	}

?>

$("#joining_date").datepicker({ format: 'dd-mm-yyyy', maxDate:0, timepicker: false, autoclose: true });
$(document).ready(function(){
	$("#salaryInfo").find("input[type=text]").on("change",function(){calculateNetPayable();});
	$(".form-control").bind("keyup change",function(){ if($(this).parent().hasClass("has-error")) {$(this).parent().find('.help-block').html(""); $(this).parent().removeClass("has-error"); } }); });
  $("#phone").numeric();

function calculateNetPayable()
{
	var basic=($("#basic").val()!="")?(parseFloat($("#basic").val())):0;
	var hra=($("#hra").val()!="")?(parseFloat($("#hra").val())):0;
	var medical=($("#medical").val()!="")?(parseFloat($("#medical").val())):0;
	var conv=($("#conv").val()!="")?(parseFloat($("#conv").val())):0;
	var wash=($("#wash").val()!="")?(parseFloat($("#wash").val())):0;
	var edu=($("#edu").val()!="")?(parseFloat($("#edu").val())):0;
	var lt=($("#lt").val()!="")?(parseFloat($("#lt").val())):0;
	var spe=($("#spe").val()!="")?(parseFloat($("#spe").val())):0;
	var gross=($("#gross").val()!="")?(parseFloat($("#gross").val())):0;
	var it=($("#it").val()!="")?(parseFloat($("#it").val())):0;
	var pt=($("#pt").val()!="")?(parseFloat($("#pt").val())):0;
	var pf=($("#pf").val()!="")?(parseFloat($("#pf").val())):0;
	net_payable=(basic+hra+medical+conv+wash+edu+lt+spe+gross)-(it+pt+pf);
	$("#net_payable").val(net_payable);
}
function isSalaryAvailable()
{
	if($("#emp_id").val()!="" && $("#emp_id").val()!=0)
	return true;
	else
	{
		toastr.error("Save Employee Personal Information First!!","Error!!");
		 $('.nav-tabs a[href="#tab_employee_info"]').tab('show');
		return false;	
	}
	
		
}
function check_form(){
	$(".form-body").children().removeClass("has-error");
	var isValid=true;
	if($("#emp_code").val()=="" || $("#emp_code").val().split(" ").join("")==""){
		vd=aj.error('emp_code',"Please enter Employee code.","add_error");
		isValid=false;
	}
    if($("#first_name").val()=="" || $("#first_name").val().split(" ").join("")==""){
		vd=aj.error('first_name',"Please enter First Name.","add_error");
		isValid=false;
	}
    if($("#phone").val()=="" || $("#phone").val().split(" ").join("")=="" || $("#phone").val().length!=10){
		vd=aj.error('phone',"Please enter valid phone number.","add_error");
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

var searchName="";
var data_emp_salary_url = "employee_salary_get_ajax.php";
<?php 
	if(isset($_REQUEST['mode'])  && $_REQUEST['mode']=='edit' && isset($_REQUEST['id']) && $_REQUEST['id']!='')
	{
		echo "var cid=".$_REQUEST['id'].";";
	}
	else
	{
		echo "var cid=0;";
	}
?>
function isEmployeeAvailable()
{
	var isValid=true;
	if(cid==""){
		toastr.error('Please Save Employee Information First');
		isValid=false;
	}
	else{
		if($("#designation").val()=="" || $("#designation").val().split(" ").join("")==""){		
			vd=aj.error('designation',"Please Select designation.","add_error");
			isValid=false;
		}
		if($("#department").val()=="" || $("#department").val().split(" ").join("")==""){		
			vd=aj.error('department',"Please Select department.","add_error");
			isValid=false;
		}
		if($("#shift").val()=="" || $("#shift").val().split(" ").join("")==""){		
			vd=aj.error('shift',"Please Select shift.","add_error");
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

	if($("#emp_id").val()!="" && $("#emp_id").val()!=0)
	return true;
	else 
	{
		toastr.error("Save Employee Personal Information First!!","Error!!");
		 $('.nav-tabs a[href="#tab_employee_info"]').tab('show');
		return false;
	}

}

$(document).ready(function(){	
	displaySalaryRecords(100,1);	
	$("#searchName").keyup(function(event)
	{
		if(event.keyCode == 13){
			$("#searchByName").click();
		}
	});
	$('#add-salary-info').on('click',function()
	{
		
		var isInformationAvailable=check_form();
		if(!isInformationAvailable || cid=="")
		{
			toastr.error('Please Save Employee Information First');
		}
		else
		{
			
			if(checkSalaryInfo())
			{ 
				var year=$('#year').val();	
				var month=$('#month').val();	
				var basic=$('#basic').val();
				var hra=$('#hra').val();							
				var medical=$('#medical').val();							
				var conv=$('#conv').val();							
				var wash=$('#wash').val();
				var edu=$('#edu').val();
				var lt=$('#lt').val();
				var spe=$('#spe').val();
				var gross=$('#gross').val();
				var it=$('#it').val();
				var pt=$('#pt').val();
				var pf=$('#pf').val();
				var net_payable=$('#net_payable').val();
				var remark=$('#remark').val();
				var mode=$(this).attr('data-mode');	
				var cpid=$(this).attr('data-id');	
				$.ajax({
					url:"employee_salary_ajax_function.php",
					type:"POST",
					data:{
						mode:mode,
						hra:hra,
						year:year,
						month:month,
						basic:basic,
						medical:medical,
						conv:conv,
						wash:wash,
						edu:edu,
						lt:lt,
						spe:spe,
						gross:gross,
						it:it,
						pt:pt,
						pf:pf,
						net_payable:net_payable,
						remark:remark,
						cid:cid,
						cpid:cpid
						
					},
					 success:function(json, textStatus, jqXHR) 
					{
						json=$.parseJSON(json);
						msg=json.ack_msg;
						if(json.ack==1)
						{
							
							toastr.success(msg,"Success!!");
							$("#year").select2("val", "");
							$("#month").select2("val", "");
                            $('#basic').val("");
							$('#hra').val("");
							$('#medical').val("");
							$('#conv').val("");
							$('#wash').val("");
							$('#edu').val("");
							$('#lt').val("");
							$('#pt').val("");
							$('#pf').val("");
							$('#net_payable').val("");
							$('#spe').val("");
							$('#gross').val("");
							$('#it').val("");
							$('#add-salary-info').attr('data-mode','add_salary');
							$('#add-salary-info').html('<i class="fa fa-user-plus"></i> &nbsp; Add Salary');
							displaySalaryRecords(100);
							
						}
						else
						{
							toastr.error(msg, 'Error!!')
						}
					},
					error: function(jqXHR, textStatus, errorThrown) 
					{
						toastr.error('Sorry, Server Error!!.', 'Error!!')
					}
					
				})
			}
						
			
		}
	});

});

function checkSalaryInfo()
{
	var isValid=true;
	if($("#year").val()=="" || $("#year").val().split(" ").join("")==""){				
		aj.error('year','Please Select year','add_error');
		isValid=false;
	}
	if($("#month").val()=="" || $("#month").val().split(" ").join("")==""){				
		aj.error('month','Please Select month','add_error');
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
function del_conf(id)
{
	var r = confirm("Are you sure you want to delete?");
	if(r){
		$.ajax({
			url:"employee_salary_ajax_function.php",
			type:"POST",
			data:{
				mode:'delete_salary',
				cid:id,
				
			},
			 success:function(json, textStatus, jqXHR) 
			{
				json=$.parseJSON(json);
				msg=json.ack_msg;
				if(json.ack==1)
				{						
					toastr.success(msg,"Success!!");
					displaySalaryRecords();
					
				}
				else
				{
					toastr.error(msg, 'Error!!')
				}
			},
			error: function(jqXHR, textStatus, errorThrown) 
			{
				toastr.error('Sorry, Server Error!!.', 'Error!!')
			}
			
		})
		
	}
}
function  editSalary(salary_id,emp_id)
{
	$.ajax({
		type: "POST",
		url: "employee_salary_ajax_function.php",
		data: {
			cid:salary_id,			
			emp_id:emp_id,			
			mode:"get_salary",
		},
		cache: false,
		beforeSend: function() {
			
		},
		success: function(json) {
			json=$.parseJSON(json);
			msg=json.ack_msg;
			if(json.ack==1)
			{						
				toastr.success(msg,"Success!!");
				detail=json.result;
				$("#year").select2("val",detail.year);
				$("#month").select2("val",detail.month);
				$('#basic').val(detail.basic);
				$('#hra').val(detail.hra);
				$('#medical').val(detail.medical);
				$('#conv').val(detail.conv);
				$('#wash').val(detail.wash);
				$('#edu').val(detail.edu);
				$('#lt').val(detail.lt);
				$('#spe').val(detail.spe);
				$('#gross').val(detail.gross);
				$('#it').val(detail.it);
				$('#pt').val(detail.pt);
				$('#pf').val(detail.pf);
				$('#net_payable').val(detail.net_payable);
				$('#remark').val(detail.remark);
				
				$('#add-salary-info').attr('data-mode','edit_salary');
				$('#add-salary-info').attr('data-id',salary_id);
				$('#add-salary-info').html('<i class="fa fa-refresh"></i> &nbsp; Update Salary');
				displaySalaryRecords(100);
			}
			else
			{
				toastr.error(msg, 'Error!!')
			}
		}
	});
	
}
// used when user change row limit
function changeDisplayRowCountContact(numRecords) 
{
	displaySalaryRecords(numRecords, 1);
}

function displaySalaryRecords(numRecords) 
	{
	var searchName 	= ($("#searchContactName").val()==undefined)?"":$("#searchContactName").val();
	searchName 	= encodeURIComponent(searchName.trim());
	$("#results2" ).html("");
	$("#results2" ).load( data_emp_salary_url+"?cid="+cid+"&show=" + numRecords +"&eid=" + <?php echo $emp_id; ?> + "&searchName=" + searchName,function(){
		loadSalaryDataTable();
	}); //load initial records
	
	//executes code below when user click on pagination links
	$("#results2").on( "click", ".paging_simple_numbers a", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div2").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_emp_salary_url+"?cid="+cid+"&show=" + numRecords  +"&eid=" + <?php echo $emp_id; ?> + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadSalaryDataTable();
		});
		
	});
	$("#results2").on( "change", "#numRecords2", function (e){
		e.preventDefault();
		var numRecords  = $("#numRecords2").val();
		$(".loading-div").show(); //show loading element
		var page = $(this).attr("data-page"); //get page number from link
		$("#results2").load(data_emp_salary_url+"?jid="+jid+"&show=" + numRecords +"&eid=" + <?php echo $emp_id; ?> + "&searchName=" + searchName,{"page":page}, function(){ //get content from PHP page
			$(".loading-div2").hide(); //once done, hide loading element
			loadSalaryDataTable();
		});
		
	});
}

function loadSalaryDataTable()
{
	$('#datatable_2').dataTable({
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bAutoWidth": false, 
		"aoColumns": [
			  { "sWidth": "0.4%" }, 
			  { "sWidth": "15%" },
			  { "sWidth": "15%" },
			  { "sWidth": "15%" },							  						  	
			  { "sWidth": "5%","bSortable": false }
			],
		 "oLanguage": { "sEmptyTable":     "<i class='fa fa- fa-user-plus '></i> &nbsp; No Contact Found"},
	});
}
$('#image').change(
                function () {
                    var fileExtension = ['png','jpg','jpeg','PNG','JPG','JPEG'];
                    if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
                        alert("Only '.png','.jpg','.jpeg','.PNG','.JPG','.JPEG' formats are allowed.");
						$('#image').val("");
                        return false; }
});
</script>
</body>
</html>