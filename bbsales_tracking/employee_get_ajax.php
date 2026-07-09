<?php
$page_id=554;$page_slug='page_employee';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "emp_personal_info";
$ctable1 	= "Employee Personal Information";
$etable		=	"employee";
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
                        emp_code  LIKE '%".$db->clean($_REQUEST['searchName'])."%' OR
                        first_name  LIKE '%".$db->clean($_REQUEST['searchName'])."%' 
						) AND ";
}

$ctable_where .= " isDelete=0";
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " AND adate <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND adate >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST['department']) && $_REQUEST['department']!=""){
	$ctable_r = $db->rp_getData("emp_company_info","emp_id","isDelete=0 AND department='".$_REQUEST['department']."'","",0);
	$emp_id=array();
	 if(mysqli_num_rows($ctable_r)>0){
				 while($ctable_d = mysqli_fetch_array($ctable_r)){
					$emp_id[]=$ctable_d['emp_id'];
				}
				$emp_id=implode(",",$emp_id);
				$ctable_where .= " AND (
								id IN (".$emp_id.")
							) ";
	 }
	 else{
		 $emp_id=0;
		 $ctable_where .= " (
								id IN (".$emp_id.")
							) AND ";
	 }

}
if(isset($_REQUEST['designation']) && $_REQUEST['designation']!=""){
	$ctable_r = $db->rp_getData("emp_company_info","emp_id","isDelete=0 AND designation='".$_REQUEST['designation']."'","",0);
	$emp_id=array();
	 if(mysqli_num_rows($ctable_r)>0){
				 while($ctable_d = mysqli_fetch_array($ctable_r)){
					$emp_id[]=$ctable_d['emp_id'];
				}
				$emp_id=implode(",",$emp_id);
				$ctable_where .= " AND (
								id IN (".$emp_id.")
							) ";
	 }
	 else{
		 $emp_id=0;
		 $ctable_where .= " (
								id IN (".$emp_id.")
							) AND ";
	 }

}

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getDataByRights($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Employee Code</th>
				<th>Name</th>
				<th>Phone</th>				
				<th>Designation</th>				
				<th>Department</th>				
				<th>Image</th>				
				<th>Created Date</th>				
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
		if($ctable_r){
			$count = 0;
        if(mysqli_num_rows($ctable_r)>0){
            
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
				 $date = date("d-m-Y H:i:s", strtotime($ctable_d['adate']));
			$emp_designation=$db->rp_getValue("emp_company_info","designation","emp_id='".$ctable_d['id']."'",0);
			$emp_department=$db->rp_getValue("emp_company_info","department","emp_id='".$ctable_d['id']."'",0);
              
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['emp_code']); ?></span></td>
				<td><?php echo stripslashes($ctable_d['first_name']); ?></td>
				<td><?php echo stripslashes($ctable_d['phone']); ?></td>
				<td><?php echo $db->rp_getValue("designation","name","id='".$emp_designation."'",0); ?></td>
				<td><?php echo $db->rp_getValue("department","name","id='".$emp_department."'",0); ?></td>
				<td>
				<?php
					if(!empty($ctable_d['image'])){
								
					$img_path="../images/employee/".$ctable_d['image']; 						
					}
					else{
						$img_path="../images/noimage.png";
					}
				?>
				<img src="<?php echo $img_path; ?>" height="50" width="100"></td>
				<td><?php echo stripslashes($date); ?></td>
			
                <td>
                <?php 				
					if($rights['update_flag']==1)
					{
						?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $etable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php
					}
					if($rights['delete_flag']==1)
					{
						?>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
						<?php
					}
					?>
						
				<div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<ul role="menu" class="dropdown-menu">
					
						<li>
						<?php
						if($rights['update_flag']==1)
				      	{
						
							if($ctable_d['isActive']==0){
							?>
								<a  href="<?php echo $etable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Activate</span></a>
							<?php
							}else{
							?>
								<a  href="<?php echo $etable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger" ><i class="fa fa-circle-o"></i> &nbsp; Deactivate </span></a>
							<?php
							}
						}
							?>
						</li>
						<?php
						if($rights['view_flag']==1)
				      	{ ?>
						<li>
							<a  href="#empInfoView" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Employee"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;View Employee</span></a>
							<a  href="#empSalaryView" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Salary Info"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;View Salary</span></a>
							<a download href="..\images\employee\document\<?php echo stripslashes($ctable_d['proof_document']); ?>" title="Download ID Proof"><span class="text-success"><i class="fa fa-download"></i> &nbsp;IdentificationProof</span></a>

						</li>
						<?php
							}
						?>

					</ul>
				</div>
					
				
				
                </td>
            </tr>
        <?php
            }
        }
		
		}
        ?>
        </tbody>
    </table>
    <div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
</form>
<?php require_once 'disconnect.php';  ?>
