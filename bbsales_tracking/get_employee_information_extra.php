<?php

	$page_id=658;$page_slug='sales_executive_info_form';
	include("connect.php");
	$mode = $_REQUEST['mode'];
	$sales_executive_id = $_REQUEST['sales_executive_id'];


// ---------------------------------- For Pre-Joining Connections----------------------
?>
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

	table th {
  color: #2B547E;
}
</style>
</head>
<?php
	if ($mode == "get_pre_joining_connections_details") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
		$Results=$db->rp_getData("sal_person_info_vs_pre_joining_connections_details","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 10%;">Employee Name</th> 
								<th style="width: 10%;">Designation</th> 
								<th style="width: 10%;">Department</th> 
								<th style="width: 10%;">Relationship</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
				<?php 
							if($Results)
							{	
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
				?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>						
								<td><?= $R['employee_name']?></td>
								<td><?= $R['designation']?></td>
								<td><?= $R['department']?></td>
								<td><?= $R['relationship']?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_pre_joining_connections_details('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

// ---------------------------------- For Family Background ----------------------

	if ($mode == "get_family_background") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
		$Results=$db->rp_getData("sal_person_info_vs_family_background","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 10%;">Name</th> 
								<th style="width: 10%;">Relation</th> 
								<th style="width: 10%;">Date Of Birth</th>
								<th style="width: 10%;">Age</th>
								<th style="width: 10%;">Education</th> 
								<th style="width: 10%;">Profession</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
		<?php 
							if($Results)
							{	
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
		?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>
								<td><?= $R['name']?></td>
								<td><?= $R['relation']?></td>
								<td><?= $R['date_of_birth'] != "0000-00-00" && $R['date_of_birth'] != "" ? date("d-m-Y",strtotime($R['date_of_birth'])) : ""; ?></td>
								<td>
									<?php
										if($R['date_of_birth'] != "0000-00-00"){
											$dateOfBirth = $R['date_of_birth'];
											$currentDate = date('Y-m-d');
											$dateOfBirthObj = new DateTime($dateOfBirth);
											$currentDateObj = new DateTime($currentDate);
											$diff = $dateOfBirthObj->diff($currentDateObj);
											$years = $diff->y;
											$months = $diff->m;
											$days = $diff->d;
											$result = '';
											if ($years > 0) {
											    $result .= "{$years} year";
											    if ($years > 1) {
											        $result .= 's';
											    }
											}
											if ($months > 0) {
											    if ($result != '') {
											        $result .= ', ';
											    }
											    $result .= "{$months} month";
											    if ($months > 1) {
											        $result .= 's';
											    }
											}
											if ($days > 0) {
											    if ($result != '') {
											        $result .= ', ';
											    }
											    $result .= "{$days} day";
											    if ($days > 1) {
											        $result .= 's';
											    }
											}
											echo $result;
										}
									?>
								</td>
								<td><?= $R['education']?></td>
								<td><?= $R['profession']?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_family_background('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

//--------------------------------- For Educational Details ----------------------

	if ($mode == "get_educational_details") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
		$Results=$db->rp_getData("sal_person_info_vs_educational_details","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 10%;">Education</th> 
								<th style="width: 10%;">Month & Year of Passing</th> 
								<th style="width: 10%;">Percentage</th>
								<th style="width: 10%;">School / College / University Name</th>
								<th style="width: 10%;">City</th> 
								<th style="width: 10%;">Special Remarks</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
		<?php 
							if($Results)
							{	
								// print_r($R=mysqli_fetch_assoc($Results));die;
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
		?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>
								<td><?= $R['education']?></td>
								<td><?= $R['passing_year']?></td>
								<td><?= $R['percentage'] != "" ? $R['percentage']."%" : "" ?></td>
								<td><?= $R['place_name']?></td>
								<td><?= $R['city']?></td>
								<td><?= $R['special_remarks']?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_educational_details('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

//--------------------------------- For Present Course Learning ----------------------

	if ($mode == "get_present_course_learning") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
		$Results=$db->rp_getData("sal_person_info_vs_present_course_learning","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 10%;">Name of Course</th> 
								<th style="width: 10%;">Institute</th> 
								<th style="width: 10%;">City</th>
								<th style="width: 10%;">Course Start Date</th>
								<th style="width: 10%;">Course End Date</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
		<?php 
							if($Results)
							{	
								// print_r($R=mysqli_fetch_assoc($Results));die;
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
		?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>
								<td><?= $R['name_of_course']?></td>
								<td><?= $R['institute']?></td>
								<td><?= $R['city'] ?></td>
								<td><?= $R['start_date'] != "0000-00-00" ? date("d-m-Y",strtotime($R['start_date'])) : "" ?></td>
								<td><?= $R['end_data'] != "0000-00-00" ? date("d-m-Y",strtotime($R['end_data'])) : "" ?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_present_course_learning('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

//--------------------------For Computer Proficiency ----------------------

	if ($mode == "get_computer_proficiency") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
		$Rating_array = array(1 => "Basic", 2 => "Good", 3 => "Excellent");
		$Results=$db->rp_getData("sal_person_info_vs_computer_proficiency","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 10%;">Course</th> 
								<th style="width: 10%;">Rating</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
		<?php 
							if($Results)
							{	
								// print_r($R=mysqli_fetch_assoc($Results));die;
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
		?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>
								<td><?= $R['course']?></td>
								<td><?= $Rating_array[$R['rating']]?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_computer_proficiency('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

//--------------------------For Language Details ----------------------

	if ($mode == "get_language_details") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
		$Rating_array = array(1 => "Basic", 2 => "Good", 3 => "Excellent");
		$Results=$db->rp_getData("sal_person_info_vs_language_details","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 10%;">Language Name</th>
								<th style="width: 10%;">Language Skills</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
				<?php 
							if($Results)
							{	
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
				?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>
								<td><?= $R['language_name'] ?></td>
								<td><?= $R['language_skills']?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_language_details('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

// ---------------------------------- For Work Experience -----------------------------

	if ($mode == "get_work_experience") {
		$Where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
		$Results=$db->rp_getData("sal_person_info_vs_work_experience","*",$Where,"",0);
		?>
			<div class="row">
				<div class="col-md-12 table-responsive">
					<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
					    <thead>
					        <tr>
					            <th style="width: 5%;">Sr.no</th>
								<th style="width: 15%;">Company Name</th> 
								<th style="width: 15%;">location</th> 
								<th style="width: 15%;">Kind Of Business</th> 
								<th style="width: 15%;">Designation</th> 
								<th style="width: 15%;">From Date</th>
								<th style="width: 15%;">To Date</th>
								<th style="width: 5%;" class="text-center">Action</th>
					        </tr>
					    </thead>
					    <tbody>
		<?php 
							if($Results)
							{	
								$cnt=0;
								while($R=mysqli_fetch_assoc($Results))
								{
									$cnt++;
		?>
						  	<tr class="">

								<td><?php echo $cnt; ?></td>
								<td><?= $R['company_name']?></td>
								<td><?= $R['location']?></td>
								<td><?= $R['kind_of_business']?></td>
								<td><?= $R['designation']?></td>
								<td><?= $R['from_date'] != "0000-00-00" && $R['from_date'] != "" ? date("d-m-Y",strtotime($R['from_date'])) : ""; ?></td>
								<td><?= $R['to_date'] != "0000-00-00" && $R['to_date'] != "" ? date("d-m-Y",strtotime($R['to_date'])) : ""; ?></td>
								<td style="font-size: 11px;text-align: center;">
									<a class="btn btn-danger btn-sm" onClick="del_work_experience('<?php echo $R['id']; ?>');" title="Delete" style="padding: 5px 5px 5px 5px;"><i class="fa fa-times"></i></a><br>			
								</td>
						 	</tr> 
							<?php													 
								}
							}
							else
							{
								?>
								<tr>
									<td colspan="11" class="text-center">No Data Found!!</td>
								</tr>
								<?php
							}
							?>   
					    </tbody>
					</table> 
				</div>
			</div>
		<?php
	}

?>