<?php 
$page_id=583;$page_slug='future_followup_manage';
include('connect.php');
include("../include/followup.class.php");
$ctable 	= "followup";
$ctable1 	= "Followup";
$next_action_array=array("1"=>"Next Followup","2"=>"In Future","-1"=>"Followup End");
$ctable_where = "";
// Get the total number of rows in the table

if($_REQUEST['followup_flag']=="inquiry_followup")
{
    $ctable_where .= "reference_id = '".$_REQUEST['inquiry_id']."' AND isDelete=0  AND reference_table='no_order_inquiry'";
}
else if($_REQUEST['followup_flag']=="leads_followup")
{
    $ctable_where .= "reference_id = '".$_REQUEST['inquiry_id']."' AND isDelete=0 AND reference_table='customer_inquiry'";
}
else if($_REQUEST['followup_flag']=="customer_followup")
{
    $ctable_where .= "reference_id = '".$_REQUEST['executive_id']."' AND isDelete=0 AND reference_table='executive'";
}
else
{
    $ctable_where .= "visitor_id = '".$_REQUEST['visitor_id']."' AND isDelete=0 ";
}


if(isset($_REQUEST['sales_id']) && $_REQUEST['sales_id']!="" && $_REQUEST['sales_id']!=NULL)
{
	$ctable_where .= " And visitor_id='".$_REQUEST['sales_id']."'";
}

if(isset($_REQUEST['ToDate']) && $_REQUEST['ToDate']!="" && $_REQUEST['ToDate']!=NULL)
{
	$ctable_where .= " And DATE(adate)<='".date_format(date_create($_REQUEST['ToDate']),"Y-m-d")."' ";
}

if(isset($_REQUEST['FromDate']) && $_REQUEST['FromDate']!="" && $_REQUEST['FromDate']!=NULL)
{
	$ctable_where .= " AND DATE(adate)>= '".date_format(date_create($_REQUEST['FromDate']),"Y-m-d")."' ";
}
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="" && $_REQUEST['searchName']!=NULL)
{
	$ctable_where .= " and name like '%".$db->clean($_REQUEST['searchName'])."%'";
}
$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 10;
//echo $_SESSION[SITE_SESS.'_ADMIN_TYPE'];
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

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"followup_date DESC limit $page_position, $item_per_page",0);

$ObjFollowup = new  Followup();
$reply=$ObjFollowup->GetFollowupContent();	
$icons=array(2=>"icon-speech",1=>" icon-screen-smartphone",3=>"icon-envelope");
?>
<form action="" name="frm" id="frm" method="post">
	<div class="mt-timeline-2">
			<div class="mt-timeline-line border-grey-steel"></div>
			<ul class="mt-container">
			
			<?php 
				if($ctable_r)
				{
				   while($Section = mysqli_fetch_assoc($ctable_r))
				   {
						if(date('Y-m-d',strtotime($Section['followup_date'])) < date('Y-m-d') && $Section['status']==0)
						{
							$current_color= "red";
						}
						else if($Section['status']==1)
						{
							$current_color = "purple";
						}
						else
						{
							$current_color = "blue";
						}
						 // $colors=array("blue","red","green","purple");
						 // $current_color=$colors[array_rand($colors)];
						?>
				<li class="mt-item">
					<div class="mt-timeline-icon bg-<?php echo $current_color;?> bg-font-<?php echo $current_color;?> border-grey-steel">
						<i class="<?php echo $icons[$Section['through']];?>"></i>
					</div>
					<div class="mt-timeline-content">
						<div class="mt-content-container bg-<?php echo $current_color;?> bg-font-<?php echo $current_color;?>  border-<?php echo $current_color;?>">
							<div class="mt-title">
								<h3 class="mt-content-title"><?php echo $Section['followup_date']; ?></h3>
							</div>
							<div class="mt-content border-white">
								<p><?php echo $Section['description']; ?></p>
								<?php if($Section['status']==0) { ?>
								<a type="button" href="#FollowupResponse" data-toggle="modal" target="#FollowupResponse" class="btn btn-circle white" data-mode="add" data-date="<?php echo date('d-m-Y H:i:s',strtotime($Section['followup_date'])); ?>" data-id="<?php echo $Section['id']; ?>" data-next_action="<?php echo $Section['next_action']; ?>">Response</a>
								<?php } ?>
							</div>
							<?php if($Section['status']!=0) { ?>
							<div class="mt-title">
								<h4 class="mt-content-title">Action - <?php echo $next_action_array[$Section['next_action']]; ?>
								<a type="button" href="#FollowupResponse" data-mode="edit" class="btn-info btn-sm" target="#FollowupResponse" data-id="<?php echo $Section['id']; ?>" data-response="<?php echo $Section['response']; ?>" data-next_action="<?php echo $Section['next_action']; ?>" data-toggle="modal" title="Edit"><i class="fa fa-edit"></i></a></h4>
							</div>
							<div class="mt-content border-white">
								<p><?php echo $Section['response']; ?></p>
							</div>
							<?php } ?>
						</div>
					</div>
				</li>
				<?php 
						}
				}
				else
				{ ?>
					<h2 align="center">No Any Feeds !!</h2>
				<?php }
				?>
				</ul>
				
			</div>
			<?php 
				if($_REQUEST['sales_id']!="")
				{
					$where = "visitor_id='".$_REQUEST['sales_id']."' AND isDelete=0";
				}
				else
				{
					$where = "isDelete=0";
				}
				$rp=$db->rp_getTotalRecord("followup",$where); 
				if($rp>10) { ?>
				<div align="center"><a data-page="<?php echo $page_number+1;?>" href="javascript:void(0);" class="btn btn-success loadMoreBtn">Load More</a></div>
					
				<?php } ?>
			<div class="row" hidden>
				<div class="col-md-6">
					<div class="dataTables_info"> Rows Limit:
						<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
							<option value="10" <?php if ($_REQUEST["show"] == 10 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >10</option>
							<option value="50" <?php if ($_REQUEST["show"] == 50) { echo ' selected="selected"'; }  ?> >50</option>
							<option value="100" <?php if ($_REQUEST["show"] == 100) { echo ' selected="selected"'; }  ?> >100</option>
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