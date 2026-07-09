<?php
$page_id=566;$page_slug='page_order_ajax';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "inward_store";
$ctable1 	= "Inward Store";
$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	
		$ctable_where .= " (
							vendor_name LIKE '%".$_REQUEST['searchName']."%' 
						) AND ";
	
	
}

//for admin login

$ctable_where .="isDelete=0";
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
 $ctable_where .= " AND status = '".$_REQUEST['status']."' ";
}
///For ToDate & FromDate
if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
  $ctable_where .= " AND adate <= '".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
     $ctable_where .= " AND adate >= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['type']) && $_REQUEST['type']!="" && $_REQUEST['type']!=NULL)
{
 $ctable_where .= " AND customer_type = '".$_REQUEST['type']."' ";
}
$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>vendor Name</th>
                <th>Total Qty</th>
                <th style="text-align:right;">Total Amount</th>
				<th>Inward Date</th>
				<!--<th>Remark</th>-->
				<th>Action</th	>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
          
        ?>
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo $ctable_d['vendor_name']; ?></td>
                <td><?php echo stripslashes($ctable_d['total_qty']); ?></td>
                <td align="right"><?php echo stripslashes(CURR.$db->rp_num($ctable_d['grand_total'])); ?></td>
				<td><?php echo date('d-m-Y',strtotime($ctable_d['adate'])); ?></td>
				<!--<td><?php //echo $ctable_d['remark']; ?></td>-->
                
             <td>
			 <?php
			 if($rights['delete_flag']==1)
			{	
			?>
			<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
			<?php
						
			}
			if($rights['update_flag']==1)
			{
				?>
				<a class="btn btn-info btn-sm" onClick="window.location.href='inward_store_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
				<?php 	
			}
			
					?>
                <div class="btn-group">
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm green dropdown-toggle"> 
						 More
					</button>
					<ul role="menu" class="dropdown-menu">
					
						<?php
						if($rights['view_flag']==1)
				      	{ ?>
						<li>
							<a  href="#inward_store_view" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" title="View Inward Store"><span class="text-success"><i class="fa fa-circle"></i> &nbsp;View Inward Store Info</span></a>
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