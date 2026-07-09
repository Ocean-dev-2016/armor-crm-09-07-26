<?php
$page_id=608;$page_slug='inquiry_cancel_report';
include("connect_in.php"); 
$ctable 	= "executive";
$ctable1 	= "Executive";
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
	$ctable_where .="company_name like '%".$_REQUEST['searchName']."%' OR phone like '%".$_REQUEST['searchName']."%' OR id like '%".$_REQUEST['searchName']."%' OR cname like '%".$_REQUEST['searchName']."%'  AND ";
}

$ctable_where .= " isDelete=0 AND customer_flag=0"; 

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 500;

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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where." group by company_name having count(*) > 1","id DESC limit $page_position, $item_per_page",0);
?> 
<form action="" name="frm" id="frm" method="post">
	<div class="table-responsive">
		<table id="datatable_1" class="table table-striped table-bordered table-hover">
	        <thead>
	            <tr>
	            	<th>Sr No.</th>
	            	<th>Company Name</th>
	                <th>Persoan Name</th>
	                <th>Phone</th>
	                <th>State</th>
					<th>City</th>	
					<th>GST No</th>			
				</tr>
	        </thead>
	        <tbody>
		        <?php
				if(mysqli_num_rows($ctable_r)>0)
		        {
		            $count = 0;
		            while($ctable_d = mysqli_fetch_array($ctable_r))
		            {
		            	?>
			            <tr>
			            	<td><?php echo ++$count; ?></td>
			            	<td><?php echo $ctable_d['company_name']; ?></td>
							<td><?php echo $ctable_d['cname']; ?></td>
							<td><?php echo $ctable_d['phone']; ?></td>
							<td><?php echo $ctable_d['state']; ?></td>
							<td><?php echo $ctable_d['city']; ?></td>
							<td><?php echo $ctable_d['gst']; ?></td>
						</tr>
		        		<?php
		            }
		        }
				else
				{
					?>
					<tr>
						<th colspan="13" style="text-align: center;">No Data Found</th>
					</tr>
					<?php
				}
				?>
			</tbody>
	    </table>
	</div>
</form>
<script type="text/javascript"> 
	$("#totalcount").html('<?php echo $new_count; ?>');
</script>
<?php require_once 'disconnect.php';  ?>