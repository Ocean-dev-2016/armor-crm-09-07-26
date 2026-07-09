<?php

	$page_id=658;$page_slug='sales_executive_info_form';
	include("connect_in.php");
	$mode = $_REQUEST['mode'];
	$sales_executive_id = $_REQUEST['sales_executive_id'];
	$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"info_manage.php","title"=>"Manage Sales Executive Information "),array("link"=>"info_crud.php","title"=>" View Sales Executive Information "));


// ---------------------------------- For Wark Experience-----------------------------------

	$sales_executive_information_where="isDelete = 0 AND isActive = 1 AND id = '".$sales_executive_id."'";
			
	$sales_executive_information_r=$db->rp_getData("sales_executive_information","*",$sales_executive_information_where,"",0);

// ---------------------------------- For Wark Experience-----------------------------------

	$sal_person_info_vs_pre_joining_connections_details_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
	$sal_person_info_vs_pre_joining_connections_details_r=$db->rp_getData("sal_person_info_vs_pre_joining_connections_details","*",$sal_person_info_vs_pre_joining_connections_details_where,"",0);

// ---------------------------------- For Family Background---------------------------------

	$sal_person_info_vs_family_background_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
	$sal_person_info_vs_family_background_r=$db->rp_getData("sal_person_info_vs_family_background","*",$sal_person_info_vs_family_background_where,"",0);

//--------------------------------- For Educational Details --------------------------------

	$sal_person_info_vs_educational_details_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
	$sal_person_info_vs_educational_details_r=$db->rp_getData("sal_person_info_vs_educational_details","*",$sal_person_info_vs_educational_details_where,"",0);

//--------------------------------- For Present Course Learning ----------------------------

	$sal_person_info_vs_present_course_learning_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
			
	$sal_person_info_vs_present_course_learning_r=$db->rp_getData("sal_person_info_vs_present_course_learning","*",$sal_person_info_vs_present_course_learning_where,"",0);

//-------------------------- Computer Proficiency & Language Details -----------------------

	$Rating_array = array(1 => "Basic", 2 => "Good", 3 => "Excellent");
	$sal_person_info_vs_computer_proficiency_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
	$sal_person_info_vs_computer_proficiency_d=$db->rp_getData("sal_person_info_vs_computer_proficiency","*",$sal_person_info_vs_computer_proficiency_where,"",0);

	$sal_person_info_vs_language_details_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
	$sal_person_info_vs_language_details_where_r=$db->rp_getData("sal_person_info_vs_language_details","*",$sal_person_info_vs_language_details_where,"",0);

// ---------------------------------- For Work Experience -----------------------------

	$sal_person_info_vs_work_experience_where="isDelete = 0 AND isActive = 1 AND sales_person_id = '".$sales_executive_id."'";
		$sal_person_info_vs_work_experience_r=$db->rp_getData("sal_person_info_vs_work_experience","*",$sal_person_info_vs_work_experience_where,"",0);

?>
<html lang="en">

	<head>
		<meta charset="utf-8" />
		<title><?php echo "Sales Person All Information"; ?> | <?php echo SITETITLE; ?></title>
		<?php include("include_css.php"); ?>
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.min.css"/>
		<link rel="stylesheet" type="text/css" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.4/themes/redmond/jquery-ui.css">
		<link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
		<?php include("include_css.php"); ?>

		<link rel="stylesheet" type="text/css" href="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.css" />
		<style type="text/css">
			#wrapper {
				width: 190mm;
				margin: 0 50mm;
			}

			#wrapper {
				width: auto !important;
			}
		</style>
	</head>
	<body class="page-md">
			<?php include("header.php"); ?>
			
			<div class="page-container">
				<div class="page-head bg-grey">
					<div class="container">
						<div class="page-title">
							<h2><?php echo "View Employee Information"; ?></h2>

						</div>
						<div class="page-toolbar">				
							<div class="btn-group btn-theme-panel">
								<a class="btn dropdown-toggle blue-ebonyclay" href="javascript:;" onClick="genSalPerInfoPrint('<?php echo $sales_executive_id; ?>');" title="Print">Print</a>
							</div>
							 <!-- <div class="btn-group btn-theme-panel">
								<a class="btn btn-success" href="javascript:;" onClick="genSalPerInfoExcel('<?php echo $sales_executive_id; ?>','1');" title="Approve">Excel</a>
							</div> -->
							<div class="btn-group btn-theme-panel">
								<a class="btn dropdown-toggle blue-ebonyclay" href="<?=SITEURL."bbsales_tracking/employee_information_manage.php"?>" title="Print">Back</a>
							</div>
						</div>
					</div>
				</div>
				<div class="page-content">
					<div class="container">
						<br>
						<div class="row">
							<div class="col-md-12" id="report_content">
								<div id="wrapper1">
