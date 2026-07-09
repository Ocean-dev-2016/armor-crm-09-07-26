<?php
$page_id=584;$page_slug='reference_media_page';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "reference_media";
$ctable1 	= "Reference Media";

$ctable_where = "";
// Get the total number of rows in the table
$ctable_where .= "isDelete=0";

if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0){
	$ctable_where.=" AND company_id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."'";
}
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="" && $_REQUEST['searchName']!=NULL)
{

 $ctable_where .= " and name like '%".$db->clean($_REQUEST['searchName'])."%'";
}
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;
//echo $_SESSION[SITE_SESS.'_ADMIN_TYPE'];
if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<!--<button type="button" class="btn green-haze excel" name="excel" id="excel" onClick="genReport(this.value);" href="" title="Download XL Report"><i class="fa fa-file-excel-o"></i>Export</button>-->


<form action="" name="frm" id="frm" method="post">
<div  style="overflow-x:auto;" >
	<table id="datatable_1" class="table table-striped table-bordered table-hover table">
        <thead>
            <tr>
				<th></th>
                <th>No.</th>
                <th>Name</th>
				<th>Created Date</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
        ?>
            <tr>
              	<td>
          		<div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> 
						 <i class="fa fa-cog"></i>
					</button>
					<ul role="menu" class="dropdown-menu">
                <?php 				
					if($rights['update_flag']==1)
					{
						?>
						<li>
						<a data-toggle="modal" href="#priority" data-id="<?php echo $ctable_d['id'] ?>" data-name="<?php echo $ctable_d['name'] ?>" title="Edit"><span class="text-info"><i class="fa fa-pencil"></i> Edit</span></a>
						</li>
						<?php
					}
					if($rights['delete_flag']==1)
					{
						?>
						<li>
						<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-trash"></i> Delete</span></a>
						</li>
						<?php
					}
					if($rights['update_flag']==1)
					{
						?>
						<li>
							
							<?php
							if($ctable_d['isActive']==0){
							?>
								<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;Activate</span></a>
							<?php
							}else{
							?>
								<a  href="<?php echo $ctable; ?>_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=0" title="Deactivate"><span class="text-danger" ><i class="fa fa-circle-o"></i> &nbsp; Deactivate </span></a>
							<?php
							}
							?>
						</li>
						<?php
					}
				?>
                </td>
                <td><?php echo ++$count; ?></td>
				<td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['name']); ?></span></td>
				
				<td><?php echo date('d-m-Y',strtotime($ctable_d['adate'])); ?></td>
				
             </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>
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

<div id="print_info1" class="hidden">
	<?php include("visitor_report_print.php");?>
</div>
<?php require_once 'disconnect.php';  ?>