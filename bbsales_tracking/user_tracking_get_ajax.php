<?php
$page_id=529;$page_slug='page_user_tracking';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "activity_log";
$ctable1 	= "User Tracking";
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " (
							table_name like '%".$db->clean($_REQUEST['searchName'])."%'
						) AND ";
}
if($_REQUEST['mode']=="delete"){
	if($rights['delete_flag']!=1)
		{
			$db->rp_location('access_denied.php?msg=delete_access_denied');
		}
	$query=$db->rp_delete("activity_log","isDelete=0",0);
	if($query!=0){
		$db->addSuccessMessage($reply['ack_msg']);
	$db->rp_location("user_tracking_manage.php?msg=Deleted");
	}
	
}
$ctable_where .= " isDelete=0";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
if(isset($_REQUEST['user_id']) && $_REQUEST['user_id']!="")
{
	$ctable_where .= " AND user_id='".$_REQUEST['user_id']."' ";
}
if(isset($_REQUEST['user_type1']) && $_REQUEST['user_type1']!="")
{
	$ctable_where .= " AND user_type='".$_REQUEST['user_type1']."' ";
}
if(isset($_REQUEST['activity_type1']) && $_REQUEST['activity_type1']!="")
{
	$ctable_where .= " AND activity_type='".$_REQUEST['activity_type1']."' ";
}
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
 $ctable_where .= " AND activity_date <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
 $ctable_where .= " AND activity_date >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
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
                <th>User Name</th>
				<th>User Type</th>
				<th>Ref. ID</th>				
				<th>Table Name</th>				
				<th>Activity Type</th>				
				<th>Activity Description</th>				
				<th>Activity Date</th>	
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
				 $activity_date = date("d-m-Y H:i:s", strtotime($ctable_d['activity_date']));
			$user_name=$db->rp_getValue("production_erp","name","id='".$ctable_d['user_id']."'",0);
			$user_type=$db->rp_getValue("admin_type","name","id='".$ctable_d['user_type']."'",0);
              
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo $user_name; ?></span></td>
				<td><?php echo $user_type; ?></td>
				<td><?php echo stripslashes($ctable_d['ref_id']); ?></td>
				<td><?php echo  stripslashes($ctable_d['table_name']); ?></td>
				<td><?php echo stripslashes($ctable_d['activity_type']); ?></td>
				<td><?php echo stripslashes($ctable_d['activity_description']); ?></td>
				<td><?php echo stripslashes($activity_date); ?></td>
            </tr>
        <?php
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