<!-- --------------------------- For Wark Experience----------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="10"><b>Employee Information</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 10%;">First Name</th> 
														<th style="width: 10%;">Middle Name</th> 
														<th style="width: 10%;">Surname</th> 
														<th style="width: 5%;"> Gender </th> 
														<th style="width: 10%;">Contact No</th> 
														<th style="width: 10%;">Post Applied</th> 
														<th style="width: 20%;">Present Address</th>
														<th style="width: 10%;">Image</th>
											        </tr>
											    </thead>
											    <tbody>
													<?php 
													if($sales_executive_information_r)
													{	
														$cnt=0;
														while($sales_executive_information_d=mysqli_fetch_assoc($sales_executive_information_r))
														{
															$cnt++;
													?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sales_executive_information_d['first_name']?></td>
														<td><?= $sales_executive_information_d['middle_name']?></td>
														<td><?= $sales_executive_information_d['surname']?></td>
														<td><?= $sales_executive_information_d['gender']?></td>
														<td><?= $sales_executive_information_d['contact_no']?></td>
														<td><?= $sales_executive_information_d['post_applied']?></td>
														<td><?= $sales_executive_information_d['present_address']?></td>
														<td>
															<?php 
																$img = explode(",", $sales_executive_information_d['image_path']);
																$imgpath = array();
																for ($i=0; $i < sizeof($img); $i++)
																{ 
																	$imgpath[] = EMPLOYEE_IMAGE_A.$db->rp_getValue("media","url","reference_id='".$sales_executive_information_d["id"]."' AND id='".$img[$i]."'",0);
																}
																// print_r($imgpath);
																for ($i=0; $i < sizeof($imgpath); $i++)
																{
																	if($i==0)
																	{
																		?>
																			<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$sales_executive_information_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
																		<?php 
																	}
																	else
																	{
																		?>
																		<div class="hidden">
																			<a href="<?=$imgpath[$i]?>" data-lightbox="visit<?=$count?>" data-title="stop_visit <?=$sales_executive_information_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
																		</div>
																		<?php
																	}
																}
															?>
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

<!-- --------------------------- For Pre Joining Connections----------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="10"><b>Pre Joining Connections</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 20%;">Employee Name</th> 
														<th style="width: 25%;">Designation</th> 
														<th style="width: 25%;">Department</th> 
														<th style="width: 25%;">Relationship</th>
											        </tr>
											    </thead>
											    <tbody>
										<?php 
													if($sal_person_info_vs_pre_joining_connections_details_r)
													{	
														$cnt=0;
														while($sal_person_info_vs_pre_joining_connections_details_d=mysqli_fetch_assoc($sal_person_info_vs_pre_joining_connections_details_r))
														{
															$cnt++;
										?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>						
														<td><?= $sal_person_info_vs_pre_joining_connections_details_d['employee_name']?></td>
														<td><?= $sal_person_info_vs_pre_joining_connections_details_d['designation']?></td>
														<td><?= $sal_person_info_vs_pre_joining_connections_details_d['department']?></td>
														<td><?= $sal_person_info_vs_pre_joining_connections_details_d['relationship']?></td>
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

<!-- --------------------------- For Family Background--------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="7"><b>Family Background</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 15%;">Name</th> 
														<th style="width: 15%;">Relation</th> 
														<th style="width: 15%;">Date Of Births</th>
														<th style="width: 20%;">Age</th>
														<th style="width: 15%;">Education</th> 
														<th style="width: 15%;">Profession</th>
											        </tr>
											    </thead>
											    <tbody>
												<?php 
													if($sal_person_info_vs_family_background_r)
													{	
														$cnt=0;
														while($sal_person_info_vs_family_background_d=mysqli_fetch_assoc($sal_person_info_vs_family_background_r))
														{
															$cnt++;
												?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sal_person_info_vs_family_background_d['name']?></td>
														<td><?= $sal_person_info_vs_family_background_d['relation']?></td>
														<td><?= $sal_person_info_vs_family_background_d['date_of_birth'] != "0000-00-00" && $sal_person_info_vs_family_background_d['date_of_birth'] != "" ? date("d-m-Y",strtotime($sal_person_info_vs_family_background_d['date_of_birth'])) : ""; ?></td>
														<td>
															<?php
																if($sal_person_info_vs_family_background_d['date_of_birth'] != "0000-00-00"){
																	$dateOfBirth = $sal_person_info_vs_family_background_d['date_of_birth'];
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
														<td><?= $sal_person_info_vs_family_background_d['education']?></td>
														<td><?= $sal_person_info_vs_family_background_d['profession']?></td>
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

<!-- -------------------------- For Educational Details -------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="7"><b>Educational Details</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 20%;">Education</th> 
														<th style="width: 15%;">Month & Year of Passing</th> 
														<th style="width: 5%;">Percentage</th>
														<th style="width: 25%;">School / College / University Name</th>
														<th style="width: 15%;">City</th> 
														<th style="width: 15%;">Special Remarks</th>
											        </tr>
											    </thead>
											    <tbody>
													<?php 
													if($sal_person_info_vs_educational_details_r)
													{	
														// print_r($R=mysqli_fetch_assoc($sal_person_info_vs_educational_details_r));die;
														$cnt=0;
														while($sal_person_info_vs_educational_details_d=mysqli_fetch_assoc($sal_person_info_vs_educational_details_r))
														{
															$cnt++;
													?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sal_person_info_vs_educational_details_d['education']?></td>
														<td><?= $sal_person_info_vs_educational_details_d['passing_year']?></td>
														<td><?= $sal_person_info_vs_educational_details_d['percentage'] ?></td>
														<td><?= $sal_person_info_vs_educational_details_d['place_name']?></td>
														<td><?= $sal_person_info_vs_educational_details_d['city']?></td>
														<td><?= $sal_person_info_vs_educational_details_d['special_remarks']?></td>
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

<!-- -------------------------- For Present Course Learning ---------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="6"><b>Present Course Learning</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 15%;">Name of Course</th> 
														<th style="width: 20%;">Institute</th> 
														<th style="width: 20%;">City</th>
														<th style="width: 20%;">Course Start Date</th>
														<th style="width: 20%;">Course Start End</th>
											        </tr>
											    </thead>
											    <tbody>
													<?php 
													if($sal_person_info_vs_present_course_learning_r)
													{	
														// print_r($R=mysqli_fetch_assoc($sal_person_info_vs_present_course_learning_r));die;
														$cnt=0;
														while($sal_person_info_vs_present_course_learning_d=mysqli_fetch_assoc($sal_person_info_vs_present_course_learning_r))
														{
															$cnt++;
													?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sal_person_info_vs_present_course_learning_d['name_of_course']?></td>
														<td><?= $sal_person_info_vs_present_course_learning_d['institute']?></td>
														<td><?= $sal_person_info_vs_present_course_learning_d['city'] ?></td>
														<td><?= $sal_person_info_vs_present_course_learning_d['start_date'] != "0000-00-00" ? date("d-m-Y",strtotime($sal_person_info_vs_present_course_learning_d['start_date'])) : "" ?></td>
														<td><?= $sal_person_info_vs_present_course_learning_d['end_data'] != "0000-00-00" ? date("d-m-Y",strtotime($sal_person_info_vs_present_course_learning_d['end_data'])) : "" ?></td>
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

<!-- ------------------- Computer Proficiency & Language Details ----------------------- -->

									<div class="row">
										<div class="col-md-6 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="5"><b>Computer Proficiency </b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 20%;">Course</th> 
														<th style="width: 25%;">Rating</th> 
											        </tr>
											    </thead>
											    <tbody>
													<?php 
													if($sal_person_info_vs_computer_proficiency_d)
													{	
														// print_r($R=mysqli_fetch_assoc($sal_person_info_vs_computer_proficiency_d));die;
														$cnt=0;
														while($sal_person_info_vs_computer_proficiency_y=mysqli_fetch_assoc($sal_person_info_vs_computer_proficiency_d))
														{
															$cnt++;
													?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sal_person_info_vs_computer_proficiency_y['course']?></td>
														<td><?= $Rating_array[$sal_person_info_vs_computer_proficiency_y['rating']]?></td>
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
										<div class="col-md-6 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="5"><b>Language Details</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 20%;">Language Name</th>
														<th style="width: 25%;">Language Skills</th>
											        </tr>
											    </thead>
											    <tbody>
													<?php 
													if($sal_person_info_vs_language_details_where_r)
													{	
														// print_r($R=mysqli_fetch_assoc($sal_person_info_vs_language_details_where_r));die;
														$cnt=0;
														while($sal_person_info_vs_language_details_where_d=mysqli_fetch_assoc($sal_person_info_vs_language_details_where_r))
														{
															$cnt++;
													?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sal_person_info_vs_language_details_where_d['language_name'] ?></td>
														<td><?= $sal_person_info_vs_language_details_where_d['language_skills']?></td>
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

<!-- ------------------------------ For Work Experience -------------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="7"><b>Work Experience</b></td>
											    	</tr>
											        <tr>
											            <th style="width: 5%;">Sr.no</th>
														<th style="width: 15%;">Company Name</th> 
														<th style="width: 16%;">location</th> 
														<th style="width: 16%;">Kind Of Business</th> 
														<th style="width: 16%;">Designation</th> 
														<th style="width: 16%;">From Date</th>
														<th style="width: 16%;">To Date</th>
											        </tr>
											    </thead>
											    <tbody>
											<?php 
													if($sal_person_info_vs_work_experience_r)
													{	
														$cnt=0;
														while($sal_person_info_vs_work_experience_d=mysqli_fetch_assoc($sal_person_info_vs_work_experience_r))
														{
															$cnt++;
											?>
												  	<tr class="">

														<td><?php echo $cnt; ?></td>
														<td><?= $sal_person_info_vs_work_experience_d['company_name']?></td>
														<td><?= $sal_person_info_vs_work_experience_d['location']?></td>
														<td><?= $sal_person_info_vs_work_experience_d['kind_of_business']?></td>
														<td><?= $sal_person_info_vs_work_experience_d['designation']?></td>
														<td><?= $sal_person_info_vs_work_experience_d['from_date'] != "0000-00-00" && $sal_person_info_vs_work_experience_d['from_date'] != "" ? date("d-m-Y",strtotime($sal_person_info_vs_work_experience_d['from_date'])) : ""; ?></td>
														<td><?= $sal_person_info_vs_work_experience_d['to_date'] != "0000-00-00" && $sal_person_info_vs_work_experience_d['to_date'] != "" ? date("d-m-Y",strtotime($sal_person_info_vs_work_experience_d['to_date'])) : ""; ?></td>
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
								</div>											
							</div>
						</div>
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
		<script type="text/javascript">

		function genSalPerInfoPrint(sales_executive_id){
	     	var myWindow = window.open('print_employee_information_ajax.php?sales_executive_id='+sales_executive_id,'','width=700,height=800');
	     	myWindow.print();
	    }
	    function genSalPerInfoExcel(sales_executive_id)
		{
			df = $("#material_request_filter_input").val();

		      	$.ajax({
					method: "POST",
					url: "info_genReport_ajax.php",
					data:{
		        		sales_executive_id:sales_executive_id,
					},
					dataType : 'json',
					beforeSend: function() {
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
					success: function(result){
			        		// $("#loading-modal").modal('hide');
			        		$('.preloader').fadeOut('slow');
			        		window.location.href="<?=SITEURL?>"+result.file_path;
			        	},
				});
		}
	</script>
	</body>
</html>