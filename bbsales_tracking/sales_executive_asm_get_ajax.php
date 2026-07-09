<?php
$page_id=556;$page_slug='page_sales_executive';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "sales_executive";
$ctable1 	= "Sales Officer";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							name like '%".$db->clean($_REQUEST['searchName'])."%'
							OR email like '%".$db->clean($_REQUEST['searchName'])."%'
							OR phone  LIKE '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}

$ctable_where .= " isDelete=0 AND type='area_sales_manager'";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
if(isset($_REQUEST['class_name']) && $_REQUEST['class_name']!="" && $_REQUEST['class_name']!=NULL)
{
	 $ctable_where .= " AND class_id = '".$_REQUEST['class_name']."'";
}
if(isset($_REQUEST['area']) && $_REQUEST['area']!="" && $_REQUEST['area']!=NULL)
{
	$executive_id=array();
	$ctable_area = "class_id=".$_REQUEST['class_name']." AND area_id = '".$_REQUEST['area']."' AND executive_type='area_sales_manager' AND isDelete=0";
	$area_list=$db->rp_getData("sales_executive_map_area","*",$ctable_area,"",0);
	while($area_list_d=mysqli_fetch_assoc($area_list))
	{
		$executive_id[]=$area_list_d['sales_executive_id'];
	}
	$ids=implode(",",$executive_id);
	$ctable_where .= " AND id IN (".$ids.")";
}
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND type = '".$_REQUEST['status']."' ";
}
if(isset($_REQUEST['state']) && $_REQUEST['state']!="" && $_REQUEST['state']!=NULL)
{
 $ctable_where .= " AND state = '".$_REQUEST['state']."' ";
}
if(isset($_REQUEST['city']) && $_REQUEST['city']!="" && $_REQUEST['city']!=NULL)
{
 $ctable_where .= " AND city = '".$_REQUEST['city']."' AND state='".$_REQUEST['state']."'";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where,0); //hold total records in variable

//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_asm" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Name</th>
				<th>Email</th>
				<th>Phone</th>	
				<th>City</th>	
				<th>State</th>	
				<th>Sales Officer Type</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
            if($ctable_d['type']=="sales_manager"){
				$type="Regional Sales Manager";
			}
			else if($ctable_d['type']=="area_sales_manager"){
				$type="Business Development Manager";
			}
			else if($ctable_d['type']=="sales_officer"){
				$type="Area Sales Manager";
			}
			else{
				$type="Sales Officer";
			}
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['name']); ?></span></td>
				<td><?php echo stripslashes($ctable_d['email']); ?></td>
				<td><?php echo stripslashes($ctable_d['phone']); ?></td>
				<td><?php echo  stripslashes($ctable_d['city']); ?></td>
				<td><?php echo  stripslashes($ctable_d['state']); ?></td>
				<td><?php echo stripslashes($type); ?></td>
				<td>				
				<?php 
					if($rights['update_flag']==1)
					{
						$type=$ctable_d['type'];
						if($type=='sales_manager')
						{?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='sales_executive_crud.php?mode=edit&type=sales_manager&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php 
						} 
						
						else if($type=='area_sales_manager')
						{?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='sales_executive_crud.php?mode=edit&type=area_sales_manager&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php 
						} 
						else if($type=='sales_officer')
						{?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='sales_executive_crud.php?mode=edit&type=sales_officer&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php 
						} 
						else
						{?>
						<a class="btn btn-info btn-sm" onClick="window.location.href='sales_executive_crud.php?mode=edit&type=sales_executive&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
						<?php 
						}
					}
					if($rights['delete_flag']==1)
					{
						?>
						<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
						<?php
					}
					if($rights['update_flag']==1)
					{
						?>
						
				<div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<a class="btn btn-info btn-sm" href="#changePasswordModal" data-id="<?php echo $ctable_d['id']; ?>" class="btn sbold blue-ebonyclay" data-toggle="modal" title="Change Password"> <i class="fa fa-gear"></i>Change Password</a>
					<ul role="menu" class="dropdown-menu">
						<li>
							
							<?php
							if($ctable_d['isActive']==1){
							?>
								<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger"><i class="fa fa-circle"></i> &nbsp;Deactivate</span></a>
							<?php
							}else{
							?>
								<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Aactivate"><span class="text-success" ><i class="fa fa-circle-o"></i> &nbsp; Activate </span></a>
							<?php
							}
							?>
						</li>
						
						<li>
							<a href="#myModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Super Stockist"><span class="text-success"><i class="fa fa-circle"></i>&nbsp; View information</span></a>
						</li>
						
					</ul>
				</div>
						<?php
					}
					?>
					<!-- <a href="sales_executive_tracking.php?id=<?php echo $ctable_d['id']?>" class="btn btn-success btn-sm" title="track">Track</a> -->

					<a href="attendance_manage.php?id=<?php echo $ctable_d['id']?>" class="btn btn-success btn-sm" title="track">Attendance</a>
					
					<a href="target_manage.php?sales_id=<?php echo $ctable_d['id']?>" class="btn btn-primary btn-sm" title="track">Target</a>

					<a style="margin-top: 5px;" href="followup.php?mode=followup&sales_id=<?php echo $ctable_d['id']?>" class="btn btn-primary btn-sm" title="track">Followup</a>

					<a style="margin-top: 5px;" href="add_sales_executive_class_area.php?sales_id=<?php echo $ctable_d['id']?>&type=<?php echo $type ?>" class="btn btn-success btn-sm" title="track">Add Class Area</a>
					<?php
            }
        }
		else{
			?>
			<tr>
			<td colspan="8"><p style="text-align:center;">No data available in table</p></td>
			</tr>
			<?php
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
<?php require_once("disconnect.php"); ?>