<?php
$page_id=594;$page_slug='leave_request';
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
//print_r($_REQUEST);exit;
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

if($_REQUEST['leave_month'] != "" || $_REQUEST['leave_year'] != "")
{

	if($_REQUEST['leave_month'] != "")
	{

		$ctable_where.=" AND MONTH(start_date)='".$_REQUEST['leave_month']."' ";
	}
	if($_REQUEST['expense_year'] != "")
	{
		
		$ctable_where.=" AND YEAR(start_date)='".$_REQUEST['leave_year']."' ";
	}

}
if(isset($_REQUEST['df']) && $_REQUEST['df']!=""){
	//echo $_REQUEST['df'];exit;
	$date_filter_query = urldecode( $_REQUEST['df'] );

	$date_filter_query_ex=explode(" to ",$date_filter_query);

	$ctable_where .= " AND ( DATE(created_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(created_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";

	// $ctable_where .= " AND ( DATE(start_date) BETWEEN '".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND '".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
	
	//$ctable_where .= " AND ( DATE(start_date)>='".date("Y-m-d",strtotime($date_filter_query_ex['0']))."' AND DATE(start_date)<='".date("Y-m-d",strtotime($date_filter_query_ex['1']))."'  ) ";
	
}

if (isset($_REQUEST['todate']) && $_REQUEST['todate'] != "" && $_REQUEST['todate'] != NULL && $_REQUEST['todate'] != undefined && $_REQUEST['todate']!="01-01-1970") {
	$ctable_where .= " AND start_date >= '" . $_REQUEST['todate'] . "' ";
}

if (isset($_REQUEST['fromdate']) && $_REQUEST['fromdate'] != "" && $_REQUEST['fromdate'] != NULL && $_REQUEST['fromdate'] != undefined && $_REQUEST['fromdate']!="01-01-1970") {
	$ctable_where .= " AND start_date <= '" .$_REQUEST['fromdate']. "' ";
}

// if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
// {
//     $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
//     $ctable_where .= " AND sales_executive_id='".$check_id."' ";
// }



