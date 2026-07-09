<?php
$page_id=5909;$page_slug='manage_notification';
$ctable 	= "notification";
$ctable1 	= "Notification";
$main_page 	= $ctable;
$page 		= "add_".$ctable;
include("connect.php");
$notification_type=array(2=>"Admin Message","1"=>"Expense Notification");
$pending = date('Y-m-d');
$ctable_where = "";
// Get the total number of rows in the table
if(isset($_REQUEST['searchName']) && $_REQUEST['searchName']!="")
{
	$ctable_where .= " (notification_title like '%".$_REQUEST['searchName']."%') AND ";
}

if(isset($_REQUEST['sales']) && $_REQUEST['sales']!="" && $_REQUEST['sales']!=NULL)
{
	$ctable_where .= "user_id ='".$_REQUEST['sales']."' AND ";
}

if($_SESSION['SESS_TYPE']!=0)
{
	//$ctable_where .= "isDelete=0 AND user_id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' AND DATE(respective_date) < '".$pending."'";
	$ctable_where .= "isDelete=0 AND referance_type!='expense' AND user_id='".$_SESSION[SITE_SESS.'_ADMIN_SESS_ID']."' AND DATE(created_date) < '".$pending."'";
}
else
{
    //$ctable_where .= "isDelete=0 AND DATE(respective_date) < '".$pending."'";
    $ctable_where .= "isDelete=0 AND referance_type!='expense' AND DATE(created_date) < '".$pending."'";
}

$item_per_page =  ($_REQUEST["show"] <> "" && is_numeric($_REQUEST["show"]) ) ? intval($_REQUEST["show"]) : 25;

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
	<table id="datatable_1" class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>User Name</th>
				<th>Notification Title</th>
				<th>Description</th>
				<th>Respective Date</th>
				<th>Created Date</th>
				<th>View</th>
			</tr>
        </thead>
        <tbody>
        <?php
        if(mysqli_num_rows($ctable_r)>0)
        {
        	while($ctable_d = mysqli_fetch_array($ctable_r))
        	{
                $count++;
        		?>
            	<tr>
                	<td><?php echo $count; ?></td>
                	<td><?php $username =  $db->rp_getValue("dealer_distributor_network","name","sales_executive_id='".$ctable_d['user_id']."'",0); if($username==""){ echo "Admin";}else{echo $username;}?>
	                </td>
					<td><?php echo stripslashes($ctable_d['notification_title']); ?></td>
					<td><?php echo stripslashes($ctable_d['notification_description']);?></td>
					<td><?php echo stripslashes(date('d-m-Y',strtotime($ctable_d['created_date']))); ?></td>
					<td><?php echo stripslashes(date('d-m-Y',strtotime($ctable_d['created_date']))); ?></td>
					<td>
						<?php
						if($ctable_d['referance_type']=="no_order_inquiry")
						{
							$url = "no_order_inquiry_crud.php?mode=edit&type=0&id=".$ctable_d['referance_id'];
						}
						else if($ctable_d['referance_type']=="invoice_new")
						{
							$url = "invoice_viewer.php?invoice_id=".$ctable_d['referance_id'];
						}
						else if($ctable_d['referance_type']=="dispatch_detail")
						{
							$url = "view_dispatch.php?id=".$ctable_d['referance_id'];
						}
						else if($ctable_d['referance_type']=="orders")
						{
							$url = "order_viewer.php?order_id=".$ctable_d['referance_id'];
						}
						else if($ctable_d['referance_type']=="quotation_detail")
						{
							$url = "quotation_viewer.php?quotation_id=".$ctable_d['referance_id'];
						}
						else 
						{
							$url = "#";
						}
						?>
						<a href="<?= $url ?>" target="_blank"  title="track"><span class="text-success"><i class="fa fa-eye"></i></span></a>
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
				<select id="numRecords2" onChange="changeDisplayRowCount2(this.value);">
					<option value="25" <?php if ($_REQUEST["show"] == 25 || $_REQUEST["show"] == "" ) { echo ' selected="selected"'; }  ?> >25</option>
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
<?php require_once 'disconnect.php';  ?>