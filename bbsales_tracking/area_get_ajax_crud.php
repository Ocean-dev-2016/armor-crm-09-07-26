<?php
$page_id=606;$page_slug='page_area';
include("connect.php");
$ctable = "area";
$ctable1 = "Area";
$ctable_where = "";
// Get the total number of rows in the table

$ctable_where .= " isDelete=0 AND class_id='".$_REQUEST['class_id']."' AND city_id='".$_REQUEST['city_id']."'";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"name ASC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_area" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Route Name</th>
                <th>Action</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
        ?>
            <tr>
                <td><?php echo $count; ?></td>
				<td>
					<table>
						<tr>
							<td>
								<span id="lblName<?php echo $ctable_d['id']; ?>" class="lblQk">
									<span><?php echo stripslashes($ctable_d['name']);?>&nbsp;&nbsp;</span>									
								</span>
							</td>
							<td>
							<span id="txtName<?php echo $ctable_d['id']; ?>" class="txtQk" style="display:none;">
									
									
							<div class="input-group">
							  <input type="text" class="form-control" id="name<?php echo $ctable_d['id']; ?>" value="<?php echo stripslashes($ctable_d['name']); ?>">
							  <div class="input-group-btn">
								<button id="btnSave<?php echo $ctable_d['id']; ?>" style="display:none; margin-right:0px" href="javascript:void(0);"  type="button" class="btn btn-primary btnQk" title="Save Quick Edit Branch" onClick="saveQuickEdit('<?php echo $ctable_d['id']; ?>')"><i class="fa fa-floppy-o"></i></button>
								<button id="btnCancel<?php echo $ctable_d['id']; ?>" style="display:none;" href="javascript:void(0);"   type="button" class="btn btn-danger btnQk" title="Cancle Quick Edit Branch"  onClick="cancelQuickEdit('<?php echo $ctable_d['id']; ?>')"><i class="fa fa-times-circle"></i></button>								
							  </div>
							</div>
								
								</span>
							</td>
						</tr>
					</table>
			    	<table>
						<tr>
							<td>
								
							</td>
						</tr>
					</table>
				</td>
				<td>
                <a class="btn btn-primary btn-sm btnQuickEdit" id="btnQuickEdit<?php echo $ctable_d['id']; ?>" href="javascript:void(0);" title="Quick Edit Area" onClick="quickEdit('<?php echo $ctable_d['id']; ?>')"><i class="fa fa-pencil"></i></a>
				<?php
				if($rights['delete_flag']==1)
				{
					?>
					<a onClick="del_conf('<?php echo $ctable_d['id']; ?>');" class='delete btn btn-danger btn-sm'  title='Delete'><i class='fa fa-times'></i></a>
					<?php
				}
				?>
                </td>
            </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div class="row" hidden >
		<div class="col-md-1">
			<div class="dataTables_info">
				<label >Rows Limit:</label>
				<select id="numRecords" class="form-control" onChange="changeDisplayRowCount(this.value);">
					<option value="500" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
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
<?php require_once "disconnect.php"; ?>