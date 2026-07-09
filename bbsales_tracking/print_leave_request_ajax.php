<?php
$page_id=589;$page_slug='customer_type';
include("connect.php");
$ctable 	= "leave_request";
$ctable1 	= "Leave Request";

$ctable_where = "";
// Get the total number of rows in the table

if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!=""){
	$ctable_where .= " sales_executive_name like '%".$_REQUEST['searchName']."%' AND ";
}

$ctable_where .= " isDelete='0' AND id!='0'";
// $month=date("Y-m-d");
// $ctable_where .= " isDelete='0' AND id!='0' AND Date(start_date) = '".$month."'";

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 10;

if(isset($_REQUEST["page"])){
	$page_number = filter_var($_REQUEST["page"], FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_STRIP_HIGH); //filter number
	if(!is_numeric($page_number)){die('Invalid page number!');} //incase of invalid page number
}else{
	$page_number = 1; //if there's no page number, set it to 1
}

if(isset($_REQUEST["leave_type"]) && $_REQUEST["leave_type"]!="" && $_REQUEST["leave_type"]!=undefined){
	$ctable_where .= " AND leave_type='".$_REQUEST["leave_type"]."'";
}
if(isset($_REQUEST["leave_category"]) && $_REQUEST["leave_category"]!="" && $_REQUEST["leave_category"]!=undefined){
	$ctable_where .= " AND leave_category='".$_REQUEST["leave_category"]."'";
}
if(isset($_REQUEST["status"]) && $_REQUEST["status"]!="" && $_REQUEST["status"]!=undefined){
	if($_REQUEST['status']==-1)
	{
		$ctable_where .= " AND status='0'";
	}
	else
	{
		$ctable_where .= " AND status='".$_REQUEST["status"]."'";
	}
}
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
	//$ctable_where .= " AND ( DATE(start_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(start_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<style>
.mainDiv, table{
  height: auto;   
  width:100%;
  font-family: Calibri,sans-serif;
  font-style: normal;
  font-weight: 400;
  padding: 0;
  text-decoration: none;
  font-size: 10pt;
  margin:auto;
  padding:auto;
}
tr{height: 30px;}
table , td, th { border-collapse: collapse;border: 1px solid #000;}
td, th {  padding: 5px;}
th { border: 1px solid #595959;background: #f0e6cc;}
.text-right{text-align: right;}
.center{text-align:center;}
.space{ padding: 10px;}
.no-border{border-bottom: 1px solid #fff;}
h2
{
	text-transform: uppercase;
	margin-bottom: 0px;
}
</style>
<table id="example1" class="table table-bordered table-striped dataTable">
	<thead>
		<tr>
			<th colspan="12" class="center">
				<h2>Leave Request Report <?= date("d-m-Y h:i a");?> Printed By : <?=$_SESSION[SITE_SESS.'SESS_NAME'];?></h2>
			</th>
	    </tr>
		<tr>
			<!-- <th></th> -->
			<th>Sr No.</th>
			<th>Sales Officer Name</th>
			<th>Leave Type</th>
			<th>Leave Request Created Date</th>
			<th>From Date & Time</th>
			<th>To Date & Time</th>
			<th>Leave Category</th>
			<th>Total Days Of Leave</th>
			<th>Cancel/Reject Reason</th>
			<th>Status</th> 
			<th>Entry Type</th>
		  <!-- <th>Update Entry Type</th> -->
		</tr>
	</thead>
	<tbody>
	<?php
	if($ctable_r){
		$count = 0;
		$entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
		
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			$count++;
			
			$datetime1 = new DateTime($ctable_d['end_date']."".$ctable_d['end_time']);
			$datetime2 = new DateTime($ctable_d['start_date']."".$ctable_d['start_time']);
			$interval = $datetime1->diff($datetime2);
			$elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
			
	?>
		<tr>
			<!-- <td><?php $ctable_d['id']; ?>				
			<?php				
			if($rights['update_flag']==1)
			{
			?>	
				<div class="btn-group">				
					<button aria-expanded="false" data-toggle="dropdown" type="button" class="btn btn-sm blue dropdown-toggle"> <i class="fa fa-gear"></i>
					</button>
					<ul role="menu" class="dropdown-menu">
						<li>
							<a  href="<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>" title="Edit"><span class="text-primary"><i class="fa fa-pencil"></i> &nbsp;Edit</span></a>
						</li>
						<?php
						if($rights['delete_flag']==1 && $ctable_d['id']!=-1)
						{
						?>
						<li>
							<a  onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><span class="text-danger"><i class="fa fa-times"></i> &nbsp;Delete</span></a>
						</li>
						<?php
						}
						?>	
					</ul>
				</div>
			<?php
			}
			?>
			</td> -->
			<td><?php echo $count; ?></td>
			<td><?php echo stripslashes($ctable_d['sales_executive_name']); ?></td>
			<td><?php echo $db->rp_getValue("leave_type","name","id='".$ctable_d['leave_type']."'"); ?></td>
			<td><?php echo date("d-m-Y",strtotime($ctable_d['created_date'])); ?></td>
			<td><?php echo "<b>From Date: </b>".date("d-m-Y",strtotime($ctable_d['start_date']))."<br/><b>From Time:</b> " . date("h:i a",strtotime($ctable_d['start_time'])); ?></td>
		    <td><?php echo "<b>To Date: </b>".date("d-m-Y",strtotime($ctable_d['end_date']))."<br/><b>To Time:</b> " . date("h:i a",strtotime($ctable_d['end_time'])); ?></td>
		    <td>
			    	<?php
			    	if($ctable_d['leave_category']==1)
						{
							echo "First Half";
						}
						else if($ctable_d['leave_category']==2)
						{
							echo "Second Half" ;	
						}
						else if($ctable_d['leave_category']==3)
						{
							echo "Full Day";	
						}
						?>
			    </td>
			<td><?php echo $elapsed; ?></td>
			<td><?= $ctable_d['cancel_reject_reason'] ?></td>
			<td>
				<?php
					if($ctable_d['status']==1)
					{
						echo "ACCEPTED";
					}
					else if($ctable_d['status']==2)
					{
						echo "REJECTED";	
					}
					else if($ctable_d['status']==3)
					{
						echo "CANCEL";	
					} 
					else if ($ctable_d['status']==0) {
						echo "GENERATED";	
					}
				?>
			</td>
			  <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
				<!-- <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
			<!-- <td>
			<a class="btn btn-info btn-sm" onClick="window.location.href='<?php echo $ctable; ?>_crud.php?mode=edit&id=<?php echo $ctable_d['id']; ?>'" title="Edit"><i class="fa fa-pencil"></i></a>
			<a class="btn btn-danger btn-sm" onClick="del_conf('<?php echo $ctable_d['id']; ?>');" title="Delete"><i class="fa fa-times"></i></a>
			</td> -->
		</tr>
	<?php
		}
	}
	else
	{
		?>
		<tr>
			<td colspan="10"; align = "center";>No Data Found!!</td>
		</tr>
		<?php
	}
	?>
	</tbody>
	</table>
	<?php include("disconnect.php"); ?>
	