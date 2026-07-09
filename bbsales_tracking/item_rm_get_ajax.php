<?php
$page_id=430;$page_slug='page_item';
include("connect.php");
$ctable = "item_rm";
$ctable1 = "Finish Good Items";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
$ctable_where .= " (
				rm_item_name like '%".$_REQUEST['searchName']."%'	OR rm_item_code like '%".$_REQUEST['searchName']."%'					
			) AND ";
}
$ctable_where .= " isDelete=0";
if(isset($_REQUEST['category']) && $_REQUEST['category']!="" && $_REQUEST['category']!=NULL)
{
 $ctable_where .= " AND rm_item_category = '".$_REQUEST['category']."' ";
}

if(isset($_REQUEST['pakaging']) && $_REQUEST['pakaging']!="" && $_REQUEST['pakaging']!=NULL)
{
 $ctable_where .= " AND rm_packaging_type = '".$_REQUEST['pakaging']."' ";
}
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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_fg_item_master" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Row Material Name</th>
                <th>Row Material Code</th>
                <th>Row Material Category</th>
                <th>Action</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if($ctable_r){
            $count = 0;
            
            while($ctable_d = mysqli_fetch_array($ctable_r)){
                $count++;
				/*$bom_id=$db->rp_getValue("bom_info","id","item_id='".$ctable_d['id']."' AND item_type='row_material'");
				if($bom_id!="" && $bom_id!=0){
					$bom_rediret_url="bom_item_rm_crud.php?mode=edit&id=".$bom_id;
				}
				else{
					$bom_rediret_url="bom_item_rm_crud.php?mode=add&id=".$ctable_d['id'];
				}*/
				
        ?>
            <tr>
                <td><?php echo $count; ?></td>
                <td><span class="<?php echo ($ctable_d['isActive']==0)?"text-danger":"text-success"; ?>"><?php echo stripslashes($ctable_d['rm_item_name']); ?></span></td>
                <td><?php echo stripslashes($ctable_d['rm_item_code']); ?></td>
                <td><?php echo $db->rp_getValue("item_rm_category","item_rm_category_name","id='".$ctable_d['rm_item_category']."' AND isDelete=0"); ?></td>
                <td>
                <a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
				<!--<a class="btn btn-success btn-sm" href="<?php //echo $bom_rediret_url; ?>" title="Delete">BOM</a>-->
				
				
                </td>
            </tr>
        <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div class="row">
		<div class="col-md-1">
			<div class="dataTables_info">
				<label >Rows Limit:</label>
				<select id="numRecords" class="form-control" onChange="changeDisplayRowCount(this.value);">
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