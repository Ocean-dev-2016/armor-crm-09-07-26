<?php
$page_id=554;$page_slug='page_employee';
include("connect.php");
$emp_id = $_REQUEST['id'];
$ctable_where	= "id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_where1	= "emp_id='".$_REQUEST['id']."' AND isDelete=0";
$ctable_r = $db->rp_getData("emp_personal_info","*",$ctable_where,"",0);
$ctable_c = $db->rp_getData("emp_company_info","*",$ctable_where1,"",0);

?>
<div id="print_info">					
	<div class="row">
	<div class="col-md-12">	
	
		<table id="datatable_2" class="table table-striped table-bordered table-hover">
		<thead>
		<tr><th colspan="4" class="bg-grey"><b>Employee Personal Information-<?php echo $name = $db->rp_getValue("emp_personal_info","first_name","id='".$_REQUEST['id']."' AND isDelete=0",0);  ?></b></th></tr>
		</thead>
		<tbody>
		<?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
			<tr>
				<th>Employee Code </th>
				<td><label><?php echo $ctable_d['emp_code']; ?><label></td>
				<th>First Name</th>
				<td><label><?php echo  $ctable_d['first_name']; ?></label></td>
			</tr>
			
			<tr>
				<th>Middle Name</th>
				<td><label><?php echo  $ctable_d['middle_name']; ?></label></td>
				<th>Last Name</th>
				<td><label><?php echo  $ctable_d['last_name']; ?></label></td>
			</tr>
			<tr>									
				<th>Phone</th>
				<td><label><?php echo $ctable_d['phone']; ?></label></td>
				<th>Other Contact</th>
				<td><label><?php echo $ctable_d['other_contact']; ?></label></td>
			</tr>
			
			<tr>
				<th>Birth Date</th>
				<td><label><?php echo $ctable_d['birth_date']; ?></label></td>
				<th>Permanent Address</th>
				<td><label><?php echo $ctable_d['perment_address']; ?></label></td>										
			</tr>
			
			<tr>
				<th>Residential Address </th>
				<td><label><?php echo $ctable_d['residential_address']; ?></label></td>
			
				<th>Identification Proof</th>
				<td><label><?php echo $db->rp_getValue("identification_proof","title","id='".$ctable_d['identification_proof']."'",0); ?></label></td>
			</tr>
			<tr>
				<th>Blood Group</th>
				<td><label><?php echo $ctable_d['blood_group']; ?></label></td>
				<th>Remark</th>
				<td><label><?php echo $ctable_d['remark']; ?></label></td>
			</tr>
			<?php
					if(empty($ctable_d['image'])){
						$img_path="../images/noimage.png";						
					}
					else{
						$img_path="../images/employee/".$ctable_d['image']; 
					}
				?>
			<tr>
				<th>Image</th>
				<td><label><img src="<?php echo $img_path; ?>" height="80px" width="80px"></label></td>
				<th></th>
				<td></td>
			</tr>
			<?php
            }
        }
        ?>
	<tbody>	
	</table>
	</div>
	</div>
	<div class="row">
	<div class="col-sm-12">
		<table id="datatable_2" class="table table-striped table-bordered table-hover">
		<thead>
		<tr><th colspan="6" class="bg-grey">Employee Company Information</th></tr>
            <tr>
                <th>Designation.</th>
                <th>Department</th>
				<th>Shift</th>
				<th>Joining Date</th>				
				<th>Account Number</th>				
				<th>Bank Name</th>	
            </tr>
        </thead>
        <tbody>
		<?php
        if(mysqli_num_rows($ctable_c)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_c)){
                $count++;
        ?>
			<tr>
				<td><label><?php echo $db->rp_getValue("designation","name","id='".$ctable_d['designation']."'",0); ?></label></td>
				<td><label><?php echo  $db->rp_getValue("department","name","id='".$ctable_d['department']."'",0); ?></label></td>
				<td><label><?php echo  $db->rp_getValue("working_shift","name","id='".$ctable_d['shift']."'",0); ?></label></td>
				<td><label><?php echo  $ctable_d['joining_date']; ?></label></td>
				<td><label><?php echo $ctable_d['account_number']; ?></label></td>
				<td><label><?php echo $ctable_d['bank_name']; ?></label></td>
			</tr>
			
			<?php
            }
        }
        ?>
		</tbody>
	</table>
	</div>
	</div>
</div>
	<div class="row">
		<div class="col-md-2">
			<a onClick="printPDF()" class="btn btn-info" title="print"><i class="fa fa-print"></i>Print</a>
		</div>
		<div class="col-md-2">
			<a class="btn btn-info" onClick="genReport('<?php echo $emp_id; ?>')" title="Edit"><i class="fa fa-file-pdf-o"></i>Export</a>
		</div>
	</div>
	<?php require_once 'disconnect.php';  ?>
