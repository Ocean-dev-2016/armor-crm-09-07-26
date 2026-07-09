<?php
/*
 * @author Ravi Patel
 */
$page_id=562;$page_slug='page_category';
include("connect.php");
$ctable = "invoice";
$ctable1 = "Invoice";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " invoice_no like '%".$_REQUEST['searchName']."%' AND ";
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
			<th></th>
			<th>No.</th>
			<th>Invoice No.</th>
			<th>Customer Name</th>
			<th>Amount</th>
			<th>Remark</th>
			<th>Invoice Date</th>
			<th>Image</th>
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
			<td>
			<div class="btn-group">             
            <button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle">
                <i class="fa fa-gear"></i>
            </button>
			  <ul role="menu" class="dropdown-menu">
                <li>		
					<a class="btn btn-info btn-sm" onClick="window.location.href='invoice_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				</li>
				<li>	
					<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				</li>
			  </ul>
			</div>		
			</td>
			<td><?php echo $count; ?></td>
			<td><?php echo stripslashes($ctable_d['invoice_no']);?></td>
			<td><?php echo $db->rp_getValue("executive","company_name","id='".$ctable_d['customer_id']."'"); ?></td>
			<td><?php echo stripslashes($ctable_d['amount']); ?></td>
			<td><?php echo stripslashes($ctable_d['remark']); ?></td>
			<td><?php echo date('d-m-Y',strtotime($ctable_d['invoice_date'])); ?></td>
			<td>
				<?php
				if($ctable_d['image_path']!="" && file_exists(INVOICE_A.$ctable_d['image_path'])){
				?>
					<img src="<?php echo INVOICE_A.$ctable_d['image_path']; ?>" width="50" />
				<?php
				}else{
					echo "No Image Available.";
				}
				?>
			</td>
			<!-- <td>
			<a class="btn btn-info btn-sm" onClick="window.location.href='invoice_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
			<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
			</td> -->
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
	<?php require_once 'disconnect.php';  ?>			