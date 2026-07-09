<?php
/*
 * @author Ravi Patel
 */
$page_id=618;$page_slug='hsncode_master';
include("connect.php");
$ctable = "hsncode_master";
// $ctable = "category_master";
$ctable1 = "Category";

// echo "<pre>"; print_r($ctable1); exit;
$ctable_where = "";
// Get the total number of rows in the table

if (isset($_REQUEST['searchName']) && $_REQUEST['searchName'] != "") {
    $ctable_where .= " (name like '%" . $_REQUEST['searchName'] . "%')  ";


	$where11="";
	$pro_r1=$db->rp_getData("tax","id","name LIKE '%".$_REQUEST['searchName']."%' AND isDelete=0","",0);
	$PROIDS1=array();
	if($pro_r1)
	{
		while($pro_d1=mysqli_fetch_assoc($pro_r1))
		{
			$PROIDS1[]=$pro_d1['id'];
			
		}
	}
	else{
		$ctable_where = " (name like '%" . $_REQUEST['searchName'] . "%') AND isDelete='0'";
	}
	if(!empty($PROIDS1))
	{

		$PROIDS1=implode(",", $PROIDS1);
		// echo $PROIDS1; exit;
		$where11=" OR tax_id IN (".$PROIDS1.") AND isDelete='0' ";
		$ctable_where .= $where11;
		
	}
		
}
else {
	$ctable_where .= "  isDelete='0' ";
}


if(isset($_REQUEST['top_category_id']) && $_REQUEST['top_category_id']!="" && $_REQUEST['top_category_id']!=NULL && $_REQUEST['top_category_id']!=undefined)
{
 $ctable_where .= " AND tax_id = '".$_REQUEST['top_category_id']."' ";
}



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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>

<table id="example1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th></th>
			<th></th>	
			<th></th>
			<th>
				<select class="form-control input-medium status" name="top_category_id" id="top_category_id">
					<option value="">Select Tax Category</option>
					 <?php 
						$top_category_list_r=$db->rp_getData('tax',"*","isDelete=0","",0);
						while($top_category_list_d=mysqli_fetch_assoc($top_category_list_r))
						{
							?>
							<option <?php echo ($_REQUEST['top_category_id']==$top_category_list_d['id'])?"selected":"" ; ?> value="<?php echo $top_category_list_d['id']?>">
							<?php echo $top_category_list_d['name'];?>
							</option>
							<?php
						}
					?>
				</select>
			</th>
			<!-- <th></th> -->
		</tr>
		<tr>
			<th style="width: 5%;"></th>
			<th style="width: 5%;">No.</th>
			<th>Name</th>
			<th>Tax Category</th>
			<!-- <th>Image</th> -->
			<!-- <th>Action</th> -->
		</tr>
	</thead>
	<tbody>
	<?php
	if(mysqli_num_rows($ctable_r)>0){
		$count = 0;
		
		while($ctable_d = mysqli_fetch_array($ctable_r)){
			$count++;
	?>
		<tr>
			<td><?php $ctable_d['id']; ?>				
				<?php				
				if($rights['update_flag']==1)
				{
					?>
					<div class="btn-group">				
						<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
							<i class="fa fa-gear"></i>
						</button>
						<ul role="menu" class="dropdown-menu">
							<li>
								<a href="hsncode_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit">
									<span class="text-primary">
										<i class="fa fa-pencil"></i>
										&nbsp;Edit
									</span>
								</a>
							</li>
							<?php
							if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
							{
								?>
								<li>
									<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete">
										<span class="text-danger">
											<i class="fa fa-times"></i>
											&nbsp;Delete
										</span>
									</a>
								</li>
								<?php
							}
							?>	
						</ul>
					</div>
					<?php
				}
				?>
			</td>
			<td><?php echo $count; ?></td>
			<td>
			<?php 
			echo stripslashes($ctable_d['name']); 
			?>
			</td>
			<td>
			<?php 
			echo $db->rp_getValue("tax","name","id='".$ctable_d['tax_id']."'"); 
			?>
			</td>
			
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
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {
											echo ' selected="selected"';
										}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {
											echo ' selected="selected"';
										}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {
												echo ' selected="selected"';
											}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {
												echo ' selected="selected"';
											}  ?>>5000</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12">
			<br>
			<!-- <?php
				echo $db->getAddButton("category");
			?> -->
		</div>
	</div>						
<script type="text/javascript">
	$("#top_category_id").select2(); 
</script>
<?php require_once 'disconnect.php';  ?>