<?php
$page_id=444;$page_slug='page_dispatch';
include("connect.php");
$ctable = "dispatch_detail";
$status=array("0"=>"Ready to dispatch","1"=>"On the way","2"=>"Delivered");
$ctable_where = "";
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	
		$ctable_where .= " (
							customer_name LIKE '%".$_REQUEST['searchName']."%' 
						) AND ";
	
	
}

$ctable_where .= " isDelete=0 AND isActive=1 ";

//status
/* if(isset($_REQUEST['status']) && $_REQUEST['status']!="" && $_REQUEST['status']!=NULL)
{
	$ctable_where .= "  AND status = '".$_REQUEST['status']."'";
} */
///For ToDate & FromDate


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
	<table id="datatable1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
				<th>Dispatch No.</th>
                <th>Billing Person Name</th>
				<!--th>PinCode</th-->
				<th>Dispatch Date.</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
        </thead>
        <tbody>
			<?php
        if($ctable_r){
            $count=0;
            while($ctable_d = mysqli_fetch_array($ctable_r)){
				
				$count++;
				/* $dispatch_date=($db->parseDate($ctable_d['dispatch_date'])!="0000-00-00")?$db->parseDate($ctable_d['dispatch_date']):""; */
				$dispatch_date=($ctable_d['dispatch_date']!="0000-00-00")?$ctable_d['dispatch_date']:"";
				
         ?>
              <tr>
                <td><?php echo $count; ?></td>
                <td><a data-toggle="collapse" data-target="#<?php echo "O".$_REQUEST['date'].$ctable_d['id']; ?>" class="accordion-toggle"  title="view"><?php echo stripslashes($ctable_d['dispatch_no']); ?></a></td>
                <td><?php echo stripslashes($ctable_d['customer_name']); ?></td>
                <!--td><!?php echo stripslashes($ctable_d['dispatch_billing_pincode']); ?></td-->
				<td><?php echo stripslashes($dispatch_date); ?></td>
				<td><?php echo stripslashes($ctable_d['payment_status']); ?></td>
			
                <td>
				<?php 				
				if($rights['view_flag']==1)
				{
				?>
				<a href="#dispatchModal" data-id="<?php echo  stripslashes($ctable_d['id']); ?>" data-toggle="modal" class="btn btn-info btn-sm">View Dispatch</a>
				<?php
				}
				?>
				
				</td>
            </tr>
			<?php 				
				}?>
			
        <?php
            }
           
        ?>
				
			</tbody>
    </table>
    <div class="row">
		<div class="col-md-2">
			<div class="dataTables_info">
				<label >Rows Limit:</label>
				<select id="numRecords" class="form-control dispatch-row-count" onChange="changeDisplayRowCount(this.value);">
					<option value="100" <?php if ($_REQUEST["show"] == 100 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >100</option>
					<option value="500" <?php if ($_REQUEST["show"] == 500) { echo ' selected="selected"'; }  ?> >500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) { echo ' selected="selected"'; }  ?> >1000</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
			<div class="dataTables_paginate paging_simple_numbers">
				<ul class="pagination dispatch-page-count">
				<?php 
				echo $db->rp_paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
				?>
				</ul>
			</div>
		</div>
	</div>
</form>
<?php require_once 'disconnect.php';  ?>