<?php
$page_id=569;$page_slug='dispatch_pages';
/*
 * @author Ravi Patel
 */
include("connect.php");
$ctable 	= "salesexecutive_tracking";
$ctable1 	= "Sales Officer Tracking";
$sales_id=$_REQUEST['sales_id'];
$ctable_where = "";
// Get the total number of rows in the table

//for admin login


$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 100;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}
//status
$ctable_where .="sales_executive_id=".$sales_id." AND isDelete=0";
if(isset($_REQUEST['month_id']) && $_REQUEST['month_id']!="" && $_REQUEST['month_id']!=NULL)
{
 $ctable_where .= " AND MONTH(date) = '".$_REQUEST['month_id']."'";
 $ctable_where_month .= " AND MONTH(date) = '".$_REQUEST['month_id']."'";
}
else{
	 $date = date("m");
	 $ctable_where .= " AND MONTH(date)='".$date."' ";
}
////filter by Year
if(isset($_REQUEST['year_id']) && $_REQUEST['year_id']!="" && $_REQUEST['year_id']!=NULL)
{

 $ctable_where .= " AND YEAR(date) = '".$_REQUEST['year_id']."'";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"date,sales_executive_id",$ctable_where,"date DESC",0);
?>
<form action="" name="frm" id="frm" method="post">
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Date</th>
				<th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0){
            $count = 0;
			$previous_date="";
            while($ctable_d = mysqli_fetch_array($ctable_r)){
				$current_date=date("Y-m-d",strtotime($ctable_d['date']));
				if($previous_date==$current_date)
				{
					continue;
				}
				else{
					$previous_date=$current_date;
				}
        ?>
		
            <tr>
                <td><?php echo ++$count; ?></td>
                <td><?php echo date('d-m-Y',strtotime($ctable_d['date'])); ?></td>
                
             <td>
			 <a target="_blank" href="tracking_all.php?id=<?php echo $ctable_d['sales_executive_id']?>&date=<?php echo $ctable_d['date'];?>" class="btn btn-success btn-sm" title="track">Show Track</a>
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
<?php require_once("disconnect.php"); ?>