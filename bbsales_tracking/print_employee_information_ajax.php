<?php
	$page_id=658;$page_slug='sales_executive_info_form';
	include("connect_in.php");
	$mode = $_REQUEST['mode'];
	// print_r($_REQUEST);die();
	$sales_executive_id = $_REQUEST['sales_executive_id'];
	$page_hierarchy=array(array("link"=>"","title"=>"HR"),array("link"=>"info_manage.php","title"=>"Manage Sales Executive Information "),array("link"=>"info_crud.php","title"=>" View Sales Executive Information "));


// ---------------------------------- For Wark Experience-----------------------------------

	$sales_executive_information_where="isDelete = 0 AND isActive = 1 AND id = '".$sales_executive_id."'";
			
	$sales_executive_information_r=$db->rp_getData("sales_executive_information","*",$sales_executive_information_where,"",0);
	$sales_executive_information_d=mysqli_fetch_assoc($sales_executive_information_r);

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
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
body {
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 20px;
    }
    h3 {
      color: #333;
      font-size: 20px;
      margin: 0;
      margin-bottom: 10px;
    }
    p {
      margin: 0;
      margin-bottom: 5px;
    }
    .label {
      font-weight: bold;
    }
</style>
<!-- --------------------------- For Wark Experience----------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table>
												<tr>
													<td colspan="8" class="center">
														<h1>Sales Executive Information <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h1>
													</td>
										    </tr>
											  <tr>
											    <td colspan="8"><h2><?=$sales_executive_information_d['first_name']." ".$sales_executive_information_d['middle_name']." ".$sales_executive_information_d['surname']?></h2></td>
											  </tr>
											  <tr>
											    <th style="width: 10%;">Date 
											    </th><td style="width: 10%;"> <?=date("d-m-Y",strtotime($sales_executive_information_d['date']))?></td>
													<th style="width: 10%;">Post Applied 
													</th><td style="width: 10%;"> <?=$sales_executive_information_d['post_applied']?></td>
													<th style="width: 10%;">Reference 
													</th><td style="width: 30%;" colspan="3"> <?=$sales_executive_information_d['reference']?></td>
											  </tr>
											  <tr>
											  	<th style="width: 10%;">Date Of Birth 
											  	</th><td> <?=$sales_executive_information_d['birth_date'] != "0000-00-00" && $sales_executive_information_d['birth_date'] != "1970-01-01" && $sales_executive_information_d['birth_date'] != "" ? date("d-m-Y",strtotime($sales_executive_information_d['birth_date'])) : ""?></td>
													<th style="width: 10%;">Religion 
													</th><td> <?=$sales_executive_information_d['religion']?></td>
													<th style="width: 10%;">Cast 
													</th><td> <?=$sales_executive_information_d['cast']?></td>
													<th style="width: 10%;">Mother Tongue 
													</th><td> <?=$sales_executive_information_d['mother_tongue']?></td>
											  </tr>
											  <tr>
											  	<th style="width: 10%;">Marital Status 
											  	</th><td> <?=$sales_executive_information_d['marital_status']?></td>
													<th style="width: 10%;">Place Of Birth 
													</th><td> <?=$sales_executive_information_d['plaece_of_birth']?></td>
													<th style="width: 10%;">Present Address  
													</th><td> <?=$sales_executive_information_d['present_address']?></td>
													<th style="width: 10%;">Permanent Address 
													</th><td> <?=$sales_executive_information_d['permanent_address']?></td>
											  </tr>
											  <tr>
											  	<th style="width: 10%;">Contact No. 
											  	</th><td> <?=$sales_executive_information_d['contact_no']?></td>
													<th style="width: 10%;">Email 
													</th><td> <?=$sales_executive_information_d['email']?></td>
													<th style="width: 10%;">Emergency Contact Person  
													</th><td> <?=$sales_executive_information_d['emergency_contact_person']?></td>
													<th style="width: 10%;">Contact Person Relation 
													</th><td> <?=$sales_executive_information_d['contact_person_relation']?></td>
											  </tr>
											  <tr>
											  	<th style="width: 10%;">Blood Group 
											  	</th><td> <?=$sales_executive_information_d['blood_group']?></td>
													<th style="width: 10%;">Type Of Vehicle 
													</th><td> <?=$sales_executive_information_d['type_of_vehicle']?></td>
													<th style="width: 10%;" >Vehicle No.  
													</th><td colspan="3"> <?=$sales_executive_information_d['vehicle_model_no']?></td>
											  </tr>
											  <tr>
											  	<td colspan="8" align="center" style="font-size: 15px;"><b>Reference Person</b></td>
											  </tr>
											  <tr>
											  	<th style="width: 10%;">Name 
											  	</th><td> <?=$sales_executive_information_d['rp1_name']?></td>
													<th style="width: 10%;">Relation 
													</th><td> <?=$sales_executive_information_d['rp1_relation']?></td>
													<th style="width: 10%;">Occupation 
													</th><td> <?=$sales_executive_information_d['rp1_occupation']?></td>
													<th style="width: 10%;">Contact No 
													</th><td> <?=$sales_executive_information_d['rp1_contact_no']?> </td>
											  </tr>
											  <tr>
											  	<th style="width: 10%;">Name 
											  	</th><td> <?=$sales_executive_information_d['rp2_name']?></td>
													<th style="width: 10%;">Relation 
													</th><td> <?=$sales_executive_information_d['rp2_relation']?></td>
													<th style="width: 10%;">Occupation 
													</th><td> <?=$sales_executive_information_d['rp2_occupation']?></td>
													<th style="width: 10%;">Contact No 
													</th><td> <?=$sales_executive_information_d['rp2_contact_no']?> </td>
											  </tr>
											</table>
										</div>
									</div><br>

<!-- --------------------------- For Pre Joining Connections----------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="10"><b><h2>Pre Joining Connections</h2></b></td>
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
									</div><br>

<!-- --------------------------- For Family Background--------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="7"><b><h2>Family Background</h2></b></td>
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
									</div><br>

<!-- -------------------------- For Educational Details -------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="7"><b><h2>Educational Details</h2></b></td>
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
									</div><br>

<!-- -------------------------- For Present Course Learning ---------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="6"><b><h2>Present Course Learning</h2></b></td>
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
									</div><br>

<!-- ------------------- Computer Proficiency & Language Details ----------------------- -->

									<div class="row">
										<table>
											<tr>
												<td style="width: 50%;">
													<div class="col-md-6 col-xl-6 col-sm-6 col-lg-6">
														<table>
														    <thead>
														    	<tr>
														    		<td colspan="3"><b><h2>Computer Proficiency</h2></b></td>
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
																		<td colspan="3" class="text-center">No Data Found!!</td>
																	</tr>
																	<?php
																}
																?>   
														    </tbody>
														</table> 
													</div>
												</td>
												<td style="width: 50%;">
													<div class="col-md-6 col-xl-6 col-sm-6 col-lg-6">
														<table>
														    <thead>
														    	<tr>
														    		<td colspan="3"><b><h2>Language Details</h2></b></td>
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
																		<td colspan="3" class="text-center">No Data Found!!</td>
																	</tr>
																	<?php
																}
																?>   
														    </tbody>
														</table> 
													</div>
												</td>
											</tr>
										</table>
									</div><br>

<!-- ------------------------------ For Work Experience -------------------------------------- -->

									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-striped table-hover table-bordered unit_table_class" id="country_table_id">
											    <thead>
											    	<tr>
											    		<td colspan="7"><b><h2>Work Experience</h2></b></td>
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