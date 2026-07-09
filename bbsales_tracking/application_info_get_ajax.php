<?php
/*
 * @author Ravi Patel
 */
$page_id=562;$page_slug='page_category';
include("connect.php");
$ctable = "application_info";
$ctable1 = "Application Info";
$type=array("android_customer"=>"Android Customer","android_sales"=>"Android Sales","ios_customer"=>"IOS Customer","ios_sales"=>"IOS Sales");
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " version_name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " 1=1 AND isDelete='0' AND id!='0'";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 10;

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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page");
?>

<table id="example1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th>No.</th>
			<th>Version Name</th>
			<th>Version Code</th>
			<th>Type</th>
			<th>Apk File</th>
			<th>Action</th>
		</tr>
	</thead>
	<tbody>
	<?php
	
	$last_added=$db->rp_getValue($ctable,"MAX(id)","isDelete=0");
	$last_insert_status=$db->rp_getValue($ctable,"isActive","isDelete=0 AND isActive=1",0);
	if(mysqli_num_rows($ctable_r)>0){
		$count = 0;
		
		while($ctable_d = mysqli_fetch_array($ctable_r)){
			$count++;
			
	?>
		<tr>
			<td><?php echo $ctable_d['id']; ?></td>
			<td><?php echo stripslashes($ctable_d['version_name']);?></td>
			<td><?php echo stripslashes($ctable_d['version_code']);  ?></td>
			<td><?php echo stripslashes($type[$ctable_d['type']]);  ?></td>
			<td><?php echo stripslashes($ctable_d['file']);  ?><br/><br/>
				<?php 
				// if($last_insert_status==$ctable_d['isActive']){
				?>
				<b>Download URL - <a style="text-decoration: underline;" href="<?= SITEURL ?>apk/<?= $ctable_d['file']; ?>"><?= SITEURL ?>apk/<?= $ctable_d['file']; ?></a></b>
				<?php 
				// }
				?>	
			</td>
			<td>
			<a class="btn btn-info btn-sm" onClick="window.location.href='application_info_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
			<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
			
			<?php if($last_added==$ctable_d['id'] && $last_insert_status!=$ctable_d['isActive']){ ?>
			<a  href="application_info_crud.php?mode=isActive&id=<?php echo $ctable_d['id']; ?>&status=1" title="Activate" class='btn btn-success'>Activate</a>
		<?php }
		  
		if($last_insert_status==$ctable_d['isActive']){
		?>
			<a  class='btn btn-success' disabled>Activated</a>
			<?php } ?>
			</td>
		</tr>
	<?php
		}
	}
	else
	{?>
		<tr>
		<td colspan="6" align="center"> No Data Available</td>
		</tr>
	<?php }
	?>
	</tbody>
	</table>
	<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="10" <?php if ($_REQUEST["show"] == 10 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >10</option>
					<option value="20" <?php if ($_REQUEST["show"] == 20) { echo ' selected="selected"'; }  ?> >20</option>
					<option value="30" <?php if ($_REQUEST["show"] == 30) { echo ' selected="selected"'; }  ?> >30</option>
				</select>
			</div>
		</div>
	</div>	
	<?php require_once 'disconnect.php';  ?>					