if($_SESSION[SITE_SESS.'_ADMIN_TYPE']!=0)
{

    if($rights['personal_flag']==1)
    {
        $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];
        $ctable_where .= " AND sales_executive_id='".$check_id."' ";
    }
    else
    {
        if($rights['chain_vise_flag'] == 1)
        {
            $check_id = $_SESSION[SITE_SESS.'REFERANCE_ID'];

                $get_sales_type=$db->rp_getValue("sales_executive","type","isDelete=0 AND id='". $check_id."'",0);
                if ($get_sales_type== "sales_manager") 
                {
                    $sales_executive_type = "Regional Sales Manager";
                    $key="sm_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }

                else if ($get_sales_type == "area_sales_manager") 
                {
                    $sales_executive_type = "National Sales Manager";//Business Development Manager
                    $key="asm_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }

                else if ($get_sales_type == "sales_officer") 
                {
                    $sales_executive_type = "Area Sales Manager";//Area Sales Manager
                    $key="so_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }
                else if ($get_sales_type == "sales_executive") 
                {
                    $sales_executive_type = "Sales Officer";
                    $key="se_id";
                    $WhereCondition.=' ' .$key.'='.$check_id;
                }
                else
                {
                    $WhereCondition.=' type = "service_engineer"';
                }

                $data = $db->rp_getData("sales_executive","id",$WhereCondition,"",0);

                $SALEID1=array();
                if($data)
                {
                    while($data_d=mysqli_fetch_assoc($data))
                    {
                        $SALEID1[]=$data_d['id'];
                    }
                }
                if(!empty($SALEID1))
                {
                    $SALEID1=implode(",", $SALEID1);
                    
                        $ctable_where .= "  AND sales_executive_id IN (".$SALEID1.','.$_SESSION[SITE_SESS.'REFERANCE_ID'].")";  
                    
                    
                }
                else
                {
                        $ctable_where .= "  AND sales_executive_id IN (".$_SESSION[SITE_SESS.'REFERANCE_ID'].")";       
                }

        }
        else
        {

        }

    }
}

$get_total_rows = $db->rp_getTotalRecord($ctable,$ctable_where); //hold total records in variable
//break records into pages
$total_pages = ceil($get_total_rows/$item_per_page);

//get starting position to fetch the records
$page_position = (($page_number-1) * $item_per_page);

$ctable_r = $db->rp_getData($ctable,"*",$ctable_where,"id DESC limit $page_position, $item_per_page",0);
?>
<style>
.table-scrollable 
{
	width: auto;
	height: 600px;
	overflow-x: scroll;
	overflow-y: scroll;
	border: 1px solid #e7ecf1;
	margin: 10px 0 !important;
}
.fix-th
    {
        background-color: #f5f5f5 !important;position: sticky;top: 0; z-index: 1;
    }
    .fix-th1
    {
        background-color: #e5e5e5 !important;position: sticky;top: 0; z-index: 1;
    }
</style>
	 <span style="color: red;font-size: 14px;font-style: italic;"><?= CURRENT_DATA_INFO ?></span>
<div class="table-scrollable">
<table id="example1" class="table table-bordered table-striped dataTable ">
	<thead class="fix-th">
	    <tr>
	        <th style="width: 5%;"></th>
	        <th></th>
	        <th></th>
	        <th>
                <select class="form-control input-small" name="leave_type" id="leave_type">
                    <option value="">Select Leave Type</option>
                    <?php
            	    $D_r = $db->rp_getData("leave_type","id,name","isDelete=0","",0);
            		while ($D = mysqli_fetch_assoc($D_r))
            		{
        		    ?>
        			<option value="<?=$D['id']?>" <?=($_REQUEST["leave_type"] == $D['id'])?"selected":"";?>><?=$D['name']?></option>
        			<?php
            		}
                	?>
                </select>
            </th>
            <th>
            	<div class="input-group input-medium pull-left">
					<input class="form-control datetimerange-picker-input " id="material_request_filter_input" value="<?php echo $date_filter_query; ?>" name="df" placeholder="Select Date Range" type="text" style="border-radius: 0;height: 34px!important;">
					<span class="input-group-addon datetimerange-picker-btn">
						<i class="fa fa-calendar"></i>
					</span>
				
					<span class="input-group-btn">
		          	<button class="btn btn-success filterBtn" type="submit" value="search">Filter</button>
		        	</span>
		        </div>
            </th>
            <th>
		  		
            </th>
            <th></th>
            <th>
            	<select class="form-control" name="leave_category" id="leave_category">
				<option value="">Select Leave Category</option>
				<option value="1" <?= ($_REQUEST['leave_category']==1)?"selected":""; ?>>First Half</option>
				<option value="2" <?= ($_REQUEST['leave_category']==2)?"selected":""; ?>>Second Half</option>
				<option value="3" <?= ($_REQUEST['leave_category']==3)?"selected":""; ?>>Full Day</option>
				
				</select>
            </th>
            <th></th>
            <th></th>
            <th>
            	<select class="form-control" name="status" id="status" onChange="return searchBystatus(this.value);">
				<option value="">select status</option>
				<option value="-1" <?= ($_REQUEST['status']==-1)?"selected":""; ?>>GENERATED</option>
				<option value="1" <?= ($_REQUEST['status']==1)?"selected":""; ?>>ACCEPTED</option>
				<option value="2" <?= ($_REQUEST['status']==2)?"selected":""; ?>>REJECTED</option>
				<option value="3" <?= ($_REQUEST['status']==3)?"selected":""; ?>>CANCEL</option>
				</select>
            </th>
            <th></th>
            <th></th>
            <!-- <th></th> -->
        </tr>
		<tr>
			<th class="fix-th1"></th>
			<th class="fix-th1">Sr No.</th>
			<th class="fix-th1">Sales Person Name</th>
			<th class="fix-th1">Leave Type</th>
			<th class="fix-th1">Leave Request Created Date</th>
			<th class="fix-th1">From Date & Time</th>
			<th class="fix-th1">To Date & Time</th>
			<th class="fix-th1">Leave category</th>
			<th class="fix-th1">Total Days Of Leave</th>
			<th class="fix-th1">Cancel/Reject Reason</th>
			<th class="fix-th1">Status</th> 
			<th class="fix-th1">Image</th> 
			<th class="fix-th1">Entry Type</th>
		    <!-- <th>Update Entry Type</th> -->
		</tr>
	</thead>
	<tbody>
	<?php
	if($ctable_r)
	{
		$count = 0;
		while($ctable_d = mysqli_fetch_array($ctable_r))
		{
			// echo "<pre>";
			// print_r($ctable_d);die;
			$count++;
			$datetime1 = new DateTime($ctable_d['end_date']."".$ctable_d['end_time']);
			$datetime2 = new DateTime($ctable_d['start_date']."".$ctable_d['start_time']);
			$interval = $datetime1->diff($datetime2);
			$entry_type_status = array("1"=>"Admin Panel","2"=>"customer","3"=>"Web Sales",4=>"Web Customer",5=>"Sales App",6=> "Customer App");
			$elapsed = $interval->format('%a days %h hours %i minutes %s seconds');
			?>
			<tr>
				<td><?php $ctable_d['id']; ?>				
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
				</td>
				<td><?php echo $count; ?></td>
				<td><?php echo stripslashes($ctable_d['sales_executive_name']); ?></td>
				<td><?php echo $db->rp_getValue("leave_type","name","id='".$ctable_d['leave_type']."'"); ?></td>
				<td><?php echo date("d-m-Y",strtotime($ctable_d['created_date'])); ?></td>

				<td><?php echo "<b>From Date: </b>".date("d-m-Y",strtotime($ctable_d['start_date']))."<br/><b>From Time:</b> "?><?php if($ctable_d['start_time'] == "00:00:00" || $ctable_d['start_time'] == ""){
					echo "";
				}
				else{
				 echo date("h:i a",strtotime($ctable_d['start_time']));
				}
			   ?>
		      </td>
		      <td><?php echo "<b>To Date: </b>".date("d-m-Y",strtotime($ctable_d['end_date']))."<br/><b>To Time:</b> "?><?php if($ctable_d['end_time']=="00:00:00"){
					echo "";
				}
				else{
				 echo date("h:i a",strtotime($ctable_d['end_time']));
				}
			   ?>
		       </td>
			   
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
				<td <?php  if($ctable_d['status']==1){?> style="background: #7bd0a9;text-align: center;margin: 0 auto;" <?php } ?><?php  if($ctable_d['status']==2){?> style="background: #ffe167;text-align: center;margin: 0 auto;" <?php } ?>
					<?php  if($ctable_d['status']==3){?> style="background: #ec9b97;text-align: center;margin: 0 auto;" <?php } ?>>
					<?php if($ctable_d['status']==0 && ($_SESSION[SITE_SESS.'_ADMIN_TYPE']==0 || $rights['approve_flag']==1)){ ?>
					<button type="submit" name="submit" value="print"  onClick="acceptLeave('<?php echo $ctable_d['id']; ?>','accept','Are you sure you want to Accept Leave?')" class="btn yellow btn-sm">Accept Leave</button>
					<button type="submit" name="submit" value="print" data-toggle="modal" data-target="#leaveReasonModal" data-id="<?= $ctable_d['id'] ?>" data-status="Reject Leave" data-status1="Reject Reason" data-status2="reject" data-msg="Are you sure you want to Reject Leave?" class="btn btn-info btn-sm">Reject Leave</button>
					<button type="submit" name="submit" value="print"  data-toggle="modal" data-target="#leaveReasonModal" data-id="<?= $ctable_d['id'] ?>" data-status="Cancel Leave" data-status1="Cancel Reason" data-status2="cancel" data-msg="Are you sure you want to Cancel Leave?" class="btn btn-danger btn-sm">Cancel Leave</button>
					<?php
					}
					else
					{
						if($ctable_d['status']==1)
						{
							echo '<b>'."ACCEPTED".'</b>';
						}
						else if($ctable_d['status']==2)
						{
							echo '<b>'."REJECTED".'</b>';	
						}
						else if($ctable_d['status']==3)
						{
							echo '<b>'."CANCEL".'</b>';	
						}
					} 
					?>
				</td>

				<td>
	            	<?php 
	                if($ctable_d['file_path']!="")
	                {
	                    $img = explode(",", $ctable_d['file_path']);
	                    $imgpath = array();
	                    for ($i=0; $i < sizeof($img); $i++)
	                    { 
	                        $imgpath[] = SITEURL."resource/image/".$db->rp_getValue("media","url","reference_id='".$ctable_d["id"]."' AND id='".$img[$i]."'",0);
	                    }
	                    for ($i=0; $i < sizeof($imgpath); $i++)
	                    {
	                        if($i==0){
	                        ?>
	                            <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="leave <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
	                        <?php 
	                        }else{
	                        ?>
	                            <div class="hidden">
	                                <a href="<?=$imgpath[$i]?>" data-lightbox="complain<?=$count?>" data-title="leave <?=$ctable_d['id']?>"><img src="<?=$imgpath[$i]?>" style="height: 80px;"></a>
	                            </div>
	                        <?php
	                        }
	                    }
	                }
	                else
	                {
	                    $img = $ctable_d['image_path'] = DEFAULTIMG;
	                    ?>
	                    <a href="<?=$img?>" data-lightbox="complain<?=$count?>" data-title="leave <?=$ctable_d['id']?>"><img src="<?=$img?>" style="height: 80px;"></a>
	                    <?php
	                }
	                ?>
		        </td>
		        <td><?php echo $entry_type_status[$ctable_d['entry_flag']]; ?></td>
				<!-- <td><?php echo $entry_type_status[$ctable_d['update_entry_flag']]; ?></td> -->
			</tr>
			<?php
		}
	}
	else
	{
		?>
		<tr>
			<td colspan="12" class="text-center">No Data Found!!</td>
		</tr>
		<?php
	}
	?>
	</tbody>
	</table>
	<br />		


</div>
	<div class="row">
		<div class="col-md-6">
			<div class="dataTables_info"> Rows Limit:
				<select id="numRecords" onChange="changeDisplayRowCount(this.value);">
					<option value="500" <?php if ($_REQUEST["show"] == 500 || $_REQUEST["show"] == "") {echo ' selected="selected"';}  ?>>500</option>
					<option value="1000" <?php if ($_REQUEST["show"] == 1000) {echo ' selected="selected"';}  ?>>1000</option>
					<option value="2000" <?php if ($_REQUEST["show"] == 2000) {echo ' selected="selected"';}  ?>>2000</option>
					<option value="5000" <?php if ($_REQUEST["show"] == 5000) {echo ' selected="selected"';}  ?>>5000</option>
				</select>
			</div>
		</div>
		<div class="col-md-6">
		<div class="dataTables_paginate paging_simple_numbers">
			<ul class="pagination">
			<?php 
			echo paginate_function($item_per_page, $page_number, $get_total_rows, $total_pages); 
			?>
			</ul>
			<span>
			<?php
			for($i=1; $i<=$last; $i++) {
				if ($i == $pagenum ) {
				?>
					<a class="paginate_button current" aria-controls="datatable1"><?php echo $i ?></a>
			<?php
				} else {  
				?>
					<a class="paginate_button" aria-controls="datatable1" onclick="displayRecords('<?php echo $page_limit;  ?>', '<?php echo $i; ?>');"><?php echo $i ?></a>
			<?php 
				}
			} 
			?>
			</span>
		</div>
		</div>
	</div>	
<div class="modal fade" id="leaveReasonModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="leave_status"></h4>
				<button style="margin-top: -15px!important;" type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<label for="cancel-reject reason" id="status1"></label>
				<textarea class="form-control" name="cancel_reject_reason" id="cancel_reject_reason"></textarea>
				<p style="color:red;" id="require_msg"></p>
				<input type="hidden" class="leave_id" name="leave_id" id="leave_id">
				<input type="hidden" class="status2" name="mode" id="mode">
				<input type="hidden" class="msg" name="msg" id="msg">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" onclick="CancelRejectReason()">Save changes</button>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	$('#leaveReasonModal').on('show.bs.modal', function (event) 
    {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id=button.data("id");
        $(".leave_id").val(id);
        var status=button.data("status");
        $("#leave_status").html(status);
        var status1=button.data("status1");
        $("#status1").html(status1);
        var status2=button.data("status2");
        $(".status2").val(status2);
        var msg=button.data("msg");
        $(".msg").val(msg);
    })
</script>

<!-- <?php
	echo $db->getAddButton($ctable,$ctable1);
?> -->
<?php
################ pagination function #########################################
function paginate_function($item_per_page, $current_page, $total_records, $total_pages)
{
    $pagination = '';
    if($total_pages > 0 && $total_pages != 1 && $current_page <= $total_pages){ //verify total pages and current page number
        $right_links    = $current_page + 3; 
        $previous       = $current_page - 3; //previous link 
        $next           = $current_page + 1; //next link
        $first_link     = true; //boolean var to decide our first link
        
        if($current_page > 1){
			$previous_link = ($previous<=0)?1:$previous;
            $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="1" title="First">&laquo;</a></li>'; //first link
            $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$previous_link.'" title="Previous">&lt;</a></li>'; //previous link
                for($i = ($current_page-2); $i < $current_page; $i++){ //Create left-hand side links
                    if($i > 0){
                        $pagination .= '<li class="paginate_button "><a href="#"  data-page="'.$i.'" aria-controls="datatable1" title="Page'.$i.'">'.$i.'</a></li>';
                    }
                }   
            $first_link = false; //set first link to false
        }
        
        if($first_link){ //if current active page is first link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }elseif($current_page == $total_pages){ //if it's the last active link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }else{ //regular current link
            $pagination .= '<li class="paginate_button active"><a aria-controls="datatable1">'.$current_page.'</a></li>';
        }
                
        for($i = $current_page+1; $i < $right_links ; $i++){ //create right-hand side links
            if($i<=$total_pages){
                $pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$i.'" title="Page '.$i.'">'.$i.'</a></li>';
            }
        }
        if($current_page < $total_pages){ 
			$next_link = ($i > $total_pages)? $total_pages : $i;
			$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$next_link.'" title="Next">&gt;</a></li>'; //next link
			$pagination .= '<li class="paginate_button "><a href="#" aria-controls="datatable1" data-page="'.$total_pages.'" title="Last">&raquo;</a></li>'; //last link
        }
    }
    return $pagination; //return pagination links
}
?>


<script type="text/javascript">
	function acceptLeave(id,mode,msg)
	{   
		var cancel_reject_reason = $("#cancel_reject_reason").val();
		if(cancel_reject_reason=="" && mode!="accept")
		{
			var require_msg = "Please Provide Reason";
			$("#require_msg").html(require_msg);
		}
		else
		{
			var r = confirm(msg);
	        if(r){
	        	$.ajax({
	                url:"leave_request_ajax_function.php",
	                type:"POST",
	                data:{
	                    m:mode,
	                    id:id, 
	                    cancel_reject_reason,cancel_reject_reason,               
	                },
	                beforeSend: function() {
						// $("#loading-modal").modal('show');
						$('.preloader').fadeIn('slow');
					},
	                success:function(result) 
	                {
	                    var result=JSON.parse(result);
	                    // $("#loading-modal").modal('hide');
	                    $('.preloader').fadeOut('slow');
	                    if(result.ack==1)
	                    {                       
	                        toastr.success(result.ack_msg,"Success!!"); 
	                        location.reload();
	                    }
	                    else
	                    {
	                        toastr.error(result.ack_msg, 'Error!!');
	                    }
	                },            
	            })
	        }
	    }
	}
	function CancelRejectReason()
	{
		var cancel_reject_reason = $("#cancel_reject_reason").val();
		var leave_id = $("#leave_id").val();
		var mode = $("#mode").val();
		var msg = $("#msg").val();
		// $('#leaveReasonModal').modal('hide');
		acceptLeave(leave_id,mode,msg);
	}
</script> 
<script type="text/javascript">
	$(".filterBtn").on("click",function()
	{
		sales_executive = $("#sales_executive").val();
		status = $("#status").val();
		customer_id = $("#customer_id").val();
		df=$("#material_request_filter_input").val();
		df = encodeURI(df)
		displayRecords(100,1);
	})
	
	$(".datetimerange-picker-btn").on("click",function(){
		$(".datetimerange-picker-input",$(this).closest(".input-group")).focus();
	});
	$(".datetimerange-picker-input").daterangepicker({"format":"dd-mm-yy ",autoUpdateInput: false,timePicker:false,ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
}});
	$('.datetimerange-picker-input').on('apply.daterangepicker', function(ev, picker) {
 $(".datetimerange-picker-input").val(picker.startDate.format('YYYY-MM-DD')+" to "+picker.endDate.format('YYYY-MM-DD'));
});
</script>
<script type="text/javascript">
	$("#leave_type").select2();
	$("#status").select2();
	$("#leave_category").select2();
</script>
<?php include("disconnect.php"); ?>